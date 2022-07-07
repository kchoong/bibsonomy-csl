<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;

use AcademicPuma\BibsonomyCsl\Utils\ApiUtils;
use AcademicPuma\BibsonomyCsl\Utils\PostUtils;
use AcademicPuma\RestClient\Config\Grouping;
use AcademicPuma\RestClient\Config\Resourcetype;
use AcademicPuma\RestClient\Config\Sorting;
use AcademicPuma\RestClient\Model\Post;
use AcademicPuma\RestClient\Model\Posts;
use AcademicPuma\RestClient\RESTClient;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * This file is part of the "BibSonomy CSL" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2022 Kevin Choong <choong.kvn@gmail.com>
 *          Sebastian Böttger <boettger@cs.uni-kassel.de>
 */

/**
 * PublicationController
 */
class PublicationController extends ApiActionController
{

    const POST_COUNT_LIMIT = 250;
    const SEARCHTYPE = 'searchindex';

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        // create API accessor
        $this->makeAccessor();

        // assign settings to view
        $this->view->assign('settings', $this->settings);
        $this->view->assign('grouping', $this->settings['grouping']);
        $this->view->assign('sorting', $this->settings['sorting']);
        $this->view->assign('filtering', $this->settings['filtering']);
        $this->view->assign('layout', $this->settings['layout']);
        $this->view->assign('custom', $this->settings['custom']);

        // get posts
        $posts = $this->getPosts($this->settings);
        $titles = '';
        foreach ($posts as $post) {
            $titles .= $post->getResource()->getTitle();
        }
        $this->view->assign('listHash', md5($titles));

        // prepare entrytype list with labels
        $entrytypeLabels = $this->getEntrytypeLabels($this->settings['sorting']['entrytypeOrder'], $this->settings['custom']['entrytype']);
        $entrytypeOrder = array_keys($entrytypeLabels);
        $this->view->assign('entrytypes', $entrytypeLabels);

        // filtering, grouping & sorting of posts
        $this->filterPosts($posts, $this->settings['filtering']);
        $this->groupAndSortPosts($posts, $this->settings, $entrytypeOrder);

        // assign posts
        $this->view->assign('posts', $posts);

        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param Post $publication
     * @return ResponseInterface
     */
    public function showAction(Post $publication): ResponseInterface
    {
        $this->view->assign('publication', $publication);
        return $this->htmlResponse();
    }

    public function getPosts(array $settings): Posts
    {
        // create REST client
        $client = ApiUtils::getRestClient($this->accessor, $settings);

        // get settings for request and model
        $content = $settings['content'];
        $grouping = $content['sourceType'];
        $groupingName = $content['sourceId'];
        $tags = $this->getFilterTags($client, $content);
        $search = $content['search'];
        $maxCount = $content['maxCount'];

        $resourceType = Resourcetype::BIBTEX;
        if ($grouping === Grouping::PERSON) {
            $resourceType = Resourcetype::GOLD_STANDARD_PUBLICATION;
        }

        $layout = $settings['layout'];
        $treatCurlyBraces = intval($layout['curlyBraces']);
        $treatBackslashes = intval($layout['treatBackslashes']);
        $bibtexCleaning = $layout['bibtexCleaning'] === '1';

        // get posts

        try {
            $result = [];
            $limit = !empty($maxCount) ? intval(filter_var($maxCount, FILTER_SANITIZE_NUMBER_INT)) : 0;

            if ($limit > self::POST_COUNT_LIMIT) {
                for ($i = 1; ($i * self::POST_COUNT_LIMIT) <= $limit + self::POST_COUNT_LIMIT; ++$i) {
                    $start = ($i - 1) * self::POST_COUNT_LIMIT;
                    $end = $start + self::POST_COUNT_LIMIT;
                    if ($end > $limit) {
                        $end = $limit;
                    }

                    $client->getPosts($resourceType, $grouping, $groupingName, $tags, '', $search, [], [], self::SEARCHTYPE, $start, $end)->model();
                    $result = array_merge($result, $client->model($treatCurlyBraces, $treatBackslashes, $bibtexCleaning)->toArray());
                }
            } else {
                $client->getPosts($resourceType, $grouping, $groupingName, $tags, '', $search, [], [], self::SEARCHTYPE, 0, $limit)->model();
                $result = $client->model($treatCurlyBraces, $treatBackslashes, $bibtexCleaning)->toArray();
            }
        } catch (BadResponseException|Exception $e) {
            return new Posts();
        }

        return new Posts($result);
    }

    private function filterPosts(Posts &$posts, array $settings): void
    {
        $filterDuplicates = $settings['duplicates'];
        $filterYear = $settings['year'];
        $filterNumericYear = $settings['numericYear'];

        $hashes = array();

        foreach ($posts as $key => $post) {
            $postYear = $post->getResource()->getYear();

            // Filter posts with not the filter year
            if ($filterYear) {
                if ($postYear !== $filterYear) {
                    unset($posts[$key]);
                }
            }

            // Filter posts with non-numeric year
            if (!$filterNumericYear) {
                if (!is_numeric($postYear)) {
                    unset($posts[$key]);
                }
            }

            // Filter duplicates
            if ($filterDuplicates > 0) {
                // Either use inter- or intrahash depending on the plugin settings
                $hash = $filterDuplicates == 1 ? $post->getResource()->getIntraHash() : $post->getResource()->getInterHash();

                if (in_array($hash, $hashes)) {
                    unset($posts[$key]);
                } else {
                    $hashes[] = $hash;
                }
            }
        }
    }

    private function groupAndSortPosts(Posts &$posts, array $settings, array $entrytypeOrder)
    {
        $grouping = $settings['grouping'];
        $groupingKey = $grouping['key'];

        $sorting = $settings['sorting'];
        $sortKey = $sorting['sortKey'];
        $sortOrder = $sorting['sortOrder'];

        // grouping & sorting
        if ($groupingKey != 'none') {
            $posts = $this->prepareGrouping($posts, $groupingKey, $entrytypeOrder);

            // Set the sorting within the group for DBLP grouping
            if ($grouping == 'dblp') {
                $sortKey = 'dblp';
                $sortOrder = 'desc';
            }

            // sort within sublists of groups
            $groupKeys = array_keys($posts->toArray()); //get groups
            foreach ($groupKeys as $groupKey) {
                if ($posts[$groupKey] instanceof Posts) {
                    $sublist = new Posts($posts[$groupKey]->toArray());
                } else {
                    $sublist = new Posts($posts[$groupKey]);
                }
                $sublist->sort($sortKey, $sortOrder === 'desc' ? Sorting::ORDER_DESC : Sorting::ORDER_ASC);
                $posts[$groupKey] = $sublist;
            }
        } else {
            $posts->sort($sortKey, $sortOrder == 'asc' ? Sorting::ORDER_ASC : Sorting::ORDER_DESC);
            $posts[''] = $posts;
        }
    }

    /**
     * Prepares type order and filters publications (grouping by type) or does
     * pre-sorting (grouping by year). This function also transforms <code>$posts</code>
     * to an grouped Posts ArrayList.
     * for instance:
     * <pre>
     * array(
     *   [2010] => array(
     *          [0] => pub1
     *          [1] => pub2)
     *   [2014] => array(
     *          [0] => pub3
     *          [1] => pub5)
     * )
     * </pre>
     *
     * @param Posts $posts un-grouped Posts ArrayList
     * @param string $grouping
     * @param array $entrytypeOrder
     * @return Posts
     */
    private function prepareGrouping(Posts &$posts, string $grouping, array $entrytypeOrder): Posts
    {
        switch ($grouping) {
            case 'entrytype':
                $posts->sort('entrytype', Sorting::ORDER_ASC, $entrytypeOrder);
                $this->filterPublicationsByType($posts, $entrytypeOrder, $this->settings['sorting']);
                break;
            case 'dblp':
                $posts->sort('year', Sorting::ORDER_DESC);
                PostUtils::transformToYearGroupedArray($posts);
                break;
            case 'yearAsc':
                //pre-sorting (asc) for year grouping
                $posts->sort('year', Sorting::ORDER_ASC);
                PostUtils::transformToYearGroupedArray($posts);
                break;
            case 'yearDesc':
                //pre-sorting (desc) for year grouping
            default:
                $posts->sort('year', Sorting::ORDER_DESC);
                PostUtils::transformToYearGroupedArray($posts);
        }

        return $posts;
    }

    private function filterPublicationsByType(Posts &$posts, array $entrytypeOrder, array $settings)
    {
        $sortKey = $settings['sortKey'];
        $sortOrder = $settings['sortOrder'];

        $filteredPublications = [];
        for ($i = 0; $i < count($posts); ++$i) {
            $entrytype = $posts[$i]->getResource()->getEntrytype();
            $type = PostUtils::getTypeOfType($entrytype);

            if (in_array($type, $entrytypeOrder)) {
                $filteredPublications[$type][] = $posts[$i];
            }
        }

        $posts->replace([]);
        foreach (array_keys($filteredPublications) as $group) {
            $subList = new Posts($filteredPublications[$group]);
            $subList->sort($sortKey, $sortOrder);
            $posts->add($group, $subList);
        }
    }

    private function getEntrytypeLabels(string $entrytypeOrder, string $customEntrytypeOrder): array
    {
        $result = [];
        $entrytypes = $entrytypeOrder ? explode(',', $entrytypeOrder) : PostUtils::$DEFAULT_TYPE_ORDER;
        foreach ($entrytypes as $entrytype) {
            $longLabel = LocalizationUtility::translate("entrytype.$entrytype", 'BibsonomyCsl');
            $longLabel = $longLabel ? $longLabel : $entrytype;
            $shortLabel = LocalizationUtility::translate("entrytype.$entrytype.short", 'BibsonomyCsl');
            $shortLabel = $shortLabel ? $shortLabel : $entrytype;
            $result[$entrytype] = [
                'long' => $longLabel,
                'short' => $shortLabel,
            ];
        }

        $customEntrtypes = $customEntrytypeOrder ? explode("\n", $customEntrytypeOrder) : [];
        foreach ($customEntrtypes as $entrytype) {
            $customArr = explode('; ', $entrytype);
            if (!empty($customArr) && count($customArr) == 3) {
                $result[$customArr[0]] = [
                    'long' => $customArr[1],
                    'short' => $customArr[2],
                ];
            }
        }

        return $result;
    }

    private function getFilterTags(RESTClient $client, array $settings): array
    {
        $tags = [];

        $groupingName = $settings['sourceId'];
        $tagsSelected = $settings['tagsSelect'];
        $tagsUnselected = $settings['tagsUnselect'];
        $conceptsSelected = $settings['conceptsSelect'];
        $conceptsUnselected = $settings['conceptsUnselect'];

        // Add tags
        if (!empty($tagsSelected)) {
            $tags = array_merge($tags, explode(" ", $tagsSelected));
        }

        // Exclude content with tags by prepending sys:not: to the tags
        if (!empty($tagsUnselected)) {
            $tagsUnselectedArr = explode(" ", trim($tagsUnselected));
            foreach ($tagsUnselectedArr as $tagUn) {
                $tags[] = 'sys:not:' . trim(filter_var($tagUn, FILTER_SANITIZE_URL));
            }
        }

        // Add subtags from concept
        if (!empty($conceptsSelected)) {
            $concepts = explode(" ", $conceptsSelected);

            foreach ($concepts as $concept) {
                try {
                    $query = $client->getConceptDetails($concept, $groupingName);
                    foreach ($query->model()->getSubTags() as $subtag) {
                        $tags[] = $subtag->getName();
                    }
                } catch (BadResponseException|Exception $e) {

                }
            }
        }

        // Exclude content with concepts by prepending sys:not: to the subtags
        if (!empty($conceptsUnselected)) {
            $concepts = explode(" ", $conceptsUnselected);

            foreach ($concepts as $concept) {

                try {
                    $query = $client->getConceptDetails($concept, $groupingName);
                    foreach ($query->model()->getSubTags() as $subtag) {
                        $tags[] = 'sys:not:' . trim(filter_var($subtag, FILTER_SANITIZE_URL));
                    }
                } catch (BadResponseException|Exception $e) {

                }
            }
        }

        return $tags;
    }
}

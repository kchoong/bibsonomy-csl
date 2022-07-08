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
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\StyleSheet;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 *  PUMA/BibSonomy CSL (bibsonomy_csl) is a TYPO3 extension which
 *  enables users to render publication lists from PUMA or BibSonomy in
 *  various styles.
 *
 *  Copyright notice
 * (c) 2022 Kevin Choong <choong.kvn@gmail.com>
 *          Sebastian Böttger <boettger@cs.uni-kassel.de>
 *
 *  HothoData GmbH (http://www.academic-puma.de)
 *  Knowledge and Data Engineering Group (University of Kassel)
 *
 *  All rights reserved
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
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

        // assign citation stylesheet
        switch ($this->settings['layout']['stylesheet']) {
            case 'default':
                $databaseId = $this->settings['layout']['stylesheetDefault'];
                if (!is_numeric($databaseId)) {
                    // ID is not numeric, it means it's a default CSL of the plugin we have to load
                    $fileContent = file_get_contents(GeneralUtility::getFileAbsFileName("EXT:bibsonomy_csl/Resources/Private/CSL/$databaseId"));
                    $this->view->assign('stylesheet', $fileContent);
                } else {

                }
                break;
            case 'xml':
                $this->view->assign('stylesheet', $this->settings['layout']['stylesheetXML']);
                break;
            case 'name':
            default:
                try {
                    $this->view->assign('stylesheet', StyleSheet::loadStyleSheet($this->settings['layout']['stylesheetName']));
                } catch (CiteProcException $e) {
                    $this->view->assign('stylesheet', StyleSheet::loadStyleSheet('apa'));
                }
                break;
        }

        // get posts
        $posts = $this->getPosts($this->settings);
        $titles = '';
        foreach ($posts as $post) {
            $titles .= $post->getResource()->getTitle();
        }
        $this->view->assign('listHash', md5($titles));

        // filtering, grouping & sorting of posts
        $this->filterPosts($posts, $this->settings['filtering']);
        $this->groupAndSortPosts($posts, $this->settings);

        // prepare entrytype list with labels
        if ($this->settings['grouping']['key'] == 'entrytype') {
            $entrytypeLabels = $this->getEntrytypeLabels(array_keys($posts->toArray()), $this->settings['layout']['language']);
            $this->view->assign('entrytypes', $entrytypeLabels);
        }

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
        $filterEntrytypes = $settings['entrytypes'] != "" ? explode(" ", $settings['entrytypes']) : [];

        $hashes = array();

        foreach ($posts as $key => $post) {
            $resource = $post->getResource();
            $postYear = $resource->getYear();
            $postEntrytype = $resource->getEntrytype();

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

            // Filter posts with not in the included entrytypes
            if ($filterEntrytypes) {
                if (!in_array($postEntrytype, $filterEntrytypes)) {
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

    private function groupAndSortPosts(Posts &$posts, array $settings)
    {
        $grouping = $settings['grouping'];
        $groupingKey = $grouping['key'];

        $sorting = $settings['sorting'];
        $sortKey = $sorting['sortKey'];
        $sortOrder = $sorting['sortOrder'];

        // grouping & sorting
        if ($groupingKey != 'none') {
            $posts = $this->prepareGrouping($posts, $groupingKey);

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
     * @return Posts
     */
    private function prepareGrouping(Posts &$posts, string $grouping): Posts
    {
        switch ($grouping) {
            case 'entrytype':
                $posts->sort('entrytype', Sorting::ORDER_ASC);
                $this->transformToEntrytypeGroupedArray($posts, $this->settings['sorting']);
                break;
            case 'yearAsc':
                //pre-sorting (asc) for year grouping
                $posts->sort('year', Sorting::ORDER_ASC);
                PostUtils::transformToYearGroupedArray($posts);
                break;
            case 'dblp':
            case 'yearDesc':
                //pre-sorting (desc) for year grouping
            default:
                $posts->sort('year', Sorting::ORDER_DESC);
                PostUtils::transformToYearGroupedArray($posts);
        }

        return $posts;
    }

    private function transformToEntrytypeGroupedArray(Posts &$posts, array $settings)
    {
        $sortKey = $settings['sortKey'];
        $sortOrder = $settings['sortOrder'];

        $processed = [];
        for ($i = 0; $i < count($posts); ++$i) {
            $entrytype = $posts[$i]->getResource()->getEntrytype();
            $processed[$entrytype][] = $posts[$i];
        }

        $posts->replace([]);
        foreach (array_keys($processed) as $group) {
            $subList = new Posts($processed[$group]);
            $subList->sort($sortKey, $sortOrder);
            $posts->add($group, $subList);
        }
    }

    private function getEntrytypeLabels(array $entrytypes, string $lang='en-US'): array
    {
        $result = [];
        $customEntrytypesCsv = GeneralUtility::makeInstance(ExtensionConfiguration::class)
            ->get('bibsonomy_csl')['customEntrytypes'];

        if ($customEntrytypesCsv) {
            $customEntrytypesArr = explode(PHP_EOL, $customEntrytypesCsv);
            if ($customEntrytypesArr) {
                foreach ($customEntrytypesArr as $entrytype) {
                    $entryArr = explode(',', $entrytype);
                    if (count($entryArr) == 4) {
                        $label = $lang == 'de-DE' ? $entryArr[2] : $entryArr[1];
                        $result[$entryArr[0]] = [
                            'label' => $label,
                            'description' => $label,
                        ];
                    }
                }
            }
        }

        foreach ($entrytypes as $entrytype) {
            if (!array_key_exists($entrytype, $result)) {
                $label = LocalizationUtility::translate("entrytype.$entrytype", 'BibsonomyCsl');
                $label = $label ?: $entrytype;
                $description = LocalizationUtility::translate("entrytype.$entrytype.desc", 'BibsonomyCsl');
                $description = $description ?: '';
                $result[$entrytype] = [
                    'label' => $label,
                    'description' => $description,
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

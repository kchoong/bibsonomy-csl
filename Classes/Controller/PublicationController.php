<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;

use AcademicPuma\BibsonomyCsl\Utils\ApiUtils;
use AcademicPuma\RestClient\Config\Grouping;
use AcademicPuma\RestClient\Config\Resourcetype;
use AcademicPuma\RestClient\Model\Post;
use AcademicPuma\RestClient\RESTClient;
use Exception;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;

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

        // assign posts
        $posts = $this->getPosts($this->settings);
        $this->filterPosts($posts);
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

    public function getPosts(array $settings): array
    {
        // create RESTclient
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
            return array();
        }

        return $result;
    }

    private function filterPosts(array &$posts): void
    {
        $filterDuplicates = $this->settings['filtering']['duplicates'];
        $filterYear = $this->settings['filtering']['year'];
        $filterNumericYear = $this->settings['filtering']['numericYear'];

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

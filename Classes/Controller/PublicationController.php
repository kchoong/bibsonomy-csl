<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Domain\Repository\PublicationRepository;
use AcademicPuma\BibsonomyCsl\Utils\ApiUtils;
use AcademicPuma\RestClient\Config\Grouping;
use AcademicPuma\RestClient\Config\Resourcetype;
use AcademicPuma\RestClient\Config\RESTConfig;
use AcademicPuma\RestClient\Model\Post;
use AcademicPuma\RestClient\Model\Posts;
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
        $contentSettings = $settings['content'];
        $grouping = $contentSettings['sourceType'];
        $groupingName = $contentSettings['sourceId'];

        $resourceType = Resourcetype::BIBTEX;
        if ($grouping === Grouping::PERSON) {
            $resourceType = Resourcetype::GOLD_STANDARD_PUBLICATION;
        }

        $client = ApiUtils::getRestClient($this->accessor, $settings);
        $client->getPosts($resourceType, $grouping, $groupingName, [], "", $contentSettings['search'], [], [], 'searchindex', 0, 20, 'xml')->model();
        $posts = $client->model()->toArray();
        return $posts;
    }

    public function preparePosts(array $settings, Posts $posts): void
    {

    }
}

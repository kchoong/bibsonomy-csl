<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


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
 * TagController
 */
class TagController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * tagRepository
     *
     * @var \AcademicPuma\BibsonomyCsl\Domain\Repository\TagRepository
     */
    protected $tagRepository = null;

    /**
     * @param \AcademicPuma\BibsonomyCsl\Domain\Repository\TagRepository $tagRepository
     */
    public function injectTagRepository(\AcademicPuma\BibsonomyCsl\Domain\Repository\TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $tags = $this->tagRepository->findAll();
        $this->view->assign('tags', $tags);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\Tag $tag
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\AcademicPuma\BibsonomyCsl\Domain\Model\Tag $tag): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('tag', $tag);
        return $this->htmlResponse();
    }
}

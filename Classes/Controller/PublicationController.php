<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Domain\Repository\PublicationRepository;
use AcademicPuma\RestClient\Authentication\BasicAuthAccessor;
use AcademicPuma\RestClient\Config\Grouping;
use AcademicPuma\RestClient\Config\Resourcetype;
use AcademicPuma\RestClient\Model\Post;
use AcademicPuma\RestClient\RESTClient;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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
class PublicationController extends ActionController
{

    /**
     * publicationRepository
     *
     * @var PublicationRepository
     */
    protected $publicationRepository = null;

    /**
     * @param PublicationRepository $publicationRepository
     */
    public function injectPublicationRepository(PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {

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
}

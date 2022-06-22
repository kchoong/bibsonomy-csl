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
 * PublicationController
 */
class PublicationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * publicationRepository
     *
     * @var \AcademicPuma\BibsonomyCsl\Domain\Repository\PublicationRepository
     */
    protected $publicationRepository = null;

    /**
     * @param \AcademicPuma\BibsonomyCsl\Domain\Repository\PublicationRepository $publicationRepository
     */
    public function injectPublicationRepository(\AcademicPuma\BibsonomyCsl\Domain\Repository\PublicationRepository $publicationRepository)
    {
        $this->publicationRepository = $publicationRepository;
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $publications = $this->publicationRepository->findAll();
        $this->view->assign('publications', $publications);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\Publication $publication
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\AcademicPuma\BibsonomyCsl\Domain\Model\Publication $publication): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('publication', $publication);
        return $this->htmlResponse();
    }
}

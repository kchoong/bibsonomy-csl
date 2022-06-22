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
 * DocumentController
 */
class DocumentController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * documentRepository
     *
     * @var \AcademicPuma\BibsonomyCsl\Domain\Repository\DocumentRepository
     */
    protected $documentRepository = null;

    /**
     * @param \AcademicPuma\BibsonomyCsl\Domain\Repository\DocumentRepository $documentRepository
     */
    public function injectDocumentRepository(\AcademicPuma\BibsonomyCsl\Domain\Repository\DocumentRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $documents = $this->documentRepository->findAll();
        $this->view->assign('documents', $documents);
        return $this->htmlResponse();
    }

    /**
     * action show
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\Document $document
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function showAction(\AcademicPuma\BibsonomyCsl\Domain\Model\Document $document): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('document', $document);
        return $this->htmlResponse();
    }

    /**
     * action download
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function downloadAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }
}

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
 * CitationStylesheetController
 */
class CitationStylesheetController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * citationStylesheetRepository
     *
     * @var \AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository
     */
    protected $citationStylesheetRepository = null;

    /**
     * @param \AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository $citationStylesheetRepository
     */
    public function injectCitationStylesheetRepository(\AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository $citationStylesheetRepository)
    {
        $this->citationStylesheetRepository = $citationStylesheetRepository;
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $citationStylesheets = $this->citationStylesheetRepository->findAll();
        $this->view->assign('citationStylesheets', $citationStylesheets);
        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function newAction(): \Psr\Http\Message\ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $newCitationStylesheet
     */
    public function createAction(\AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $newCitationStylesheet)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->citationStylesheetRepository->add($newCitationStylesheet);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $citationStylesheet
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("citationStylesheet")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $citationStylesheet): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('citationStylesheet', $citationStylesheet);
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $citationStylesheet
     */
    public function updateAction(\AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $citationStylesheet)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->citationStylesheetRepository->update($citationStylesheet);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $citationStylesheet
     */
    public function deleteAction(\AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet $citationStylesheet)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->citationStylesheetRepository->remove($citationStylesheet);
        $this->redirect('list');
    }
}

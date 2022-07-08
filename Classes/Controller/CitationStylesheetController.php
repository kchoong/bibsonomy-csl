<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet;
use AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
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
 * CitationStylesheetController
 */
class CitationStylesheetController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    protected $moduleTemplateFactory = null;
    protected $moduleTemplate = null;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * citationStylesheetRepository
     *
     * @var CitationStylesheetRepository
     */
    protected $citationStylesheetRepository = null;

    /**
     * @param CitationStylesheetRepository $citationStylesheetRepository
     */
    public function injectCitationStylesheetRepository(CitationStylesheetRepository $citationStylesheetRepository)
    {
        $this->citationStylesheetRepository = $citationStylesheetRepository;
    }

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $citationStylesheets = $this->citationStylesheetRepository->findAll();
        $this->view->assign('citationStylesheets', $citationStylesheets);

        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        // Adding title, menus, buttons, etc. using $moduleTemplate ...
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action new
     *
     * @return ResponseInterface
     */
    public function newAction(): ResponseInterface
    {
        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        // Adding title, menus, buttons, etc. using $moduleTemplate ...
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action create
     *
     * @param CitationStylesheet $newCitationStylesheet
     */
    public function createAction(CitationStylesheet $newCitationStylesheet)
    {
        $message = LocalizationUtility::translate("LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.new.success",
            'BibsonomyCsl');
        $this->addFlashMessage($message, "", \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->citationStylesheetRepository->add($newCitationStylesheet);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param CitationStylesheet $citationStylesheet
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("citationStylesheet")
     * @return ResponseInterface
     */
    public function editAction(CitationStylesheet $citationStylesheet): ResponseInterface
    {
        $this->view->assign('citationStylesheet', $citationStylesheet);

        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);

        // Adding title, menus, buttons, etc. using $moduleTemplate ...
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action update
     *
     * @param CitationStylesheet $citationStylesheet
     */
    public function updateAction(CitationStylesheet $citationStylesheet)
    {
        $message = LocalizationUtility::translate("LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.edit.success",
            'BibsonomyCsl');
        $this->addFlashMessage($message, "", \TYPO3\CMS\Core\Messaging\AbstractMessage::OK);
        $this->citationStylesheetRepository->update($citationStylesheet);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param CitationStylesheet $citationStylesheet
     */
    public function deleteAction(CitationStylesheet $citationStylesheet)
    {
        $message = LocalizationUtility::translate("LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.delete.success",
            'BibsonomyCsl');
        $this->addFlashMessage($message, "", \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->citationStylesheetRepository->remove($citationStylesheet);
        $this->redirect('list');
    }
}

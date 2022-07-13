<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Domain\Model\CitationStylesheet;
use AcademicPuma\BibsonomyCsl\Domain\Repository\CitationStylesheetRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
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
 * CitationStylesheetController
 */
class CitationStylesheetController extends ActionController
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
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action create
     *
     * @param CitationStylesheet $newCitationStylesheet
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function createAction(CitationStylesheet $newCitationStylesheet)
    {
        $message = LocalizationUtility::translate("LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.new.success",
            'BibsonomyCsl');
        $this->addFlashMessage($message, "", AbstractMessage::OK);
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
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action update
     *
     * @param CitationStylesheet $citationStylesheet
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnknownObjectException
     */
    public function updateAction(CitationStylesheet $citationStylesheet)
    {
        $message = LocalizationUtility::translate("LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.edit.success",
            'BibsonomyCsl');
        $this->addFlashMessage($message, "", AbstractMessage::OK);
        $this->citationStylesheetRepository->update($citationStylesheet);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param CitationStylesheet $citationStylesheet
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     */
    public function deleteAction(CitationStylesheet $citationStylesheet)
    {
        $message = LocalizationUtility::translate("LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.citationstylesheet.delete.success",
            'BibsonomyCsl');
        $this->addFlashMessage($message, "", AbstractMessage::WARNING);
        $this->citationStylesheetRepository->remove($citationStylesheet);
        $this->redirect('list');
    }
}

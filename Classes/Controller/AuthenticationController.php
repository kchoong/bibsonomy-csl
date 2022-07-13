<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Domain\Model\Authentication;
use AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository;
use AcademicPuma\BibsonomyCsl\Utils\BackendUtils;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

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
 * AuthenticationController
 */
class AuthenticationController extends ActionController
{

    protected $moduleTemplateFactory = null;
    protected $moduleTemplate = null;

    public function __construct(ModuleTemplateFactory $moduleTemplateFactory)
    {
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    /**
     * authenticationRepository
     *
     * @var AuthenticationRepository
     */
    protected $authenticationRepository = null;

    /**
     * @param AuthenticationRepository $authenticationRepository
     */
    public function injectAuthenticationRepository(AuthenticationRepository $authenticationRepository)
    {
        $this->authenticationRepository = $authenticationRepository;
    }

    /**
     * action list
     *
     * @return ResponseInterface
     */
    public function listAction(): ResponseInterface
    {
        $authentications = $this->authenticationRepository->findAll();
        $this->view->assign('authentications', $authentications);

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
        $hosts = [];
        $config = [];
        foreach (BackendUtils::getHosts($config)['items'] as $host) {
            $hosts[] = [
                "key" => $host[1],
                "value" => $host[0],
            ];
        }
        $this->view->assign('hosts', $hosts);

        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action create
     *
     * @param Authentication $newAuthentication
     * @throws StopActionException
     * @throws IllegalObjectTypeException
     */
    public function createAction(Authentication $newAuthentication)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->authenticationRepository->add($newAuthentication);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param Authentication $authentication
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("authentication")
     * @return ResponseInterface
     */
    public function editAction(Authentication $authentication): ResponseInterface
    {
        $this->view->assign('authentication', $authentication);

        $this->moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $this->moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($this->moduleTemplate->renderContent());
    }

    /**
     * action update
     *
     * @param Authentication $authentication
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     * @throws UnknownObjectException
     */
    public function updateAction(Authentication $authentication)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->authenticationRepository->update($authentication);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param Authentication $authentication
     * @throws IllegalObjectTypeException
     * @throws StopActionException
     */
    public function deleteAction(Authentication $authentication)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->authenticationRepository->remove($authentication);
        $this->redirect('list');
    }
}

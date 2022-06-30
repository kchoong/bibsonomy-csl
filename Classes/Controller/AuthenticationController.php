<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Domain\Model\Authentication;
use AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository;
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
 * AuthenticationController
 */
class AuthenticationController extends ActionController
{

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
        return $this->htmlResponse();
    }

    /**
     * action new
     *
     * @return ResponseInterface
     */
    public function newAction(): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action create
     *
     * @param Authentication $newAuthentication
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
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param Authentication $authentication
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
     */
    public function deleteAction(Authentication $authentication)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->authenticationRepository->remove($authentication);
        $this->redirect('list');
    }
}

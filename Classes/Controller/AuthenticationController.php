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
 * AuthenticationController
 */
class AuthenticationController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * authenticationRepository
     *
     * @var \AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository
     */
    protected $authenticationRepository = null;

    /**
     * @param \AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository $authenticationRepository
     */
    public function injectAuthenticationRepository(\AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository $authenticationRepository)
    {
        $this->authenticationRepository = $authenticationRepository;
    }

    /**
     * action list
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function listAction(): \Psr\Http\Message\ResponseInterface
    {
        $authentications = $this->authenticationRepository->findAll();
        $this->view->assign('authentications', $authentications);
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
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $newAuthentication
     */
    public function createAction(\AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $newAuthentication)
    {
        $this->addFlashMessage('The object was created. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->authenticationRepository->add($newAuthentication);
        $this->redirect('list');
    }

    /**
     * action edit
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $authentication
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("authentication")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function editAction(\AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $authentication): \Psr\Http\Message\ResponseInterface
    {
        $this->view->assign('authentication', $authentication);
        return $this->htmlResponse();
    }

    /**
     * action update
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $authentication
     */
    public function updateAction(\AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $authentication)
    {
        $this->addFlashMessage('The object was updated. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->authenticationRepository->update($authentication);
        $this->redirect('list');
    }

    /**
     * action delete
     *
     * @param \AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $authentication
     */
    public function deleteAction(\AcademicPuma\BibsonomyCsl\Domain\Model\Authentication $authentication)
    {
        $this->addFlashMessage('The object was deleted. Please be aware that this action is publicly accessible unless you implement an access check. See https://docs.typo3.org/p/friendsoftypo3/extension-builder/master/en-us/User/Index.html', '', \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING);
        $this->authenticationRepository->remove($authentication);
        $this->redirect('list');
    }
}

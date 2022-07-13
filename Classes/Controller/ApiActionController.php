<?php

namespace AcademicPuma\BibsonomyCsl\Controller;

use AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository;
use AcademicPuma\RestClient\Authentication\BasicAuthAccessor;
use AcademicPuma\RestClient\Authentication\OAuthAccessor;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
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
 * ApiActionController
 */
class ApiActionController extends ActionController
{
    protected $host;
    protected $accessor;

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

    public function makeAccessor(): void
    {
        $authSettings = $this->settings['auth'];
        if (filter_var($authSettings['beauth'], FILTER_VALIDATE_BOOLEAN)) {
            $auth = $this->authenticationRepository->findByUid($this->settings['auth']['beauthUser']);

            if (empty($auth)) {
                $message = LocalizationUtility::translate("LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:module.api.authentication.error",
                    'BibsonomyCsl');
                $this->addFlashMessage($message, "", AbstractMessage::ERROR);
                return;
            }

            $host = $auth->getHostAddress();
            if (!$auth->isOAuthEnabled()) {
                $apiUser = $auth->getUserName();
                $apiKey = $auth->getApiKey();
                $this->accessor = new BasicAuthAccessor($host, $apiUser, $apiKey);
            } else {
                $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('bibsonomy_csl');
                $this->accessor = new OAuthAccessor($host, unserialize($auth->getAccessToken()),
                    $extensionConfiguration['oauthConsumerToken'], $extensionConfiguration['oauthConsumerSecret']);
            }
        } else {
            $host = $authSettings['host'];
            $apiUser = $authSettings['userName'];
            $apiKey = $authSettings['apiKey'];
            $this->accessor = new BasicAuthAccessor($host, $apiUser, $apiKey);
        }

        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

}
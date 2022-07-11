<?php

namespace AcademicPuma\BibsonomyCsl\Controller;

use AcademicPuma\BibsonomyCsl\Domain\Repository\AuthenticationRepository;
use AcademicPuma\RestClient\Authentication\BasicAuthAccessor;
use AcademicPuma\RestClient\Authentication\OAuthAccessor;
use Exception;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

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
                throw new Exception("Could not find valid API credentials. Please check the extension settings.");
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
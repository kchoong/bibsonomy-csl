<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory as RequestFactoryAlias;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUtils
{

    public static function getHosts(array &$config): array
    {
        $extensionConfiguration = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('bibsonomy_csl');
        $serverListUrl = $extensionConfiguration['instanceListUrl'];

        $requestFactory = GeneralUtility::makeInstance(RequestFactoryAlias::class);
        $additionalOptions = [
            // Additional headers for this specific request
            'headers' => ['Cache-Control' => 'no-cache']
        ];
        // Return a PSR-7 compliant response object
        $response = $requestFactory->request($serverListUrl, 'GET', $additionalOptions);
        // Get the content as a string on a successful request
        $content = '';
        if ($response->getStatusCode() === 200) {
            if (strpos($response->getHeaderLine('Content-Type'), 'application/json') === 0) {
                $content = $response->getBody()->getContents();
            }
        }
        $serverList = json_decode($content);

        $config['items'][] = array('BibSonomy', 'https://www.bibsonomy.org/');
        if ($serverList) {
            foreach ($serverList->server as $server) {
                $config['items'][] = array($server->instanceName, $server->instanceUrl);
            }
        }

        return $config;
    }
}
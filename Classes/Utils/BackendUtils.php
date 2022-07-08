<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use ReflectionClass;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory as RequestFactoryAlias;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUtils
{
    public static $MIME_TYPE_MAP = array(
        'pdf' => 'application/pdf',
        'png' => 'image/png',
        'jpg' => 'image/jpg',
        'ps' => 'application/postscript',
        'eps' => 'application/postscript',
        'svg' => 'image/svg+xml',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'ppt' => 'application/mspowerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'xls' => 'application/msexcel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'djv' => 'image/x.djvu',
        'djvu' => 'image/x.djvu',
        'txt' => 'text/plain',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odt' => 'application/vnd.oasis.opendocument.text ',
        'odp' => 'application/vnd.oasis.opendocument.presentation'
    );

    public static function getMimeType($fileName): string
    {
        $match = array();
        if (preg_match('/.+\.([a-zA-Z0-9]{2,4})$/i', $fileName, $match)) {
            return self::$MIME_TYPE_MAP[$match[1]];
        }

        return 'text/plain';
    }

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

        $config['items'][] = array('Local', 'http://bibsonomy.azg/');
        $config['items'][] = array('BibSonomy', 'https://www.bibsonomy.org/');
        if ($serverList) {
            foreach ($serverList->server as $server) {
                $config['items'][] = array($server->instanceName, $server->instanceUrl);
            }
        }

        return $config;
    }

    public function getLocales($config)
    {
        // Add default languages
        $config['items'][] = array('English', 'en-US');
        $config['items'][] = array('German', 'de-DE');

        $reflector = new ReflectionClass(\AcademicPuma\RestClient\RESTClient::class);
        $fileName = $reflector->getFileName();
        $filePath = substr($fileName, 0, strripos($fileName, "/"));

        include_once realpath($filePath . '/../') . '/vendorPath.php';
        $path = vendorPath() . '/citation-style-language/locales/locales.json';
        $fileContent = file_get_contents($path);
        if ($fileContent === false) {
            return $config;
        }

        $languages = json_decode($fileContent, true)['language-names'];
        foreach ($languages as $code => $labels) {
            if ($code != 'en-US' and $code != 'de-DE') {
                $config['items'][] = array($labels[1], $code);
            }
        }

        return $config;
    }
}
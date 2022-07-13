<?php

namespace AcademicPuma\BibsonomyCsl\Utils;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use ReflectionClass;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\RequestFactory as RequestFactoryAlias;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * BackendUtils
 */
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
        // Add default hosts
        $config['items'][] = array('BibSonomy', 'https://www.bibsonomy.org/');

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

        if ($serverList) {
            foreach ($serverList->server as $server) {
                $config['items'][] = array($server->instanceName, $server->instanceUrl);
            }
        }

        return $config;
    }

    public static function getLocales(array &$config): array
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

    public static function getStylesheets(array &$config): array
    {
        // Add default stylesheets
        $config['items'][] = array('BibSonomy CSL Style', 'everyAware.csl');
        $config['items'][] = array('APA', 'apa.csl');
        $config['items'][] = array('Springer LNCS', 'springer-lecture-notes-in-computer-science.csl');
        $config['items'][] = array('DIN 1505-2', 'din-1505-2.csl');
        $config['items'][] = array('IEEE', 'ieee.csl');
        $config['items'][] = array('Chicago Manual of Style', 'chicago-author-date.csl');

        // Load stylesheets from database
        global $GLOBALS;
        $userId = $GLOBALS["BE_USER"]->user["uid"];

        $dbName = "tx_bibsonomycsl_domain_model_citationstylesheet";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($dbName);
        try {
            $result = $queryBuilder
                ->select('uid', 'name')
                ->from($dbName)
                ->execute();
            while ($row = $result->fetchAssociative()) {
                $config['items'][] = array($row['name'], $row['uid']);
            }
        } catch (DBALException|Exception $e) {
            $e->getMessage();
        }

        return $config;
    }

    public static function getAuthentications(array &$config): array
    {
        global $GLOBALS;
        $userId = $GLOBALS["BE_USER"]->user["uid"];

        $dbName = "tx_bibsonomycsl_domain_model_authentication";
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable($dbName);
        try {
            $result = $queryBuilder
                ->select('uid', 'host_address', 'user_name', 'api_key', 'o_auth_enabled')
                ->from($dbName)
                ->execute();
            while ($row = $result->fetchAssociative()) {
                if ($row["o_auth_enabled"] == 0) {
                    $config['items'][] = array("{$row['host_address']}:{$row['user_name']} (API Key)", $row["uid"]);
                } else {
                    $config['items'][] = array("{$row['host_address']}:{$row['user_name']} (OAuth)", $row["uid"]);
                }
            }
        } catch (DBALException|Exception $e) {
            $e->getMessage();
        }

        return $config;
    }
}
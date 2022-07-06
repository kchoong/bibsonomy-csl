<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Utils\ApiUtils;
use AcademicPuma\BibsonomyCsl\Utils\BackendUtils;
use AcademicPuma\RestClient\Config\DocumentType;
use AcademicPuma\RestClient\Model\Document;
use Psr\Http\Message\ResponseInterface;

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
 * DocumentController
 */
class DocumentController extends ApiActionController
{

    /**
     * action show
     *
     *
     * @param $intraHash
     * @param $fileName
     * @param $userName
     * @return ResponseInterface
     */
    public function showAction(string $intraHash, string $fileName, string $userName): ResponseInterface
    {
        // create API accessor
        $this->makeAccessor();

        // create REST client
        $client = ApiUtils::getRestClient($this->accessor, $this->settings);

        header('Content-Disposition: attachment; filename="' . basename($fileName) . '.jpg');
        print $client->getDocumentFile($userName, $intraHash, $fileName, DocumentType::SMALL_PREVIEW)->file();

        exit();
    }

    /**
     * action download
     *
     * @return ResponseInterface
     */
    public function downloadAction(string $intraHash, string $fileName, string $userName): ResponseInterface
    {
        // create API accessor
        $this->makeAccessor();

        // create REST client
        $client = ApiUtils::getRestClient($this->accessor, $this->settings);

        header('Content-Type: ' . BackendUtils::getMimeType($fileName));
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        print $client->getDocumentFile($userName, $intraHash, $fileName)->file();

        exit();
    }
}

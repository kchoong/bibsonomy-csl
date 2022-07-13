<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Utils\ApiUtils;
use AcademicPuma\BibsonomyCsl\Utils\BackendUtils;
use AcademicPuma\RestClient\Config\DocumentType;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

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
 * DocumentController
 */
class DocumentController extends ApiActionController
{

    /**
     * action show
     *
     *
     * @param string $intraHash
     * @param string $fileName
     * @param string $userName
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function showAction(string $intraHash, string $fileName, string $userName): ResponseInterface
    {
        // create API accessor
        $this->makeAccessor();

        // create REST client
        $client = ApiUtils::getRestClient($this->accessor, $this->settings);

        header('Content-Disposition: attachment; filename="' . basename($fileName) . '.jpg');
        print $client->getDocumentFile($userName, $intraHash, $fileName, DocumentType::LARGE_PREVIEW)->file();

        exit();
    }

    /**
     * action download
     *
     * @param string $intraHash
     * @param string $fileName
     * @param string $userName
     * @return ResponseInterface
     * @throws GuzzleException
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

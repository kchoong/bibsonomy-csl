<?php

declare(strict_types=1);

namespace AcademicPuma\BibsonomyCsl\Controller;


use AcademicPuma\BibsonomyCsl\Utils\ApiUtils;
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
    public function showAction($intraHash, $fileName, $userName): ResponseInterface
    {
        return $this->htmlResponse();
    }

    /**
     * action download
     *
     * @return ResponseInterface
     */
    public function downloadAction(): ResponseInterface
    {
        // create API accessor
        $this->makeAccessor();

        return $this->htmlResponse();
    }
}

<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\BibsonomyCsl\Utils\UrlUtils;
use AcademicPuma\RestClient\Model\Document;
use AcademicPuma\RestClient\Model\Post;
use Closure;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

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
 * LinkViewHelper
 */
class LinkViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('type', 'string', 'The link type', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $post = $arguments['post'];
        $resource = $post->getResource();
        $type = $arguments['type'];

        switch ($arguments['type']) {
            case 'doi':
                $doi = $resource->getMiscField('doi');
                if ($doi) {
                    return self::renderDOI($doi, $type);
                }
                break;
            case 'download':
                $documents = $arguments['post']->getDocuments();
                if ($documents !== null and $documents->count() > 0) {
                    return self::renderDownload($post, $documents[0], $type, $renderingContext);
                }
                break;
            case 'host':
                $host = $resource->getHref();
                if ($host) {
                    return self::renderHost($host, $type);
                }
                break;
            case 'url':
                $url = $resource->getUrl();
                if ($url) {
                    return self::renderUrl($url, $type);
                }
                break;
            default:
                break;
        }

        return '';
    }

    static private function renderDOI(string $doi, string $type): string
    {
        $url = UrlUtils::getDOIUrl($doi);
        $label = LocalizationUtility::translate('post.links.doi', 'BibsonomyCsl');
        return self::createLink($url, $type, $label);
    }

    static private function renderDownload(Post $post, Document $document, string $type, RenderingContextInterface $renderingContext): string
    {
        $userName = $post->getUser()->getName();
        $intraHash = $post->getResource()->getIntraHash();
        $fileName = $document->getFileName();
        $arguments = ["intraHash" => $intraHash, "fileName" => $fileName, "userName" => $userName];

        $uriBuilder = $renderingContext->getControllerContext()->getUriBuilder();
        $uriBuilder->reset();
        $url = $uriBuilder->uriFor('download', $arguments, 'Document', 'bibsonomycsl', 'publicationlist');
        $label = LocalizationUtility::translate('post.links.download', 'BibsonomyCsl');

        return self::createLink($url, $type, $label);
    }

    static private function renderHost(string $host, string $type): string
    {
        $label = LocalizationUtility::translate('bibsonomy.post.links.host.puma', 'BibsonomyCsl');
        if (strpos($host, 'bibsonomy.org') >= 0) {
            $label = LocalizationUtility::translate('post.links.host.bibsonomy', 'BibsonomyCsl');
        }

        return self::createLink($host, $type, $label);
    }

    static private function renderUrl(string $url, string $type): string
    {
        $label = LocalizationUtility::translate('post.links.url', 'BibsonomyCsl');
        return self::createLink($url, $type, $label);
    }

    static private function createLink(string $url, string $type, string $label): string
    {
        $link = new TagBuilder('a');
        $link->addAttribute('href', $url);
        $link->addAttribute('target', '_blank');
        $link->setContent($label);

        $button = new TagBuilder('li');
        $button->addAttribute('class', 'bibsonomy-link bibsonomy-link-' . $type );
        $button->setContent('[ ' . $link->render() . ' ]');

        return $button->render();
    }
}
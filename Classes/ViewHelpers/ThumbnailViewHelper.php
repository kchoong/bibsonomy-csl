<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\BibsonomyCsl\Utils\PostUtils;
use AcademicPuma\RestClient\Model\Post;
use Closure;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
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
 * ThumbnailViewHelper
 */
class ThumbnailViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('enableLink', 'string', 'Enable link to download on click', true);
    }

    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $enableLink = $arguments['enableLink'] == '1';
        $post = $arguments['post'];
        $documents = $post->getDocuments();

        if ($documents !== null and $documents->count() > 0) {
            $fileName = $documents[0]->getFileName();
            $userName = $post->getUser()->getName();
            $intraHash = $post->getResource()->getIntraHash();
            $arguments = ["intraHash" => $intraHash, "fileName" => $fileName, "userName" => $userName];
            $uriBuilder = $renderingContext->getControllerContext()->getUriBuilder();

            $uriBuilder->reset();
            $imgUrl = $uriBuilder->uriFor('show', $arguments, 'Document', 'bibsonomycsl', 'publicationlist');
            $img = self::buildImage($post, $imgUrl, $renderingContext);

            if ($enableLink) {
                $uriBuilder->reset();
                $downloadUrl = $uriBuilder->uriFor('download', $arguments, 'Document', 'bibsonomycsl', 'publicationlist');
                $link = new TagBuilder('a');
                $link->addAttribute('href', $downloadUrl);
                $link->addAttribute('target', '_blank');
                $link->setContent($img);

                return $link->render();
            }

            return $img;
        } else {
            // no documents, showing dummy thumbnail for entrytype
            $entrytype = $post->getResource()->getEntrytype();
            $entrytype = PostUtils::isDefaultEntrytype($entrytype) ? $entrytype : 'misc';
            $extPath = PathUtility::getRelativePathTo(ExtensionManagementUtility::extPath('bibsonomy_csl'));
            $imgPath = $extPath . "Resources/Public/Images/entrytypes/$entrytype.jpg";

            $img = new TagBuilder('img');
            $img->addAttribute('src', $imgPath);

            return $img->render();
        }
    }

    protected static function buildImage(Post $post, string $url, RenderingContextInterface $renderingContext): string
    {
        // no way to guarantee line wrap for img alt-text
        // https://stackoverflow.com/questions/2731484/can-i-wrap-img-alt-text
        $title = $post->getResource()->getTitle();
        $alt = strlen($title) > 25 ? substr($title, 0, 25) . "..." : $title;

        $img = new TagBuilder('img');
        $img->addAttribute('src', $url);
        $img->addAttribute('title', $post->getResource()->getTitle());
        $img->addAttribute('alt', $alt);
        $img->forceClosingTag(true);

        return $img->render();
    }
}
<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\RestClient\Model\Tag;
use Closure;
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
 * TagCloudViewHelper
 */
class TagCloudViewHelper extends AbstractViewHelper
{
    const TAG_MIN_SIZE = 0.7;
    const TAG_MAX_SIZE = 2;

    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('tags', '\AcademicPuma\RestClient\Model\Tags', 'List of tags', true);
        $this->registerArgument('maxcount', 'int', 'Maximum number of a tag used within the selection', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $min = self::TAG_MIN_SIZE;
        $max = self::TAG_MAX_SIZE;

        $tags = $arguments['tags'];
        $maxcount = $arguments['maxcount'];

        $items = [];
        foreach ($tags as $tag) {
            $href = $tag->getHref();

            // Temporary fix for REST-API returning wrong tag-links currently
            if (strpos($href, "/api/tags/") > 0) {
                $href = str_replace('/api/tags/', '/tag/', $href);
            }

            $count = $tag->getUsercount();
            $size = ($count / $maxcount) * ($max - $min) + $min;
            $fontSize = sprintf("%01.2f", $size);

            $link = new TagBuilder('a');
            $link->addAttribute('href', $href);
            $link->addAttribute('target', '_blank');
            $link->setContent($tag->getName());

            $span = new TagBuilder('li');
            $span->addAttribute('class', 'bibsonomy-tagcloud-item');
            $span->addAttribute('style', "font-size: {$fontSize}em");
            $span->setContent($link->render());

            $items[] = $span->render();
        }

        return implode(" ", $items);
    }

}
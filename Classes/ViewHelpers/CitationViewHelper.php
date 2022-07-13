<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\RestClient\Renderer\CSLModelRenderer;
use Closure;
use Exception;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
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
 * CitationViewHelper
 */
class CitationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('language', 'string', 'The language', true);
        $this->registerArgument('stylesheet', 'string', 'The CSL stylesheet as XML', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $post = $arguments['post'];
        $language = $arguments['language'];
        $stylesheet = $arguments['stylesheet'];

        $additionalMarkup = [
            "bibliography" => [
                "author" => [
                    'function' => function($cslItem, $renderedText) {
                        return '<span class="csl-author">' . $renderedText . '</span>';
                    },
                    'affixes' => true
                ],
                "citation-number" => [
                    'function' => function($cslItem, $renderedText) {
                        return '<span class="csl-number">' . $cslItem->citationNumber . '</span>';
                    },
                    'affixes' => true
                ],
                "title" => [
                    'function' => function($cslItem, $renderedText) {
                        return '<span class="csl-title">' . $renderedText . '</span>';
                    },
                    'affixes' => true
                ]
            ]
        ];

        $output = '';
        try {
            $cslRenderer = new CSLModelRenderer();
            $citeProc = new CiteProc($stylesheet, $language, $additionalMarkup);
            $csl = $cslRenderer->render($post);
            $output .= $citeProc->render(array((object) $csl));
        } catch (Exception | CiteProcException $e) {
            return $e->getMessage();
        }
        return $output;
    }
}
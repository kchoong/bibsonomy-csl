<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\RestClient\Renderer\CSLModelRenderer;
use Closure;
use Exception;
use Seboettg\CiteProc\CiteProc;
use Seboettg\CiteProc\Exception\CiteProcException;
use Seboettg\CiteProc\StyleSheet;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class CitationViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('stylesheet', 'string', 'The CSL stylesheet as XML', true);
        $this->registerArgument('language', 'string', 'The language', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $language = $arguments['language'] ?? 'en-US';
        $stylesheet = $arguments['stylesheet'] ?? 'apa';

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

        // keep in case of errors rendering the entire posts list at once
        $output = '';
        try {
            $cslRenderer = new CSLModelRenderer();
            $citeProc = new CiteProc(StyleSheet::loadStyleSheet($stylesheet), $language, $additionalMarkup);
            $csl = $cslRenderer->render($arguments['post']);
            $output .= $citeProc->render(array((object) $csl));
        } catch (Exception | CiteProcException $e) {
            return $e->getMessage();
        }
        return $output;
    }
}
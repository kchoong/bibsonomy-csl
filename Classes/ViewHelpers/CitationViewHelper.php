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
        $this->registerArgument('lang', 'string', 'The language', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $additionalMarkup = [
            "title" => [
                'function' => function ($cslItem, $renderedText) {
                    return '<span class="citeproc-title">' . $renderedText . '</span>';
                },
                'affixes' => false
            ],
            "citation-number" => [
                'function' => function ($cslItem, $renderedText) {
                    return '<span class="citation-number">' . $cslItem->citationNumber . '</span>';
                },
                'affixes' => true
            ]
        ];

        // keep in case of errors rendering the entire posts list at once
        $output = '';
        try {
            $cslRenderer = new CSLModelRenderer();
            $citeProc = new CiteProc(StyleSheet::loadStyleSheet($arguments['stylesheet']), $arguments['lang'], $additionalMarkup);
            $csl = $cslRenderer->render($arguments['post']);
            $output .= $citeProc->render(array((object) $csl));
        } catch (Exception | CiteProcException $e) {
            return $e->getMessage();
        }
        return $output;
    }
}
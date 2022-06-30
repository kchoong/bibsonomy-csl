<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\BibsonomyCsl\Utils\URLUtils;
use AcademicPuma\ExtBibsonomyCsl\Lib\Helper;
use AcademicPuma\RestClient\Model\Bibtex;
use Closure;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class MiscLinkViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('misc', 'string', 'List of misc fields to link', true);
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $links = array();
        $bibtex = $arguments['post']->getResource();
        $miscItems = explode(',', $arguments['misc']);

        foreach ($miscItems as $miscKey) {
            $miscKey = trim($miscKey);
            $miscValue = trim($bibtex->getMiscField($miscKey));

            if (!empty($miscValue)) {
                if (URLUtils::isUrl($miscValue)) {
                    $links[] = self::createLink($miscValue, $miscKey);
                }
            }
        }

        return implode(" ", $links);
    }

    static private function createLink(string $url, string $label): string
    {
        $link = new TagBuilder('a');
        $link->addAttribute('href', $url);
        $link->addAttribute('target', '_blank');
        $link->setContent($label);

        $button = new TagBuilder('li');
        $button->addAttribute('class', 'bibsonomy-link bibsonomy-link-misc');
        $button->setContent('[ ' . $link->render() . ' ]');

        return $button->render();
    }
}
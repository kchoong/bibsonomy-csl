<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\BibsonomyCsl\Utils\URLUtils;
use Closure;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

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
        $resource = $arguments['post']->getResource();
        $type = $arguments['type'];

        switch ($arguments['type']) {
            case 'doi':
                $doi = $resource->getMiscField('doi');
                if ($doi) {
                    return self::renderDOI($doi, $type);
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
        $url = URLUtils::getDOIUrl($doi);
        $label = LocalizationUtility::translate('bibsonomy.post.links.doi', 'BibsonomyCsl');
        return self::createLink($url, $type, $label);
    }

    static private function renderHost(string $host, string $type): string
    {
        $label = LocalizationUtility::translate('bibsonomy.post.links.host.puma', 'BibsonomyCsl');
        if (strpos($host, 'bibsonomy.org') >= 0) {
            $label = LocalizationUtility::translate('bibsonomy.post.links.host.bibsonomy', 'BibsonomyCsl');
        }

        return self::createLink($host, $type, $label);
    }

    static private function renderUrl(string $url, string $type): string
    {
        $label = LocalizationUtility::translate('bibsonomy.post.links.url', 'BibsonomyCsl');
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
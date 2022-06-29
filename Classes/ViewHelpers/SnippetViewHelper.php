<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\RestClient\Model\Post;
use AcademicPuma\RestClient\Renderer\BibtexModelRenderer;
use Closure;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class SnippetViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('type', 'string', 'The link type', true);
    }

    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $post = $arguments['post'];
        $type = $arguments['type'];

        switch ($arguments['type']) {
            case 'abstract':
                if ($post->getResource()->getAbstract()) {
                    return self::renderAbstract($post, $type);
                }
                break;
            case 'bibtex':
                return self::renderBibtex($post, $type);
                break;
            case 'csl':
                return self::renderCSL($post, $type);
                break;
            case 'endnote':
                return self::renderEndnote($post, $type);
                break;
            default:
                break;
        }

        return '';
    }

    private static function renderAbstract(Post $post, string $type): string
    {
        $abstract = $post->getResource()->getBibtexAbstract();
        $id = self::generateSnippetId($post, $type);
        $label = LocalizationUtility::translate('bibsonomy.post.snippet.abstract', 'BibsonomyCsl');
        return self::createLink($id, $type, $label) . self::createSnippet($id, $type, $abstract);
    }

    private static function renderBibtex(Post $post, string $type): string
    {
        $renderer = new BibtexModelRenderer();
        $bibtex = $renderer->render($post);
        $id = self::generateSnippetId($post, $type);
        $label = LocalizationUtility::translate('bibsonomy.post.snippet.bibtex', 'BibsonomyCsl');
        return self::createLink($id, $type, $label) . self::createSnippet($id, $type, $bibtex);
    }

    private static function renderCSL(Post $post, string $type): string
    {
        return '';
    }

    private static function renderEndnote(Post $post, string $type): string
    {
        return '';
    }

    static private function createLink(string $id, string $type, string $label): string
    {
        $link = new TagBuilder('a');
        $link->addAttribute('href', '#');
        $link->addAttribute('onclick', 'toggleSnippet(' . $id . ')');
        $link->setContent($label);

        $button = new TagBuilder('li');
        $button->addAttribute('class', 'bibsonomy-link bibsonomy-link-' . $type);
        $button->setContent('[ ' . $link->render() . ' ]');

        return $button->render();
    }

    static private function createSnippet(string $id, string $type, string $content): string
    {
        $textarea = new TagBuilder('textarea');
        $textarea->addAttribute('readonly', 'readonly'); //style="width: 100%" cols="40" rows="6"
        $textarea->addAttribute('cols', '40');
        $textarea->addAttribute('rows', '6');
        $textarea->setContent($content);

        $container = new TagBuilder('div');
        $container->addAttribute('id', $id);
        $container->addAttribute('class', 'bibsonomy-snippet bibsonomy-snippet-' . $type);
        $container->setContent($textarea->render());
        return $container->render();
    }

    static private function generateSnippetId(Post $post, string $type) {
        return 'bibsonomy-' . $type . '-' . $post->getResource()->getIntraHash();
    }
}
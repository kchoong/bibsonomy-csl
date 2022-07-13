<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\RestClient\Model\Post;
use AcademicPuma\RestClient\Renderer\BibtexModelRenderer;
use AcademicPuma\RestClient\Renderer\CSLModelRenderer;
use AcademicPuma\RestClient\Renderer\EndnoteModelRenderer;
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
        $this->registerArgument('type', 'string', 'The snippet type', true);
        $this->registerArgument('mode', 'string', 'Link or Snippet', true);
    }

    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $post = $arguments['post'];
        $type = $arguments['type'];
        $mode = $arguments['mode'];

        switch ($arguments['type']) {
            case 'abstract':
                if ($post->getResource()->getBibtexAbstract()) {
                    return self::renderAbstract($post, $type, $mode);
                }
                break;
            case 'bibtex':
                return self::renderBibtex($post, $type, $mode);
            case 'csl':
                return self::renderCSL($post, $type, $mode);
            case 'endnote':
                return self::renderEndnote($post, $type, $mode);
            default:
                break;
        }

        return '';
    }

    private static function renderAbstract(Post $post, string $type, string $mode): string
    {
        $intrahash = $post->getResource()->getIntraHash();
        $postId = "bibsonomy-post-$intrahash";
        $snippetId = "bibsonomy-$type-$intrahash";
        $label = LocalizationUtility::translate('post.snippet.abstract', 'BibsonomyCsl');

        if ($mode == 'snippet') {
            $abstract = $post->getResource()->getBibtexAbstract();
            return self::createSnippet($snippetId, $type, $abstract);
        } else {
            return self::createLink($postId, $snippetId, $type, $label);
        }
    }

    private static function renderBibtex(Post $post, string $type, string $mode): string
    {
        $intrahash = $post->getResource()->getIntraHash();
        $postId = "bibsonomy-post-$intrahash";
        $snippetId = "bibsonomy-$type-$intrahash";
        $label = LocalizationUtility::translate('post.snippet.bibtex', 'BibsonomyCsl');

        if ($mode == 'snippet') {
            $renderer = new BibtexModelRenderer();
            $bibtex = $renderer->render($post);
            return self::createSnippet($snippetId, $type, $bibtex);
        } else {
            return self::createLink($postId, $snippetId, $type, $label);
        }
    }

    private static function renderCSL(Post $post, string $type, string $mode): string
    {
        $intrahash = $post->getResource()->getIntraHash();
        $postId = "bibsonomy-post-$intrahash";
        $snippetId = "bibsonomy-$type-$intrahash";
        $label = LocalizationUtility::translate('post.snippet.csl', 'BibsonomyCsl');

        if ($mode == 'snippet') {
            $renderer = new CSLModelRenderer();
            $csl = $renderer->render($post);
            return self::createSnippet($snippetId, $type, json_encode($csl, JSON_PRETTY_PRINT));
        } else {
            return self::createLink($postId, $snippetId, $type, $label);
        }
    }

    private static function renderEndnote(Post $post, string $type, string $mode): string
    {
        $intrahash = $post->getResource()->getIntraHash();
        $postId = "bibsonomy-post-$intrahash";
        $snippetId = "bibsonomy-$type-$intrahash";
        $label = LocalizationUtility::translate('post.snippet.endnote', 'BibsonomyCsl');

        if ($mode == 'snippet') {
            $renderer = new EndnoteModelRenderer();
            $endnote = $renderer->render($post);
            return self::createSnippet($snippetId, $type, $endnote);
        } else {
            return self::createLink($postId, $snippetId, $type, $label);
        }
    }

    static private function createLink(string $postId, string $snippetId, string $type, string $label): string
    {
        $link = new TagBuilder('a');
        $link->addAttribute('href', "javascript:void(0)");
        $link->addAttribute('onclick', "toggleSnippet(this)");
        $link->addAttribute('data-post', $postId);
        $link->addAttribute('data-snippet', $snippetId);
        $link->setContent($label);

        $button = new TagBuilder('li');
        $button->addAttribute('class', "bibsonomy-link bibsonomy-link-$type");
        $button->setContent('[ ' . $link->render() . ' ]');

        return $button->render();
    }

    static private function createSnippet(string $snippetId, string $type, string $content): string
    {
        $textarea = new TagBuilder('textarea');
        $textarea->addAttribute('readonly', 'readonly');
        $textarea->addAttribute('cols', '40');
        $textarea->addAttribute('rows', '6');
        $textarea->setContent($content);

        $container = new TagBuilder('div');
        $container->addAttribute('id', $snippetId);
        $container->addAttribute('class', "bibsonomy-snippet bibsonomy-snippet-$type");
        $container->setContent($textarea->render());
        return $container->render();
    }

}
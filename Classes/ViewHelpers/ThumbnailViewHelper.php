<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\RestClient\Model\Document;
use AcademicPuma\RestClient\Model\Post;
use Closure;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;
use TYPO3Fluid\Fluid\Core\ViewHelper\TagBuilder;
use TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

class ThumbnailViewHelper extends AbstractViewHelper
{
    use CompileWithRenderStatic;

    protected $escapeOutput = false;

    public function initializeArguments()
    {
        $this->registerArgument('post', '\AcademicPuma\RestClient\Model\Post', 'The post to render as citation', true);
        $this->registerArgument('type', 'string', 'The thumbnail type', true);
    }

    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $post = $arguments['post'];
        $documents = $post->getDocuments();

        if ($documents !== null and $documents->count() > 0) {
            $userName = $post->getUser()->getName();
            $intraHash = $post->getResource()->getIntraHash();
            $document = $documents[0];

            return self::buildImage($post, $document, $intraHash, $userName, $renderingContext);
        } else {
            // no documents, showing dummy thumbnail for entrytype
            $entrytype = $post->getResource()->getEntrytype();
            $extPath = PathUtility::getRelativePathTo(ExtensionManagementUtility::extPath('bibsonomy_csl'));
            $imgPath = $extPath . "Resources/Public/Images/entrytypes/$entrytype.jpg";

            $img = new TagBuilder('img');
            $img->addAttribute('src', $imgPath);

            return $img->render();
        }

        return '';
    }

    protected static function buildImage(Post $post, Document $document, string $intraHash, string $userName, RenderingContextInterface $renderingContext): string
    {
        $fileName = $document->getFileName();
        $arguments = ["intraHash" => $intraHash, "fileName" => $fileName, "userName" => $userName];
        $uriBuilder = $renderingContext->getControllerContext()->getUriBuilder();
        $uriBuilder->reset();
        $src = $uriBuilder->uriFor('show', $arguments, 'Document', 'bibsonomycsl', 'publicationlist');

        // no way to guarantee line wrap for img alt-text
        // https://stackoverflow.com/questions/2731484/can-i-wrap-img-alt-text
        $title = $post->getResource()->getTitle();
        $alt = strlen($title) > 25 ? substr($title, 0, 25) . "..." : $title;

        $img = new TagBuilder('img');
        $img->addAttribute('src', $src);
        $img->addAttribute('title', $post->getResource()->getTitle());
        $img->addAttribute('alt', $alt . " - Download");
        $img->forceClosingTag(true);

        return $img->render();
    }
}
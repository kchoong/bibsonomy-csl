<?php

namespace AcademicPuma\BibsonomyCsl\ViewHelpers;

use AcademicPuma\ExtBibsonomyCsl\Lib\TYPO3Utils;
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
    }

    public static function renderStatic(
        array                     $arguments,
        Closure                   $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ): string
    {
        $post = $arguments['post'];

        if ($post->getDocuments() !== null and $post->getDocuments()->count() > 0) {

        } else {
            // no documents, showing dummy thumbnail for entrytype
            $entrytype = $post->getResource()->getEntrytype();
            $extPath = PathUtility::getRelativePathTo(ExtensionManagementUtility::extPath('bibsonomy_csl'));
            $imgPath = $extPath . "Resources/Public/Images/entrytypes/$entrytype.jpg";

            $link = new TagBuilder('img');
            $link->addAttribute('src', $imgPath);

            return $link->render();
        }

        return '';
    }
}
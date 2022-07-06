<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BibsonomyCsl',
        'PublicationList',
        [
            \AcademicPuma\BibsonomyCsl\Controller\PublicationController::class => 'list, show',
            \AcademicPuma\BibsonomyCsl\Controller\DocumentController::class => 'show, download'
        ],
        // non-cacheable actions
        []
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BibsonomyCsl',
        'TagCloud',
        [
            \AcademicPuma\BibsonomyCsl\Controller\TagController::class => 'list'
        ],
        // non-cacheable actions
        []
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    publicationlist {
                        iconIdentifier = bibsonomy_csl-plugin-publicationlist
                        title = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_be.xlf:tx_bibsonomy_csl_publicationlist.name
                        description = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_be.xlf:tx_bibsonomy_csl_publicationlist.description
                        tt_content_defValues {
                            CType = list
                            list_type = bibsonomycsl_publicationlist
                        }
                    }
                    tagcloud {
                        iconIdentifier = bibsonomy_csl-plugin-tagcloud
                        title = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_be.xlf:tx_bibsonomy_csl_tagcloud.name
                        description = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_be.xlf:tx_bibsonomy_csl_tagcloud.description
                        tt_content_defValues {
                            CType = list
                            list_type = bibsonomycsl_tagcloud
                        }
                    }
                }
                show = *
            }
       }'
    );
})();

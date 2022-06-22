<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BibsonomyCsl',
        'Publicationlist',
        [
            \AcademicPuma\BibsonomyCsl\Controller\DocumentController::class => 'list, show, download'
        ],
        // non-cacheable actions
        [
            \AcademicPuma\BibsonomyCsl\Controller\PublicationController::class => 'list, show'
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'BibsonomyCsl',
        'Tagcloud',
        [
            \AcademicPuma\BibsonomyCsl\Controller\PublicationController::class => 'list, show',
            \AcademicPuma\BibsonomyCsl\Controller\DocumentController::class => 'list, show, download',
            \AcademicPuma\BibsonomyCsl\Controller\TagController::class => 'list, show',
            \AcademicPuma\BibsonomyCsl\Controller\CitationStylesheetController::class => 'list, new, create, edit, update, delete',
            \AcademicPuma\BibsonomyCsl\Controller\AuthenticationController::class => 'list, new, create, edit, update, delete'
        ],
        // non-cacheable actions
        [
            \AcademicPuma\BibsonomyCsl\Controller\TagController::class => 'list'
        ]
    );

    // wizards
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        'mod {
            wizards.newContentElement.wizardItems.plugins {
                elements {
                    publicationlist {
                        iconIdentifier = bibsonomy_csl-plugin-publicationlist
                        title = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomy_csl_publicationlist.name
                        description = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomy_csl_publicationlist.description
                        tt_content_defValues {
                            CType = list
                            list_type = bibsonomycsl_publicationlist
                        }
                    }
                    tagcloud {
                        iconIdentifier = bibsonomy_csl-plugin-tagcloud
                        title = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomy_csl_tagcloud.name
                        description = LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomy_csl_tagcloud.description
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

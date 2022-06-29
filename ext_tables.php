<?php
defined('TYPO3') || die();

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'BibsonomyCsl',
        'web',
        'backend',
        '',
        [
            \AcademicPuma\BibsonomyCsl\Controller\AuthenticationController::class => 'list, new, create, edit, update, delete',
            \AcademicPuma\BibsonomyCsl\Controller\CitationStylesheetController::class => 'list, new, create, edit, update, delete',
            
        ],
        [
            'access' => 'user,group',
            'icon'   => 'EXT:bibsonomy_csl/Resources/Public/Icons/user_mod_backend.svg',
            'labels' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_be.xlf',
        ]
    );

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bibsonomycsl_domain_model_citationstylesheet', 'EXT:bibsonomy_csl/Resources/Private/Language/locallang_csh_tx_bibsonomycsl_domain_model_citationstylesheet.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bibsonomycsl_domain_model_citationstylesheet');

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_bibsonomycsl_domain_model_authentication', 'EXT:bibsonomy_csl/Resources/Private/Language/locallang_csh_tx_bibsonomycsl_domain_model_authentication.xlf');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_bibsonomycsl_domain_model_authentication');
})();

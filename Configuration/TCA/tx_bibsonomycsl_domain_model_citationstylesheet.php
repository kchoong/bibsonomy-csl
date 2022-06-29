<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_citationstylesheet',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:bibsonomy_csl/Resources/Public/Icons/tx_bibsonomycsl_domain_model_citationstylesheet.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'name, xmlSource'],
    ],
    'columns' => [
        'name' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_citationstylesheet.name',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim'
            ],
        ],
        'xmlSource' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_citationstylesheet.xmlSource',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
            ],
        ]
    ],
];

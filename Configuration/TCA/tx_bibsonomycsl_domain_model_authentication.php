<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_authentication',
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
        'iconfile' => 'EXT:bibsonomy_csl/Resources/Public/Icons/tx_bibsonomycsl_domain_model_authentication.gif'
    ],
    'types' => [
        '1' => ['showitem' => 'hostAddress, userName, apiKey, accessToken, enabledOAuth, createdDate'],
    ],
    'columns' => [
        'hostAddress' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_authentication.hostAddress',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim'
            ],
        ],
        'userName' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_authentication.userName',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'apiKey' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_authentication.apiKey',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'accessToken' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_authentication.accessToken',
            'config' => [
                'type' => 'input',
                'size' => 60,
                'eval' => 'trim'
            ],
        ],
        'enabledOAuth' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_authentication.enabledOAuth',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'createdDate' => [
            'label' => 'LLL:EXT:bibsonomy_csl/Resources/Private/Language/locallang_db.xlf:tx_bibsonomycsl_domain_model_authentication.createdDate',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'datetime',
                'checkbox' => 1,
                'default' => time()
            ],
        ],
    ],
];

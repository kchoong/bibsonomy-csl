CREATE TABLE tx_bibsonomycsl_domain_model_authentication
(
    uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    hostAddress  varchar(225) NOT NULL DEFAULT '',
    userName     varchar(255) NOT NULL DEFAULT '',
    apiKey       varchar(255)          DEFAULT '',
    accessToken  text,
    enabledOAuth tinyint(3) unsigned NOT NULL,
    createdDate  int(11) unsigned NOT NULL DEFAULT '0',

    PRIMARY KEY (uid),
    KEY parent (pid)
)
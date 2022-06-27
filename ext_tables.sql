CREATE TABLE tx_bibsonomycsl_domain_model_authentication
(
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    hostAddress  varchar(255) NOT NULL DEFAULT '',
    userName     varchar(255) NOT NULL DEFAULT '',
    apiKey       varchar(255)          DEFAULT '',
    accessToken  text,
    enabledOAuth tinyint(3) unsigned NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_bibsonomycsl_domain_model_citationstylesheet
(
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    name varchar(255) NOT NULL DEFAULT '',
    xmlSource text NOT NULL DEFAULT '',

    PRIMARY KEY (uid),
    KEY parent (pid)
);
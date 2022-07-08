CREATE TABLE tx_bibsonomycsl_domain_model_authentication
(
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    host_address  varchar(255) NOT NULL DEFAULT '',
    user_name     varchar(255) NOT NULL DEFAULT '',
    api_key       varchar(255)          DEFAULT '',
    access_token  text,
    o_auth_enabled tinyint(3) unsigned NOT NULL,

    PRIMARY KEY (uid),
    KEY parent (pid)
);

CREATE TABLE tx_bibsonomycsl_domain_model_citationstylesheet
(
    uid int(11) unsigned NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,

    name varchar(255) NOT NULL DEFAULT '',
    xml_source text,

    PRIMARY KEY (uid),
    KEY parent (pid)
);
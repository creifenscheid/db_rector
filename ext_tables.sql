#
# Table structure for table 'tx_dbrector_domain_model_element'
#
CREATE TABLE tx_dbrector_domain_model_element
(
    origin_uid int(11) DEFAULT 0 NOT NULL,
    origin_pid int(11) DEFAULT 0 NOT NULL,
    origin_title varchar(255) DEFAULT '' NOT NULL,
    origin_typoscript text DEFAULT '',
    processed_typoscript text DEFAULT '',
    applied tinyint(1) DEFAULT 0 NOT NULL,
    processed tinyint(1) DEFAULT 0 NOT NULL
);
#
# Table structure for table 'tx_dbrector_domain_model_element'
#
CREATE TABLE tx_guidelines_domain_model_standard
(
    origin int(11) DEFAULT 0 NOT NULL,
    original_data text,
    refactored_data text ,
    applied tinyint(1) DEFAULT 0 NOT NULL,
    processed tinyint(1) DEFAULT 0 NOT NULL
);
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `cms_ad_campaign_affiliation`
-- ----------------------------
CREATE TABLE `cms_ad_campaign_affiliation` (
  `ad_campaign_affiliation_id` int(1) NOT NULL AUTO_INCREMENT,
  `ad_id` int(1) NOT NULL,
  `ad_campaign_id` int(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `show_chance_percentage` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_campaign_affiliation_id`),
  KEY `aca_ad_id_fk` (`ad_id`),
  KEY `aca_ad_campaign_id_fk` (`ad_campaign_id`),
  CONSTRAINT `aca_ad_campaign_id_fk` FOREIGN KEY (`ad_campaign_id`) REFERENCES `cms_ad_campaigns` (`ad_campaign_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `aca_ad_id_fk` FOREIGN KEY (`ad_id`) REFERENCES `cms_ads` (`ad_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_ad_campaigns`
-- ----------------------------
CREATE TABLE `cms_ad_campaigns` (
  `ad_campaign_id` int(1) NOT NULL AUTO_INCREMENT,
  `module_id` int(1) NOT NULL,
  `ad_campaign_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_campaign_id`),
  KEY `ac_module_id_fk` (`module_id`),
  CONSTRAINT `ac_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_ads`
-- ----------------------------
CREATE TABLE `cms_ads` (
  `ad_id` int(1) NOT NULL AUTO_INCREMENT,
  `module_id` int(1) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `code` text COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`),
  KEY `ca_module_id_fk` (`module_id`),
  CONSTRAINT `ca_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
-- ----------------------------
-- Table structure for `cms_banned_ip_addresses`
-- ----------------------------
CREATE TABLE `cms_banned_ip_addresses` (
  `banned_ip_address_id` int(1) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `expiration_time` datetime DEFAULT NULL,
  PRIMARY KEY (`banned_ip_address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_censored_words`
-- ----------------------------
CREATE TABLE `cms_censored_words` (
  `censored_word_id` int(1) NOT NULL AUTO_INCREMENT,
  `original_word` text COLLATE utf8_unicode_ci NOT NULL,
  `translated_to` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '[CENSORED]',
  PRIMARY KEY (`censored_word_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_configuration_parameters`
-- ----------------------------
DROP TABLE IF EXISTS `cms_configuration_parameters`;
CREATE TABLE `cms_configuration_parameters` (
  `configuration_parameter_id` int(1) NOT NULL AUTO_INCREMENT,
  `module_id` int(1) DEFAULT NULL,
  `parameter_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `default_value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` int(1) NOT NULL,
  `parameter_data_type_id` tinyint(1) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `display_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `has_value_list` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`configuration_parameter_id`),
  KEY `ccp_parameter_data_type_id_fk` (`parameter_data_type_id`),
  KEY `ccp_module_id_fk` (`module_id`),
  CONSTRAINT `ccp_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `ccp_parameter_data_type_id_fk` FOREIGN KEY (`parameter_data_type_id`) REFERENCES `cms_parameter_data_types` (`parameter_data_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_configuration_parameters
-- ----------------------------
INSERT INTO `cms_configuration_parameters` VALUES (1, '1', 'theme', null, 'light', '1', '1', null, 'Theme', '0');
INSERT INTO `cms_configuration_parameters` VALUES (2, null, 'version', null, '1.0', '1', '1', null, 'Framework Version', '0');
INSERT INTO `cms_configuration_parameters` VALUES (3, null, 'environment', null, 'production', '2', '1', null, 'Environment', '1');
INSERT INTO `cms_configuration_parameters` VALUES (4, null, 'session_name', null, 'default', '4', '1', null, 'Client Session Name', '0');
INSERT INTO `cms_configuration_parameters` VALUES (5, null, 'session_storage_engine', null, 'file', '5', '1', null, 'Session Storage Engine', '1');
INSERT INTO `cms_configuration_parameters` VALUES (6, null, 'aws_region', null, 'com', '6', '1', null, 'AWS Region', '0');
INSERT INTO `cms_configuration_parameters` VALUES (7, null, 'aws_public_key', null, null, '7', '1', null, 'AWS Public Key', '0');
INSERT INTO `cms_configuration_parameters` VALUES (8, null, 'aws_private_key', null, null, '8', '1', null, 'AWS Private Key', '0');
INSERT INTO `cms_configuration_parameters` VALUES (9, null, 'default_module', null, 'admin', '9', '1', null, 'Default Module', '0');
INSERT INTO `cms_configuration_parameters` VALUES (10, null, 'javascript_minifier', null, 'simple', '10', '1', null, 'Javascript Minifier', '1');
INSERT INTO `cms_configuration_parameters` VALUES (11, null, 'closure_compiler_path', null, null, '11', '1', null, 'Closure Compiler Path', '0');
INSERT INTO `cms_configuration_parameters` VALUES (12, null, 'enable_javascript', null, '1', '12', '4', null, 'Enable Javascript', '0');
INSERT INTO `cms_configuration_parameters` VALUES (13, '1', 'encrypt_urls', null, '1', '2', '4', null, 'Encrypt URLs', '0');
INSERT INTO `cms_configuration_parameters` VALUES (14, null, 'recaptcha_api_method', null, 'https', '13', '1', null, 'reCaptcha API Method', '1');
INSERT INTO `cms_configuration_parameters` VALUES (15, null, 'recaptcha_private_key', null, null, '14', '1', null, 'reCaptcha Private Key', '0');
INSERT INTO `cms_configuration_parameters` VALUES (16, null, 'recaptcha_public_key', null, null, '15', '1', null, 'reCaptcha Public Key', '0');
INSERT INTO `cms_configuration_parameters` VALUES (17, null, 'attempts_form_max_attempts', null, 5, '16', '2', null, 'Limited Attempts Form Max Attempts', '0');
INSERT INTO `cms_configuration_parameters` VALUES (18, null, 'attempts_form_timeout_duration', null, 600, '17', '2', null, 'Limited Attempts Form Timeout Duration', '0');

-- ----------------------------
-- Table structure for `cms_errors`
-- ----------------------------
DROP TABLE IF EXISTS `cms_errors`;
CREATE TABLE `cms_errors` (
  `error_id` bigint(1) NOT NULL AUTO_INCREMENT,
  `incident_number` varchar(9) COLLATE utf8_unicode_ci NOT NULL,
  `error_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `error_message` text COLLATE utf8_unicode_ci,
  `error_file` text COLLATE utf8_unicode_ci,
  `error_line` smallint(1) DEFAULT NULL,
  `error_trace` text COLLATE utf8_unicode_ci,
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `module_id` int(1) DEFAULT NULL,
  PRIMARY KEY (`error_id`),
  KEY `ce_module_id_fk` (`module_id`),
  CONSTRAINT `ce_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_meta_settings`
-- ----------------------------
CREATE TABLE `cms_meta_settings` (
  `meta_setting_id` smallint(1) NOT NULL AUTO_INCREMENT,
  `module_id` int(1) NOT NULL,
  `tag_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `http_equiv` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`meta_setting_id`),
  KEY `cms_module_id_fk` (`module_id`),
  CONSTRAINT `cms_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_modules`
-- ----------------------------
DROP TABLE IF EXISTS `cms_modules`;
CREATE TABLE `cms_modules` (
  `module_id` int(1) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` tinyint(1) DEFAULT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_modules
-- ----------------------------
INSERT INTO `cms_modules` VALUES ('1', 'admin', 'Admin', '1', '1');

-- ----------------------------
-- Table structure for `cms_parameter_data_types`
-- ----------------------------
DROP TABLE IF EXISTS `cms_parameter_data_types`;
CREATE TABLE `cms_parameter_data_types` (
  `parameter_data_type_id` tinyint(1) NOT NULL,
  `data_type` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parameter_data_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_parameter_data_types
-- ----------------------------
INSERT INTO `cms_parameter_data_types` VALUES ('1', 'string');
INSERT INTO `cms_parameter_data_types` VALUES ('2', 'integer');
INSERT INTO `cms_parameter_data_types` VALUES ('3', 'float');
INSERT INTO `cms_parameter_data_types` VALUES ('4', 'boolean');
INSERT INTO `cms_parameter_data_types` VALUES ('5', 'array');

-- ----------------------------
-- Table structure for `cms_parameter_values`
-- ----------------------------
DROP TABLE IF EXISTS `cms_parameter_values`;
CREATE TABLE `cms_parameter_values` (
  `parameter_value_id` int(1) NOT NULL AUTO_INCREMENT,
  `parameter_value` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  `configuration_parameter_id` int(1) NOT NULL,
  PRIMARY KEY (`parameter_value_id`),
  KEY `cpv_configuration_parameter_id_fk` (`configuration_parameter_id`),
  CONSTRAINT `cpv_configuration_parameter_id_fk` FOREIGN KEY (`configuration_parameter_id`) REFERENCES `cms_configuration_parameters` (`configuration_parameter_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_parameter_values
-- ----------------------------
INSERT INTO `cms_parameter_values` VALUES ('1', 'development', '1', '3');
INSERT INTO `cms_parameter_values` VALUES ('2', 'production', '2', '3');
INSERT INTO `cms_parameter_values` VALUES ('3', 'file', '1', '5');
INSERT INTO `cms_parameter_values` VALUES ('4', 'database', '2', '5');
INSERT INTO `cms_parameter_values` VALUES ('5', 'simple', '1', '10');
INSERT INTO `cms_parameter_values` VALUES ('6', 'uglify-js', '2', '10');
INSERT INTO `cms_parameter_values` VALUES ('7', 'closure', '3', '10');
INSERT INTO `cms_parameter_values` VALUES ('8', 'http', '1', '14');
INSERT INTO `cms_parameter_values` VALUES ('9', 'https', '2', '14');

-- ----------------------------
-- Table structure for `cms_permissions`
-- ----------------------------
DROP TABLE IF EXISTS `cms_permissions`;
CREATE TABLE `cms_permissions` (
  `permission_id` int(1) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module_id` int(1) NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`permission_id`),
  KEY `cp_module_id_fk` (`module_id`),
  CONSTRAINT `cp_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_role_permission_affiliation`
-- ----------------------------
DROP TABLE IF EXISTS `cms_role_permission_affiliation`;
CREATE TABLE `cms_role_permission_affiliation` (
  `role_permission_affiliation_id` int(1) NOT NULL AUTO_INCREMENT,
  `role_id` int(1) NOT NULL,
  `permission_id` int(1) NOT NULL,
  `can` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_permission_affiliation_id`),
  KEY `crpa_permission_id_fk` (`permission_id`),
  KEY `crpa_role_id_fk` (`role_id`),
  CONSTRAINT `crpa_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `cms_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `crpa_permission_id_fk` FOREIGN KEY (`permission_id`) REFERENCES `cms_permissions` (`permission_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_roles`
-- ----------------------------
DROP TABLE IF EXISTS `cms_roles`;
CREATE TABLE `cms_roles` (
  `role_id` int(1) NOT NULL AUTO_INCREMENT,
  `display_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `module_id` int(1) NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`role_id`),
  KEY `cr_module_id_fk` (`module_id`),
  CONSTRAINT `cr_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_sessions`
-- ----------------------------
DROP TABLE IF EXISTS `cms_sessions`;
CREATE TABLE `cms_sessions` (
  `session_id` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `session_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `session_data` text COLLATE utf8_unicode_ci NOT NULL,
  `expire_time` int(1) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_static_pages`
-- ----------------------------
CREATE TABLE `cms_static_pages` (
  `static_page_id` int(1) NOT NULL AUTO_INCREMENT,
  `module_id` int(1) NOT NULL,
  `page_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `is_active` tinyint(1) NOT NULL,
  PRIMARY KEY (`static_page_id`),
  KEY `csp_module_id_fk` (`module_id`),
  CONSTRAINT `csp_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_update_types`
-- ----------------------------
CREATE TABLE `cms_update_types` (
  `update_type_id` int(1) NOT NULL AUTO_INCREMENT,
  `update_type` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`update_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_updates`
-- ----------------------------
CREATE TABLE `cms_updates` (
  `update_id` int(1) NOT NULL AUTO_INCREMENT,
  `module_id` int(1) DEFAULT NULL,
  `version_id` int(1) NOT NULL,
  `update_type_id` int(1) NOT NULL,
  `update_file` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `run` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`update_id`),
  KEY `cu_module_id_fk` (`module_id`),
  KEY `cu_version_id_fk` (`version_id`),
  KEY `cu_update_type_id_fk` (`update_type_id`),
  CONSTRAINT `cu_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cu_update_type_id_fk` FOREIGN KEY (`update_type_id`) REFERENCES `cms_update_types` (`update_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cu_version_id_fk` FOREIGN KEY (`version_id`) REFERENCES `cms_versions` (`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_us_states`
-- ----------------------------
DROP TABLE IF EXISTS `cms_us_states`;
CREATE TABLE `cms_us_states` (
  `state_id` int(1) NOT NULL,
  `abbreviation` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `state_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_us_states
-- ----------------------------
INSERT INTO `cms_us_states` VALUES ('1', 'AL', 'Alabama');
INSERT INTO `cms_us_states` VALUES ('2', 'AK', 'Alaska');
INSERT INTO `cms_us_states` VALUES ('3', 'AZ', 'Arizona');
INSERT INTO `cms_us_states` VALUES ('4', 'AR', 'Arkansas');
INSERT INTO `cms_us_states` VALUES ('5', 'CA', 'California');
INSERT INTO `cms_us_states` VALUES ('6', 'CO', 'Colorado');
INSERT INTO `cms_us_states` VALUES ('7', 'CT', 'Connecticut');
INSERT INTO `cms_us_states` VALUES ('8', 'DE', 'Delaware');
INSERT INTO `cms_us_states` VALUES ('9', 'FL', 'Florida');
INSERT INTO `cms_us_states` VALUES ('10', 'GA', 'Georgia');
INSERT INTO `cms_us_states` VALUES ('11', 'HI', 'Hawaii');
INSERT INTO `cms_us_states` VALUES ('12', 'ID', 'Idaho');
INSERT INTO `cms_us_states` VALUES ('13', 'IL', 'Illinois');
INSERT INTO `cms_us_states` VALUES ('14', 'IN', 'Indiana');
INSERT INTO `cms_us_states` VALUES ('15', 'IA', 'Iowa');
INSERT INTO `cms_us_states` VALUES ('16', 'KS', 'Kansas');
INSERT INTO `cms_us_states` VALUES ('17', 'KY', 'Kentucky');
INSERT INTO `cms_us_states` VALUES ('18', 'LA', 'Louisiana');
INSERT INTO `cms_us_states` VALUES ('19', 'ME', 'Maine');
INSERT INTO `cms_us_states` VALUES ('20', 'MD', 'Maryland');
INSERT INTO `cms_us_states` VALUES ('21', 'MA', 'Massachusetts');
INSERT INTO `cms_us_states` VALUES ('22', 'MI', 'Michigan');
INSERT INTO `cms_us_states` VALUES ('23', 'MN', 'Minnesota');
INSERT INTO `cms_us_states` VALUES ('24', 'MS', 'Mississippi');
INSERT INTO `cms_us_states` VALUES ('25', 'MO', 'Missouri');
INSERT INTO `cms_us_states` VALUES ('26', 'MT', 'Montana');
INSERT INTO `cms_us_states` VALUES ('27', 'NE', 'Nebraska');
INSERT INTO `cms_us_states` VALUES ('28', 'NV', 'Nevada');
INSERT INTO `cms_us_states` VALUES ('29', 'NH', 'New Hampshire');
INSERT INTO `cms_us_states` VALUES ('30', 'NJ', 'New Jersey');
INSERT INTO `cms_us_states` VALUES ('31', 'NM', 'New Mexico');
INSERT INTO `cms_us_states` VALUES ('32', 'NY', 'New York');
INSERT INTO `cms_us_states` VALUES ('33', 'NC', 'North Carolina');
INSERT INTO `cms_us_states` VALUES ('34', 'ND', 'North Dakota');
INSERT INTO `cms_us_states` VALUES ('35', 'OH', 'Ohio');
INSERT INTO `cms_us_states` VALUES ('36', 'OK', 'Oklahoma');
INSERT INTO `cms_us_states` VALUES ('37', 'OR', 'Oregon');
INSERT INTO `cms_us_states` VALUES ('38', 'PA', 'Pennsylvania');
INSERT INTO `cms_us_states` VALUES ('39', 'RI', 'Rhode Island');
INSERT INTO `cms_us_states` VALUES ('40', 'SC', 'South Carolina');
INSERT INTO `cms_us_states` VALUES ('41', 'SD', 'South Dakota');
INSERT INTO `cms_us_states` VALUES ('42', 'TN', 'Tennessee');
INSERT INTO `cms_us_states` VALUES ('43', 'TX', 'Texas');
INSERT INTO `cms_us_states` VALUES ('44', 'UT', 'Utah');
INSERT INTO `cms_us_states` VALUES ('45', 'VT', 'Vermont');
INSERT INTO `cms_us_states` VALUES ('46', 'VA', 'Virginia');
INSERT INTO `cms_us_states` VALUES ('47', 'WA', 'Washington');
INSERT INTO `cms_us_states` VALUES ('48', 'WV', 'West Virginia');
INSERT INTO `cms_us_states` VALUES ('49', 'WI', 'Wisconsin');
INSERT INTO `cms_us_states` VALUES ('50', 'WY', 'Wyoming');

-- ----------------------------
-- Table structure for `cms_user_role_affiliation`
-- ----------------------------
DROP TABLE IF EXISTS `cms_user_role_affiliation`;
CREATE TABLE `cms_user_role_affiliation` (
  `user_role_affiliation_id` bigint(1) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(1) NOT NULL,
  `role_id` int(1) NOT NULL,
  PRIMARY KEY (`user_role_affiliation_id`),
  KEY `cura_role_id_fk` (`role_id`),
  KEY `cura_user_id_fk` (`user_id`),
  CONSTRAINT `cura_user_id_fk` FOREIGN KEY (`user_id`) REFERENCES `cms_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cura_role_id_fk` FOREIGN KEY (`role_id`) REFERENCES `cms_roles` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_users`
-- ----------------------------
DROP TABLE IF EXISTS `cms_users`;
CREATE TABLE `cms_users` (
  `user_id` bigint(1) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_site_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_versions`
-- ----------------------------
CREATE TABLE `cms_versions` (
  `version_id` int(1) NOT NULL AUTO_INCREMENT,
  `module_id` int(1) DEFAULT NULL,
  `version` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `finished` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version_id`),
  KEY `cv_module_id_fk` (`module_id`),
  CONSTRAINT `cv_module_id_fk` FOREIGN KEY (`module_id`) REFERENCES `cms_modules` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
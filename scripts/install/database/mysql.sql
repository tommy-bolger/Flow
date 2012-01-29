SET FOREIGN_KEY_CHECKS=0;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_configuration_parameters
-- ----------------------------
INSERT INTO `cms_configuration_parameters` VALUES ('1', '2', 'theme', null, 'default', '1', '1', null, 'Theme', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('2', '2', 'code_example_file_extensions', null, 'php,html,aspx,asp,js,css,htc,inc', '2', '5', null, 'Code Example File Extensions', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('3', '1', 'theme', null, 'default', '1', '1', null, 'Theme', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('4', null, 'version', null, '1.0', '1', '1', null, 'Framework Version', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('5', null, 'environment', null, 'production', '2', '1', null, 'Environment', '1');
INSERT INTO `cms_configuration_parameters` VALUES ('11', null, 'session_name', null, 'default', '4', '1', null, 'Client Session Name', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('12', null, 'session_storage_engine', null, 'file', '5', '1', null, 'Session Storage Engine', '1');
INSERT INTO `cms_configuration_parameters` VALUES ('16', null, 'aws_region', null, 'com', '6', '1', null, 'AWS Region', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('17', null, 'aws_public_key', null, null, '7', '1', null, 'AWS Public Key', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('18', null, 'aws_private_key', null, null, '8', '1', null, 'AWS Private Key', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('19', null, 'default_module', null, 'admin', '9', '1', null, 'Default Module', '1');
INSERT INTO `cms_configuration_parameters` VALUES ('21', null, 'javascript_minifier', null, 'simple', '10', '1', null, 'Javascript Minifier', '1');
INSERT INTO `cms_configuration_parameters` VALUES ('22', null, 'closure_compiler_path', null, null, '11', '1', null, 'Closure Compiler Path', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('24', null, 'enable_javascript', null, 'true', '12', '4', null, 'Enable Javascript', '0');
INSERT INTO `cms_configuration_parameters` VALUES ('25', '1', 'encrypt_urls', null, 'true', '2', '4', null, 'Encrypt URLs', '0');

-- ----------------------------
-- Table structure for `cms_errors`
-- ----------------------------
DROP TABLE IF EXISTS `cms_errors`;
CREATE TABLE `cms_errors` (
  `error_id` bigint(1) NOT NULL AUTO_INCREMENT,
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_modules
-- ----------------------------
INSERT INTO `cms_modules` VALUES ('1', 'admin', 'Admin', '1', '1');
INSERT INTO `cms_modules` VALUES ('2', 'resume', 'Resume', '3', '1');
INSERT INTO `cms_modules` VALUES ('3', 'blog', 'Blog', '2', '1');

-- ----------------------------
-- Table structure for `cms_pages`
-- ----------------------------
DROP TABLE IF EXISTS `cms_pages`;
CREATE TABLE `cms_pages` (
  `page_id` int(1) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `page_location` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `cms_parameter_data_types`
-- ----------------------------
DROP TABLE IF EXISTS `cms_parameter_data_types`;
CREATE TABLE `cms_parameter_data_types` (
  `parameter_data_type_id` tinyint(1) NOT NULL,
  `data_type` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parameter_data_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;

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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of cms_parameter_values
-- ----------------------------
INSERT INTO `cms_parameter_values` VALUES ('1', 'development', '1', '5');
INSERT INTO `cms_parameter_values` VALUES ('2', 'production', '2', '5');
INSERT INTO `cms_parameter_values` VALUES ('3', 'file', '1', '12');
INSERT INTO `cms_parameter_values` VALUES ('4', 'database', '2', '12');
INSERT INTO `cms_parameter_values` VALUES ('5', 'admin', '1', '19');
INSERT INTO `cms_parameter_values` VALUES ('6', 'resume', '2', '19');
INSERT INTO `cms_parameter_values` VALUES ('7', 'blog', '3', '19');
INSERT INTO `cms_parameter_values` VALUES ('8', 'simple', '1', '21');
INSERT INTO `cms_parameter_values` VALUES ('9', 'uglify-js', '2', '21');
INSERT INTO `cms_parameter_values` VALUES ('10', 'closure', '3', '21');

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
  `role_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `module_id` int(1) NOT NULL,
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
-- Table structure for `resume_code_example_skills`
-- ----------------------------
DROP TABLE IF EXISTS `resume_code_example_skills`;
CREATE TABLE `resume_code_example_skills` (
  `code_example_skill_id` int(1) NOT NULL AUTO_INCREMENT,
  `code_example_id` int(1) NOT NULL,
  `skill_id` int(1) NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`code_example_skill_id`),
  KEY `rces_code_example_id_fk` (`code_example_id`),
  KEY `rces_skill_id_fk` (`skill_id`),
  CONSTRAINT `rces_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `resume_skills` (`skill_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rces_code_example_id_fk` FOREIGN KEY (`code_example_id`) REFERENCES `resume_code_examples` (`code_example_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_code_examples`
-- ----------------------------
DROP TABLE IF EXISTS `resume_code_examples`;
CREATE TABLE `resume_code_examples` (
  `code_example_id` int(1) NOT NULL AUTO_INCREMENT,
  `source_file_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `portfolio_project_id` int(1) DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `sort_order` smallint(1) NOT NULL,
  `code_example_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `purpose` text COLLATE utf8_unicode_ci NOT NULL,
  `work_history_id` int(1) DEFAULT NULL,
  PRIMARY KEY (`code_example_id`),
  KEY `rce_portfolio_project_id_fk` (`portfolio_project_id`),
  KEY `rce_work_history_id_fk` (`work_history_id`),
  CONSTRAINT `rce_work_history_id_fk` FOREIGN KEY (`work_history_id`) REFERENCES `resume_work_history` (`work_history_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rce_portfolio_project_id_fk` FOREIGN KEY (`portfolio_project_id`) REFERENCES `resume_portfolio_projects` (`portfolio_project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_degree_levels`
-- ----------------------------
DROP TABLE IF EXISTS `resume_degree_levels`;
CREATE TABLE `resume_degree_levels` (
  `degree_level_id` tinyint(1) NOT NULL,
  `abbreviation` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `degree_level_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`degree_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of resume_degree_levels
-- ----------------------------
INSERT INTO `resume_degree_levels` VALUES ('1', 'A.A.', 'Associate of Arts');
INSERT INTO `resume_degree_levels` VALUES ('2', 'A.S.', 'Associate of Science');
INSERT INTO `resume_degree_levels` VALUES ('3', 'AAS', 'Associate of Applied Science');
INSERT INTO `resume_degree_levels` VALUES ('4', 'B.A.', 'Bachelor of Arts');
INSERT INTO `resume_degree_levels` VALUES ('5', 'B.S.', 'Bachelor of Science');
INSERT INTO `resume_degree_levels` VALUES ('6', 'BFA', 'Bachelor of Fine Arts');
INSERT INTO `resume_degree_levels` VALUES ('7', 'M.A.', 'Master of Arts');
INSERT INTO `resume_degree_levels` VALUES ('8', 'M.S.', 'Master of Science');
INSERT INTO `resume_degree_levels` VALUES ('9', 'MBA', 'Master of Business Administration');
INSERT INTO `resume_degree_levels` VALUES ('10', 'MFA', 'Master of Fine Arts');
INSERT INTO `resume_degree_levels` VALUES ('11', 'Ph.D.', 'Doctor of Philosophy');
INSERT INTO `resume_degree_levels` VALUES ('12', 'J.D.', 'Juris Doctor');
INSERT INTO `resume_degree_levels` VALUES ('13', 'M.D.', 'Doctor of Medicine');
INSERT INTO `resume_degree_levels` VALUES ('14', 'DDS', 'Doctor of Dental Surgery');

-- ----------------------------
-- Table structure for `resume_education`
-- ----------------------------
DROP TABLE IF EXISTS `resume_education`;
CREATE TABLE `resume_education` (
  `education_id` int(1) NOT NULL AUTO_INCREMENT,
  `institution_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `institution_city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(1) NOT NULL,
  `degree_level_id` tinyint(1) NOT NULL,
  `degree_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_graduated` date NOT NULL,
  `cumulative_gpa` decimal(3,2) NOT NULL,
  `sort_order` smallint(1) DEFAULT NULL,
  PRIMARY KEY (`education_id`),
  KEY `re_degree_level_id_fk` (`degree_level_id`),
  KEY `re_state_id_fk` (`state_id`),
  CONSTRAINT `re_state_id_fk` FOREIGN KEY (`state_id`) REFERENCES `cms_us_states` (`state_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `re_degree_level_id_fk` FOREIGN KEY (`degree_level_id`) REFERENCES `resume_degree_levels` (`degree_level_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_general_information`
-- ----------------------------
DROP TABLE IF EXISTS `resume_general_information`;
CREATE TABLE `resume_general_information` (
  `general_information_id` tinyint(1) NOT NULL,
  `first_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `state_id` int(1) NOT NULL,
  `phone_number` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email_address` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `resume_pdf_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `resume_word_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `summary` text COLLATE utf8_unicode_ci,
  `specialty` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`general_information_id`),
  KEY `rgi_state_id_fk` (`state_id`),
  CONSTRAINT `rgi_state_id_fk` FOREIGN KEY (`state_id`) REFERENCES `cms_us_states` (`state_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_portfolio_project_images`
-- ----------------------------
DROP TABLE IF EXISTS `resume_portfolio_project_images`;
CREATE TABLE `resume_portfolio_project_images` (
  `portfolio_project_image_id` int(1) NOT NULL AUTO_INCREMENT,
  `portfolio_project_id` int(1) NOT NULL,
  `image_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `thumbnail_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sort_order` smallint(1) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`portfolio_project_image_id`),
  KEY `rppi_portfolio_project_id_fk` (`portfolio_project_id`),
  CONSTRAINT `rppi_portfolio_project_id_fk` FOREIGN KEY (`portfolio_project_id`) REFERENCES `resume_portfolio_projects` (`portfolio_project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_portfolio_project_skills`
-- ----------------------------
DROP TABLE IF EXISTS `resume_portfolio_project_skills`;
CREATE TABLE `resume_portfolio_project_skills` (
  `portfolio_project_skill_id` int(1) NOT NULL AUTO_INCREMENT,
  `portfolio_project_id` int(1) NOT NULL,
  `skill_id` int(1) NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`portfolio_project_skill_id`),
  KEY `rpps_portfolio_project_id_fk` (`portfolio_project_id`),
  KEY `rpps_skill_id_fk` (`skill_id`),
  CONSTRAINT `rpps_skill_id_fk` FOREIGN KEY (`skill_id`) REFERENCES `resume_skills` (`skill_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rpps_portfolio_project_id_fk` FOREIGN KEY (`portfolio_project_id`) REFERENCES `resume_portfolio_projects` (`portfolio_project_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_portfolio_projects`
-- ----------------------------
DROP TABLE IF EXISTS `resume_portfolio_projects`;
CREATE TABLE `resume_portfolio_projects` (
  `portfolio_project_id` int(1) NOT NULL AUTO_INCREMENT,
  `project_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `involvement_description` text COLLATE utf8_unicode_ci,
  `sort_order` smallint(1) NOT NULL,
  `site_url` text COLLATE utf8_unicode_ci,
  `work_history_id` int(1) DEFAULT NULL,
  PRIMARY KEY (`portfolio_project_id`),
  KEY `rpp_work_history_id_fk` (`work_history_id`),
  CONSTRAINT `rpp_work_history_id_fk` FOREIGN KEY (`work_history_id`) REFERENCES `resume_work_history` (`work_history_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_proficiency_levels`
-- ----------------------------
DROP TABLE IF EXISTS `resume_proficiency_levels`;
CREATE TABLE `resume_proficiency_levels` (
  `proficiency_level_id` tinyint(1) NOT NULL,
  `proficiency_level_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`proficiency_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of resume_proficiency_levels
-- ----------------------------
INSERT INTO `resume_proficiency_levels` VALUES ('1', 'Beginner');
INSERT INTO `resume_proficiency_levels` VALUES ('2', 'Intermediate');
INSERT INTO `resume_proficiency_levels` VALUES ('3', 'Advanced');

-- ----------------------------
-- Table structure for `resume_skill_categories`
-- ----------------------------
DROP TABLE IF EXISTS `resume_skill_categories`;
CREATE TABLE `resume_skill_categories` (
  `skill_category_id` int(1) NOT NULL AUTO_INCREMENT,
  `skill_category_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`skill_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_skills`
-- ----------------------------
DROP TABLE IF EXISTS `resume_skills`;
CREATE TABLE `resume_skills` (
  `skill_id` int(1) NOT NULL AUTO_INCREMENT,
  `skill_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `skill_category_id` int(1) NOT NULL,
  `years_proficient` tinyint(1) NOT NULL,
  `proficiency_level_id` tinyint(1) NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`skill_id`),
  KEY `rs_proficiency_level_id_fk` (`proficiency_level_id`),
  KEY `rs_skill_category_id_fk` (`skill_category_id`),
  CONSTRAINT `rs_skill_category_id_fk` FOREIGN KEY (`skill_category_id`) REFERENCES `resume_skill_categories` (`skill_category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rs_proficiency_level_id_fk` FOREIGN KEY (`proficiency_level_id`) REFERENCES `resume_proficiency_levels` (`proficiency_level_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_work_history`
-- ----------------------------
DROP TABLE IF EXISTS `resume_work_history`;
CREATE TABLE `resume_work_history` (
  `work_history_id` int(1) NOT NULL AUTO_INCREMENT,
  `organization_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `job_title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`work_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_work_history_durations`
-- ----------------------------
DROP TABLE IF EXISTS `resume_work_history_durations`;
CREATE TABLE `resume_work_history_durations` (
  `work_history_duration_id` int(1) NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  `work_history_id` int(1) NOT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`work_history_duration_id`),
  KEY `rwhd_work_history_id_fk` (`work_history_id`),
  CONSTRAINT `rwhd_work_history_id_fk` FOREIGN KEY (`work_history_id`) REFERENCES `resume_work_history` (`work_history_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for `resume_work_history_tasks`
-- ----------------------------
DROP TABLE IF EXISTS `resume_work_history_tasks`;
CREATE TABLE `resume_work_history_tasks` (
  `work_history_task_id` int(1) NOT NULL AUTO_INCREMENT,
  `work_history_id` int(1) NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `sort_order` smallint(1) NOT NULL,
  PRIMARY KEY (`work_history_task_id`),
  KEY `rwht_work_history_id_fk` (`work_history_id`),
  CONSTRAINT `rwht_work_history_id_fk` FOREIGN KEY (`work_history_id`) REFERENCES `resume_work_history` (`work_history_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
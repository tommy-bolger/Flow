<?php
    $module_id = db()->insert('cms_modules', array(
        'module_name' => 'resume',
        'display_name' => 'Resume',
        'sort_order' => $sort_order,
        'enabled' => 1
    ));
?>
SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Records of cms_configuration_parameters
-- ----------------------------
INSERT INTO `cms_configuration_parameters` (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', null, '1.0', '1', '1', null, 'Version', '0');
INSERT INTO `cms_configuration_parameters` (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', null, 'default', '2', '1', null, 'Theme', '0');
INSERT INTO `cms_configuration_parameters` (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'code_example_file_extensions', null, 'php,html,aspx,asp,js,css,htc,inc', '3', '5', null, 'Code Example File Extensions', '0');

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
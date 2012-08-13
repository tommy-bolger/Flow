<?php
    $module_id = db()->insert('cms_modules', array(
        'module_name' => 'resume',
        'display_name' => 'Resume',
        'sort_order' => $sort_order,
        'enabled' => 1
    ));
?>
PRAGMA foreign_keys=OFF;
-- ----------------------------
-- Records of cms_configuration_parameters
-- ----------------------------
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', null, '1.0', 1, 1, null, 'Version', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', NULL, 'default', 2, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'code_example_file_extensions', NULL, 'php,html,aspx,asp,js,css,htc,inc', 3, 5, NULL, 'Code Example File Extensions', 0);

-- ----------------------------
-- Table structure for resume_code_example_skills
-- ----------------------------
CREATE TABLE resume_code_example_skills (
  code_example_skill_id integer PRIMARY KEY,
  code_example_id integer NOT NULL,
  skill_id integer NOT NULL,
  sort_order smallint NOT NULL,
  FOREIGN KEY (skill_id) REFERENCES resume_skills (skill_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (code_example_id) REFERENCES resume_code_examples (code_example_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rces_code_example_id_fk ON resume_code_example_skills (code_example_id);
CREATE INDEX rces_skill_id_fk ON resume_code_example_skills (skill_id);

-- ----------------------------
-- Table structure for resume_code_examples
-- ----------------------------
CREATE TABLE resume_code_examples (
  code_example_id integer PRIMARY KEY,
  source_file_name varchar(100),
  portfolio_project_id integer,
  description text,
  sort_order smallint NOT NULL,
  code_example_name varchar(255) NOT NULL,
  purpose text NOT NULL,
  work_history_id integer,
  FOREIGN KEY (work_history_id) REFERENCES resume_work_history (work_history_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects (portfolio_project_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rce_portfolio_project_id_fk ON resume_code_examples (portfolio_project_id);
CREATE INDEX rce_work_history_id_fk ON resume_code_examples (work_history_id);

-- ----------------------------
-- Table structure for resume_degree_levels
-- ----------------------------
CREATE TABLE resume_degree_levels (
  degree_level_id integer PRIMARY KEY,
  abbreviation varchar(10) NOT NULL,
  degree_level_name varchar(50) NOT NULL
);

-- ----------------------------
-- Records of resume_degree_levels
-- ----------------------------
INSERT INTO resume_degree_levels VALUES (1, 'A.A.', 'Associate of Arts');
INSERT INTO resume_degree_levels VALUES (2, 'A.S.', 'Associate of Science');
INSERT INTO resume_degree_levels VALUES (3, 'AAS', 'Associate of Applied Science');
INSERT INTO resume_degree_levels VALUES (4, 'B.A.', 'Bachelor of Arts');
INSERT INTO resume_degree_levels VALUES (5, 'B.S.', 'Bachelor of Science');
INSERT INTO resume_degree_levels VALUES (6, 'BFA', 'Bachelor of Fine Arts');
INSERT INTO resume_degree_levels VALUES (7, 'M.A.', 'Master of Arts');
INSERT INTO resume_degree_levels VALUES (8, 'M.S.', 'Master of Science');
INSERT INTO resume_degree_levels VALUES (9, 'MBA', 'Master of Business Administration');
INSERT INTO resume_degree_levels VALUES (10, 'MFA', 'Master of Fine Arts');
INSERT INTO resume_degree_levels VALUES (11, 'Ph.D.', 'Doctor of Philosophy');
INSERT INTO resume_degree_levels VALUES (12, 'J.D.', 'Juris Doctor');
INSERT INTO resume_degree_levels VALUES (13, 'M.D.', 'Doctor of Medicine');
INSERT INTO resume_degree_levels VALUES (14, 'DDS', 'Doctor of Dental Surgery');

-- ----------------------------
-- Table structure for resume_education
-- ----------------------------
CREATE TABLE resume_education (
  education_id integer PRIMARY KEY,
  institution_name varchar(255) NOT NULL,
  institution_city varchar(100) NOT NULL,
  state_id integer NOT NULL,
  degree_level_id integer NOT NULL,
  degree_name varchar(255) NOT NULL,
  date_graduated date NOT NULL,
  cumulative_gpa decimal(3,2) NOT NULL,
  sort_order smallint,
  FOREIGN KEY (state_id) REFERENCES cms_us_states (state_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (degree_level_id) REFERENCES resume_degree_levels (degree_level_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX re_degree_level_id_fk ON resume_education (degree_level_id);
CREATE INDEX re_state_id_fk ON resume_education (state_id);

-- ----------------------------
-- Table structure for resume_general_information
-- ----------------------------
CREATE TABLE resume_general_information (
  general_information_id integer PRIMARY KEY,
  first_name varchar(100) NOT NULL,
  last_name varchar(100) NOT NULL,
  address varchar(100),
  city varchar(100) NOT NULL,
  state_id integer NOT NULL,
  phone_number varchar(15),
  photo varchar(255),
  email_address varchar(100) NOT NULL,
  resume_pdf_name varchar(100),
  resume_word_name varchar(100),
  summary text,
  specialty varchar(255) NOT NULL,
  FOREIGN KEY (state_id) REFERENCES cms_us_states (state_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rgi_state_id_fk ON resume_general_information (state_id);

-- ----------------------------
-- Table structure for resume_portfolio_project_images
-- ----------------------------
CREATE TABLE resume_portfolio_project_images (
  portfolio_project_image_id integer PRIMARY KEY,
  portfolio_project_id integer NOT NULL,
  image_name varchar(255),
  thumbnail_name varchar(255),
  sort_order smallint NOT NULL,
  title varchar(255) NOT NULL,
  description text,
  FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects (portfolio_project_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rppi_portfolio_project_id_fk ON resume_portfolio_project_images (portfolio_project_id);

-- ----------------------------
-- Table structure for resume_portfolio_project_skills
-- ----------------------------
CREATE TABLE resume_portfolio_project_skills (
  portfolio_project_skill_id integer PRIMARY KEY,
  portfolio_project_id integer NOT NULL,
  skill_id integer NOT NULL,
  sort_order smallint NOT NULL,
  FOREIGN KEY (skill_id) REFERENCES resume_skills (skill_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects (portfolio_project_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rpps_portfolio_project_id_fk ON resume_portfolio_project_skills (portfolio_project_id);
CREATE INDEX rpps_skill_id_fk ON resume_portfolio_project_skills (skill_id);

-- ----------------------------
-- Table structure for resume_portfolio_projects
-- ----------------------------
CREATE TABLE resume_portfolio_projects (
  portfolio_project_id integer PRIMARY KEY,
  project_name varchar(255) NOT NULL,
  description text NOT NULL,
  involvement_description text,
  sort_order smallint NOT NULL,
  site_url text,
  work_history_id integer,
  FOREIGN KEY (work_history_id) REFERENCES resume_work_history (work_history_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rpp_work_history_id_fk ON resume_portfolio_projects (work_history_id);

-- ----------------------------
-- Table structure for resume_proficiency_levels
-- ----------------------------
CREATE TABLE resume_proficiency_levels (
  proficiency_level_id integer PRIMARY KEY,
  proficiency_level_name varchar(100) NOT NULL
);

-- ----------------------------
-- Records of resume_proficiency_levels
-- ----------------------------
INSERT INTO resume_proficiency_levels VALUES (1, 'Beginner');
INSERT INTO resume_proficiency_levels VALUES (2, 'Intermediate');
INSERT INTO resume_proficiency_levels VALUES (3, 'Advanced');

-- ----------------------------
-- Table structure for resume_skill_categories
-- ----------------------------
CREATE TABLE resume_skill_categories (
  skill_category_id integer PRIMARY KEY,
  skill_category_name varchar(100) NOT NULL,
  sort_order smallint NOT NULL
);

-- ----------------------------
-- Table structure for resume_skills
-- ----------------------------
CREATE TABLE resume_skills (
  skill_id integer PRIMARY KEY,
  skill_name varchar(100) NOT NULL,
  skill_category_id integer NOT NULL,
  years_proficient tinyint NOT NULL,
  proficiency_level_id integer NOT NULL,
  sort_order smallint NOT NULL,
  FOREIGN KEY (skill_category_id) REFERENCES resume_skill_categories (skill_category_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (proficiency_level_id) REFERENCES resume_proficiency_levels (proficiency_level_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rs_proficiency_level_id_fk ON resume_skills (proficiency_level_id);
CREATE INDEX rs_skill_category_id_fk ON resume_skills (skill_category_id);

-- ----------------------------
-- Table structure for resume_work_history
-- ----------------------------
CREATE TABLE resume_work_history (
  work_history_id integer PRIMARY KEY,
  organization_name varchar(255) NOT NULL,
  job_title varchar(100) NOT NULL,
  sort_order smallint NOT NULL
);

-- ----------------------------
-- Table structure for resume_work_history_durations
-- ----------------------------
CREATE TABLE resume_work_history_durations (
  work_history_duration_id integer PRIMARY KEY,
  start_date date NOT NULL,
  sort_order smallint NOT NULL,
  work_history_id integer NOT NULL,
  end_date date,
  FOREIGN KEY (work_history_id) REFERENCES resume_work_history (work_history_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rwhd_work_history_id_fk ON resume_work_history_durations (work_history_id);

-- ----------------------------
-- Table structure for resume_work_history_tasks
-- ----------------------------
CREATE TABLE resume_work_history_tasks (
  work_history_task_id integer PRIMARY KEY,
  work_history_id integer NOT NULL,
  description text NOT NULL,
  sort_order smallint NOT NULL,
  FOREIGN KEY (work_history_id) REFERENCES resume_work_history (work_history_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX rwht_work_history_id_fk ON resume_work_history_tasks (work_history_id);
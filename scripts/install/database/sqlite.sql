PRAGMA foreign_keys=OFF;

-- ----------------------------
-- Table structure for cms_configuration_parameters
-- ----------------------------
CREATE TABLE cms_configuration_parameters (
  configuration_parameter_id integer PRIMARY KEY,
  module_id integer,
  parameter_name varchar(100) NOT NULL,
  value varchar(255),
  default_value varchar(255),
  sort_order int NOT NULL,
  parameter_data_type_id integer NOT NULL,
  description text,
  display_name varchar(100) NOT NULL,
  has_value_list tinyint NOT NULL DEFAULT 0,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (parameter_data_type_id) REFERENCES cms_parameter_data_types (parameter_data_type_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX ccp_parameter_data_type_id_fk ON cms_configuration_parameters (parameter_data_type_id);
CREATE INDEX ccp_module_id_fk ON cms_configuration_parameters (module_id);

-- ----------------------------
-- Records of cms_configuration_parameters
-- ----------------------------
INSERT INTO cms_configuration_parameters VALUES (1, 2, 'theme', NULL, 'default', 1, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters VALUES (2, 2, 'code_example_file_extensions', NULL, 'php,html,aspx,asp,js,css,htc,inc', 2, 5, NULL, 'Code Example File Extensions', 0);
INSERT INTO cms_configuration_parameters VALUES (3, 1, 'theme', NULL, 'default', 1, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters VALUES (4, NULL, 'version', NULL, 1.0, 1, 1, NULL, 'Framework Version', 0);
INSERT INTO cms_configuration_parameters VALUES (5, NULL, 'environment', NULL, 'production', 2, 1, NULL, 'Environment', 1);
INSERT INTO cms_configuration_parameters VALUES (11, NULL, 'session_name', NULL, 'default', 4, 1, NULL, 'Client Session Name', 0);
INSERT INTO cms_configuration_parameters VALUES (12, NULL, 'session_storage_engine', NULL, 'file', 5, 1, NULL, 'Session Storage Engine', 1);
INSERT INTO cms_configuration_parameters VALUES (16, NULL, 'aws_region', NULL, 'com', 6, 1, NULL, 'AWS Region', 0);
INSERT INTO cms_configuration_parameters VALUES (17, NULL, 'aws_public_key', NULL, NULL, 7, 1, NULL, 'AWS Public Key', 0);
INSERT INTO cms_configuration_parameters VALUES (18, NULL, 'aws_private_key', NULL, NULL, 8, 1, NULL, 'AWS Private Key', 0);
INSERT INTO cms_configuration_parameters VALUES (19, NULL, 'default_module', NULL, 'admin', 9, 1, NULL, 'Default Module', 1);
INSERT INTO cms_configuration_parameters VALUES (21, NULL, 'javascript_minifier', NULL, 'simple', 10, 1, NULL, 'Javascript Minifier', 1);
INSERT INTO cms_configuration_parameters VALUES (22, NULL, 'closure_compiler_path', NULL, NULL, 11, 1, NULL, 'Closure Compiler Path', 0);
INSERT INTO cms_configuration_parameters VALUES (24, NULL, 'enable_javascript', NULL, '1', 12, 4, NULL, 'Enable Javascript', 0);
INSERT INTO cms_configuration_parameters VALUES (25, 1, 'encrypt_urls', NULL, '1', 2, 4, NULL, 'Encrypt URLs', 0);

-- ----------------------------
-- Table structure for cms_errors
-- ----------------------------
CREATE TABLE cms_errors (
  error_id integer PRIMARY KEY,
  error_code varchar(20),
  error_message text,
  error_file text,
  error_line smallint,
  error_trace text,
  created_time datetime,
  module_id integer,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ----------------------------
-- Table structure for cms_modules
-- ----------------------------
CREATE TABLE cms_modules (
  module_id integer PRIMARY KEY,
  module_name varchar(50) NOT NULL,
  display_name varchar(100),
  sort_order tinyint,
  enabled tinyint NOT NULL DEFAULT 0
);

-- ----------------------------
-- Records of cms_modules
-- ----------------------------
INSERT INTO cms_modules VALUES (1, 'admin', 'Admin', 1, 1);
INSERT INTO cms_modules VALUES (2, 'resume', 'Resume', 3, 1);
INSERT INTO cms_modules VALUES (3, 'blog', 'Blog', 2, 1);

-- ----------------------------
-- Table structure for cms_pages
-- ----------------------------
CREATE TABLE cms_pages (
  page_id integer PRIMARY KEY,
  page_name varchar(25) NOT NULL,
  page_location text NOT NULL
);

-- ----------------------------
-- Table structure for cms_parameter_data_types
-- ----------------------------
CREATE TABLE cms_parameter_data_types (
  parameter_data_type_id integer PRIMARY KEY,
  data_type varchar(15) NOT NULL
);

-- ----------------------------
-- Records of cms_parameter_data_types
-- ----------------------------
INSERT INTO cms_parameter_data_types VALUES (1, 'string');
INSERT INTO cms_parameter_data_types VALUES (2, 'integer');
INSERT INTO cms_parameter_data_types VALUES (3, 'float');
INSERT INTO cms_parameter_data_types VALUES (4, 'boolean');
INSERT INTO cms_parameter_data_types VALUES (5, 'array');

-- ----------------------------
-- Table structure for cms_parameter_values
-- ----------------------------
CREATE TABLE cms_parameter_values (
  parameter_value_id integer PRIMARY KEY,
  parameter_value varchar(50) NOT NULL,
  sort_order smallint NOT NULL,
  configuration_parameter_id integer NOT NULL,
  FOREIGN KEY (configuration_parameter_id) REFERENCES cms_configuration_parameters (configuration_parameter_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX cpv_configuration_parameter_id_fk ON cms_parameter_values (configuration_parameter_id);

-- ----------------------------
-- Records of cms_parameter_values
-- ----------------------------
INSERT INTO cms_parameter_values VALUES (1, 'development', 1, 5);
INSERT INTO cms_parameter_values VALUES (2, 'production', 2, 5);
INSERT INTO cms_parameter_values VALUES (3, 'file', 1, 12);
INSERT INTO cms_parameter_values VALUES (4, 'database', 2, 12);
INSERT INTO cms_parameter_values VALUES (5, 'admin', 1, 19);
INSERT INTO cms_parameter_values VALUES (6, 'resume', 2, 19);
INSERT INTO cms_parameter_values VALUES (7, 'blog', 3, 19);
INSERT INTO cms_parameter_values VALUES (8, 'simple', 1, 21);
INSERT INTO cms_parameter_values VALUES (9, 'uglify-js', 2, 21);
INSERT INTO cms_parameter_values VALUES (10, 'closure', 3, 21);

-- ----------------------------
-- Table structure for cms_permissions
-- ----------------------------
CREATE TABLE cms_permissions (
  permission_id integer PRIMARY KEY,
  permission_name varchar(100) NOT NULL,
  display_name varchar(100) NOT NULL,
  description varchar(255),
  module_id integer NOT NULL,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX cp_module_id_fk ON cms_permissions (module_id);

-- ----------------------------
-- Table structure for cms_role_permission_affiliation
-- ----------------------------
CREATE TABLE cms_role_permission_affiliation (
  role_permission_affiliation_id integer PRIMARY KEY,
  role_id integer NOT NULL,
  permission_id integer NOT NULL,
  can tinyint NOT NULL DEFAULT 0,
  FOREIGN KEY (role_id) REFERENCES cms_roles (role_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (permission_id) REFERENCES cms_permissions (permission_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX crpa_permission_id_fk ON cms_role_permission_affiliation (permission_id);
CREATE INDEX crpa_role_id_fk ON cms_role_permission_affiliation (role_id);

-- ----------------------------
-- Table structure for cms_roles
-- ----------------------------
CREATE TABLE cms_roles (
  role_id integer PRIMARY KEY,
  role_name varchar(100) NOT NULL,
  display_name varchar(100) NOT NULL,
  module_id integer NOT NULL,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ----------------------------
-- Table structure for cms_sessions
-- ----------------------------
CREATE TABLE cms_sessions (
  session_id varchar(40) PRIMARY KEY,
  session_name varchar(100) NOT NULL,
  session_data text NOT NULL,
  expire_time int NOT NULL
);

-- ----------------------------
-- Table structure for cms_us_states
-- ----------------------------
CREATE TABLE cms_us_states (
  state_id integer PRIMARY KEY,
  abbreviation varchar(3) NOT NULL,
  state_name varchar(50) NOT NULL
);

-- ----------------------------
-- Records of cms_us_states
-- ----------------------------
INSERT INTO cms_us_states VALUES (1, 'AL', 'Alabama');
INSERT INTO cms_us_states VALUES (2, 'AK', 'Alaska');
INSERT INTO cms_us_states VALUES (3, 'AZ', 'Arizona');
INSERT INTO cms_us_states VALUES (4, 'AR', 'Arkansas');
INSERT INTO cms_us_states VALUES (5, 'CA', 'California');
INSERT INTO cms_us_states VALUES (6, 'CO', 'Colorado');
INSERT INTO cms_us_states VALUES (7, 'CT', 'Connecticut');
INSERT INTO cms_us_states VALUES (8, 'DE', 'Delaware');
INSERT INTO cms_us_states VALUES (9, 'FL', 'Florida');
INSERT INTO cms_us_states VALUES (10, 'GA', 'Georgia');
INSERT INTO cms_us_states VALUES (11, 'HI', 'Hawaii');
INSERT INTO cms_us_states VALUES (12, 'ID', 'Idaho');
INSERT INTO cms_us_states VALUES (13, 'IL', 'Illinois');
INSERT INTO cms_us_states VALUES (14, 'IN', 'Indiana');
INSERT INTO cms_us_states VALUES (15, 'IA', 'Iowa');
INSERT INTO cms_us_states VALUES (16, 'KS', 'Kansas');
INSERT INTO cms_us_states VALUES (17, 'KY', 'Kentucky');
INSERT INTO cms_us_states VALUES (18, 'LA', 'Louisiana');
INSERT INTO cms_us_states VALUES (19, 'ME', 'Maine');
INSERT INTO cms_us_states VALUES (20, 'MD', 'Maryland');
INSERT INTO cms_us_states VALUES (21, 'MA', 'Massachusetts');
INSERT INTO cms_us_states VALUES (22, 'MI', 'Michigan');
INSERT INTO cms_us_states VALUES (23, 'MN', 'Minnesota');
INSERT INTO cms_us_states VALUES (24, 'MS', 'Mississippi');
INSERT INTO cms_us_states VALUES (25, 'MO', 'Missouri');
INSERT INTO cms_us_states VALUES (26, 'MT', 'Montana');
INSERT INTO cms_us_states VALUES (27, 'NE', 'Nebraska');
INSERT INTO cms_us_states VALUES (28, 'NV', 'Nevada');
INSERT INTO cms_us_states VALUES (29, 'NH', 'New Hampshire');
INSERT INTO cms_us_states VALUES (30, 'NJ', 'New Jersey');
INSERT INTO cms_us_states VALUES (31, 'NM', 'New Mexico');
INSERT INTO cms_us_states VALUES (32, 'NY', 'New York');
INSERT INTO cms_us_states VALUES (33, 'NC', 'North Carolina');
INSERT INTO cms_us_states VALUES (34, 'ND', 'North Dakota');
INSERT INTO cms_us_states VALUES (35, 'OH', 'Ohio');
INSERT INTO cms_us_states VALUES (36, 'OK', 'Oklahoma');
INSERT INTO cms_us_states VALUES (37, 'OR', 'Oregon');
INSERT INTO cms_us_states VALUES (38, 'PA', 'Pennsylvania');
INSERT INTO cms_us_states VALUES (39, 'RI', 'Rhode Island');
INSERT INTO cms_us_states VALUES (40, 'SC', 'South Carolina');
INSERT INTO cms_us_states VALUES (41, 'SD', 'South Dakota');
INSERT INTO cms_us_states VALUES (42, 'TN', 'Tennessee');
INSERT INTO cms_us_states VALUES (43, 'TX', 'Texas');
INSERT INTO cms_us_states VALUES (44, 'UT', 'Utah');
INSERT INTO cms_us_states VALUES (45, 'VT', 'Vermont');
INSERT INTO cms_us_states VALUES (46, 'VA', 'Virginia');
INSERT INTO cms_us_states VALUES (47, 'WA', 'Washington');
INSERT INTO cms_us_states VALUES (48, 'WV', 'West Virginia');
INSERT INTO cms_us_states VALUES (49, 'WI', 'Wisconsin');
INSERT INTO cms_us_states VALUES (50, 'WY', 'Wyoming');

-- ----------------------------
-- Table structure for cms_user_role_affiliation
-- ----------------------------
CREATE TABLE cms_user_role_affiliation (
  user_role_affiliation_id integer PRIMARY KEY,
  user_id integer NOT NULL,
  role_id integer NOT NULL,
  FOREIGN KEY (user_id) REFERENCES cms_users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (role_id) REFERENCES cms_roles (role_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX cura_role_id_fk ON cms_user_role_affiliation (role_id);
CREATE INDEX cura_user_id_fk ON cms_user_role_affiliation (user_id);

-- ----------------------------
-- Table structure for cms_users
-- ----------------------------
CREATE TABLE cms_users (
  user_id integer PRIMARY KEY,
  user_name varchar(50) NOT NULL,
  password varchar(255) NOT NULL,
  email_address varchar(255) NOT NULL,
  is_site_admin tinyint NOT NULL DEFAULT 0
);

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
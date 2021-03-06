PRAGMA foreign_keys=OFF;

-- ----------------------------
-- Table structure for cms_ad_campaign_affiliation
-- ----------------------------
CREATE TABLE cms_ad_campaign_affiliation (
  ad_campaign_affiliation_id integer PRIMARY KEY,
  ad_id integer NOT NULL,
  ad_campaign_id integer NOT NULL,
  is_active tinyint NOT NULL DEFAULT 0,
  start_date date NOT NULL,
  end_date date,
  show_chance_percentage tinyint NOT NULL DEFAULT 0,
  FOREIGN KEY (ad_campaign_id) REFERENCES cms_ad_campaigns (ad_campaign_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (ad_id) REFERENCES cms_ads (ad_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX aca_ad_id_fk ON cms_ad_campaign_affiliation (ad_id);
CREATE INDEX aca_ad_campaign_id_fk ON cms_ad_campaign_affiliation (ad_campaign_id);

-- ----------------------------
-- Table structure for cms_ad_campaigns
-- ----------------------------
CREATE TABLE cms_ad_campaigns (
  ad_campaign_id integer PRIMARY KEY,
  module_id integer NOT NULL,
  ad_campaign_name varchar(50) NOT NULL,
  description varchar(255),
  is_active tinyint NOT NULL DEFAULT 0,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX ac_module_id_fk ON cms_ad_campaigns (module_id);

-- ----------------------------
-- Table structure for cms_ads
-- ----------------------------
CREATE TABLE cms_ads (
  ad_id integer PRIMARY KEY,
  module_id integer NOT NULL,
  description varchar(255),
  code text NOT NULL,
  is_active tinyint NOT NULL DEFAULT 0,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX ca_module_id_fk ON cms_ads (module_id);

-- ----------------------------
-- Table structure for cms_banned_ip_addresses
-- ----------------------------
CREATE TABLE cms_banned_ip_addresses (
  banned_ip_address_id integer PRIMARY KEY,
  ip_address varchar(15) NOT NULL,
  expiration_time datetime
);

-- ----------------------------
-- Table structure for cms_censored_words
-- ----------------------------
CREATE TABLE cms_censored_words (
  censored_word_id integer PRIMARY KEY,
  original_word text NOT NULL,
  translated_to varchar(255) NOT NULL DEFAULT '[CENSORED]'
);

-- ----------------------------
-- Table structure for cms_configuration_parameters
-- ----------------------------
CREATE TABLE cms_configuration_parameters (
  configuration_parameter_id integer PRIMARY KEY,
  module_id integer,
  parameter_name varchar(100) NOT NULL,
  value text,
  default_value text,
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
INSERT INTO cms_configuration_parameters VALUES (1, 1, 'theme', NULL, 'light', 1, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters VALUES (2, NULL, 'version', NULL, '1.0', 1, 1, NULL, 'Framework Version', 0);
INSERT INTO cms_configuration_parameters VALUES (3, NULL, 'environment', NULL, 'production', 2, 1, NULL, 'Environment', 1);
INSERT INTO cms_configuration_parameters VALUES (4, NULL, 'session_name', NULL, 'default', 4, 1, NULL, 'Client Session Name', 0);
INSERT INTO cms_configuration_parameters VALUES (5, NULL, 'session_storage_engine', NULL, 'file', 5, 1, NULL, 'Session Storage Engine', 1);
INSERT INTO cms_configuration_parameters VALUES (6, NULL, 'aws_region', NULL, 'com', 6, 1, NULL, 'AWS Region', 0);
INSERT INTO cms_configuration_parameters VALUES (7, NULL, 'aws_public_key', NULL, NULL, 7, 1, NULL, 'AWS Public Key', 0);
INSERT INTO cms_configuration_parameters VALUES (8, NULL, 'aws_private_key', NULL, NULL, 8, 1, NULL, 'AWS Private Key', 0);
INSERT INTO cms_configuration_parameters VALUES (9, NULL, 'default_module', NULL, 'admin', 9, 1, NULL, 'Default Module', 0);
INSERT INTO cms_configuration_parameters VALUES (10, NULL, 'javascript_minifier', NULL, 'simple', 10, 1, NULL, 'Javascript Minifier', 1);
INSERT INTO cms_configuration_parameters VALUES (11, NULL, 'closure_compiler_path', NULL, NULL, 11, 1, NULL, 'Closure Compiler Path', 0);
INSERT INTO cms_configuration_parameters VALUES (12, NULL, 'enable_javascript', NULL, 1, 12, 4, NULL, 'Enable Javascript', 0);
INSERT INTO cms_configuration_parameters VALUES (13, 1, 'encrypt_urls', NULL, 1, 2, 4, NULL, 'Encrypt URLs', 0);
INSERT INTO cms_configuration_parameters VALUES (14, NULL, 'recaptcha_api_method', NULL, 'https', 13, 1, NULL, 'reCaptcha API Method', 1);
INSERT INTO cms_configuration_parameters VALUES (15, NULL, 'recaptcha_private_key', NULL, NULL, 14, 1, NULL, 'reCaptcha Private Key', 0);
INSERT INTO cms_configuration_parameters VALUES (16, NULL, 'recaptcha_public_key', NULL, NULL, 15, 1, NULL, 'reCaptcha Public Key', 0);
INSERT INTO cms_configuration_parameters VALUES (17, NULL, 'attempts_form_max_attempts', NULL, 5, 16, 2, NULL, 'Limited Attempts Form Max Attempts', 0);
INSERT INTO cms_configuration_parameters VALUES (18, NULL, 'attempts_form_timeout_duration', NULL, 600, 17, 2, NULL, 'Limited Attempts Form Timeout Duration', 0);
INSERT INTO cms_configuration_parameters VALUES (19, NULL, 'port_numbers_in_urls', NULL, 1, 18, 4, NULL, 'Insert Port Numbers in URLs', 0);

-- ----------------------------
-- Table structure for cms_errors
-- ----------------------------
CREATE TABLE cms_errors (
  error_id integer PRIMARY KEY,
  incident_number varchar(9) NOT NULL,
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
-- Table structure for cms_meta_settings
-- ----------------------------
CREATE TABLE cms_meta_settings (
  meta_setting_id integer PRIMARY KEY,
  module_id integer NOT NULL,
  tag_name varchar(30),
  http_equiv varchar(30),
  content text NOT NULL,
  is_active smallint NOT NULL,
  sort_order smallint NOT NULL,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX cms_module_id_fk ON cms_meta_settings (module_id);

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
INSERT INTO cms_parameter_values VALUES (1, 'development', 1, 3);
INSERT INTO cms_parameter_values VALUES (2, 'production', 2, 3);
INSERT INTO cms_parameter_values VALUES (3, 'file', 1, 5);
INSERT INTO cms_parameter_values VALUES (4, 'database', 2, 5);
INSERT INTO cms_parameter_values VALUES (5, 'simple', 1, 10);
INSERT INTO cms_parameter_values VALUES (6, 'uglify-js', 2, 10);
INSERT INTO cms_parameter_values VALUES (7, 'closure', 3, 10);
INSERT INTO cms_parameter_values VALUES (8, 'http', 1, 14);
INSERT INTO cms_parameter_values VALUES (9, 'https', 2, 14);

-- ----------------------------
-- Table structure for cms_permissions
-- ----------------------------
CREATE TABLE cms_permissions (
  permission_id integer PRIMARY KEY,
  permission_name varchar(100) NOT NULL,
  display_name varchar(100) NOT NULL,
  description varchar(255),
  module_id integer NOT NULL,
  sort_order integer NOT NULL,
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
  display_name varchar(100) NOT NULL,
  module_id integer NOT NULL,
  sort_order smallint NOT NULL,
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
-- Table structure for cms_static_pages
-- ----------------------------
CREATE TABLE cms_static_pages (
  static_page_id integer PRIMARY KEY,
  module_id integer NOT NULL,
  page_name varchar(100) NOT NULL,
  display_name varchar(255) NOT NULL,
  title varchar(255),
  content text,
  is_active tinyint NOT NULL,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX csp_module_id_fk ON cms_static_pages (module_id);

-- ----------------------------
-- Table structure for cms_update_types
-- ----------------------------
CREATE TABLE cms_update_types (
  update_type_id integer PRIMARY KEY,
  update_type varchar(50) NOT NULL
);

-- ----------------------------
-- Table structure for cms_updates
-- ----------------------------
CREATE TABLE cms_updates (
  update_id integer PRIMARY KEY,
  module_id integer,
  version_id integer NOT NULL,
  update_type_id integer NOT NULL,
  update_file varchar(20) NOT NULL,
  run tinyint NOT NULL DEFAULT 0,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (update_type_id) REFERENCES cms_update_types (update_type_id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (version_id) REFERENCES cms_versions (version_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX cu_module_id_fk ON cms_updates (module_id);
CREATE INDEX cu_version_id_fk ON cms_updates (version_id);
CREATE INDEX cu_update_type_id_fk ON cms_updates (update_type_id);

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
-- Table structure for cms_versions
-- ----------------------------
CREATE TABLE cms_versions (
  version_id integer PRIMARY KEY,
  module_id integer,
  version varchar(20) NOT NULL,
  finished tinyint NOT NULL DEFAULT 0,
  FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE INDEX cv_module_id_fk ON cms_versions (module_id);
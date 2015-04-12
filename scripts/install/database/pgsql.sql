SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

-- Create Tables and Sequences --

CREATE TABLE cms_ad_campaign_affiliation (
  ad_campaign_affiliation_id integer,
  ad_id integer NOT NULL,
  ad_campaign_id integer NOT NULL,
  is_active smallint DEFAULT 0 NOT NULL,
  start_date date NOT NULL,
  end_date date,
  show_chance_percentage smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_ad_campaign_affiliation_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_ad_campaign_affiliation_seq OWNED BY cms_ad_campaign_affiliation.ad_campaign_affiliation_id;


CREATE TABLE cms_ad_campaigns (
  ad_campaign_id integer,
  module_id integer NOT NULL,
  ad_campaign_name character varying(50) NOT NULL,
  description character varying(255),
  is_active smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_ad_campaigns_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_ad_campaigns_seq OWNED BY cms_ad_campaigns.ad_campaign_id;


CREATE TABLE cms_ads (
  ad_id integer,
  module_id integer NOT NULL,
  description character varying(255),
  code text NOT NULL,
  is_active smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_ads_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_ads_seq OWNED BY cms_ads.ad_id;


CREATE TABLE cms_banned_ip_addresses (
  banned_ip_address_id integer,
  ip_address character varying(15) NOT NULL,
  expiration_time timestamp without time zone
);

CREATE SEQUENCE cms_banned_ip_addresses_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_banned_ip_addresses_seq OWNED BY cms_banned_ip_addresses.banned_ip_address_id;


CREATE TABLE cms_censored_words (
  censored_word_id integer,
  original_word text NOT NULL,
  translated_to character varying(255) DEFAULT '[CENSORED]' NOT NULL
);

CREATE SEQUENCE cms_censored_words_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_censored_words_seq OWNED BY cms_censored_words.censored_word_id;


CREATE TABLE cms_configuration_parameters (
    configuration_parameter_id integer NOT NULL,
    module_id integer,
    parameter_name character varying(100) NOT NULL,
    value character varying(255),
    default_value character varying(255),
    sort_order integer NOT NULL,
    parameter_data_type_id smallint NOT NULL,
    description text,
    display_name character varying(100) NOT NULL,
    has_value_list smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_configuration_parameters_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_configuration_parameters_seq OWNED BY cms_configuration_parameters.configuration_parameter_id;

SELECT pg_catalog.setval('cms_configuration_parameters_seq', 19, true);


CREATE TABLE cms_errors (
    error_id bigint NOT NULL,
    incident_number character varying(9) NOT NULL,
    error_code character varying(20),
    error_message text,
    error_file text,
    error_line smallint,
    error_trace text,
    created_time timestamp without time zone NOT NULL,
    module_id integer
);

CREATE SEQUENCE cms_errors_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_errors_seq OWNED BY cms_errors.error_id;


CREATE TABLE cms_meta_settings (
  meta_setting_id integer NOT NULL,
  module_id integer NOT NULL,
  tag_name character varying(30),
  http_equiv character varying(30),
  content text NOT NULL,
  is_active smallint NOT NULL,
  sort_order smallint NOT NULL
);

CREATE SEQUENCE cms_meta_settings_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_meta_settings_seq OWNED BY cms_meta_settings.meta_setting_id;


CREATE TABLE cms_modules (
    module_id integer NOT NULL,
    module_name character varying(50) NOT NULL,
    display_name character varying(100),
    sort_order smallint,
    enabled smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_modules_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_modules_seq OWNED BY cms_modules.module_id;

SELECT pg_catalog.setval('cms_modules_seq', 1, true);


CREATE TABLE cms_parameter_data_types (
    parameter_data_type_id smallint NOT NULL,
    data_type character varying(15) NOT NULL
);


CREATE TABLE cms_parameter_values (
    parameter_value_id integer NOT NULL,
    parameter_value character varying(50) NOT NULL,
    sort_order smallint NOT NULL,
    configuration_parameter_id integer NOT NULL
);

CREATE SEQUENCE cms_parameter_values_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_parameter_values_seq OWNED BY cms_parameter_values.parameter_value_id;

SELECT pg_catalog.setval('cms_parameter_values_seq', 8, true);


CREATE TABLE cms_permissions (
    permission_id integer NOT NULL,
    permission_name character varying(100) NOT NULL,
    display_name character varying(100) NOT NULL,
    description character varying(255),
    module_id integer NOT NULL,
    smallint integer NOT NULL
);

CREATE SEQUENCE cms_permissions_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
    
ALTER SEQUENCE cms_permissions_seq OWNED BY cms_permissions.permission_id;


CREATE TABLE cms_role_permission_affiliation (
    role_permission_affiliation_id integer NOT NULL,
    role_id integer NOT NULL,
    permission_id integer NOT NULL,
    can smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_role_permission_affiliation_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_role_permission_affiliation_seq OWNED BY cms_role_permission_affiliation.role_permission_affiliation_id;


CREATE TABLE cms_roles (
    role_id integer NOT NULL,
    display_name character varying(100) NOT NULL,
    module_id integer NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE cms_roles_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_roles_seq OWNED BY cms_roles.role_id;


CREATE TABLE cms_sessions (
    session_id character varying(40) NOT NULL,
    session_name character varying(100) NOT NULL,
    session_data text NOT NULL,
    expire_time integer NOT NULL
);


CREATE TABLE cms_static_pages (
  static_page_id integer NOT NULL,
  module_id integer NOT NULL,
  page_name character varying(100) NOT NULL,
  display_name character varying(255) NOT NULL,
  title character varying(255),
  content text,
  is_active smallint NOT NULL
);

CREATE SEQUENCE cms_static_pages_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_static_pages_seq OWNED BY cms_static_pages.static_page_id;


CREATE TABLE cms_update_types (
  update_type_id integer NOT NULL,
  update_type character varying(50) NOT NULL
);

CREATE SEQUENCE cms_update_types_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_update_types_seq OWNED BY cms_update_types.update_type_id;


CREATE TABLE cms_updates (
  update_id integer NOT NULL,
  module_id integer,
  version_id integer NOT NULL,
  update_type_id integer NOT NULL,
  update_file character varying(20) NOT NULL,
  run smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_updates_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_updates_seq OWNED BY cms_updates.update_id;


CREATE TABLE cms_us_states (
    state_id integer NOT NULL,
    abbreviation character varying(3) NOT NULL,
    state_name character varying(50) NOT NULL
);


CREATE TABLE cms_user_role_affiliation (
    user_role_affiliation_id bigint NOT NULL,
    user_id bigint NOT NULL,
    role_id integer NOT NULL
);

CREATE SEQUENCE cms_user_role_affiliation_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_user_role_affiliation_seq OWNED BY cms_user_role_affiliation.user_role_affiliation_id;


CREATE TABLE cms_users (
    user_id bigint NOT NULL,
    user_name character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    email_address character varying(255) NOT NULL,
    is_site_admin smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_users_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_users_seq OWNED BY cms_users.user_id;


CREATE TABLE cms_versions (
  version_id integer NOT NULL,
  module_id integer,
  version character varying(20) NOT NULL,
  finished smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cms_versions_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_versions_seq OWNED BY cms_versions.version_id;

-- Set Primary Key Auto Increment Default Value --

ALTER TABLE cms_ad_campaign_affiliation ALTER COLUMN ad_campaign_affiliation_id SET DEFAULT nextval('cms_ad_campaign_affiliation_seq'::regclass);

ALTER TABLE cms_ad_campaigns ALTER COLUMN ad_campaign_id SET DEFAULT nextval('cms_ad_campaigns_seq'::regclass);

ALTER TABLE cms_ads ALTER COLUMN ad_id SET DEFAULT nextval('cms_ads_seq'::regclass);

ALTER TABLE cms_banned_ip_addresses ALTER COLUMN banned_ip_address_id SET DEFAULT nextval('cms_banned_ip_addresses_seq'::regclass);

ALTER TABLE cms_censored_words ALTER COLUMN censored_word_id SET DEFAULT nextval('cms_censored_words_seq'::regclass);

ALTER TABLE cms_configuration_parameters ALTER COLUMN configuration_parameter_id SET DEFAULT nextval('cms_configuration_parameters_seq'::regclass);

ALTER TABLE cms_errors ALTER COLUMN error_id SET DEFAULT nextval('cms_errors_seq'::regclass);

ALTER TABLE cms_meta_settings ALTER COLUMN meta_setting_id SET DEFAULT nextval('cms_meta_settings_seq'::regclass);

ALTER TABLE cms_modules ALTER COLUMN module_id SET DEFAULT nextval('cms_modules_seq'::regclass);

ALTER TABLE cms_parameter_values ALTER COLUMN parameter_value_id SET DEFAULT nextval('cms_parameter_values_seq'::regclass);

ALTER TABLE cms_permissions ALTER COLUMN permission_id SET DEFAULT nextval('cms_permissions_seq'::regclass);

ALTER TABLE cms_role_permission_affiliation ALTER COLUMN role_permission_affiliation_id SET DEFAULT nextval('cms_role_permission_affiliation_seq'::regclass);

ALTER TABLE cms_roles ALTER COLUMN role_id SET DEFAULT nextval('cms_roles_seq'::regclass);

ALTER TABLE cms_static_pages ALTER COLUMN static_page_id SET DEFAULT nextval('cms_static_pages_seq'::regclass);

ALTER TABLE cms_update_types ALTER COLUMN update_type_id SET DEFAULT nextval('cms_update_types_seq'::regclass);

ALTER TABLE cms_updates ALTER COLUMN update_id SET DEFAULT nextval('cms_updates_seq'::regclass);

ALTER TABLE cms_user_role_affiliation ALTER COLUMN user_role_affiliation_id SET DEFAULT nextval('cms_user_role_affiliation_seq'::regclass);

ALTER TABLE cms_users ALTER COLUMN user_id SET DEFAULT nextval('cms_users_seq'::regclass);

ALTER TABLE cms_versions ALTER COLUMN version_id SET DEFAULT nextval('cms_versions_seq'::regclass);

-- Table Rows --

INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (1, 1, 'theme', NULL, 'light', 1, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (2, NULL, 'version', NULL, '1.0', 1, 1, NULL, 'Framework Version', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (3, NULL, 'environment', NULL, 'production', 2, 1, NULL, 'Environment', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (4, NULL, 'session_name', NULL, 'default', 4, 1, NULL, 'Client Session Name', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (5, NULL, 'session_storage_engine', NULL, 'file', 5, 1, NULL, 'Session Storage Engine', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (6, NULL, 'aws_region', NULL, 'com', 6, 1, NULL, 'AWS Region', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (7, NULL, 'aws_public_key', NULL, NULL, 7, 1, NULL, 'AWS Public Key', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (8, NULL, 'aws_private_key', NULL, NULL, 8, 1, NULL, 'AWS Private Key', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (9, NULL, 'default_module', NULL, 'admin', 9, 1, NULL, 'Default Module', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (10, NULL, 'javascript_minifier', NULL, 'simple', 10, 1, NULL, 'Javascript Minifier', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (11, NULL, 'closure_compiler_path', NULL, NULL, 11, 1, NULL, 'Closure Compiler Path', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (12, NULL, 'enable_javascript', NULL, 1, 12, 4, NULL, 'Enable Javascript', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (13, 1, 'encrypt_urls', NULL, 1, 2, 4, NULL, 'Encrypt URLs', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (14, NULL, 'recaptcha_api_method', NULL, 'https', 13, 1, NULL, 'reCaptcha API Method', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (15, NULL, 'recaptcha_private_key', NULL, NULL, 14, 1, NULL, 'reCaptcha Private Key', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (16, NULL, 'recaptcha_public_key', NULL, NULL, 15, 1, NULL, 'reCaptcha Public Key', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (17, NULL, 'attempts_form_max_attempts', NULL, 5, 16, 2, NULL, 'Limited Attempts Form Max Attempts', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (18, NULL, 'attempts_form_timeout_duration', NULL, 600, 17, 2, NULL, 'Limited Attempts Form Timeout Duration', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (19, null, 'port_numbers_in_urls', NULL, 1, 18, 4, NULL, 'Insert Port Numbers in URLs', 0);

INSERT INTO cms_modules (module_id, module_name, display_name, sort_order, enabled) VALUES (1, 'admin', 'Admin', 1, 1);

INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (1, 'string');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (2, 'integer');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (3, 'float');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (4, 'boolean');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (5, 'array');

INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (1, 'development', 1, 3);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (2, 'production', 2, 3);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (3, 'file', 1, 5);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (4, 'database', 2, 5);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (5, 'simple', 1, 10);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (6, 'uglify-js', 2, 10);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (7, 'closure', 3, 10);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (8, 'http', 1, 14);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (9, 'https', 2, 14);

INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (1, 'AL', 'Alabama');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (2, 'AK', 'Alaska');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (3, 'AZ', 'Arizona');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (4, 'AR', 'Arkansas');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (5, 'CA', 'California');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (6, 'CO', 'Colorado');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (7, 'CT', 'Connecticut');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (8, 'DE', 'Delaware');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (9, 'FL', 'Florida');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (10, 'GA', 'Georgia');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (11, 'HI', 'Hawaii');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (12, 'ID', 'Idaho');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (13, 'IL', 'Illinois');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (14, 'IN', 'Indiana');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (15, 'IA', 'Iowa');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (16, 'KS', 'Kansas');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (17, 'KY', 'Kentucky');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (18, 'LA', 'Louisiana');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (19, 'ME', 'Maine');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (20, 'MD', 'Maryland');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (21, 'MA', 'Massachusetts');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (22, 'MI', 'Michigan');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (23, 'MN', 'Minnesota');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (24, 'MS', 'Mississippi');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (25, 'MO', 'Missouri');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (26, 'MT', 'Montana');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (27, 'NE', 'Nebraska');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (28, 'NV', 'Nevada');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (29, 'NH', 'New Hampshire');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (30, 'NJ', 'New Jersey');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (31, 'NM', 'New Mexico');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (32, 'NY', 'New York');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (33, 'NC', 'North Carolina');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (34, 'ND', 'North Dakota');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (35, 'OH', 'Ohio');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (36, 'OK', 'Oklahoma');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (37, 'OR', 'Oregon');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (38, 'PA', 'Pennsylvania');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (39, 'RI', 'Rhode Island');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (40, 'SC', 'South Carolina');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (41, 'SD', 'South Dakota');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (42, 'TN', 'Tennessee');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (43, 'TX', 'Texas');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (44, 'UT', 'Utah');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (45, 'VT', 'Vermont');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (46, 'VA', 'Virginia');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (47, 'WA', 'Washington');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (48, 'WV', 'West Virginia');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (49, 'WI', 'Wisconsin');
INSERT INTO cms_us_states (state_id, abbreviation, state_name) VALUES (50, 'WY', 'Wyoming');

-- Primary Keys --

ALTER TABLE ONLY cms_ad_campaign_affiliation
    ADD CONSTRAINT caca_ad_campaign_affiliation_id_pk PRIMARY KEY (ad_campaign_affiliation_id);

ALTER TABLE ONLY cms_ad_campaigns
    ADD CONSTRAINT cac_ad_campaign_id_pk PRIMARY KEY (ad_campaign_id);
    
ALTER TABLE ONLY cms_ads
    ADD CONSTRAINT ca_ad_id_pk PRIMARY KEY (ad_id);

ALTER TABLE ONLY cms_banned_ip_addresses
    ADD CONSTRAINT cbia_banned_ip_address_id_pk PRIMARY KEY (banned_ip_address_id);
    
ALTER TABLE ONLY cms_censored_words
    ADD CONSTRAINT ccw_censored_word_id_pk PRIMARY KEY (censored_word_id);

ALTER TABLE ONLY cms_configuration_parameters
    ADD CONSTRAINT ccp_configuration_parameter_id_pk PRIMARY KEY (configuration_parameter_id);

ALTER TABLE ONLY cms_errors
    ADD CONSTRAINT ce_error_id_pk PRIMARY KEY (error_id);

ALTER TABLE ONLY cms_meta_settings
    ADD CONSTRAINT cms_meta_setting_id_pk PRIMARY KEY (meta_setting_id);

ALTER TABLE ONLY cms_modules
    ADD CONSTRAINT cm_module_id_pk PRIMARY KEY (module_id);

ALTER TABLE ONLY cms_parameter_data_types
    ADD CONSTRAINT cpdt_parameter_data_type_id_pk PRIMARY KEY (parameter_data_type_id);

ALTER TABLE ONLY cms_parameter_values
    ADD CONSTRAINT cpv_parameter_value_id_pk PRIMARY KEY (parameter_value_id);

ALTER TABLE ONLY cms_permissions
    ADD CONSTRAINT cp_permission_id_pk PRIMARY KEY (permission_id);

ALTER TABLE ONLY cms_roles
    ADD CONSTRAINT cr_role_id_pk PRIMARY KEY (role_id);

ALTER TABLE ONLY cms_role_permission_affiliation
    ADD CONSTRAINT crpa_role_permission_affiliation_id_pk PRIMARY KEY (role_permission_affiliation_id);

ALTER TABLE ONLY cms_sessions
    ADD CONSTRAINT cs_session_id_pk PRIMARY KEY (session_id);
    
ALTER TABLE ONLY cms_static_pages
    ADD CONSTRAINT csp_static_page_id_pk PRIMARY KEY (static_page_id);

ALTER TABLE ONLY cms_update_types
    ADD CONSTRAINT cut_update_type_id_pk PRIMARY KEY (update_type_id);

ALTER TABLE ONLY cms_updates
    ADD CONSTRAINT cut_update_id_pk PRIMARY KEY (update_id);

ALTER TABLE ONLY cms_us_states
    ADD CONSTRAINT cus_state_id_pk PRIMARY KEY (state_id);

ALTER TABLE ONLY cms_users
    ADD CONSTRAINT cu_user_id_pk PRIMARY KEY (user_id);

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_user_role_affiliation_id_pk PRIMARY KEY (user_role_affiliation_id);
    
ALTER TABLE ONLY cms_versions
    ADD CONSTRAINT cv_version_id_pk PRIMARY KEY (version_id);
    
-- Foreign Keys --
ALTER TABLE ONLY cms_ad_campaign_affiliation
    ADD CONSTRAINT caca_ad_campaign_id_fk FOREIGN KEY (ad_campaign_id) REFERENCES cms_ad_campaigns (ad_campaign_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ONLY cms_ad_campaign_affiliation
    ADD CONSTRAINT caca_ad_id_fk FOREIGN KEY (ad_id) REFERENCES cms_ads (ad_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ONLY cms_ad_campaigns
    ADD CONSTRAINT cac_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ONLY cms_ads
    ADD CONSTRAINT ca_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ONLY cms_parameter_values
    ADD CONSTRAINT cpv_configuration_parameter_id_fk FOREIGN KEY (configuration_parameter_id) REFERENCES cms_configuration_parameters(configuration_parameter_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_roles
    ADD CONSTRAINT cr_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_permissions
    ADD CONSTRAINT cp_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_errors
    ADD CONSTRAINT ce_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_meta_settings
    ADD CONSTRAINT cms_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_configuration_parameters
    ADD CONSTRAINT ccp_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_configuration_parameters
    ADD CONSTRAINT ccp_parameter_data_type_id_fk FOREIGN KEY (parameter_data_type_id) REFERENCES cms_parameter_data_types(parameter_data_type_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_role_permission_affiliation
    ADD CONSTRAINT crpa_permission_id_fk FOREIGN KEY (permission_id) REFERENCES cms_permissions(permission_id) ON UPDATE CASCADE ON DELETE CASCADE;
   
ALTER TABLE ONLY cms_static_pages
    ADD CONSTRAINT csp_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_updates
    ADD CONSTRAINT cu_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE;
    
ALTER TABLE ONLY cms_updates
    ADD CONSTRAINT cu_update_type_id_fk FOREIGN KEY (update_type_id) REFERENCES cms_update_types (update_type_id) ON DELETE CASCADE ON UPDATE CASCADE;
    
ALTER TABLE ONLY cms_updates
    ADD CONSTRAINT cu_version_id_fk FOREIGN KEY (version_id) REFERENCES cms_versions (version_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE ONLY cms_role_permission_affiliation
    ADD CONSTRAINT crpa_role_id_fk FOREIGN KEY (role_id) REFERENCES cms_roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_role_id_fk FOREIGN KEY (role_id) REFERENCES cms_roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_user_id_fk FOREIGN KEY (user_id) REFERENCES cms_users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_versions
    ADD CONSTRAINT cv_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules (module_id) ON DELETE CASCADE ON UPDATE CASCADE;

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;
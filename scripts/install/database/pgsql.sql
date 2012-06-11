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

CREATE SEQUENCE ccp_configuration_parameter_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE ccp_configuration_parameter_id_seq OWNED BY cms_configuration_parameters.configuration_parameter_id;

SELECT pg_catalog.setval('ccp_configuration_parameter_id_seq', 14, true);


CREATE TABLE cms_errors (
    error_id bigint NOT NULL,
    error_code character varying(20),
    error_message text,
    error_file text,
    error_line smallint,
    error_trace text,
    created_time timestamp without time zone NOT NULL,
    module_id integer
);

CREATE SEQUENCE ce_error_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE ce_error_id_seq OWNED BY cms_errors.error_id;


CREATE TABLE cms_meta_settings (
  meta_setting_id integer NOT NULL,
  module_id integer NOT NULL,
  tag_name character varying(30),
  http_equiv character varying(30),
  content text NOT NULL,
  is_active smallint NOT NULL,
  sort_order smallint NOT NULL
);

CREATE SEQUENCE cms_meta_setting_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cms_meta_setting_id_seq OWNED BY cms_meta_settings.meta_setting_id;


CREATE TABLE cms_modules (
    module_id integer NOT NULL,
    module_name character varying(50) NOT NULL,
    display_name character varying(100),
    sort_order smallint,
    enabled smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cm_module_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cm_module_id_seq OWNED BY cms_modules.module_id;

SELECT pg_catalog.setval('cm_module_id_seq', 1, true);


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

CREATE SEQUENCE cpv_parameter_value_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cpv_parameter_value_id_seq OWNED BY cms_parameter_values.parameter_value_id;

SELECT pg_catalog.setval('cpv_parameter_value_id_seq', 8, true);


CREATE TABLE cms_permissions (
    permission_id integer NOT NULL,
    permission_name character varying(100) NOT NULL,
    display_name character varying(100) NOT NULL,
    description character varying(255),
    module_id integer NOT NULL,
    smallint integer NOT NULL
);

CREATE SEQUENCE cp_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;
    
ALTER SEQUENCE cp_permission_id_seq OWNED BY cms_permissions.permission_id;


CREATE TABLE cms_role_permission_affiliation (
    role_permission_affiliation_id integer NOT NULL,
    role_id integer NOT NULL,
    permission_id integer NOT NULL,
    can smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE crpa_role_permission_affiliation_id_
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE crpa_role_permission_affiliation_id_ OWNED BY cms_role_permission_affiliation.role_permission_affiliation_id;


CREATE TABLE cms_roles (
    role_id integer NOT NULL,
    display_name character varying(100) NOT NULL,
    module_id integer NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE cr_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cr_role_id_seq OWNED BY cms_roles.role_id;


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

CREATE SEQUENCE csp_static_page_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE csp_static_page_id_seq OWNED BY cms_static_pages.static_page_id;


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

CREATE SEQUENCE cura_user_role_affiliation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cura_user_role_affiliation_id_seq OWNED BY cms_user_role_affiliation.user_role_affiliation_id;


CREATE TABLE cms_users (
    user_id bigint NOT NULL,
    user_name character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    email_address character varying(255) NOT NULL,
    is_site_admin smallint DEFAULT 0 NOT NULL
);

CREATE SEQUENCE cu_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cu_user_id_seq OWNED BY cms_users.user_id;


-- Set Primary Key Auto Increment Default Value --

ALTER TABLE cms_configuration_parameters ALTER COLUMN configuration_parameter_id SET DEFAULT nextval('ccp_configuration_parameter_id_seq'::regclass);

ALTER TABLE cms_errors ALTER COLUMN error_id SET DEFAULT nextval('ce_error_id_seq'::regclass);

ALTER TABLE cms_meta_settings ALTER COLUMN meta_setting_id SET DEFAULT nextval('cms_meta_setting_id_seq'::regclass);

ALTER TABLE cms_modules ALTER COLUMN module_id SET DEFAULT nextval('cm_module_id_seq'::regclass);

ALTER TABLE cms_parameter_values ALTER COLUMN parameter_value_id SET DEFAULT nextval('cpv_parameter_value_id_seq'::regclass);

ALTER TABLE cms_permissions ALTER COLUMN permission_id SET DEFAULT nextval('cp_permission_id_seq'::regclass);

ALTER TABLE cms_role_permission_affiliation ALTER COLUMN role_permission_affiliation_id SET DEFAULT nextval('crpa_role_permission_affiliation_id_'::regclass);

ALTER TABLE cms_roles ALTER COLUMN role_id SET DEFAULT nextval('cr_role_id_seq'::regclass);

ALTER TABLE cms_static_pages ALTER COLUMN static_page_id SET DEFAULT nextval('csp_static_page_id_seq'::regclass);

ALTER TABLE cms_user_role_affiliation ALTER COLUMN user_role_affiliation_id SET DEFAULT nextval('cura_user_role_affiliation_id_seq'::regclass);

ALTER TABLE cms_users ALTER COLUMN user_id SET DEFAULT nextval('cu_user_id_seq'::regclass);

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

ALTER TABLE ONLY cms_us_states
    ADD CONSTRAINT cus_state_id_pk PRIMARY KEY (state_id);

ALTER TABLE ONLY cms_users
    ADD CONSTRAINT cu_user_id_pk PRIMARY KEY (user_id);

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_user_role_affiliation_id_pk PRIMARY KEY (user_role_affiliation_id);
    
-- Foreign Keys --

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

ALTER TABLE ONLY cms_role_permission_affiliation
    ADD CONSTRAINT crpa_role_id_fk FOREIGN KEY (role_id) REFERENCES cms_roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_role_id_fk FOREIGN KEY (role_id) REFERENCES cms_roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_user_id_fk FOREIGN KEY (user_id) REFERENCES cms_users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;
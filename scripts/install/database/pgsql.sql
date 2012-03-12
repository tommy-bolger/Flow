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

SELECT pg_catalog.setval('ccp_configuration_parameter_id_seq', 29, true);


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

SELECT pg_catalog.setval('cm_module_id_seq', 3, true);


CREATE TABLE cms_pages (
    page_id integer NOT NULL,
    page_name character varying(25) NOT NULL,
    page_location text NOT NULL
);

CREATE SEQUENCE cp_page_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE cp_page_id_seq OWNED BY cms_pages.page_id;


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

SELECT pg_catalog.setval('cpv_parameter_value_id_seq', 11, true);


CREATE TABLE cms_permissions (
    permission_id integer NOT NULL,
    permission_name character varying(100) NOT NULL,
    display_name character varying(100) NOT NULL,
    description character varying(255),
    module_id integer NOT NULL,
    sort_order smallint NOT NULL
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
    sort_order smallint NOT NULL,
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


CREATE TABLE resume_code_example_skills (
    code_example_skill_id integer NOT NULL,
    code_example_id integer NOT NULL,
    skill_id integer NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE rces_code_example_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rces_code_example_skill_id_seq OWNED BY resume_code_example_skills.code_example_skill_id;


CREATE TABLE resume_code_examples (
    code_example_id integer NOT NULL,
    source_file_name character varying(100),
    portfolio_project_id integer,
    description text,
    sort_order smallint NOT NULL,
    code_example_name character varying(255) NOT NULL,
    purpose text NOT NULL,
    work_history_id integer
);

CREATE SEQUENCE rce_code_example_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rce_code_example_id_seq OWNED BY resume_code_examples.code_example_id;


CREATE TABLE resume_degree_levels (
    degree_level_id smallint NOT NULL,
    abbreviation character varying(10) NOT NULL,
    degree_level_name character varying(50) NOT NULL
);


CREATE TABLE resume_education (
    education_id integer NOT NULL,
    institution_name character varying(255) NOT NULL,
    institution_city character varying(100) NOT NULL,
    state_id smallint NOT NULL,
    degree_level_id smallint NOT NULL,
    degree_name character varying(255) NOT NULL,
    date_graduated date NOT NULL,
    cumulative_gpa numeric(3,2) NOT NULL,
    sort_order smallint
);

CREATE SEQUENCE re_education_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE re_education_id_seq OWNED BY resume_education.education_id;


CREATE TABLE resume_general_information (
    general_information_id smallint NOT NULL,
    first_name character varying(100) NOT NULL,
    last_name character varying(100) NOT NULL,
    address character varying(100),
    city character varying(100) NOT NULL,
    state_id integer NOT NULL,
    phone_number character varying(15),
    photo character varying(255),
    email_address character varying(100) NOT NULL,
    resume_pdf_name character varying(100),
    resume_word_name character varying(100),
    summary text,
    specialty character varying(255) NOT NULL
);


CREATE TABLE resume_portfolio_project_images (
    portfolio_project_image_id integer NOT NULL,
    portfolio_project_id integer NOT NULL,
    image_name character varying(255),
    thumbnail_name character varying(255),
    sort_order smallint NOT NULL,
    title character varying(255) NOT NULL,
    description text
);

CREATE SEQUENCE rppi_portfolio_project_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rppi_portfolio_project_image_id_seq OWNED BY resume_portfolio_project_images.portfolio_project_image_id;


CREATE TABLE resume_portfolio_project_skills (
    portfolio_project_skill_id integer NOT NULL,
    portfolio_project_id integer NOT NULL,
    skill_id integer NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE rpps_portfolio_project_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rpps_portfolio_project_skill_id_seq OWNED BY resume_portfolio_project_skills.portfolio_project_skill_id;


CREATE TABLE resume_portfolio_projects (
    portfolio_project_id integer NOT NULL,
    project_name character varying(255) NOT NULL,
    description text NOT NULL,
    involvement_description text,
    sort_order smallint NOT NULL,
    site_url text,
    work_history_id integer
);

CREATE SEQUENCE rpp_portfolio_project_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rpp_portfolio_project_id_seq OWNED BY resume_portfolio_projects.portfolio_project_id;


CREATE TABLE resume_proficiency_levels (
    proficiency_level_id smallint NOT NULL,
    proficiency_level_name character varying(100) NOT NULL
);


CREATE TABLE resume_skill_categories (
    skill_category_id integer NOT NULL,
    skill_category_name character varying(100) NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE rsc_skill_category_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rsc_skill_category_id_seq OWNED BY resume_skill_categories.skill_category_id;


CREATE TABLE resume_skills (
    skill_id integer NOT NULL,
    skill_name character varying(100) NOT NULL,
    skill_category_id integer NOT NULL,
    years_proficient smallint NOT NULL,
    proficiency_level_id smallint NOT NULL,
    sort_order smallint NOT NULL
);


CREATE SEQUENCE rs_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rs_skill_id_seq OWNED BY resume_skills.skill_id;


CREATE TABLE resume_work_history (
    work_history_id integer NOT NULL,
    organization_name character varying(255) NOT NULL,
    job_title character varying(100) NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE rwh_work_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rwh_work_history_id_seq OWNED BY resume_work_history.work_history_id;


CREATE TABLE resume_work_history_durations (
    work_history_duration_id integer NOT NULL,
    start_date date NOT NULL,
    sort_order smallint NOT NULL,
    work_history_id integer NOT NULL,
    end_date date
);

CREATE SEQUENCE rwhd_work_history_duration_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rwhd_work_history_duration_id_seq OWNED BY resume_work_history_durations.work_history_duration_id;


CREATE TABLE resume_work_history_tasks (
    work_history_task_id integer NOT NULL,
    work_history_id integer NOT NULL,
    description text NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE rwht_work_history_task_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE rwht_work_history_task_id_seq OWNED BY resume_work_history_tasks.work_history_task_id;


-- Set Primary Key Auto Increment Default Value --

ALTER TABLE cms_configuration_parameters ALTER COLUMN configuration_parameter_id SET DEFAULT nextval('ccp_configuration_parameter_id_seq'::regclass);

ALTER TABLE cms_errors ALTER COLUMN error_id SET DEFAULT nextval('ce_error_id_seq'::regclass);

ALTER TABLE cms_modules ALTER COLUMN module_id SET DEFAULT nextval('cm_module_id_seq'::regclass);

ALTER TABLE cms_pages ALTER COLUMN page_id SET DEFAULT nextval('cp_page_id_seq'::regclass);

ALTER TABLE cms_parameter_values ALTER COLUMN parameter_value_id SET DEFAULT nextval('cpv_parameter_value_id_seq'::regclass);

ALTER TABLE cms_permissions ALTER COLUMN permission_id SET DEFAULT nextval('cp_permission_id_seq'::regclass);

ALTER TABLE cms_role_permission_affiliation ALTER COLUMN role_permission_affiliation_id SET DEFAULT nextval('crpa_role_permission_affiliation_id_'::regclass);

ALTER TABLE cms_roles ALTER COLUMN role_id SET DEFAULT nextval('cr_role_id_seq'::regclass);

ALTER TABLE cms_user_role_affiliation ALTER COLUMN user_role_affiliation_id SET DEFAULT nextval('cura_user_role_affiliation_id_seq'::regclass);

ALTER TABLE cms_users ALTER COLUMN user_id SET DEFAULT nextval('cu_user_id_seq'::regclass);

ALTER TABLE resume_code_example_skills ALTER COLUMN code_example_skill_id SET DEFAULT nextval('rces_code_example_skill_id_seq'::regclass);

ALTER TABLE resume_code_examples ALTER COLUMN code_example_id SET DEFAULT nextval('rce_code_example_id_seq'::regclass);

ALTER TABLE resume_education ALTER COLUMN education_id SET DEFAULT nextval('re_education_id_seq'::regclass);

ALTER TABLE resume_portfolio_project_images ALTER COLUMN portfolio_project_image_id SET DEFAULT nextval('rppi_portfolio_project_image_id_seq'::regclass);

ALTER TABLE resume_portfolio_project_skills ALTER COLUMN portfolio_project_skill_id SET DEFAULT nextval('rpps_portfolio_project_skill_id_seq'::regclass);

ALTER TABLE resume_portfolio_projects ALTER COLUMN portfolio_project_id SET DEFAULT nextval('rpp_portfolio_project_id_seq'::regclass);

ALTER TABLE resume_skill_categories ALTER COLUMN skill_category_id SET DEFAULT nextval('rsc_skill_category_id_seq'::regclass);

ALTER TABLE resume_skills ALTER COLUMN skill_id SET DEFAULT nextval('rs_skill_id_seq'::regclass);

ALTER TABLE resume_work_history ALTER COLUMN work_history_id SET DEFAULT nextval('rwh_work_history_id_seq'::regclass);

ALTER TABLE resume_work_history_durations ALTER COLUMN work_history_duration_id SET DEFAULT nextval('rwhd_work_history_duration_id_seq'::regclass);

ALTER TABLE resume_work_history_tasks ALTER COLUMN work_history_task_id SET DEFAULT nextval('rwht_work_history_task_id_seq'::regclass);

-- Table Rows --

INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (16, NULL, 'aws_region', NULL, 'com', 6, 1, NULL, 'AWS Region', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (17, NULL, 'aws_public_key', NULL, NULL, 7, 1, NULL, 'AWS Public Key', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (18, NULL, 'aws_private_key', NULL, NULL, 8, 1, NULL, 'AWS Private Key', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (22, NULL, 'closure_compiler_path', NULL, NULL, 11, 1, NULL, 'Closure Compiler Path', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (24, NULL, 'enable_javascript', NULL, '1', 12, 4, NULL, 'Enable Javascript', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (1, 2, 'theme', NULL, 'default', 1, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (2, 2, 'code_example_file_extensions', NULL, 'php,html,aspx,asp,js,css,htc,inc', 2, 5, NULL, 'Code Example File Extensions', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (3, 1, 'theme', NULL, 'light', 1, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (11, NULL, 'session_name', NULL, 'default', 4, 1, NULL, 'Client Session Name', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (12, NULL, 'session_storage_engine', NULL, 'file', 5, 1, NULL, 'Session Storage Engine', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (19, NULL, 'default_module', NULL, 'admin', 9, 1, NULL, 'Default Module', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (21, NULL, 'javascript_minifier', NULL, 'simple', 10, 1, NULL, 'Javascript Minifier', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (5, NULL, 'environment', NULL, 'production', 2, 1, NULL, 'Environment', 1);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (4, NULL, 'version', NULL, '1.0', 1, 1, NULL, 'Framework Version', 0);
INSERT INTO cms_configuration_parameters (configuration_parameter_id, module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (25, 1, 'encrypt_urls', NULL, '1', 2, 4, NULL, 'Encrypt URLs', 0);

INSERT INTO cms_modules (module_id, module_name, display_name, sort_order, enabled) VALUES (1, 'admin', 'Admin', 1, 1);
INSERT INTO cms_modules (module_id, module_name, display_name, sort_order, enabled) VALUES (2, 'resume', 'Resume', 3, 1);
INSERT INTO cms_modules (module_id, module_name, display_name, sort_order, enabled) VALUES (3, 'blog', 'Blog', 2, 1);

INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (1, 'string');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (2, 'integer');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (3, 'float');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (4, 'boolean');
INSERT INTO cms_parameter_data_types (parameter_data_type_id, data_type) VALUES (5, 'array');

INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (1, 'development', 1, 5);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (2, 'production', 2, 5);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (3, 'file', 1, 12);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (4, 'database', 2, 12);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (5, 'admin', 1, 19);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (8, 'blog', 3, 19);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (9, 'simple', 1, 21);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (10, 'uglify-js', 2, 21);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (11, 'closure', 3, 21);
INSERT INTO cms_parameter_values (parameter_value_id, parameter_value, sort_order, configuration_parameter_id) VALUES (6, 'resume', 2, 19);

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

INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (1, 'A.A.', 'Associate of Arts');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (2, 'A.S.', 'Associate of Science');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (3, 'AAS', 'Associate of Applied Science');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (4, 'B.A.', 'Bachelor of Arts');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (5, 'B.S.', 'Bachelor of Science');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (6, 'BFA', 'Bachelor of Fine Arts');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (7, 'M.A.', 'Master of Arts');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (8, 'M.S.', 'Master of Science');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (9, 'MBA', 'Master of Business Administration');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (10, 'MFA', 'Master of Fine Arts');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (11, 'Ph.D.', 'Doctor of Philosophy');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (12, 'J.D.', 'Juris Doctor');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (13, 'M.D.', 'Doctor of Medicine');
INSERT INTO resume_degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (14, 'DDS', 'Doctor of Dental Surgery');

INSERT INTO resume_proficiency_levels (proficiency_level_id, proficiency_level_name) VALUES (1, 'Beginner');
INSERT INTO resume_proficiency_levels (proficiency_level_id, proficiency_level_name) VALUES (2, 'Intermediate');
INSERT INTO resume_proficiency_levels (proficiency_level_id, proficiency_level_name) VALUES (3, 'Advanced');

-- Primary Keys --

ALTER TABLE ONLY resume_code_examples
    ADD CONSTRAINT rce_code_example_id_pk PRIMARY KEY (code_example_id);

ALTER TABLE ONLY resume_code_example_skills
    ADD CONSTRAINT rces_code_example_skill_id_pk PRIMARY KEY (code_example_skill_id);

ALTER TABLE ONLY cms_configuration_parameters
    ADD CONSTRAINT ccp_configuration_parameter_id_pk PRIMARY KEY (configuration_parameter_id);

ALTER TABLE ONLY resume_degree_levels
    ADD CONSTRAINT rdl_degree_level_id_pk PRIMARY KEY (degree_level_id);

ALTER TABLE ONLY resume_education
    ADD CONSTRAINT re_education_id_pk PRIMARY KEY (education_id);

ALTER TABLE ONLY cms_errors
    ADD CONSTRAINT ce_error_id_pk PRIMARY KEY (error_id);

ALTER TABLE ONLY resume_general_information
    ADD CONSTRAINT rgi_general_information_id_pk PRIMARY KEY (general_information_id);

ALTER TABLE ONLY cms_modules
    ADD CONSTRAINT cm_module_id_pk PRIMARY KEY (module_id);

ALTER TABLE ONLY cms_pages
    ADD CONSTRAINT cp_pages_pk PRIMARY KEY (page_id);

ALTER TABLE ONLY cms_parameter_data_types
    ADD CONSTRAINT cpdt_parameter_data_type_id_pk PRIMARY KEY (parameter_data_type_id);

ALTER TABLE ONLY cms_parameter_values
    ADD CONSTRAINT cpv_parameter_value_id_pk PRIMARY KEY (parameter_value_id);

ALTER TABLE ONLY cms_permissions
    ADD CONSTRAINT cp_permission_id_pk PRIMARY KEY (permission_id);

ALTER TABLE ONLY resume_portfolio_projects
    ADD CONSTRAINT rpp_portfolio_project_id_pk PRIMARY KEY (portfolio_project_id);

ALTER TABLE ONLY resume_portfolio_project_images
    ADD CONSTRAINT rppi_portfolio_project_image_id_pk PRIMARY KEY (portfolio_project_image_id);

ALTER TABLE ONLY resume_portfolio_project_skills
    ADD CONSTRAINT rpps_portfolio_project_skill_id_pk PRIMARY KEY (portfolio_project_skill_id);

ALTER TABLE ONLY resume_proficiency_levels
    ADD CONSTRAINT rpl_proficiency_level_id_pk PRIMARY KEY (proficiency_level_id);

ALTER TABLE ONLY cms_roles
    ADD CONSTRAINT cr_role_id_pk PRIMARY KEY (role_id);

ALTER TABLE ONLY cms_role_permission_affiliation
    ADD CONSTRAINT crpa_role_permission_affiliation_id_pk PRIMARY KEY (role_permission_affiliation_id);

ALTER TABLE ONLY cms_sessions
    ADD CONSTRAINT cs_session_id_pk PRIMARY KEY (session_id);

ALTER TABLE ONLY resume_skill_categories
    ADD CONSTRAINT rsc_skill_category_id_pk PRIMARY KEY (skill_category_id);

ALTER TABLE ONLY resume_skills
    ADD CONSTRAINT rs_skill_id_pk PRIMARY KEY (skill_id);

ALTER TABLE ONLY cms_us_states
    ADD CONSTRAINT cus_state_id_pk PRIMARY KEY (state_id);

ALTER TABLE ONLY cms_users
    ADD CONSTRAINT cu_user_id_pk PRIMARY KEY (user_id);

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_user_role_affiliation_id_pk PRIMARY KEY (user_role_affiliation_id);

ALTER TABLE ONLY resume_work_history_durations
    ADD CONSTRAINT rwhd_work_history_duration_id_pk PRIMARY KEY (work_history_duration_id);

ALTER TABLE ONLY resume_work_history
    ADD CONSTRAINT rwh_work_history_id_pk PRIMARY KEY (work_history_id);

ALTER TABLE ONLY resume_work_history_tasks
    ADD CONSTRAINT rwht_work_history_task_id_pk PRIMARY KEY (work_history_task_id);
    
-- Foreign Keys --

ALTER TABLE ONLY resume_code_example_skills
    ADD CONSTRAINT rces_code_example_id_fk FOREIGN KEY (code_example_id) REFERENCES resume_code_examples(code_example_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_parameter_values
    ADD CONSTRAINT cpv_configuration_parameter_id_fk FOREIGN KEY (configuration_parameter_id) REFERENCES cms_configuration_parameters(configuration_parameter_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_education
    ADD CONSTRAINT re_degree_level_id_fk FOREIGN KEY (degree_level_id) REFERENCES resume_degree_levels(degree_level_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_roles
    ADD CONSTRAINT cr_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_permissions
    ADD CONSTRAINT cp_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_errors
    ADD CONSTRAINT ce_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_configuration_parameters
    ADD CONSTRAINT ccp_module_id_fk FOREIGN KEY (module_id) REFERENCES cms_modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_configuration_parameters
    ADD CONSTRAINT ccp_parameter_data_type_id_fk FOREIGN KEY (parameter_data_type_id) REFERENCES cms_parameter_data_types(parameter_data_type_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_role_permission_affiliation
    ADD CONSTRAINT crpa_permission_id_fk FOREIGN KEY (permission_id) REFERENCES cms_permissions(permission_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_portfolio_project_skills
    ADD CONSTRAINT rpps_portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_portfolio_project_images
    ADD CONSTRAINT rppi_portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_code_examples
    ADD CONSTRAINT rce_portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_skills
    ADD CONSTRAINT rs_proficiency_level_id_fk FOREIGN KEY (proficiency_level_id) REFERENCES resume_proficiency_levels(proficiency_level_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_role_permission_affiliation
    ADD CONSTRAINT crpa_role_id_fk FOREIGN KEY (role_id) REFERENCES cms_roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_role_id_fk FOREIGN KEY (role_id) REFERENCES cms_roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_skills
    ADD CONSTRAINT rs_skill_category_id_fk FOREIGN KEY (skill_category_id) REFERENCES resume_skill_categories(skill_category_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_portfolio_project_skills
    ADD CONSTRAINT rpps_skill_id_fk FOREIGN KEY (skill_id) REFERENCES resume_skills(skill_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_code_example_skills
    ADD CONSTRAINT rces_skill_id_fk FOREIGN KEY (skill_id) REFERENCES resume_skills(skill_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_general_information
    ADD CONSTRAINT rgi_state_id_fk FOREIGN KEY (state_id) REFERENCES cms_us_states(state_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_education
    ADD CONSTRAINT re_state_id_fk FOREIGN KEY (state_id) REFERENCES cms_us_states(state_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY cms_user_role_affiliation
    ADD CONSTRAINT cura_user_id_fk FOREIGN KEY (user_id) REFERENCES cms_users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;
    
ALTER TABLE ONLY resume_work_history_tasks
    ADD CONSTRAINT rwht_work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES resume_work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_work_history_durations
    ADD CONSTRAINT rwhd_work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES resume_work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_portfolio_projects
    ADD CONSTRAINT rpp_work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES resume_work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_code_examples
    ADD CONSTRAINT rce_work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES resume_work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;
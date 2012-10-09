<?php
    db()->insert('cms_modules', array(
        'module_name' => 'resume',
        'display_name' => 'Resume',
        'sort_order' => $sort_order,
        'enabled' => 1
    ));
    
    $module_id = db()->lastInsertId('cms_modules_seq');
?>
SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', null, '1.0', 1, 1, null, 'Version', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'theme', NULL, 'default', 2, 1, NULL, 'Theme', 0);
INSERT INTO cms_configuration_parameters (module_id, parameter_name, value, default_value, sort_order, parameter_data_type_id, description, display_name, has_value_list) VALUES (<?php echo $module_id; ?>, 'code_example_file_extensions', NULL, 'php,html,aspx,asp,js,css,htc,inc', 3, 5, NULL, 'Code Example File Extensions', 0);

-- Create Tables and Sequences --

CREATE TABLE resume_code_example_skills (
    code_example_skill_id integer NOT NULL,
    code_example_id integer NOT NULL,
    skill_id integer NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE resume_code_example_skills_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_code_example_skills_seq OWNED BY resume_code_example_skills.code_example_skill_id;


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

CREATE SEQUENCE resume_code_examples_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_code_examples_seq OWNED BY resume_code_examples.code_example_id;


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

CREATE SEQUENCE resume_education_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_education_seq OWNED BY resume_education.education_id;


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

CREATE SEQUENCE resume_portfolio_project_images_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_portfolio_project_images_seq OWNED BY resume_portfolio_project_images.portfolio_project_image_id;


CREATE TABLE resume_portfolio_project_skills (
    portfolio_project_skill_id integer NOT NULL,
    portfolio_project_id integer NOT NULL,
    skill_id integer NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE resume_portfolio_project_skills_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_portfolio_project_skills_seq OWNED BY resume_portfolio_project_skills.portfolio_project_skill_id;


CREATE TABLE resume_portfolio_projects (
    portfolio_project_id integer NOT NULL,
    project_name character varying(255) NOT NULL,
    description text NOT NULL,
    involvement_description text,
    sort_order smallint NOT NULL,
    site_url text,
    work_history_id integer
);

CREATE SEQUENCE resume_portfolio_projects_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_portfolio_projects_seq OWNED BY resume_portfolio_projects.portfolio_project_id;


CREATE TABLE resume_proficiency_levels (
    proficiency_level_id smallint NOT NULL,
    proficiency_level_name character varying(100) NOT NULL
);


CREATE TABLE resume_skill_categories (
    skill_category_id integer NOT NULL,
    skill_category_name character varying(100) NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE resume_skill_categories_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_skill_categories_seq OWNED BY resume_skill_categories.skill_category_id;


CREATE TABLE resume_skills (
    skill_id integer NOT NULL,
    skill_name character varying(100) NOT NULL,
    skill_category_id integer NOT NULL,
    years_proficient smallint NOT NULL,
    proficiency_level_id smallint NOT NULL,
    sort_order smallint NOT NULL
);


CREATE SEQUENCE resume_skills_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_skills_seq OWNED BY resume_skills.skill_id;


CREATE TABLE resume_work_history (
    work_history_id integer NOT NULL,
    organization_name character varying(255) NOT NULL,
    job_title character varying(100) NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE resume_work_history_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_work_history_seq OWNED BY resume_work_history.work_history_id;


CREATE TABLE resume_work_history_durations (
    work_history_duration_id integer NOT NULL,
    start_date date NOT NULL,
    sort_order smallint NOT NULL,
    work_history_id integer NOT NULL,
    end_date date
);

CREATE SEQUENCE resume_work_history_durations_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_work_history_durations_seq OWNED BY resume_work_history_durations.work_history_duration_id;


CREATE TABLE resume_work_history_tasks (
    work_history_task_id integer NOT NULL,
    work_history_id integer NOT NULL,
    description text NOT NULL,
    sort_order smallint NOT NULL
);

CREATE SEQUENCE resume_work_history_tasks_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

ALTER SEQUENCE resume_work_history_tasks_seq OWNED BY resume_work_history_tasks.work_history_task_id;


-- Set Primary Key Auto Increment Default Value --

ALTER TABLE resume_code_example_skills ALTER COLUMN code_example_skill_id SET DEFAULT nextval('resume_code_example_skills_seq'::regclass);

ALTER TABLE resume_code_examples ALTER COLUMN code_example_id SET DEFAULT nextval('resume_code_examples_seq'::regclass);

ALTER TABLE resume_education ALTER COLUMN education_id SET DEFAULT nextval('resume_education_seq'::regclass);

ALTER TABLE resume_portfolio_project_images ALTER COLUMN portfolio_project_image_id SET DEFAULT nextval('resume_portfolio_project_images_seq'::regclass);

ALTER TABLE resume_portfolio_project_skills ALTER COLUMN portfolio_project_skill_id SET DEFAULT nextval('resume_portfolio_project_skills_seq'::regclass);

ALTER TABLE resume_portfolio_projects ALTER COLUMN portfolio_project_id SET DEFAULT nextval('resume_portfolio_projects_seq'::regclass);

ALTER TABLE resume_skill_categories ALTER COLUMN skill_category_id SET DEFAULT nextval('resume_skill_categories_seq'::regclass);

ALTER TABLE resume_skills ALTER COLUMN skill_id SET DEFAULT nextval('resume_skills_seq'::regclass);

ALTER TABLE resume_work_history ALTER COLUMN work_history_id SET DEFAULT nextval('resume_work_history_seq'::regclass);

ALTER TABLE resume_work_history_durations ALTER COLUMN work_history_duration_id SET DEFAULT nextval('resume_work_history_duration_seq'::regclass);

ALTER TABLE resume_work_history_tasks ALTER COLUMN work_history_task_id SET DEFAULT nextval('resume_work_history_tasks_seq'::regclass);


-- Table Rows --

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

ALTER TABLE ONLY resume_degree_levels
    ADD CONSTRAINT rdl_degree_level_id_pk PRIMARY KEY (degree_level_id);

ALTER TABLE ONLY resume_education
    ADD CONSTRAINT re_education_id_pk PRIMARY KEY (education_id);
    
ALTER TABLE ONLY resume_general_information
    ADD CONSTRAINT rgi_general_information_id_pk PRIMARY KEY (general_information_id);
    
ALTER TABLE ONLY resume_portfolio_projects
    ADD CONSTRAINT rpp_portfolio_project_id_pk PRIMARY KEY (portfolio_project_id);

ALTER TABLE ONLY resume_portfolio_project_images
    ADD CONSTRAINT rppi_portfolio_project_image_id_pk PRIMARY KEY (portfolio_project_image_id);

ALTER TABLE ONLY resume_portfolio_project_skills
    ADD CONSTRAINT rpps_portfolio_project_skill_id_pk PRIMARY KEY (portfolio_project_skill_id);

ALTER TABLE ONLY resume_proficiency_levels
    ADD CONSTRAINT rpl_proficiency_level_id_pk PRIMARY KEY (proficiency_level_id);
    
ALTER TABLE ONLY resume_skill_categories
    ADD CONSTRAINT rsc_skill_category_id_pk PRIMARY KEY (skill_category_id);

ALTER TABLE ONLY resume_skills
    ADD CONSTRAINT rs_skill_id_pk PRIMARY KEY (skill_id);
    
ALTER TABLE ONLY resume_work_history_durations
    ADD CONSTRAINT rwhd_work_history_duration_id_pk PRIMARY KEY (work_history_duration_id);

ALTER TABLE ONLY resume_work_history
    ADD CONSTRAINT rwh_work_history_id_pk PRIMARY KEY (work_history_id);

ALTER TABLE ONLY resume_work_history_tasks
    ADD CONSTRAINT rwht_work_history_task_id_pk PRIMARY KEY (work_history_task_id);
    

-- Foreign Keys --

ALTER TABLE ONLY resume_code_example_skills
    ADD CONSTRAINT rces_code_example_id_fk FOREIGN KEY (code_example_id) REFERENCES resume_code_examples(code_example_id) ON UPDATE CASCADE ON DELETE CASCADE;
    
ALTER TABLE ONLY resume_education
    ADD CONSTRAINT re_degree_level_id_fk FOREIGN KEY (degree_level_id) REFERENCES resume_degree_levels(degree_level_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_portfolio_project_skills
    ADD CONSTRAINT rpps_portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_portfolio_project_images
    ADD CONSTRAINT rppi_portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_code_examples
    ADD CONSTRAINT rce_portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES resume_portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;

ALTER TABLE ONLY resume_skills
    ADD CONSTRAINT rs_proficiency_level_id_fk FOREIGN KEY (proficiency_level_id) REFERENCES resume_proficiency_levels(proficiency_level_id) ON UPDATE CASCADE ON DELETE CASCADE;

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
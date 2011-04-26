--
-- PostgreSQL database dump
--

-- Started on 2011-04-17 20:23:09

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- TOC entry 6 (class 2615 OID 17993)
-- Name: online_resume; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA online_resume;


SET search_path = online_resume, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1607 (class 1259 OID 24724)
-- Dependencies: 6
-- Name: code_example_skills; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE code_example_skills (
    code_example_skill_id integer NOT NULL,
    code_example_id integer NOT NULL,
    skill_id integer NOT NULL,
    sort_order smallint NOT NULL
);


--
-- TOC entry 1606 (class 1259 OID 24722)
-- Dependencies: 1607 6
-- Name: code_example_skills_code_example_skill_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE code_example_skills_code_example_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2004 (class 0 OID 0)
-- Dependencies: 1606
-- Name: code_example_skills_code_example_skill_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE code_example_skills_code_example_skill_id_seq OWNED BY code_example_skills.code_example_skill_id;


--
-- TOC entry 2005 (class 0 OID 0)
-- Dependencies: 1606
-- Name: code_example_skills_code_example_skill_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('code_example_skills_code_example_skill_id_seq', 6, true);


--
-- TOC entry 1605 (class 1259 OID 24702)
-- Dependencies: 6
-- Name: code_examples; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE code_examples (
    code_example_id integer NOT NULL,
    source_file_name character varying(100),
    portfolio_project_id integer,
    description text,
    sort_order smallint NOT NULL,
    code_example_name character varying(255) NOT NULL,
    purpose text NOT NULL,
    work_history_id integer
);


--
-- TOC entry 1604 (class 1259 OID 24700)
-- Dependencies: 6 1605
-- Name: code_examples_code_example_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE code_examples_code_example_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2006 (class 0 OID 0)
-- Dependencies: 1604
-- Name: code_examples_code_example_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE code_examples_code_example_id_seq OWNED BY code_examples.code_example_id;


--
-- TOC entry 2007 (class 0 OID 0)
-- Dependencies: 1604
-- Name: code_examples_code_example_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('code_examples_code_example_id_seq', 3, true);


--
-- TOC entry 1589 (class 1259 OID 18105)
-- Dependencies: 6
-- Name: degree_levels; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE degree_levels (
    degree_level_id smallint NOT NULL,
    abbreviation character varying(10) NOT NULL,
    degree_level_name character varying(50) NOT NULL
);


--
-- TOC entry 1581 (class 1259 OID 18004)
-- Dependencies: 6
-- Name: education; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE education (
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


--
-- TOC entry 1580 (class 1259 OID 18002)
-- Dependencies: 6 1581
-- Name: education_education_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE education_education_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2008 (class 0 OID 0)
-- Dependencies: 1580
-- Name: education_education_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE education_education_id_seq OWNED BY education.education_id;


--
-- TOC entry 2009 (class 0 OID 0)
-- Dependencies: 1580
-- Name: education_education_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('education_education_id_seq', 11, true);


--
-- TOC entry 1579 (class 1259 OID 17994)
-- Dependencies: 6
-- Name: general_information; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE general_information (
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


--
-- TOC entry 1603 (class 1259 OID 24681)
-- Dependencies: 6
-- Name: portfolio_project_images; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE portfolio_project_images (
    portfolio_project_image_id integer NOT NULL,
    portfolio_project_id integer NOT NULL,
    image_name character varying(255),
    thumbnail_name character varying(255),
    sort_order smallint NOT NULL,
    title character varying(255) NOT NULL,
    description text
);


--
-- TOC entry 1602 (class 1259 OID 24679)
-- Dependencies: 6 1603
-- Name: portfolio_project_images_portfolio_project_image_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE portfolio_project_images_portfolio_project_image_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2010 (class 0 OID 0)
-- Dependencies: 1602
-- Name: portfolio_project_images_portfolio_project_image_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE portfolio_project_images_portfolio_project_image_id_seq OWNED BY portfolio_project_images.portfolio_project_image_id;


--
-- TOC entry 2011 (class 0 OID 0)
-- Dependencies: 1602
-- Name: portfolio_project_images_portfolio_project_image_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('portfolio_project_images_portfolio_project_image_id_seq', 14, true);


--
-- TOC entry 1601 (class 1259 OID 24649)
-- Dependencies: 6
-- Name: portfolio_project_skills; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE portfolio_project_skills (
    portfolio_project_skill_id integer NOT NULL,
    portfolio_project_id integer NOT NULL,
    skill_id integer NOT NULL,
    sort_order smallint NOT NULL
);


--
-- TOC entry 1600 (class 1259 OID 24647)
-- Dependencies: 6 1601
-- Name: portfolio_project_skills_portfolio_project_skill_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE portfolio_project_skills_portfolio_project_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2012 (class 0 OID 0)
-- Dependencies: 1600
-- Name: portfolio_project_skills_portfolio_project_skill_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE portfolio_project_skills_portfolio_project_skill_id_seq OWNED BY portfolio_project_skills.portfolio_project_skill_id;


--
-- TOC entry 2013 (class 0 OID 0)
-- Dependencies: 1600
-- Name: portfolio_project_skills_portfolio_project_skill_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('portfolio_project_skills_portfolio_project_skill_id_seq', 15, true);


--
-- TOC entry 1599 (class 1259 OID 24638)
-- Dependencies: 6
-- Name: portfolio_projects; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE portfolio_projects (
    portfolio_project_id integer NOT NULL,
    project_name character varying(255) NOT NULL,
    description text NOT NULL,
    involvement_description text NOT NULL,
    sort_order smallint NOT NULL,
    site_url text,
    work_history_id integer
);


--
-- TOC entry 1598 (class 1259 OID 24636)
-- Dependencies: 6 1599
-- Name: portfolio_projects_portfolio_project_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE portfolio_projects_portfolio_project_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2014 (class 0 OID 0)
-- Dependencies: 1598
-- Name: portfolio_projects_portfolio_project_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE portfolio_projects_portfolio_project_id_seq OWNED BY portfolio_projects.portfolio_project_id;


--
-- TOC entry 2015 (class 0 OID 0)
-- Dependencies: 1598
-- Name: portfolio_projects_portfolio_project_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('portfolio_projects_portfolio_project_id_seq', 3, true);


--
-- TOC entry 1588 (class 1259 OID 18100)
-- Dependencies: 6
-- Name: proficiency_levels; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE proficiency_levels (
    proficiency_level_id smallint NOT NULL,
    proficiency_level_name character varying(100) NOT NULL
);


--
-- TOC entry 1585 (class 1259 OID 18087)
-- Dependencies: 6
-- Name: skill_categories; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE skill_categories (
    skill_category_id integer NOT NULL,
    skill_category_name character varying(100) NOT NULL,
    sort_order smallint NOT NULL
);


--
-- TOC entry 1584 (class 1259 OID 18085)
-- Dependencies: 1585 6
-- Name: skill_categories_skill_category_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE skill_categories_skill_category_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2016 (class 0 OID 0)
-- Dependencies: 1584
-- Name: skill_categories_skill_category_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE skill_categories_skill_category_id_seq OWNED BY skill_categories.skill_category_id;


--
-- TOC entry 2017 (class 0 OID 0)
-- Dependencies: 1584
-- Name: skill_categories_skill_category_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('skill_categories_skill_category_id_seq', 23, true);


--
-- TOC entry 1587 (class 1259 OID 18096)
-- Dependencies: 6
-- Name: skills; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE skills (
    skill_id integer NOT NULL,
    skill_name character varying(100) NOT NULL,
    skill_category_id integer NOT NULL,
    years_proficient smallint NOT NULL,
    proficiency_level_id smallint NOT NULL,
    sort_order smallint NOT NULL
);


--
-- TOC entry 1586 (class 1259 OID 18094)
-- Dependencies: 6 1587
-- Name: skills_skill_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE skills_skill_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2018 (class 0 OID 0)
-- Dependencies: 1586
-- Name: skills_skill_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE skills_skill_id_seq OWNED BY skills.skill_id;


--
-- TOC entry 2019 (class 0 OID 0)
-- Dependencies: 1586
-- Name: skills_skill_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('skills_skill_id_seq', 51, true);


--
-- TOC entry 1591 (class 1259 OID 18134)
-- Dependencies: 6
-- Name: work_history; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE work_history (
    work_history_id integer NOT NULL,
    organization_name character varying(255) NOT NULL,
    job_title character varying(100) NOT NULL,
    sort_order smallint NOT NULL
);


--
-- TOC entry 1593 (class 1259 OID 18151)
-- Dependencies: 6
-- Name: work_history_durations; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE work_history_durations (
    work_history_duration_id integer NOT NULL,
    start_date date NOT NULL,
    sort_order smallint NOT NULL,
    work_history_id integer NOT NULL,
    end_date date
);


--
-- TOC entry 1592 (class 1259 OID 18149)
-- Dependencies: 1593 6
-- Name: work_history_durations_work_history_duration_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE work_history_durations_work_history_duration_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2020 (class 0 OID 0)
-- Dependencies: 1592
-- Name: work_history_durations_work_history_duration_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE work_history_durations_work_history_duration_id_seq OWNED BY work_history_durations.work_history_duration_id;


--
-- TOC entry 2021 (class 0 OID 0)
-- Dependencies: 1592
-- Name: work_history_durations_work_history_duration_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('work_history_durations_work_history_duration_id_seq', 8, true);


--
-- TOC entry 1595 (class 1259 OID 18171)
-- Dependencies: 6
-- Name: work_history_tasks; Type: TABLE; Schema: online_resume; Owner: -; Tablespace: 
--

CREATE TABLE work_history_tasks (
    work_history_task_id integer NOT NULL,
    work_history_id integer NOT NULL,
    description text NOT NULL,
    sort_order smallint NOT NULL
);


--
-- TOC entry 1594 (class 1259 OID 18169)
-- Dependencies: 1595 6
-- Name: work_history_tasks_work_history_task_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE work_history_tasks_work_history_task_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2022 (class 0 OID 0)
-- Dependencies: 1594
-- Name: work_history_tasks_work_history_task_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE work_history_tasks_work_history_task_id_seq OWNED BY work_history_tasks.work_history_task_id;


--
-- TOC entry 2023 (class 0 OID 0)
-- Dependencies: 1594
-- Name: work_history_tasks_work_history_task_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('work_history_tasks_work_history_task_id_seq', 28, true);


--
-- TOC entry 1590 (class 1259 OID 18132)
-- Dependencies: 1591 6
-- Name: work_history_work_history_id_seq; Type: SEQUENCE; Schema: online_resume; Owner: -
--

CREATE SEQUENCE work_history_work_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2024 (class 0 OID 0)
-- Dependencies: 1590
-- Name: work_history_work_history_id_seq; Type: SEQUENCE OWNED BY; Schema: online_resume; Owner: -
--

ALTER SEQUENCE work_history_work_history_id_seq OWNED BY work_history.work_history_id;


--
-- TOC entry 2025 (class 0 OID 0)
-- Dependencies: 1590
-- Name: work_history_work_history_id_seq; Type: SEQUENCE SET; Schema: online_resume; Owner: -
--

SELECT pg_catalog.setval('work_history_work_history_id_seq', 8, true);


SET search_path = public, pg_catalog;

--
-- TOC entry 1597 (class 1259 OID 24622)
-- Dependencies: 3
-- Name: module_configurations; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE module_configurations (
    module_configuration_id integer NOT NULL,
    module_id integer NOT NULL,
    parameter_name character varying(100) NOT NULL,
    parameter_value character varying(255),
    parameter_default_value character varying(255)
);


--
-- TOC entry 1596 (class 1259 OID 24620)
-- Dependencies: 3 1597
-- Name: module_configurations_module_configuration_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE module_configurations_module_configuration_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2026 (class 0 OID 0)
-- Dependencies: 1596
-- Name: module_configurations_module_configuration_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE module_configurations_module_configuration_id_seq OWNED BY module_configurations.module_configuration_id;


--
-- TOC entry 2027 (class 0 OID 0)
-- Dependencies: 1596
-- Name: module_configurations_module_configuration_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('module_configurations_module_configuration_id_seq', 2, true);


--
-- TOC entry 1569 (class 1259 OID 17842)
-- Dependencies: 1887 3
-- Name: modules; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE modules (
    module_id integer NOT NULL,
    module_name character varying(50) NOT NULL,
    enabled boolean DEFAULT false NOT NULL,
    admin_page_name character varying(50) NOT NULL,
    display_name character varying(100),
    sort_order smallint
);


--
-- TOC entry 1568 (class 1259 OID 17840)
-- Dependencies: 3 1569
-- Name: modules_module_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE modules_module_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2028 (class 0 OID 0)
-- Dependencies: 1568
-- Name: modules_module_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE modules_module_id_seq OWNED BY modules.module_id;


--
-- TOC entry 2029 (class 0 OID 0)
-- Dependencies: 1568
-- Name: modules_module_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('modules_module_id_seq', 1, true);


--
-- TOC entry 1566 (class 1259 OID 16388)
-- Dependencies: 3
-- Name: pages; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE pages (
    page_id integer NOT NULL,
    page_name character varying(25) NOT NULL,
    page_location text NOT NULL
);


--
-- TOC entry 1565 (class 1259 OID 16386)
-- Dependencies: 3 1566
-- Name: pages_page_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE pages_page_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2030 (class 0 OID 0)
-- Dependencies: 1565
-- Name: pages_page_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE pages_page_id_seq OWNED BY pages.page_id;


--
-- TOC entry 2031 (class 0 OID 0)
-- Dependencies: 1565
-- Name: pages_page_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('pages_page_id_seq', 2, true);


--
-- TOC entry 1583 (class 1259 OID 18058)
-- Dependencies: 3
-- Name: permissions_permission_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE permissions_permission_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2032 (class 0 OID 0)
-- Dependencies: 1583
-- Name: permissions_permission_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('permissions_permission_id_seq', 1, false);


--
-- TOC entry 1576 (class 1259 OID 17960)
-- Dependencies: 1891 3
-- Name: permissions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE permissions (
    permission_id integer DEFAULT nextval('permissions_permission_id_seq'::regclass) NOT NULL,
    name character varying(100) NOT NULL,
    display_name character varying(100) NOT NULL,
    description character varying(255),
    module_id integer NOT NULL
);


--
-- TOC entry 1575 (class 1259 OID 17948)
-- Dependencies: 3
-- Name: role_permission_affiliation; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE role_permission_affiliation (
    role_permission_affiliation_id integer NOT NULL,
    role_id integer NOT NULL,
    permission_id integer NOT NULL,
    can boolean NOT NULL
);


--
-- TOC entry 1574 (class 1259 OID 17946)
-- Dependencies: 1575 3
-- Name: role_permission_affiliation_role_permission_affiliation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE role_permission_affiliation_role_permission_affiliation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2033 (class 0 OID 0)
-- Dependencies: 1574
-- Name: role_permission_affiliation_role_permission_affiliation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE role_permission_affiliation_role_permission_affiliation_id_seq OWNED BY role_permission_affiliation.role_permission_affiliation_id;


--
-- TOC entry 2034 (class 0 OID 0)
-- Dependencies: 1574
-- Name: role_permission_affiliation_role_permission_affiliation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('role_permission_affiliation_role_permission_affiliation_id_seq', 1, false);


--
-- TOC entry 1573 (class 1259 OID 17910)
-- Dependencies: 3
-- Name: roles; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE roles (
    role_id integer NOT NULL,
    name character varying(100) NOT NULL,
    display_name character varying(100) NOT NULL,
    module_id integer NOT NULL
);


--
-- TOC entry 1572 (class 1259 OID 17908)
-- Dependencies: 3 1573
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2035 (class 0 OID 0)
-- Dependencies: 1572
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE roles_role_id_seq OWNED BY roles.role_id;


--
-- TOC entry 2036 (class 0 OID 0)
-- Dependencies: 1572
-- Name: roles_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('roles_role_id_seq', 1, false);


--
-- TOC entry 1567 (class 1259 OID 16405)
-- Dependencies: 3
-- Name: sessions; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE sessions (
    session_id character varying(40) NOT NULL,
    session_name character varying(100) NOT NULL,
    session_data text NOT NULL,
    expire_time integer NOT NULL
);


--
-- TOC entry 1582 (class 1259 OID 18021)
-- Dependencies: 3
-- Name: us_states; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE us_states (
    state_id integer NOT NULL,
    abbreviation character varying(3) NOT NULL,
    name character varying(50) NOT NULL
);


--
-- TOC entry 1578 (class 1259 OID 17977)
-- Dependencies: 3
-- Name: user_role_affiliation; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE user_role_affiliation (
    user_role_affiliation_id bigint NOT NULL,
    user_id bigint NOT NULL,
    role_id integer NOT NULL
);


--
-- TOC entry 1577 (class 1259 OID 17975)
-- Dependencies: 3 1578
-- Name: user_role_affiliation_user_role_affiliation_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE user_role_affiliation_user_role_affiliation_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2037 (class 0 OID 0)
-- Dependencies: 1577
-- Name: user_role_affiliation_user_role_affiliation_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE user_role_affiliation_user_role_affiliation_id_seq OWNED BY user_role_affiliation.user_role_affiliation_id;


--
-- TOC entry 2038 (class 0 OID 0)
-- Dependencies: 1577
-- Name: user_role_affiliation_user_role_affiliation_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('user_role_affiliation_user_role_affiliation_id_seq', 1, false);


--
-- TOC entry 1571 (class 1259 OID 17852)
-- Dependencies: 3
-- Name: users; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE users (
    user_id bigint NOT NULL,
    user_name character varying(50) NOT NULL,
    password character varying(255) NOT NULL,
    email_address character varying(255) NOT NULL,
    is_site_admin boolean
);


--
-- TOC entry 1570 (class 1259 OID 17850)
-- Dependencies: 1571 3
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 2039 (class 0 OID 0)
-- Dependencies: 1570
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE users_user_id_seq OWNED BY users.user_id;


--
-- TOC entry 2040 (class 0 OID 0)
-- Dependencies: 1570
-- Name: users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('users_user_id_seq', 1, true);


SET search_path = online_resume, pg_catalog;

--
-- TOC entry 1904 (class 2604 OID 24727)
-- Dependencies: 1606 1607 1607
-- Name: code_example_skill_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE code_example_skills ALTER COLUMN code_example_skill_id SET DEFAULT nextval('code_example_skills_code_example_skill_id_seq'::regclass);


--
-- TOC entry 1903 (class 2604 OID 24705)
-- Dependencies: 1604 1605 1605
-- Name: code_example_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE code_examples ALTER COLUMN code_example_id SET DEFAULT nextval('code_examples_code_example_id_seq'::regclass);


--
-- TOC entry 1893 (class 2604 OID 18007)
-- Dependencies: 1580 1581 1581
-- Name: education_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE education ALTER COLUMN education_id SET DEFAULT nextval('education_education_id_seq'::regclass);


--
-- TOC entry 1902 (class 2604 OID 24684)
-- Dependencies: 1603 1602 1603
-- Name: portfolio_project_image_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE portfolio_project_images ALTER COLUMN portfolio_project_image_id SET DEFAULT nextval('portfolio_project_images_portfolio_project_image_id_seq'::regclass);


--
-- TOC entry 1901 (class 2604 OID 24652)
-- Dependencies: 1601 1600 1601
-- Name: portfolio_project_skill_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE portfolio_project_skills ALTER COLUMN portfolio_project_skill_id SET DEFAULT nextval('portfolio_project_skills_portfolio_project_skill_id_seq'::regclass);


--
-- TOC entry 1900 (class 2604 OID 24641)
-- Dependencies: 1599 1598 1599
-- Name: portfolio_project_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE portfolio_projects ALTER COLUMN portfolio_project_id SET DEFAULT nextval('portfolio_projects_portfolio_project_id_seq'::regclass);


--
-- TOC entry 1894 (class 2604 OID 18090)
-- Dependencies: 1584 1585 1585
-- Name: skill_category_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE skill_categories ALTER COLUMN skill_category_id SET DEFAULT nextval('skill_categories_skill_category_id_seq'::regclass);


--
-- TOC entry 1895 (class 2604 OID 18099)
-- Dependencies: 1587 1586 1587
-- Name: skill_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE skills ALTER COLUMN skill_id SET DEFAULT nextval('skills_skill_id_seq'::regclass);


--
-- TOC entry 1896 (class 2604 OID 18137)
-- Dependencies: 1590 1591 1591
-- Name: work_history_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE work_history ALTER COLUMN work_history_id SET DEFAULT nextval('work_history_work_history_id_seq'::regclass);


--
-- TOC entry 1897 (class 2604 OID 18154)
-- Dependencies: 1593 1592 1593
-- Name: work_history_duration_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE work_history_durations ALTER COLUMN work_history_duration_id SET DEFAULT nextval('work_history_durations_work_history_duration_id_seq'::regclass);


--
-- TOC entry 1898 (class 2604 OID 24612)
-- Dependencies: 1595 1594 1595
-- Name: work_history_task_id; Type: DEFAULT; Schema: online_resume; Owner: -
--

ALTER TABLE work_history_tasks ALTER COLUMN work_history_task_id SET DEFAULT nextval('work_history_tasks_work_history_task_id_seq'::regclass);


SET search_path = public, pg_catalog;

--
-- TOC entry 1899 (class 2604 OID 24625)
-- Dependencies: 1596 1597 1597
-- Name: module_configuration_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE module_configurations ALTER COLUMN module_configuration_id SET DEFAULT nextval('module_configurations_module_configuration_id_seq'::regclass);


--
-- TOC entry 1886 (class 2604 OID 17845)
-- Dependencies: 1569 1568 1569
-- Name: module_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE modules ALTER COLUMN module_id SET DEFAULT nextval('modules_module_id_seq'::regclass);


--
-- TOC entry 1885 (class 2604 OID 16391)
-- Dependencies: 1565 1566 1566
-- Name: page_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE pages ALTER COLUMN page_id SET DEFAULT nextval('pages_page_id_seq'::regclass);


--
-- TOC entry 1890 (class 2604 OID 17951)
-- Dependencies: 1575 1574 1575
-- Name: role_permission_affiliation_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE role_permission_affiliation ALTER COLUMN role_permission_affiliation_id SET DEFAULT nextval('role_permission_affiliation_role_permission_affiliation_id_seq'::regclass);


--
-- TOC entry 1889 (class 2604 OID 17913)
-- Dependencies: 1573 1572 1573
-- Name: role_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE roles ALTER COLUMN role_id SET DEFAULT nextval('roles_role_id_seq'::regclass);


--
-- TOC entry 1892 (class 2604 OID 17980)
-- Dependencies: 1578 1577 1578
-- Name: user_role_affiliation_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE user_role_affiliation ALTER COLUMN user_role_affiliation_id SET DEFAULT nextval('user_role_affiliation_user_role_affiliation_id_seq'::regclass);


--
-- TOC entry 1888 (class 2604 OID 17855)
-- Dependencies: 1571 1570 1571
-- Name: user_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE users ALTER COLUMN user_id SET DEFAULT nextval('users_user_id_seq'::regclass);


SET search_path = online_resume, pg_catalog;

--
-- TOC entry 1989 (class 0 OID 18105)
-- Dependencies: 1589
-- Data for Name: degree_levels; Type: TABLE DATA; Schema: online_resume; Owner: -
--

INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (1, 'A.A.', 'Associate of Arts');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (2, 'A.S.', 'Associate of Science');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (3, 'AAS', 'Associate of Applied Science');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (4, 'B.A.', 'Bachelor of Arts');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (5, 'B.S.', 'Bachelor of Science');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (6, 'BFA', 'Bachelor of Fine Arts');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (7, 'M.A.', 'Master of Arts');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (8, 'M.S.', 'Master of Science');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (9, 'MBA', 'Master of Business Administration');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (10, 'MFA', 'Master of Fine Arts');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (11, 'Ph.D.', 'Doctor of Philosophy');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (12, 'J.D.', 'Juris Doctor');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (13, 'M.D.', 'Doctor of Medicine');
INSERT INTO degree_levels (degree_level_id, abbreviation, degree_level_name) VALUES (14, 'DDS', 'Doctor of Dental Surgery');


SET search_path = public, pg_catalog;

--
-- TOC entry 1993 (class 0 OID 24622)
-- Dependencies: 1597
-- Data for Name: module_configurations; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO module_configurations (module_configuration_id, module_id, parameter_name, parameter_value, parameter_default_value) VALUES (1, 2, 'theme', NULL, 'default');
INSERT INTO module_configurations (module_configuration_id, module_id, parameter_name, parameter_value, parameter_default_value) VALUES (2, 2, 'code_example_file_extensions', NULL, 'php,html,aspx,asp,js,css,htc,inc');


--
-- TOC entry 1977 (class 0 OID 17842)
-- Dependencies: 1569
-- Data for Name: modules; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO modules (module_id, module_name, enabled, admin_page_name, display_name, sort_order) VALUES (1, 'admin', true, 'AdminHome', 'General', 1);
INSERT INTO modules (module_id, module_name, enabled, admin_page_name, display_name, sort_order) VALUES (2, 'online_resume', true, 'OnlineResumeAdmin', 'Online Resume', 2);

INSERT INTO us_states (state_id, abbreviation, name) VALUES (1, 'AL', 'Alabama');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (2, 'AK', 'Alaska');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (3, 'AZ', 'Arizona');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (4, 'AR', 'Arkansas');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (5, 'CA', 'California');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (6, 'CO', 'Colorado');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (7, 'CT', 'Connecticut');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (8, 'DE', 'Delaware');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (9, 'FL', 'Florida');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (10, 'GA', 'Georgia');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (11, 'HI', 'Hawaii');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (12, 'ID', 'Idaho');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (13, 'IL', 'Illinois');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (14, 'IN', 'Indiana');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (15, 'IA', 'Iowa');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (16, 'KS', 'Kansas');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (17, 'KY', 'Kentucky');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (18, 'LA', 'Louisiana');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (19, 'ME', 'Maine');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (20, 'MD', 'Maryland');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (21, 'MA', 'Massachusetts');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (22, 'MI', 'Michigan');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (23, 'MN', 'Minnesota');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (24, 'MS', 'Mississippi');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (25, 'MO', 'Missouri');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (26, 'MT', 'Montana');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (27, 'NE', 'Nebraska');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (28, 'NV', 'Nevada');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (29, 'NH', 'New Hampshire');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (30, 'NJ', 'New Jersey');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (31, 'NM', 'New Mexico');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (32, 'NY', 'New York');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (33, 'NC', 'North Carolina');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (34, 'ND', 'North Dakota');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (35, 'OH', 'Ohio');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (36, 'OK', 'Oklahoma');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (37, 'OR', 'Oregon');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (38, 'PA', 'Pennsylvania');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (39, 'RI', 'Rhode Island');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (40, 'SC', 'South Carolina');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (41, 'SD', 'South Dakota');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (42, 'TN', 'Tennessee');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (43, 'TX', 'Texas');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (44, 'UT', 'Utah');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (45, 'VT', 'Vermont');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (46, 'VA', 'Virginia');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (47, 'WA', 'Washington');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (48, 'WV', 'West Virginia');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (49, 'WI', 'Wisconsin');
INSERT INTO us_states (state_id, abbreviation, name) VALUES (50, 'WY', 'Wyoming');

SET search_path = online_resume, pg_catalog;

--
-- TOC entry 1950 (class 2606 OID 24710)
-- Dependencies: 1605 1605
-- Name: code_example_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY code_examples
    ADD CONSTRAINT code_example_id_pk PRIMARY KEY (code_example_id);


--
-- TOC entry 1952 (class 2606 OID 24729)
-- Dependencies: 1607 1607
-- Name: code_example_skill_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY code_example_skills
    ADD CONSTRAINT code_example_skill_id_pk PRIMARY KEY (code_example_skill_id);


--
-- TOC entry 1934 (class 2606 OID 18109)
-- Dependencies: 1589 1589
-- Name: degree_level_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY degree_levels
    ADD CONSTRAINT degree_level_id_pk PRIMARY KEY (degree_level_id);


--
-- TOC entry 1924 (class 2606 OID 18013)
-- Dependencies: 1581 1581
-- Name: education_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY education
    ADD CONSTRAINT education_id_pk PRIMARY KEY (education_id);


--
-- TOC entry 1922 (class 2606 OID 18001)
-- Dependencies: 1579 1579
-- Name: general_information_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY general_information
    ADD CONSTRAINT general_information_id_pk PRIMARY KEY (general_information_id);


--
-- TOC entry 1944 (class 2606 OID 24646)
-- Dependencies: 1599 1599
-- Name: portfolio_project_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY portfolio_projects
    ADD CONSTRAINT portfolio_project_id_pk PRIMARY KEY (portfolio_project_id);


--
-- TOC entry 1948 (class 2606 OID 24689)
-- Dependencies: 1603 1603
-- Name: portfolio_project_image_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY portfolio_project_images
    ADD CONSTRAINT portfolio_project_image_id_pk PRIMARY KEY (portfolio_project_image_id);


--
-- TOC entry 1946 (class 2606 OID 24654)
-- Dependencies: 1601 1601
-- Name: portfolio_project_skill_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY portfolio_project_skills
    ADD CONSTRAINT portfolio_project_skill_id_pk PRIMARY KEY (portfolio_project_skill_id);


--
-- TOC entry 1932 (class 2606 OID 18104)
-- Dependencies: 1588 1588
-- Name: proficiency_level_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY proficiency_levels
    ADD CONSTRAINT proficiency_level_id_pk PRIMARY KEY (proficiency_level_id);


--
-- TOC entry 1928 (class 2606 OID 18092)
-- Dependencies: 1585 1585
-- Name: skill_category_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY skill_categories
    ADD CONSTRAINT skill_category_id_pk PRIMARY KEY (skill_category_id);


--
-- TOC entry 1930 (class 2606 OID 18116)
-- Dependencies: 1587 1587
-- Name: skill_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY skills
    ADD CONSTRAINT skill_id_pk PRIMARY KEY (skill_id);


--
-- TOC entry 1938 (class 2606 OID 18156)
-- Dependencies: 1593 1593
-- Name: work_history_duration_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY work_history_durations
    ADD CONSTRAINT work_history_duration_id_pk PRIMARY KEY (work_history_duration_id);


--
-- TOC entry 1936 (class 2606 OID 18158)
-- Dependencies: 1591 1591
-- Name: work_history_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY work_history
    ADD CONSTRAINT work_history_id_pk PRIMARY KEY (work_history_id);


--
-- TOC entry 1940 (class 2606 OID 24611)
-- Dependencies: 1595 1595
-- Name: work_history_task_id_pk; Type: CONSTRAINT; Schema: online_resume; Owner: -; Tablespace: 
--

ALTER TABLE ONLY work_history_tasks
    ADD CONSTRAINT work_history_task_id_pk PRIMARY KEY (work_history_task_id);


SET search_path = public, pg_catalog;

--
-- TOC entry 1942 (class 2606 OID 24630)
-- Dependencies: 1597 1597
-- Name: module_configuration_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY module_configurations
    ADD CONSTRAINT module_configuration_id_pk PRIMARY KEY (module_configuration_id);


--
-- TOC entry 1910 (class 2606 OID 17848)
-- Dependencies: 1569 1569
-- Name: module_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY modules
    ADD CONSTRAINT module_id_pk PRIMARY KEY (module_id);


--
-- TOC entry 1906 (class 2606 OID 16396)
-- Dependencies: 1566 1566
-- Name: pages_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY pages
    ADD CONSTRAINT pages_pk PRIMARY KEY (page_id);


--
-- TOC entry 1918 (class 2606 OID 17964)
-- Dependencies: 1576 1576
-- Name: permission_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT permission_id_pk PRIMARY KEY (permission_id);


--
-- TOC entry 1914 (class 2606 OID 17915)
-- Dependencies: 1573 1573
-- Name: role_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT role_id_pk PRIMARY KEY (role_id);


--
-- TOC entry 1916 (class 2606 OID 17953)
-- Dependencies: 1575 1575
-- Name: role_permission_affiliation_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY role_permission_affiliation
    ADD CONSTRAINT role_permission_affiliation_id_pk PRIMARY KEY (role_permission_affiliation_id);


--
-- TOC entry 1908 (class 2606 OID 16412)
-- Dependencies: 1567 1567
-- Name: session_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY sessions
    ADD CONSTRAINT session_id_pk PRIMARY KEY (session_id);


--
-- TOC entry 1926 (class 2606 OID 18026)
-- Dependencies: 1582 1582
-- Name: state_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY us_states
    ADD CONSTRAINT state_id_pk PRIMARY KEY (state_id);


--
-- TOC entry 1912 (class 2606 OID 17857)
-- Dependencies: 1571 1571
-- Name: user_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT user_id_pk PRIMARY KEY (user_id);


--
-- TOC entry 1920 (class 2606 OID 17982)
-- Dependencies: 1578 1578
-- Name: user_role_affiliation_id_pk; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY user_role_affiliation
    ADD CONSTRAINT user_role_affiliation_id_pk PRIMARY KEY (user_role_affiliation_id);


SET search_path = online_resume, pg_catalog;

--
-- TOC entry 1973 (class 2606 OID 24730)
-- Dependencies: 1607 1949 1605
-- Name: code_example_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY code_example_skills
    ADD CONSTRAINT code_example_id_fk FOREIGN KEY (code_example_id) REFERENCES code_examples(code_example_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1961 (class 2606 OID 18110)
-- Dependencies: 1589 1933 1581
-- Name: degree_level_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY education
    ADD CONSTRAINT degree_level_id_fk FOREIGN KEY (degree_level_id) REFERENCES degree_levels(degree_level_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1968 (class 2606 OID 24655)
-- Dependencies: 1943 1599 1601
-- Name: portfolio_project_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY portfolio_project_skills
    ADD CONSTRAINT portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1970 (class 2606 OID 24690)
-- Dependencies: 1603 1599 1943
-- Name: portfolio_project_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY portfolio_project_images
    ADD CONSTRAINT portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1971 (class 2606 OID 24711)
-- Dependencies: 1943 1599 1605
-- Name: portfolio_project_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY code_examples
    ADD CONSTRAINT portfolio_project_id_fk FOREIGN KEY (portfolio_project_id) REFERENCES portfolio_projects(portfolio_project_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1963 (class 2606 OID 18122)
-- Dependencies: 1587 1931 1588
-- Name: proficiency_level_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY skills
    ADD CONSTRAINT proficiency_level_id_fk FOREIGN KEY (proficiency_level_id) REFERENCES proficiency_levels(proficiency_level_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1962 (class 2606 OID 18117)
-- Dependencies: 1585 1927 1587
-- Name: skill_category_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY skills
    ADD CONSTRAINT skill_category_id_fk FOREIGN KEY (skill_category_id) REFERENCES skill_categories(skill_category_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1969 (class 2606 OID 24660)
-- Dependencies: 1587 1601 1929
-- Name: skill_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY portfolio_project_skills
    ADD CONSTRAINT skill_id_fk FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1974 (class 2606 OID 24735)
-- Dependencies: 1929 1587 1607
-- Name: skill_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY code_example_skills
    ADD CONSTRAINT skill_id_fk FOREIGN KEY (skill_id) REFERENCES skills(skill_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1959 (class 2606 OID 18037)
-- Dependencies: 1582 1579 1925
-- Name: state_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY general_information
    ADD CONSTRAINT state_id_fk FOREIGN KEY (state_id) REFERENCES public.us_states(state_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1960 (class 2606 OID 18061)
-- Dependencies: 1581 1925 1582
-- Name: state_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY education
    ADD CONSTRAINT state_id_fk FOREIGN KEY (state_id) REFERENCES public.us_states(state_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1965 (class 2606 OID 18180)
-- Dependencies: 1595 1591 1935
-- Name: work_history_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY work_history_tasks
    ADD CONSTRAINT work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1964 (class 2606 OID 24605)
-- Dependencies: 1935 1593 1591
-- Name: work_history_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY work_history_durations
    ADD CONSTRAINT work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1967 (class 2606 OID 24695)
-- Dependencies: 1591 1935 1599
-- Name: work_history_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY portfolio_projects
    ADD CONSTRAINT work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1972 (class 2606 OID 24745)
-- Dependencies: 1605 1935 1591
-- Name: work_history_id_fk; Type: FK CONSTRAINT; Schema: online_resume; Owner: -
--

ALTER TABLE ONLY code_examples
    ADD CONSTRAINT work_history_id_fk FOREIGN KEY (work_history_id) REFERENCES work_history(work_history_id) ON UPDATE CASCADE ON DELETE CASCADE;


SET search_path = public, pg_catalog;

--
-- TOC entry 1953 (class 2606 OID 17928)
-- Dependencies: 1909 1573 1569
-- Name: module_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT module_id_fk FOREIGN KEY (module_id) REFERENCES modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1956 (class 2606 OID 17965)
-- Dependencies: 1909 1576 1569
-- Name: module_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY permissions
    ADD CONSTRAINT module_id_fk FOREIGN KEY (module_id) REFERENCES modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1966 (class 2606 OID 24631)
-- Dependencies: 1569 1597 1909
-- Name: module_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY module_configurations
    ADD CONSTRAINT module_id_fk FOREIGN KEY (module_id) REFERENCES modules(module_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1955 (class 2606 OID 17970)
-- Dependencies: 1575 1576 1917
-- Name: permission_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY role_permission_affiliation
    ADD CONSTRAINT permission_id_fk FOREIGN KEY (permission_id) REFERENCES permissions(permission_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1954 (class 2606 OID 17954)
-- Dependencies: 1575 1913 1573
-- Name: role_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY role_permission_affiliation
    ADD CONSTRAINT role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1958 (class 2606 OID 17988)
-- Dependencies: 1573 1578 1913
-- Name: role_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_role_affiliation
    ADD CONSTRAINT role_id_fk FOREIGN KEY (role_id) REFERENCES roles(role_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 1957 (class 2606 OID 17983)
-- Dependencies: 1571 1578 1911
-- Name: user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY user_role_affiliation
    ADD CONSTRAINT user_id_fk FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- TOC entry 2003 (class 0 OID 0)
-- Dependencies: 3
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2011-04-17 20:23:11

--
-- PostgreSQL database dump complete
--


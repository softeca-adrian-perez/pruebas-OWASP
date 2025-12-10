-- liquibase formatted sql

-- changeset LisaCanéSáizSoftecaI:1756821087342-1
CREATE TABLE aag_regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, creation_date date NOT NULL, image VARCHAR(255) NOT NULL, url_rm VARCHAR(255) NULL, CONSTRAINT PK_AAG_REGIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-2
CREATE TABLE agreements (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(50) NULL, codigo VARCHAR(50) NULL, fecha_creacion datetime(0) NULL, aag_region_id INT NOT NULL, CONSTRAINT PK_AGREEMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-3
CREATE TABLE alerts (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT 0 NOT NULL, alert_type_id INT DEFAULT 0 NOT NULL, rm_alert_id INT NULL, body VARCHAR(255) NOT NULL, feedback LONGTEXT NULL, url VARCHAR(255) NOT NULL, `read` BIT(1) NULL, creation_date datetime(0) NOT NULL, CONSTRAINT PK_ALERTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-4
CREATE TABLE alerts_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Alerts_types' NULL, CONSTRAINT PK_ALERTS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-5
CREATE TABLE annex_details (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Annex_details' NULL, aag_region_id INT NULL, CONSTRAINT PK_ANNEX_DETAILS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-6
CREATE TABLE appointments (id INT AUTO_INCREMENT NOT NULL, garage_id INT NULL, distributor_id INT NULL, appointment_feeling_id INT NULL, appointment_status_id INT NULL, appointment_type_id INT NULL, visit_contact_id INT NULL, visit_contact_name VARCHAR(50) NULL, date date NOT NULL, reminder INT NULL, longitude DECIMAL(30, 20) NULL, latitude DECIMAL(30, 20) NULL, end_date date NOT NULL, start_time time NULL, end_time time NULL, `description` VARCHAR(255) NULL, mtd VARCHAR(255) NULL, qtd VARCHAR(255) NULL, ytd VARCHAR(255) NULL, title VARCHAR(50) NULL, feedback LONGTEXT NULL, customer_performance_summary LONGTEXT NULL, feedback_user_modification INT NULL, feedback_date_creation datetime(0) NULL, requires_follow_up BIT(1) NULL, user_assigned_id INT NULL, user_creation_id INT NULL, creation_date datetime(0) NULL, CONSTRAINT PK_APPOINTMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-7
CREATE TABLE appointments_codes (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, office_code VARCHAR(255) NULL, CONSTRAINT PK_APPOINTMENTS_CODES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-8
CREATE TABLE appointments_comments (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, body LONGTEXT NOT NULL, creation_date datetime(0) NOT NULL, user_id INT NULL, CONSTRAINT PK_APPOINTMENTS_COMMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-9
CREATE TABLE appointments_contacts (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, contact_id INT NOT NULL, CONSTRAINT PK_APPOINTMENTS_CONTACTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-10
CREATE TABLE appointments_contacts_lists (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, contact_list_id INT NOT NULL, CONSTRAINT PK_APPOINTMENTS_CONTACTS_LISTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-11
CREATE TABLE appointments_feelings (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Appointments_feelings' NULL, color VARCHAR(255) NOT NULL, icon VARCHAR(255) NOT NULL, CONSTRAINT PK_APPOINTMENTS_FEELINGS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-12
CREATE TABLE appointments_files (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NOT NULL, source_name VARCHAR(255) NOT NULL, file_guid VARCHAR(255) NULL, CONSTRAINT PK_APPOINTMENTS_FILES PRIMARY KEY (id), UNIQUE (file_guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-13
CREATE TABLE appointments_objective_comments (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, objective_id INT NULL, objective VARCHAR(255) NULL, type INT NULL, status INT NOT NULL, comment LONGTEXT NULL, creation_date datetime(0) NOT NULL, CONSTRAINT PK_APPOINTMENTS_OBJECTIVE_COMMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-14
CREATE TABLE appointments_objectives (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, tg BIT(1) NULL, tg_personal BIT(1) NULL, tg_management BIT(1) NULL, gpc BIT(1) NULL, gpc_personal BIT(1) NULL, gpc_management BIT(1) NULL, aag_region_id INT NOT NULL, CONSTRAINT PK_APPOINTMENTS_OBJECTIVES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-15
CREATE TABLE appointments_status (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Appointments_status' NULL, is_event BIT(1) NULL, CONSTRAINT PK_APPOINTMENTS_STATUS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-16
CREATE TABLE appointments_topics (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NOT NULL, topic_id INT NOT NULL, CONSTRAINT PK_APPOINTMENTS_TOPICS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-17
CREATE TABLE appointments_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Appointments_types' NULL, is_event BIT(1) NULL, CONSTRAINT PK_APPOINTMENTS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-18
CREATE TABLE associations (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_ASSOCIATIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-19
CREATE TABLE associations_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Associantions_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_ASSOCIATIONS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-20
CREATE TABLE billings_schedules (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.BillingsSchedules' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_BILLINGS_SCHEDULES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-21
CREATE TABLE bookings (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, garage_id INT NOT NULL, child_network_id INT NULL, date date NOT NULL, time time NOT NULL, time_to time NULL, creation_date datetime(0) NOT NULL, quotation_id VARCHAR(255) NULL, quotation_id_leadgen INT NULL, customer_name VARCHAR(255) NULL, customer_phone VARCHAR(255) NULL, customer_email VARCHAR(255) NOT NULL, marketing_acceptance BIT(1) NOT NULL, plate VARCHAR(255) NULL, vin VARCHAR(255) NULL, brand VARCHAR(255) NULL, model VARCHAR(255) NULL, version VARCHAR(255) NULL, mot_exp_date date NULL, fuel VARCHAR(255) NULL, registered_on date NULL, mileage INT NULL, work_id INT NULL, additional_info LONGTEXT NULL, booking_status INT NOT NULL, CONSTRAINT PK_BOOKINGS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-22
CREATE TABLE brands (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, name VARCHAR(255) NOT NULL, active BIT(1) NULL, CONSTRAINT PK_BRANDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-23
CREATE TABLE brands_images (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NULL, type VARCHAR(255) NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NULL, CONSTRAINT PK_BRANDS_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-24
CREATE TABLE campaign_entries (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, aag_region_id INT NULL, CONSTRAINT PK_CAMPAIGN_ENTRIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-25
CREATE TABLE cities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NULL, latitude DECIMAL(20, 6) NULL, longitude DECIMAL(20, 6) NULL, province_id INT NULL, CONSTRAINT PK_CITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-26
CREATE TABLE communications (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, subtitle VARCHAR(255) NULL, url VARCHAR(255) NULL, body LONGTEXT NOT NULL, image VARCHAR(255) NULL, communication_section_id INT NOT NULL, section_subsection_id INT NOT NULL, start_date datetime(0) NULL, end_date datetime(0) NULL, active BIT(1) DEFAULT 1 NOT NULL, user_id INT NOT NULL, creation_date datetime(0) NOT NULL, without_networks BIT(1) NULL, without_distributor_networks BIT(1) NULL, without_activity BIT(1) NULL, aag_member_yes BIT(1) NULL, aag_member_no BIT(1) NULL, is_popup BIT(1) DEFAULT 0 NULL, start_date_popup date NULL, end_date_popup date NULL, aag_region_id INT NULL, CONSTRAINT PK_COMMUNICATIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-27
CREATE TABLE communications_customers_activities (id INT AUTO_INCREMENT NOT NULL, communication_id INT NOT NULL, customer_activity_id INT NOT NULL, CONSTRAINT PK_COMMUNICATIONS_CUSTOMERS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-28
CREATE TABLE communications_distributors_networks (id INT AUTO_INCREMENT NOT NULL, communication_id INT DEFAULT 0 NOT NULL, distributor_network_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_COMMUNICATIONS_DISTRIBUTORS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-29
CREATE TABLE communications_files (id INT AUTO_INCREMENT NOT NULL, communication_id INT NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NOT NULL, source_name VARCHAR(255) NOT NULL, CONSTRAINT PK_COMMUNICATIONS_FILES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-30
CREATE TABLE communications_networks (id INT AUTO_INCREMENT NOT NULL, communication_id INT NOT NULL, network_id INT NOT NULL, CONSTRAINT PK_COMMUNICATIONS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-31
CREATE TABLE communications_positions (id INT AUTO_INCREMENT NOT NULL, communication_id INT DEFAULT 0 NULL, position_id INT DEFAULT 0 NULL, CONSTRAINT PK_COMMUNICATIONS_POSITIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-32
CREATE TABLE communications_sections (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Appointments_status' NULL, scrolling BIT(1) NULL, visual BIT(1) NULL, image VARCHAR(255) NULL, creation_date datetime(0) NOT NULL, without_networks BIT(1) NULL, without_distributor_networks BIT(1) NULL, without_activity BIT(1) NULL, aag_member_yes BIT(1) NULL, aag_member_no BIT(1) NULL, aag_region_id INT NULL, CONSTRAINT PK_COMMUNICATIONS_SECTIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-33
CREATE TABLE communications_sections_customers_activities (id INT AUTO_INCREMENT NOT NULL, communication_section_id INT NOT NULL, customer_activity_id INT NOT NULL, CONSTRAINT PK_COMMUNICATIONS_SECTIONS_CUSTOMERS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-34
CREATE TABLE communications_sections_distributors_networks (id INT AUTO_INCREMENT NOT NULL, communication_section_id INT DEFAULT 0 NOT NULL, distributor_network_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_COMMUNICATIONS_SECTIONS_DISTRIBUTORS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-35
CREATE TABLE communications_sections_networks (id INT AUTO_INCREMENT NOT NULL, communication_section_id INT DEFAULT 0 NOT NULL, network_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_COMMUNICATIONS_SECTIONS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-36
CREATE TABLE communications_sections_positions (id INT AUTO_INCREMENT NOT NULL, communication_section_id INT DEFAULT 0 NULL, position_id INT DEFAULT 0 NULL, CONSTRAINT PK_COMMUNICATIONS_SECTIONS_POSITIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-37
CREATE TABLE communications_sections_trading_groups (id INT AUTO_INCREMENT NOT NULL, communication_section_id INT DEFAULT 0 NOT NULL, trading_group_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_COMMUNICATIONS_SECTIONS_TRADING_GROUPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-38
CREATE TABLE communications_trading_groups (id INT AUTO_INCREMENT NOT NULL, communication_id INT NOT NULL, trading_group_id INT NOT NULL, CONSTRAINT PK_COMMUNICATIONS_TRADING_GROUPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-39
CREATE TABLE communications_users (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, communication_id INT NULL, date_read datetime(0) NOT NULL, CONSTRAINT PK_COMMUNICATIONS_USERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-40
CREATE TABLE conferences (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, start_date date NULL, duration INT NULL, venue_id INT NULL, status BIT(1) DEFAULT 1 NULL, aag_region_id INT NULL, CONSTRAINT PK_CONFERENCES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-41
CREATE TABLE conferences_delegates (id INT AUTO_INCREMENT NOT NULL, conferences_id INT NULL, booking_date date NULL, garages_id INT NULL, distributors_id INT NULL, suppliers_id INT NULL, delegate_id INT NULL, contact VARCHAR(255) NULL, email VARCHAR(255) NULL, venue_id INT NULL, room_type_id INT NULL, nights INT DEFAULT 0 NULL, vegetarian BIT(1) DEFAULT 0 NULL, diet_requirements LONGTEXT NULL, guest_name VARCHAR(255) NULL, guest_separate_room INT NULL, stand_number VARCHAR(255) NULL, stand_size_id INT NULL, stand_power_required BIT(1) DEFAULT 0 NULL, bespoke_stand_details VARCHAR(255) NULL, website_entry_id VARCHAR(255) NULL, CONSTRAINT PK_CONFERENCES_DELEGATES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-42
CREATE TABLE config (id INT AUTO_INCREMENT NOT NULL, section_id INT NOT NULL, name_en VARCHAR(255) NOT NULL, tooltip_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, tooltip_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, tooltip_de VARCHAR(255) NOT NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Config' NULL, tooltip_lc VARCHAR(255) DEFAULT 'Bd.Config' NULL, active BIT(1) DEFAULT 0 NOT NULL, tooltip_nl VARCHAR(255) NULL, CONSTRAINT PK_CONFIG PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-43
CREATE TABLE config_modules_regions_roles (id INT AUTO_INCREMENT NOT NULL, config_id INT NOT NULL, aag_region_id INT NULL, role_id INT NULL, active BIT(1) DEFAULT 0 NULL, CONSTRAINT PK_CONFIG_MODULES_REGIONS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-44
CREATE TABLE config_sections (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Config_sections' NULL, CONSTRAINT PK_CONFIG_SECTIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-45
CREATE TABLE contacts (id INT AUTO_INCREMENT NOT NULL, title_id INT NULL, contact_id INT NULL, first_name VARCHAR(255) NULL, last_name VARCHAR(255) NULL, position_id INT NULL, garage_id INT NULL, distributor_id INT NULL, logistic_center_id INT NULL, phone VARCHAR(255) NULL, mobile_phone VARCHAR(255) NULL, identification_number VARCHAR(30) NOT NULL, email VARCHAR(255) NOT NULL, creation_date date NOT NULL, guid VARCHAR(36) NULL, aag_region_id INT NULL, CONSTRAINT PK_CONTACTS PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-46
CREATE TABLE contacts_contacts_lists (id INT AUTO_INCREMENT NOT NULL, contact_id INT NOT NULL, contact_list_id INT NOT NULL, CONSTRAINT PK_CONTACTS_CONTACTS_LISTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-47
CREATE TABLE contacts_lists (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, user_id INT DEFAULT 0 NOT NULL, global BIT(1) NULL, color VARCHAR(255) DEFAULT '3668a1' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_CONTACTS_LISTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-48
CREATE TABLE contacts_regions (id INT AUTO_INCREMENT NOT NULL, contact_id INT NOT NULL, region_id INT NOT NULL, CONSTRAINT PK_CONTACTS_REGIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-49
CREATE TABLE contacts_titles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NULL, CONSTRAINT PK_CONTACTS_TITLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-50
CREATE TABLE countries (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, country_code VARCHAR(4) NOT NULL, country_code_2 VARCHAR(4) NOT NULL, aag_region_id INT NULL, image VARCHAR(255) NOT NULL, currency VARCHAR(4) NOT NULL, symbol VARCHAR(4) NOT NULL, CONSTRAINT PK_COUNTRIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-51
CREATE TABLE courses_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.CoursesTypes' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_COURSES_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-52
CREATE TABLE courtesy_car_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Courtesy_car_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_COURTESY_CAR_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-53
CREATE TABLE customers_activities (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Customers_activities' NULL, CONSTRAINT PK_CUSTOMERS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-54
CREATE TABLE debrief_tasks (id INT AUTO_INCREMENT NOT NULL, title_en VARCHAR(255) NOT NULL, title_fr VARCHAR(255) NOT NULL, title_de VARCHAR(255) NOT NULL, title_nl VARCHAR(255) NULL, title_es VARCHAR(255) NULL, title_lc VARCHAR(255) DEFAULT 'Bd.Debrief_tasks' NULL, uses INT DEFAULT 0 NOT NULL, description_en VARCHAR(255) NOT NULL, description_fr VARCHAR(255) NOT NULL, description_de VARCHAR(255) NOT NULL, description_nl VARCHAR(255) NULL, description_es VARCHAR(255) NULL, description_lc VARCHAR(255) DEFAULT 'Bd.Debrief_tasks' NULL, specific_user INT DEFAULT 0 NOT NULL, contact_list_id INT NULL, user_assigned_id INT NULL, CONSTRAINT PK_DEBRIEF_TASKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-55
CREATE TABLE debrief_tasks_distributors (id INT AUTO_INCREMENT NOT NULL, debrief_task_id INT NOT NULL, distributor_id INT NOT NULL, CONSTRAINT PK_DEBRIEF_TASKS_DISTRIBUTORS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-56
CREATE TABLE debrief_tasks_garages (id INT AUTO_INCREMENT NOT NULL, debrief_task_id INT NOT NULL, garage_id INT NOT NULL, CONSTRAINT PK_DEBRIEF_TASKS_GARAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-57
CREATE TABLE debrief_topics (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Debrief_topics' NULL, uses INT DEFAULT 0 NOT NULL, CONSTRAINT PK_DEBRIEF_TOPICS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-58
CREATE TABLE delete_all (id INT AUTO_INCREMENT NOT NULL, id_to_delete INT NULL, last_deleted_id INT NULL, `table` VARCHAR(50) NULL, creation_date datetime(0) NOT NULL, modification_date datetime(0) NOT NULL, is_completed TINYINT(3) DEFAULT 0 NULL, CONSTRAINT PK_DELETE_ALL PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-59
CREATE TABLE dinner (id INT AUTO_INCREMENT NOT NULL, conferences_delegates_id INT NULL, weeks_id INT NULL, CONSTRAINT PK_DINNER PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-60
CREATE TABLE distance_units (id INT AUTO_INCREMENT NOT NULL, unit VARCHAR(255) NOT NULL, CONSTRAINT PK_DISTANCE_UNITS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-61
CREATE TABLE distributors (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NULL, account_number VARCHAR(255) NULL, abbreviation VARCHAR(255) NULL, head_office BIT(1) DEFAULT 0 NULL, parent_id INT NULL, client_type VARCHAR(10) NULL, subsidiary BIT(1) DEFAULT 0 NULL, aag_member BIT(1) DEFAULT 0 NULL, trading_as VARCHAR(255) NULL, phone VARCHAR(255) NULL, fax VARCHAR(255) NULL, email VARCHAR(255) NULL, language_id INT NULL, web VARCHAR(255) NULL, standalone TINYINT(3) NULL, start_date date NULL, end_date date NULL, address1 VARCHAR(255) NULL, address2 VARCHAR(255) NULL, address3 VARCHAR(255) NULL, address4 VARCHAR(255) NULL, latitude DECIMAL(30, 20) NULL, longitude DECIMAL(30, 20) NULL, town VARCHAR(255) NULL, province_id INT NULL, sales_area_id INT NULL, postcode VARCHAR(255) NULL, monday_open_1 VARCHAR(255) NULL, monday_closed_1 VARCHAR(255) NULL, monday_open_2 VARCHAR(255) NULL, monday_closed_2 VARCHAR(255) NULL, tuesday_open_1 VARCHAR(255) NULL, tuesday_closed_1 VARCHAR(255) NULL, tuesday_open_2 VARCHAR(255) NULL, tuesday_closed_2 VARCHAR(255) NULL, wednesday_open_1 VARCHAR(255) NULL, wednesday_closed_1 VARCHAR(255) NULL, wednesday_open_2 VARCHAR(255) NULL, wednesday_closed_2 VARCHAR(255) NULL, thursday_open_1 VARCHAR(255) NULL, thursday_closed_1 VARCHAR(255) NULL, thursday_open_2 VARCHAR(255) NULL, thursday_closed_2 VARCHAR(255) NULL, friday_open_1 VARCHAR(255) NULL, friday_closed_1 VARCHAR(255) NULL, friday_open_2 VARCHAR(255) NULL, friday_closed_2 VARCHAR(255) NULL, saturday_open_1 VARCHAR(255) NULL, saturday_closed_1 VARCHAR(255) NULL, saturday_open_2 VARCHAR(255) NULL, saturday_closed_2 VARCHAR(255) NULL, sunday_open_1 VARCHAR(255) NULL, sunday_closed_1 VARCHAR(255) NULL, sunday_open_2 VARCHAR(255) NULL, sunday_closed_2 VARCHAR(255) NULL, last_visit date NULL, reg_number VARCHAR(255) NULL COMMENT 'Only for the installation of UK', rebate_name VARCHAR(255) NULL, primary_activity_id INT NULL, association_id INT NULL, association_type_id INT NULL, currency VARCHAR(255) NULL, MAMID VARCHAR(255) NULL COMMENT 'Only for the installation of UK', VAT_number VARCHAR(255) NULL, detax_code VARCHAR(255) NULL COMMENT 'Only for the installation of France', siret VARCHAR(255) NULL COMMENT 'Only for the installation of France', credit_watch VARCHAR(255) NULL, trading_group_id INT NULL, distributor_id INT NULL COMMENT 'Parent Account', user_id INT NULL, creation_date datetime(0) NOT NULL, modification_date datetime(0) NOT NULL, distributor_type_id INT NULL, status INT NULL, aag_region_id INT NULL, CONSTRAINT PK_DISTRIBUTORS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-62
CREATE TABLE distributors_activities (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Distributors_activities' NULL, CONSTRAINT PK_DISTRIBUTORS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-63
CREATE TABLE distributors_activities_primary (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Distributors_activities_primary' NULL, CONSTRAINT PK_DISTRIBUTORS_ACTIVITIES_PRIMARY PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-64
CREATE TABLE distributors_comments (id INT AUTO_INCREMENT NOT NULL, distributor_id INT DEFAULT 0 NOT NULL, body LONGTEXT NOT NULL, creation_date datetime(0) NOT NULL, user_id INT DEFAULT 0 NULL, CONSTRAINT PK_DISTRIBUTORS_COMMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-65
CREATE TABLE distributors_contacts_bdm (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, contact_id INT NULL, updated_at datetime(0) NULL, created_at datetime(0) NULL, CONSTRAINT PK_DISTRIBUTORS_CONTACTS_BDM PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-66
CREATE TABLE distributors_contacts_general_branch_manager (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, contact_id INT NULL, CONSTRAINT PK_DISTRIBUTORS_CONTACTS_GENERAL_BRANCH_MANAGER PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-67
CREATE TABLE distributors_contacts_staff (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, contact_id INT NULL, CONSTRAINT PK_DISTRIBUTORS_CONTACTS_STAFF PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-68
CREATE TABLE distributors_contracts (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, trading_group_id INT NOT NULL, network_id INT NULL, distributor_network_id INT NULL, start_date date NOT NULL, end_date date NULL, leaving_reason VARCHAR(255) NULL, leaving_reason_id INT NULL, modification_date datetime(0) NOT NULL, CONSTRAINT PK_DISTRIBUTORS_CONTRACTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-69
CREATE TABLE distributors_customer_activities (id INT AUTO_INCREMENT NOT NULL, customer_activity_id INT NOT NULL, distributor_id INT NOT NULL, type BIT(1) DEFAULT 0 NULL COMMENT '0-Distributor / 1-Workshop', start_date date NULL, end_date date NULL, modification_date datetime(0) NULL, CONSTRAINT PK_DISTRIBUTORS_CUSTOMER_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-70
CREATE TABLE distributors_customer_activities_workshops (id INT AUTO_INCREMENT NOT NULL, distributor_customer_activity_id INT NOT NULL, workshop_activity_id INT NOT NULL, activity_details VARCHAR(255) NOT NULL, CONSTRAINT PK_DISTRIBUTORS_CUSTOMER_ACTIVITIES_WORKSHOPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-71
CREATE TABLE distributors_distributors_activities (id INT AUTO_INCREMENT NOT NULL, distributor_id INT DEFAULT 0 NOT NULL, distributor_activity_id INT DEFAULT 0 NOT NULL, join_date date NULL, left_date date NULL, CONSTRAINT PK_DISTRIBUTORS_DISTRIBUTORS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-72
CREATE TABLE distributors_distributors_networks (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, network_id INT NOT NULL, trading_group_id INT NULL, garage_number INT NULL, contract_start_date date NULL, contract_end_date date NULL, reason_leaving_id INT NULL, status VARCHAR(255) NULL, last BIT(1) DEFAULT 1 NULL, modification_date date NULL, CONSTRAINT PK_DISTRIBUTORS_DISTRIBUTORS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-73
CREATE TABLE distributors_figures (id INT AUTO_INCREMENT NOT NULL, customer_no INT DEFAULT 0 NOT NULL, figures MEDIUMTEXT NOT NULL, CONSTRAINT PK_DISTRIBUTORS_FIGURES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-74
CREATE TABLE distributors_figures_details (id INT AUTO_INCREMENT NOT NULL, customer_no INT DEFAULT 0 NOT NULL, figures_details MEDIUMTEXT NOT NULL, CONSTRAINT PK_DISTRIBUTORS_FIGURES_DETAILS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-75
CREATE TABLE distributors_images (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NOT NULL, principal BIT(1) DEFAULT 0 NULL COMMENT '1 for the main facade', CONSTRAINT PK_DISTRIBUTORS_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-76
CREATE TABLE distributors_kpis (id INT AUTO_INCREMENT NOT NULL, customer_no INT DEFAULT 0 NOT NULL, kpis MEDIUMTEXT NOT NULL, CONSTRAINT PK_DISTRIBUTORS_KPIS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-77
CREATE TABLE distributors_labels (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, label_type_id INT NOT NULL, start_date date NOT NULL, end_date date NULL, modification_date date NOT NULL, CONSTRAINT PK_DISTRIBUTORS_LABELS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-78
CREATE TABLE distributors_networks (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL COMMENT 'Image address of the network icon', image_pin VARCHAR(255) NOT NULL COMMENT 'Image of the network pin', image_cluster VARCHAR(255) NOT NULL COMMENT 'Image of the network cluster', web VARCHAR(255) NULL, network_type VARCHAR(255) NOT NULL, primary_color VARCHAR(255) NULL, primary_font_color VARCHAR(255) NULL, primary_background_color VARCHAR(255) NULL, secondary_color VARCHAR(255) NULL, secondary_font_color VARCHAR(255) NULL, secondary_background_color VARCHAR(255) NULL, color_active VARCHAR(255) NULL, tertiary_color VARCHAR(255) NULL, menu_color VARCHAR(255) NULL, menu_background_color VARCHAR(255) NULL, color_exito VARCHAR(255) NULL, color_fallo VARCHAR(255) NULL, color_informacion VARCHAR(255) NULL, color_disabled VARCHAR(255) NULL, creation_date date NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_DISTRIBUTORS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-79
CREATE TABLE distributors_networks_contacts_bdm (id INT AUTO_INCREMENT NOT NULL, distributor_network_id INT NOT NULL, contact_id INT NOT NULL, distributor_id INT NULL, garage_id INT NULL, CONSTRAINT PK_DISTRIBUTORS_NETWORKS_CONTACTS_BDM PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-80
CREATE TABLE distributors_objectives (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, objective_id INT NOT NULL, `from` date NULL, `to` date NULL, creation_date datetime(0) NOT NULL, CONSTRAINT PK_DISTRIBUTORS_OBJECTIVES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-81
CREATE TABLE distributors_routes (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, route_id INT NOT NULL, `order` VARCHAR(50) NULL, start_time VARCHAR(50) NULL, end_time VARCHAR(50) NULL, CONSTRAINT PK_DISTRIBUTORS_ROUTES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-82
CREATE TABLE distributors_services (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, service_type_id INT NOT NULL, start_date date NOT NULL, end_date date NULL, modification_date datetime(0) NOT NULL, CONSTRAINT PK_DISTRIBUTORS_SERVICES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-83
CREATE TABLE distributors_software (id INT AUTO_INCREMENT NOT NULL, distributor_id INT NOT NULL, software_id INT NOT NULL, software_type_id INT NULL, supplier_id INT NULL, software_manufacture_id INT NULL, start_date date NULL, modification_date date NULL, end_date date NULL, version VARCHAR(255) NULL, username VARCHAR(255) NULL, password VARCHAR(255) NULL, CONSTRAINT PK_DISTRIBUTORS_SOFTWARE PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-84
CREATE TABLE distributors_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Distributors_types' NULL, CONSTRAINT PK_DISTRIBUTORS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-85
CREATE TABLE email_types (id INT AUTO_INCREMENT NOT NULL, guid VARCHAR(36) NULL, name_en VARCHAR(50) NOT NULL, name_fr VARCHAR(50) NOT NULL, name_de VARCHAR(50) NOT NULL, name_nl VARCHAR(50) NULL, name_es VARCHAR(50) NULL, name_lc VARCHAR(50) DEFAULT 'Bd.Email_types' NULL, active BIT(1) DEFAULT 1 NULL, sendgrid_license_config_id INT NULL, external BIT(1) DEFAULT 0 NULL, CONSTRAINT PK_EMAIL_TYPES PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-86
CREATE TABLE emails (id INT AUTO_INCREMENT NOT NULL, guid VARCHAR(36) NULL, from_name VARCHAR(255) NULL, `from` VARCHAR(255) NULL, `to` VARCHAR(255) NULL, cc VARCHAR(255) NULL, bcc VARCHAR(255) NULL, subject VARCHAR(255) NULL, body MEDIUMTEXT NULL, sent BIT(1) NULL, retries INT DEFAULT 0 NULL, notification_error BIT(1) DEFAULT 0 NULL, last_error_message LONGTEXT NULL, email_type_id INT NOT NULL, view_vars LONGTEXT NULL, template LONGTEXT NULL, attachments LONGTEXT NULL, creation_date datetime(0) NOT NULL, platform_id INT NULL, language_id INT NULL, language_web_id INT NULL, country_id INT NULL, old BIT(1) DEFAULT 0 NULL, aag_region_id INT NULL, CONSTRAINT PK_EMAILS PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-87
CREATE TABLE emails_daily (my_row_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL, id INT NOT NULL, `from` VARCHAR(255) NULL, `to` VARCHAR(255) NULL, contact_id INT NOT NULL, cc VARCHAR(255) NULL, bcc VARCHAR(255) NULL, subject VARCHAR(255) NULL, body MEDIUMTEXT NULL, sent BIT(1) NULL, type INT NULL, action INT NULL, view_vars MEDIUMTEXT NULL, attachments MEDIUMTEXT NULL, creation_date datetime(0) NOT NULL, CONSTRAINT PK_EMAILS_DAILY PRIMARY KEY (my_row_id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-88
CREATE TABLE employee_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Employee_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_EMPLOYEE_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-89
CREATE TABLE enquiries (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, garage_id INT NOT NULL, child_network_id INT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, `description` LONGTEXT NOT NULL, creation_date datetime(0) NOT NULL, answer LONGTEXT NULL, answered BIT(1) DEFAULT 0 NOT NULL, date_answered datetime(0) NULL, plate VARCHAR(255) NULL, vin VARCHAR(255) NULL, brand VARCHAR(255) NULL, model VARCHAR(255) NULL, version VARCHAR(255) NULL, mot_exp_date date NULL, fuel VARCHAR(255) NULL, registered_on date NULL, mileage INT NULL, work_id INT NULL, garage_url VARCHAR(255) NULL, phone VARCHAR(255) NULL, marketing_acceptance BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_ENQUIRIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-90
CREATE TABLE equipments (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Equipments' NULL, aag_region_id INT NULL, CONSTRAINT PK_EQUIPMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-91
CREATE TABLE equipments_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Equipments_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_EQUIPMENTS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-92
CREATE TABLE erp (id INT AUTO_INCREMENT NOT NULL, erp_code VARCHAR(255) NULL, aag_region_id INT NULL, CONSTRAINT PK_ERP PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-93
CREATE TABLE facilities (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Facilities' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_FACILITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-94
CREATE TABLE fleets (id INT AUTO_INCREMENT NOT NULL, guid VARCHAR(50) DEFAULT '' NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NULL, postcode VARCHAR(255) NULL, country_id INT NULL, province_id INT NULL, erp_id INT NOT NULL, ref_code VARCHAR(255) NULL, payment_terms VARCHAR(255) NULL, tax_code VARCHAR(255) NULL, company_code VARCHAR(255) NOT NULL, company_name VARCHAR(255) NULL, aag_region_id INT NOT NULL, creation_date datetime(0) NOT NULL, modification_date datetime(0) NOT NULL, active BIT(1) DEFAULT 0 NOT NULL, repairmaintenance BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_FLEETS PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-95
CREATE TABLE fleets_networks (id INT AUTO_INCREMENT NOT NULL, fleet_id INT NOT NULL, network_id INT NOT NULL, creation_date datetime(0) NULL, CONSTRAINT PK_FLEETS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-96
CREATE TABLE fluids (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, code VARCHAR(255) NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, price DECIMAL(10, 2) NULL, parent_code INT NULL, has_advanced_settings BIT(1) DEFAULT 0 NULL, active BIT(1) NOT NULL, CONSTRAINT PK_FLUIDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-97
CREATE TABLE garages (id INT AUTO_INCREMENT NOT NULL, repairmaintenance TINYINT(3) NULL, garage_code VARCHAR(50) NULL, guid VARCHAR(255) NULL, g_number_id VARCHAR(255) NULL, ref_code VARCHAR(255) NULL, rob_code VARCHAR(50) NULL, erp_id INT NULL, creditor_number VARCHAR(255) NULL COMMENT 'Used for garages in R&M as a secondary erp code', payment_terms VARCHAR(255) NULL COMMENT 'Used in R&M for invoicing', company_code VARCHAR(255) NULL COMMENT 'Used in R&M for invoicing', business_name VARCHAR(255) NOT NULL, name VARCHAR(255) NULL, slug VARCHAR(255) NULL, email VARCHAR(255) NULL, language_id INT NULL, comment VARCHAR(255) NULL, client_type VARCHAR(10) NULL, VAT_code VARCHAR(255) NULL, is_cv BIT(1) DEFAULT 0 NOT NULL, siret VARCHAR(255) NULL COMMENT 'Only for the installation of France', detax_code VARCHAR(255) NULL COMMENT 'Only for the installation of France', sales_area_id INT NULL, insurance_agreement_id INT NULL, leaving_date date NULL COMMENT 'Fecha de baja', reason_leaving_date VARCHAR(255) NULL, status VARCHAR(255) NULL, affiliation_assembly BIT(1) NULL, documents_legal VARCHAR(255) NULL, diesel_liability VARCHAR(255) NULL, turnover_id VARCHAR(255) NULL, flat_rate VARCHAR(255) NULL, address1 VARCHAR(255) NULL, address2 VARCHAR(255) NULL, address3 VARCHAR(255) NULL, address4 VARCHAR(255) NULL, latitude DECIMAL(30, 20) NULL, longitude DECIMAL(30, 20) NULL, town VARCHAR(255) NULL, province_id INT NULL, postcode VARCHAR(255) NULL, phone VARCHAR(255) NULL, mobile VARCHAR(255) NULL, phone_international VARCHAR(255) NULL, fax VARCHAR(255) NULL, web VARCHAR(255) NULL, service_24h_phone VARCHAR(255) NULL, monday_open_1 VARCHAR(255) NULL, monday_closed_1 VARCHAR(255) NULL, monday_open_2 VARCHAR(255) NULL, monday_closed_2 VARCHAR(255) NULL, tuesday_open_1 VARCHAR(255) NULL, tuesday_closed_1 VARCHAR(255) NULL, tuesday_open_2 VARCHAR(255) NULL, tuesday_closed_2 VARCHAR(255) NULL, wednesday_open_1 VARCHAR(255) NULL, wednesday_closed_1 VARCHAR(255) NULL, wednesday_open_2 VARCHAR(255) NULL, wednesday_closed_2 VARCHAR(255) NULL, thursday_open_1 VARCHAR(255) NULL, thursday_closed_1 VARCHAR(255) NULL, thursday_open_2 VARCHAR(255) NULL, thursday_closed_2 VARCHAR(255) NULL, friday_open_1 VARCHAR(255) NULL, friday_closed_1 VARCHAR(255) NULL, friday_open_2 VARCHAR(255) NULL, friday_closed_2 VARCHAR(255) NULL, saturday_open_1 VARCHAR(255) NULL, saturday_closed_1 VARCHAR(255) NULL, saturday_open_2 VARCHAR(255) NULL, saturday_closed_2 VARCHAR(255) NULL, sunday_open_1 VARCHAR(255) NULL, sunday_closed_1 VARCHAR(255) NULL, sunday_open_2 VARCHAR(255) NULL, sunday_closed_2 VARCHAR(255) NULL, visit_frequency INT NULL, visit_monday TINYINT(3) NULL, visit_tuesday TINYINT(3) NULL, visit_wednesday TINYINT(3) NULL, visit_thursday TINYINT(3) NULL, visit_friday TINYINT(3) NULL, last_visit date NULL, ramps INT NULL, MOT_bays INT NULL, technician INT NULL, spend_this_month VARCHAR(255) NULL, spend_last_month VARCHAR(255) NULL, spend_12_month VARCHAR(255) NULL, spend_projected VARCHAR(255) NULL, foundation_year VARCHAR(255) NULL COMMENT 'Years running', lead_source VARCHAR(255) NULL, marketing_email VARCHAR(255) NULL, interests VARCHAR(255) NULL, user_id INT NULL, creation_date datetime(0) NOT NULL, modification_date datetime(0) NOT NULL, fleet_work_direction TINYINT(3) NULL, fleet_mot DECIMAL(10, 2) NULL, fleet_labour_rate DECIMAL(10, 2) NULL, long_life_oil_price_b2b DECIMAL(10, 2) NULL, standard_oil_price_b2b DECIMAL(10, 2) NULL, collection_delivery_b2b TINYINT(3) NULL, retail_mot DECIMAL(10, 2) NULL, retail_labour_rate DECIMAL(10, 2) NULL, long_life_oil_price_b2c DECIMAL(10, 2) NULL, standard_oil_price_b2c DECIMAL(10, 2) NULL, collection_delivery_b2c TINYINT(3) NULL, ev_charge_points INT DEFAULT 0 NOT NULL, kwh_charging_retail_price DECIMAL(10, 2) NULL, kwh_charging_fleet_price DECIMAL(10, 2) NULL, ev_ppe_audited_date date NULL, city_id INT NULL, courtesy_car TINYINT(3) DEFAULT 0 NULL, aag_region_id INT NULL, manually_created BIT(1) DEFAULT 0 NOT NULL, campaign_entries INT NULL, erp_email VARCHAR(255) NULL, recommended_network BIT(1) DEFAULT 0 NULL, CONSTRAINT PK_GARAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-98
CREATE TABLE garages_agreements (id INT AUTO_INCREMENT NOT NULL, garage_id INT NULL, parent_acct VARCHAR(50) NULL, fleet_agreement VARCHAR(50) NULL, fleet_reference VARCHAR(50) NULL, agreement_code VARCHAR(50) NULL, garage_ref VARCHAR(50) NULL, agreement_id INT NULL, creation_date datetime(0) NOT NULL, contract_sent_date date NULL, contract_received_date date NULL, contract_start_date date NULL, contract_end_date date NULL, date_on_hold date NULL, reason_on_hold VARCHAR(255) NULL, reason_leaving VARCHAR(255) NULL, leaving_date date NULL, status VARCHAR(255) NULL, reason_leaving_id INT NULL, reason_hold_id INT NULL, CONSTRAINT PK_GARAGES_AGREEMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-99
CREATE TABLE garages_b2b_postcodes (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, postcode_id INT NOT NULL, CONSTRAINT PK_GARAGES_B2B_POSTCODES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-100
CREATE TABLE garages_b2c_postcodes (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, postcode_id INT NOT NULL, CONSTRAINT PK_GARAGES_B2C_POSTCODES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-101
CREATE TABLE garages_brands (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, brand_id INT NOT NULL, CONSTRAINT PK_GARAGES_BRANDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-102
CREATE TABLE garages_campaign (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, garage_campaign_id INT NOT NULL, number INT NULL, CONSTRAINT PK_GARAGES_CAMPAIGN PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-103
CREATE TABLE garages_comments (id INT AUTO_INCREMENT NOT NULL, garage_id INT DEFAULT 0 NOT NULL, body LONGTEXT NOT NULL, creation_date datetime(0) NOT NULL, user_id INT DEFAULT 0 NULL, created_by_name VARCHAR(255) NULL, CONSTRAINT PK_GARAGES_COMMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-104
CREATE TABLE garages_contacts_bdm (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, contact_id INT NULL, principal BIT(1) DEFAULT 0 NULL, `order` INT NULL, CONSTRAINT PK_GARAGES_CONTACTS_BDM PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-105
CREATE TABLE garages_contacts_general_branch_manager (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, contact_id INT NULL, CONSTRAINT PK_GARAGES_CONTACTS_GENERAL_BRANCH_MANAGER PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-106
CREATE TABLE garages_contacts_lists (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, contact_list_id INT NOT NULL, CONSTRAINT PK_GARAGES_CONTACTS_LISTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-107
CREATE TABLE garages_contacts_staff (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, contact_id INT NULL, interest LONGTEXT NULL, priority TINYINT(3) DEFAULT 0 NULL, main_contact BIT(1) NULL, CONSTRAINT PK_GARAGES_CONTACTS_STAFF PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-108
CREATE TABLE garages_courtesy_car_types (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, courtesy_car_type_id INT NOT NULL, CONSTRAINT PK_GARAGES_COURTESY_CAR_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-109
CREATE TABLE garages_customers_activities (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, customer_activity_id INT NOT NULL, `order` VARCHAR(255) NOT NULL, CONSTRAINT PK_GARAGES_CUSTOMERS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-110
CREATE TABLE garages_distributors (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, distributor_id INT NOT NULL, principal BIT(1) DEFAULT 0 NULL, `order` INT NULL, CONSTRAINT PK_GARAGES_DISTRIBUTORS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-111
CREATE TABLE garages_distributors_shortcuts (id INT AUTO_INCREMENT NOT NULL, garage_id INT NULL, distributor_id INT NULL, shortcut_id INT NOT NULL, network_id INT NULL, user_id INT NULL, parameter_value_1 VARCHAR(255) NULL, parameter_value_2 VARCHAR(255) NULL, fav BIT(1) DEFAULT 0 NULL, creation_date date NOT NULL, CONSTRAINT PK_GARAGES_DISTRIBUTORS_SHORTCUTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-112
CREATE TABLE garages_employees (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, employee_type_id INT NOT NULL, number INT NULL, CONSTRAINT PK_GARAGES_EMPLOYEES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-113
CREATE TABLE garages_equipments (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, equipment_id INT NOT NULL, equipment_type_id INT NULL, supplier_id INT NULL, brand_id INT NULL, billing_schedule_id INT NULL, amount DECIMAL(11, 2) NULL, member_pay INT NULL, garage_pay INT NULL, billed_by_aag BIT(1) NULL, start_date date NULL, end_date date NULL, CONSTRAINT PK_GARAGES_EQUIPMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-114
CREATE TABLE garages_facilities (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, facility_id INT NOT NULL, CONSTRAINT PK_GARAGES_FACILITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-115
CREATE TABLE garages_figures (id INT AUTO_INCREMENT NOT NULL, customer_no INT DEFAULT 0 NOT NULL, figures LONGTEXT NOT NULL, CONSTRAINT PK_GARAGES_FIGURES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-116
CREATE TABLE garages_figures_details (id INT AUTO_INCREMENT NOT NULL, customer_no INT DEFAULT 0 NOT NULL, figures_details LONGTEXT NOT NULL, CONSTRAINT PK_GARAGES_FIGURES_DETAILS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-117
CREATE TABLE garages_files (id INT AUTO_INCREMENT NOT NULL, garage_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NOT NULL, source_name VARCHAR(255) NOT NULL, CONSTRAINT PK_GARAGES_FILES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-118
CREATE TABLE garages_images (id INT AUTO_INCREMENT NOT NULL, garage_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NOT NULL, principal BIT(1) DEFAULT 0 NULL COMMENT 'A 1 para la fachada principal', web BIT(1) DEFAULT 0 NULL COMMENT 'A 1 se ve en el frame o web', CONSTRAINT PK_GARAGES_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-119
CREATE TABLE garages_kpis (id INT AUTO_INCREMENT NOT NULL, customer_no INT DEFAULT 0 NOT NULL, kpis LONGTEXT NOT NULL, CONSTRAINT PK_GARAGES_KPIS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-120
CREATE TABLE garages_networks (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, network_id INT NOT NULL, trading_group_id INT NULL, supplier_id INT NULL, reason_leaving_id INT NULL, reason_hold_id INT NULL, network_contract_type_id INT NULL, garage_number INT NULL, contract_sent_date date NULL, contract_received_date date NULL, contract_start_date date NULL, date_on_hold date NULL, reason_on_hold VARCHAR(255) NULL, contract_end_date date NULL, reason_leaving VARCHAR(255) NULL, leaving_date date NULL, status VARCHAR(255) NULL, last BIT(1) DEFAULT 1 NULL, dd_active BIT(1) NULL, annex_detail_id INT NULL, creation_date date NULL, modification_date date NULL, credit INT NULL, default_credit INT NULL, quoting_views_active BIT(1) DEFAULT 0 NULL, quoting_active BIT(1) DEFAULT 0 NULL, enquiries_active BIT(1) DEFAULT 0 NULL, about LONGTEXT NULL, monday_planner_max_1 INT NULL, monday_planner_max_2 INT NULL, tuesday_planner_max_1 INT NULL, tuesday_planner_max_2 INT NULL, wednesday_planner_max_1 INT NULL, wednesday_planner_max_2 INT NULL, thursday_planner_max_1 INT NULL, thursday_planner_max_2 INT NULL, friday_planner_max_1 INT NULL, friday_planner_max_2 INT NULL, saturday_planner_max_1 INT NULL, saturday_planner_max_2 INT NULL, sunday_planner_max_1 INT NULL, sunday_planner_max_2 INT NULL, monday_planner_open_1 VARCHAR(255) NULL, monday_planner_closed_1 VARCHAR(255) NULL, monday_planner_open_2 VARCHAR(255) NULL, monday_planner_closed_2 VARCHAR(255) NULL, tuesday_planner_open_1 VARCHAR(255) NULL, tuesday_planner_closed_1 VARCHAR(255) NULL, tuesday_planner_open_2 VARCHAR(255) NULL, tuesday_planner_closed_2 VARCHAR(255) NULL, wednesday_planner_open_1 VARCHAR(255) NULL, wednesday_planner_closed_1 VARCHAR(255) NULL, wednesday_planner_open_2 VARCHAR(255) NULL, wednesday_planner_closed_2 VARCHAR(255) NULL, thursday_planner_open_1 VARCHAR(255) NULL, thursday_planner_closed_1 VARCHAR(255) NULL, thursday_planner_open_2 VARCHAR(255) NULL, thursday_planner_closed_2 VARCHAR(255) NULL, friday_planner_open_1 VARCHAR(255) NULL, friday_planner_closed_1 VARCHAR(255) NULL, friday_planner_open_2 VARCHAR(255) NULL, friday_planner_closed_2 VARCHAR(255) NULL, saturday_planner_open_1 VARCHAR(255) NULL, saturday_planner_closed_1 VARCHAR(255) NULL, saturday_planner_open_2 VARCHAR(255) NULL, saturday_planner_closed_2 VARCHAR(255) NULL, sunday_planner_open_1 VARCHAR(255) NULL, sunday_planner_closed_1 VARCHAR(255) NULL, sunday_planner_open_2 VARCHAR(255) NULL, sunday_planner_closed_2 VARCHAR(255) NULL, booking_days_min_from INT DEFAULT 0 NOT NULL, booking_days_max_to INT DEFAULT 30 NOT NULL, code VARCHAR(255) NULL, sap_code VARCHAR(255) NULL, recommended BIT(1) DEFAULT 0 NULL, location_id VARCHAR(255) NULL, kiyoh_api_key VARCHAR(255) NULL, rating DECIMAL(10, 2) NULL, reviews_number INT NULL, reviews_info LONGTEXT NULL, usp1 VARCHAR(50) NULL, usp2 VARCHAR(50) NULL, usp3 VARCHAR(50) NULL, dealer_discount DECIMAL(10, 2) NULL, dealer_surcharge DECIMAL(10, 2) NULL, dealer_markup DECIMAL(10, 2) NULL, labour_hourly_price DECIMAL(10, 2) NULL, labour_hourly_price_electric_vehicles DECIMAL(10, 2) NULL, current_charge DECIMAL(10, 2) NULL, member_pays DECIMAL(10, 2) NULL, garage_pays DECIMAL(10, 2) NULL, imported TINYINT(3) DEFAULT 0 NOT NULL, `loop` BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-121
CREATE TABLE garages_networks_contacts (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NULL, contact_id INT NULL, CONSTRAINT PK_GARAGES_NETWORKS_CONTACTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-122
CREATE TABLE garages_networks_fluids (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, fluid_id INT NOT NULL, price DECIMAL(10, 2) NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_FLUIDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-123
CREATE TABLE garages_networks_genarts (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, genart_id INT NOT NULL, discount DECIMAL(10, 2) NULL, markup DECIMAL(10, 2) NULL, surcharge DECIMAL(10, 2) NULL, is_labour_price FLOAT(10, 2) NULL, CONSTRAINT PK_GARAGES_NETWORKS_GENARTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-124
CREATE TABLE garages_networks_genarts_families (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, genart_family_id INT NULL, discount FLOAT(10, 2) NULL, markup FLOAT(10, 2) NULL, surcharge FLOAT(10, 2) NULL, CONSTRAINT PK_GARAGES_NETWORKS_GENARTS_FAMILIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-125
CREATE TABLE garages_networks_genarts_master (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, genart_master_id INT NOT NULL, genart_master_labour_price FLOAT(10, 2) NULL, CONSTRAINT PK_GARAGES_NETWORKS_GENARTS_MASTER PRIMARY KEY (id)) COMMENT='This table stores per garage every default price for genarts with is_labour_time 1';

-- changeset LisaCanéSáizSoftecaI:1756821087342-126
CREATE TABLE garages_networks_images (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NOT NULL, principal BIT(1) NULL, web BIT(1) NULL, optimized BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-127
CREATE TABLE garages_networks_services (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, service_id INT NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_SERVICES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-128
CREATE TABLE garages_networks_services_drivers (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, service_driver_id INT NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_SERVICES_DRIVERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-129
CREATE TABLE garages_networks_vehicle_types (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, vehicle_type_id INT NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_VEHICLE_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-130
CREATE TABLE garages_networks_vehicles (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, vehicle_id INT NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_VEHICLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-131
CREATE TABLE garages_networks_vehicles_black_list (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, vehicle_id INT NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_VEHICLES_BLACK_LIST PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-132
CREATE TABLE garages_networks_works (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, work_id INT NOT NULL, CONSTRAINT PK_GARAGES_NETWORKS_WORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-133
CREATE TABLE garages_networks_works_labours (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, work_id INT NOT NULL, labour_hourly_price DECIMAL(10, 2) NULL, labour_hourly_price_electric_vehicles DECIMAL(10, 2) NULL, CONSTRAINT PK_GARAGES_NETWORKS_WORKS_LABOURS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-134
CREATE TABLE garages_networks_works_prices (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, work_id INT NOT NULL, discount DECIMAL(10, 2) NULL, markup DECIMAL(10, 2) NULL, surcharge DECIMAL(10, 2) NULL, CONSTRAINT PK_GARAGES_NETWORKS_WORKS_PRICES PRIMARY KEY (id)) COMMENT='For works type dealer';

-- changeset LisaCanéSáizSoftecaI:1756821087342-135
CREATE TABLE garages_oils (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, oil_id INT NOT NULL, CONSTRAINT PK_GARAGES_OILS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-136
CREATE TABLE garages_products (id INT AUTO_INCREMENT NOT NULL, order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, CONSTRAINT PK_GARAGES_PRODUCTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-137
CREATE TABLE garages_routes (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, route_id INT NOT NULL, `order` VARCHAR(50) NULL, start_time VARCHAR(50) NULL, end_time VARCHAR(50) NULL, CONSTRAINT PK_GARAGES_ROUTES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-138
CREATE TABLE garages_services (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, service_id INT NOT NULL, CONSTRAINT PK_GARAGES_SERVICES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-139
CREATE TABLE garages_software (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, software_id INT NOT NULL, software_type_id INT NULL, supplier_id INT NULL, software_manufacture_id INT NULL, billing_schedule_id INT NULL, amount DECIMAL(11, 2) NULL, member_pay INT NULL, garage_pay INT NULL, billed_by_aag BIT(1) NULL, start_date date NULL, end_date date NULL, version VARCHAR(255) NULL, username VARCHAR(255) NULL, password VARCHAR(255) NULL, number_subscription INT DEFAULT 0 NULL, online_ordering BIT(1) NULL, CONSTRAINT PK_GARAGES_SOFTWARE PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-140
CREATE TABLE garages_specialist_makes (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, vehicle_id INT NOT NULL, CONSTRAINT PK_GARAGES_SPECIALIST_MAKES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-141
CREATE TABLE garages_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_GARAGES_STATUSES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-142
CREATE TABLE garages_value_add_supplier (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, value_add_supplier_id INT NOT NULL, value_add_supplier_type_id INT NOT NULL, to_date date NULL, from_date date NULL, CONSTRAINT PK_GARAGES_VALUE_ADD_SUPPLIER PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-143
CREATE TABLE garages_values_adds (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, value_add_id INT NOT NULL, start_date date NULL, end_date date NULL, version VARCHAR(255) NULL, billing_schedule_id INT NULL, amount INT NULL, member_pay INT NULL, garage_pay INT NULL, billed_by_aag BIT(1) NULL, number_subscription INT DEFAULT 0 NULL, online_ordering BIT(1) NULL, CONSTRAINT PK_GARAGES_VALUES_ADDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-144
CREATE TABLE garages_vehicle_types (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, vehicle_type_id INT NOT NULL, CONSTRAINT PK_GARAGES_VEHICLE_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-145
CREATE TABLE garages_vehicles (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, vehicle_id INT NOT NULL, CONSTRAINT PK_GARAGES_VEHICLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-146
CREATE TABLE garages_visit_frequencies (id INT AUTO_INCREMENT NOT NULL, name MEDIUMTEXT NOT NULL, CONSTRAINT PK_GARAGES_VISIT_FREQUENCIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-147
CREATE TABLE garages_websites (id INT AUTO_INCREMENT NOT NULL, garage_id INT NOT NULL, website_id INT NOT NULL, url VARCHAR(255) NOT NULL, tagline VARCHAR(255) NOT NULL, `description` VARCHAR(255) NOT NULL, CONSTRAINT PK_GARAGES_WEBSITES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-148
CREATE TABLE garages_workshop_activities (id INT AUTO_INCREMENT NOT NULL, garage_id INT NULL, workshop_activity_id INT NULL, CONSTRAINT PK_GARAGES_WORKSHOP_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-149
CREATE TABLE genarts (id INT AUTO_INCREMENT NOT NULL, grouping_genart_id INT NOT NULL, code VARCHAR(255) NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, include_vat INT DEFAULT 0 NULL, is_labour_time INT DEFAULT 0 NULL, price_labour_time FLOAT(10, 2) NULL, discount DECIMAL(10, 2) NULL, markup DECIMAL(10, 2) NULL, surcharge DECIMAL(10, 2) NULL, active BIT(1) NOT NULL, CONSTRAINT PK_GENARTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-150
CREATE TABLE genarts_families (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(50) NOT NULL, name_es VARCHAR(50) NULL, name_fr VARCHAR(50) NULL, name_nl VARCHAR(50) NULL, name_de VARCHAR(50) NULL, active BIT(1) DEFAULT 1 NULL, network_id INT NOT NULL, CONSTRAINT PK_GENARTS_FAMILIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-151
CREATE TABLE genarts_master (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, include_vat INT DEFAULT 0 NULL, is_labour_time INT DEFAULT 0 NULL, price_labour_time FLOAT(12, 2) NULL, min_labour_price FLOAT(10, 2) NULL, max_labour_price FLOAT(10, 2) NULL, slider_increment FLOAT(10, 2) NULL, network_id INT NOT NULL, genart_family_id INT NULL, active BIT(1) DEFAULT 1 NULL, CONSTRAINT PK_GENARTS_MASTER PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-152
CREATE TABLE general_manager_widgets_roles (id INT AUTO_INCREMENT NOT NULL, widget_id INT NOT NULL, role_id INT NOT NULL, `order` INT NOT NULL, row INT DEFAULT 1 NOT NULL, col INT DEFAULT 1 NOT NULL, size_x INT DEFAULT 1 NOT NULL, CONSTRAINT PK_GENERAL_MANAGER_WIDGETS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-153
CREATE TABLE grouping_genarts (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, code VARCHAR(255) NOT NULL, CONSTRAINT PK_GROUPING_GENARTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-154
CREATE TABLE groupings_permissions (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Groupings_permissions' NULL, active BIT(1) DEFAULT 1 NOT NULL, CONSTRAINT PK_GROUPINGS_PERMISSIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-155
CREATE TABLE groups_permissions (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Groups_permissions' NULL, position_config_type_id INT NOT NULL, is_fixed BIT(1) DEFAULT 0 NOT NULL, active BIT(1) DEFAULT 1 NOT NULL, CONSTRAINT PK_GROUPS_PERMISSIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-156
CREATE TABLE groups_permissions_permissions (id INT AUTO_INCREMENT NOT NULL, group_permission_id INT NOT NULL, permission_id INT NOT NULL, CONSTRAINT PK_GROUPS_PERMISSIONS_PERMISSIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-157
CREATE TABLE groups_permissions_roles (id INT AUTO_INCREMENT NOT NULL, group_permission_id INT NOT NULL, role_id INT NOT NULL, CONSTRAINT PK_GROUPS_PERMISSIONS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-158
CREATE TABLE groups_permissions_users (id INT AUTO_INCREMENT NOT NULL, group_permission_id INT NOT NULL, user_id INT NOT NULL, CONSTRAINT PK_GROUPS_PERMISSIONS_USERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-159
CREATE TABLE hold_reason_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Hold_reason_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_HOLD_REASON_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-160
CREATE TABLE insurance_agreements (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_INSURANCE_AGREEMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-161
CREATE TABLE labels_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_LABELS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-162
CREATE TABLE languages (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(2) NOT NULL COMMENT 'Code to identify the country', name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Languages' NULL, image VARCHAR(50) NULL, CONSTRAINT PK_LANGUAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-163
CREATE TABLE languages_webs_flags (id INT AUTO_INCREMENT NOT NULL, language_code VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, creation_date date NULL, CONSTRAINT PK_LANGUAGES_WEBS_FLAGS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-164
CREATE TABLE languages_webs_networks (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, language_web_flag_id INT NOT NULL, code VARCHAR(10) NOT NULL, name VARCHAR(255) NOT NULL, is_default BIT(1) DEFAULT 0 NOT NULL, active BIT(1) DEFAULT 0 NOT NULL, creation_date date NULL, CONSTRAINT PK_LANGUAGES_WEBS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-165
CREATE TABLE leaving_reason_comments (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NOT NULL, comment VARCHAR(255) NOT NULL, date datetime(0) NOT NULL, CONSTRAINT PK_LEAVING_REASON_COMMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-166
CREATE TABLE leaving_reason_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Leaving_reason_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_LEAVING_REASON_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-167
CREATE TABLE lists (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, network_id INT NULL, aag_region_id INT NULL, CONSTRAINT PK_LISTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-168
CREATE TABLE logistic_centers (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Logistics_centers' NULL, CONSTRAINT PK_LOGISTIC_CENTERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-169
CREATE TABLE logs_apis (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_email VARCHAR(50) NULL, ip VARCHAR(50) NOT NULL, date datetime(0) NOT NULL, uri VARCHAR(50) NOT NULL, request LONGTEXT NULL, response LONGTEXT NULL, notes LONGTEXT NULL, CONSTRAINT PK_LOGS_APIS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-170
CREATE TABLE logs_changes (id INT AUTO_INCREMENT NOT NULL, table_id INT NOT NULL, field_id INT NOT NULL, user_id INT NOT NULL, garage_id INT NULL, distributor_id INT NULL, contact VARCHAR(50) NULL, group_permission_id INT NULL, position_id INT NULL, date datetime(0) NOT NULL, old_value VARCHAR(255) NOT NULL, new_value VARCHAR(255) NOT NULL, CONSTRAINT PK_LOGS_CHANGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-171
CREATE TABLE logs_fields (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, table_id INT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Logs_fields' NULL, CONSTRAINT PK_LOGS_FIELDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-172
CREATE TABLE logs_login (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, error VARCHAR(255) NULL, login BIT(1) NULL, date datetime(0) NOT NULL, CONSTRAINT PK_LOGS_LOGIN PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-173
CREATE TABLE logs_tables (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Logs_tables' NULL, CONSTRAINT PK_LOGS_TABLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-174
CREATE TABLE messages (id INT AUTO_INCREMENT NOT NULL, subject VARCHAR(255) NOT NULL, body TEXT NOT NULL, date_sent datetime(0) NULL, creation_date datetime(0) NOT NULL, type INT DEFAULT 0 NOT NULL, user_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_MESSAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-175
CREATE TABLE messages_distributors (id INT AUTO_INCREMENT NOT NULL, message_id INT DEFAULT 0 NOT NULL, distributor_id INT DEFAULT 0 NOT NULL, date_read datetime(0) NULL, CONSTRAINT PK_MESSAGES_DISTRIBUTORS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-176
CREATE TABLE messages_files (id INT AUTO_INCREMENT NOT NULL, message_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NOT NULL, source_name VARCHAR(255) NOT NULL, CONSTRAINT PK_MESSAGES_FILES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-177
CREATE TABLE messages_garages (id INT AUTO_INCREMENT NOT NULL, message_id INT DEFAULT 0 NOT NULL, garage_id INT DEFAULT 0 NOT NULL, date_read datetime(0) NULL, CONSTRAINT PK_MESSAGES_GARAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-178
CREATE TABLE messages_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Messages_types' NULL, CONSTRAINT PK_MESSAGES_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-179
CREATE TABLE monitor (id INT UNSIGNED AUTO_INCREMENT NOT NULL, CONSTRAINT PK_MONITOR PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-180
CREATE TABLE network_cities (id INT AUTO_INCREMENT NOT NULL, network_id INT NULL, city_id INT NULL, CONSTRAINT PK_NETWORK_CITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-181
CREATE TABLE networks (id INT AUTO_INCREMENT NOT NULL, quoting_type_id INT NULL, pricing_type_id INT NULL, parent_network_id INT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL COMMENT 'Image address of the network icon', image_pin VARCHAR(255) NOT NULL COMMENT 'Image of the network pin', image_cluster VARCHAR(255) NOT NULL COMMENT 'Image of the network cluster', web VARCHAR(255) NULL, network_type VARCHAR(255) NOT NULL, primary_color VARCHAR(255) NULL, primary_font_color VARCHAR(255) NULL, primary_background_color VARCHAR(255) NULL, secondary_color VARCHAR(255) NULL, secondary_font_color VARCHAR(255) NULL, secondary_background_color VARCHAR(255) NULL, tertiary_color VARCHAR(255) NULL, tertiary_font_color VARCHAR(255) NULL, quaternary_color VARCHAR(255) NULL, quaternary_font_color VARCHAR(255) NULL, font_default_color VARCHAR(255) NULL, menu_background_color VARCHAR(255) NULL, creation_date date NOT NULL, labour_hourly_price DECIMAL(10, 2) NULL, labour_hourly_price_electric_vehicles DECIMAL(10, 2) NULL, min_labour_price FLOAT(10, 2) NULL, max_labour_price FLOAT(10, 2) NULL, slider_increment FLOAT(10, 2) NULL, min_labour_price_ev FLOAT(10, 2) NULL, max_labour_price_ev FLOAT(10, 2) NULL, slider_increment_ev FLOAT(10, 2) NULL, with_vat BIT(1) NULL, rating DECIMAL(10, 2) NULL, reviews_number INT NULL, reviews_info LONGTEXT NULL, color_exito VARCHAR(255) NULL, color_fallo VARCHAR(255) NULL, color_informacion VARCHAR(255) NULL, color_disabled VARCHAR(255) NULL, internal BIT(1) NULL, ref_code VARCHAR(255) NULL, credit INT NULL, date_restarting_credit date NULL, aag_region_id INT NOT NULL, training BIT(1) DEFAULT 0 NULL, guid VARCHAR(255) NULL, distance_unit_id INT NULL, default_mileage DECIMAL(10, 2) NULL, email_booking_contact_id INT NULL, email_enquiry_contact_id INT NULL, country_code_language VARCHAR(10) NULL, modification_date datetime(0) NULL, `loop` BIT(1) DEFAULT 0 NOT NULL, optimized BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_NETWORKS PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-182
CREATE TABLE networks_contacts_bdm (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, contact_id INT NOT NULL, distributor_id INT NULL, garage_id INT NULL, CONSTRAINT PK_NETWORKS_CONTACTS_BDM PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-183
CREATE TABLE networks_contacts_lists (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, contact_list_id INT NOT NULL, CONSTRAINT PK_NETWORKS_CONTACTS_LISTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-184
CREATE TABLE networks_contract_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Networks_contract_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_NETWORKS_CONTRACT_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-185
CREATE TABLE networks_recommended (id INT AUTO_INCREMENT NOT NULL, network_id INT NOT NULL, internal_network_id INT NOT NULL, recommended BIT(1) DEFAULT 0 NULL, recommended_label BIT(1) DEFAULT 0 NULL, image_recommended VARCHAR(255) NULL, image_recommended_list VARCHAR(255) NULL, optimized BIT(1) DEFAULT 0 NOT NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_NETWORKS_RECOMMENDED PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-186
CREATE TABLE networks_statuses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_NETWORKS_STATUSES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-187
CREATE TABLE oils (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, CONSTRAINT PK_OILS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-188
CREATE TABLE order_products (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'BD.Order_products' NULL, aag_region_id INT NULL, CONSTRAINT PK_ORDER_PRODUCTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-189
CREATE TABLE order_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) NULL, aag_region_id INT NOT NULL, CONSTRAINT PK_ORDER_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-190
CREATE TABLE order_types_products (id INT AUTO_INCREMENT NOT NULL, order_product_id INT NOT NULL, order_type_id INT NOT NULL, aag_region_id INT NOT NULL, CONSTRAINT PK_ORDER_TYPES_PRODUCTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-191
CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, garage_id INT NULL, order_number VARCHAR(50) NULL, invoice_date date NULL, order_date date NULL, completed_date date NULL, invoce_number VARCHAR(50) NULL, invoice_amount DECIMAL(10, 2) NULL, notes LONGTEXT NULL, signage_sign_A_q INT NULL, signage_sign_B1_q INT NULL, signage_sign_B2_q INT NULL, signage_sign_C1_q INT NULL, signage_sign_C2_q INT NULL, signage_sign_C3_q INT NULL, signage_sign_D1_q INT NULL, signage_sign_D2_q INT NULL, signage_sign_bespoke_q INT NULL, rebranding_signage INT NULL, order_type_id INT NULL, CONSTRAINT PK_ORDERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-192
CREATE TABLE panel_gm_widgets_roles (id INT AUTO_INCREMENT NOT NULL, widget_id INT NOT NULL, role_id INT NOT NULL, `order` INT NOT NULL, row INT DEFAULT 1 NOT NULL, col INT DEFAULT 1 NOT NULL, size_x INT DEFAULT 1 NOT NULL, CONSTRAINT PK_PANEL_GM_WIDGETS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-193
CREATE TABLE panels_widgets (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT '' NOT NULL, logic_model VARCHAR(255) NULL, url_view VARCHAR(255) NULL, CONSTRAINT PK_PANELS_WIDGETS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-194
CREATE TABLE panels_widgets_roles (id INT AUTO_INCREMENT NOT NULL, widget_id INT NOT NULL, role_id INT NOT NULL, `order` INT NOT NULL, row INT DEFAULT 1 NOT NULL, col INT DEFAULT 1 NOT NULL, size_x INT DEFAULT 1 NOT NULL, CONSTRAINT PK_PANELS_WIDGETS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-195
CREATE TABLE panels_widgets_users (id INT AUTO_INCREMENT NOT NULL, widget_id INT NOT NULL, user_id INT NOT NULL, `order` INT NOT NULL, row INT DEFAULT 1 NOT NULL, col INT DEFAULT 1 NOT NULL, size_x INT DEFAULT 1 NOT NULL, CONSTRAINT PK_PANELS_WIDGETS_USERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-196
CREATE TABLE permissions (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Permissions' NULL, grouping_permission_id INT NULL, position_config_type_id INT NULL, active BIT(1) DEFAULT 1 NOT NULL, CONSTRAINT PK_PERMISSIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-197
CREATE TABLE permissions_roles (id INT AUTO_INCREMENT NOT NULL, permission_id INT NOT NULL, role_id INT NOT NULL, CONSTRAINT PK_PERMISSIONS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-198
CREATE TABLE permissions_users (id INT AUTO_INCREMENT NOT NULL, permission_id INT NOT NULL, user_id INT NOT NULL, exclude_permission BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_PERMISSIONS_USERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-199
CREATE TABLE permissions_versions (id INT AUTO_INCREMENT NOT NULL, permission_id INT NOT NULL, version_id INT NOT NULL, CONSTRAINT PK_PERMISSIONS_VERSIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-200
CREATE TABLE platforms (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NULL, network_id INT NULL, aag_region_id INT NULL, external BIT(1) DEFAULT 0 NULL, CONSTRAINT PK_PLATFORMS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-201
CREATE TABLE positions (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Positions' NULL, role_id INT NOT NULL, CONSTRAINT PK_POSITIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-202
CREATE TABLE positions_config (id INT AUTO_INCREMENT NOT NULL, position_id INT NOT NULL, position_config_type_id INT NOT NULL, group_permission_id INT NOT NULL, all_networks BIT(1) DEFAULT 0 NOT NULL, all_regions BIT(1) DEFAULT 0 NOT NULL, all_trading_groups BIT(1) DEFAULT 0 NOT NULL, all_distributor_networks BIT(1) DEFAULT 0 NOT NULL, all_aag_members BIT(1) DEFAULT 0 NOT NULL, all_customer_activities BIT(1) DEFAULT 0 NOT NULL, all_profiles BIT(1) DEFAULT 0 NOT NULL, all_suppliers_categories BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_POSITIONS_CONFIG PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-203
CREATE TABLE positions_config_aag_members (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NULL, aag_member BIT(1) NULL, CONSTRAINT PK_POSITIONS_CONFIG_AAG_MEMBERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-204
CREATE TABLE positions_config_bdms (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NOT NULL, user_id INT NOT NULL, CONSTRAINT PK_POSITIONS_CONFIG_BDMS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-205
CREATE TABLE positions_config_customer_activities (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NULL, customer_activity_id INT NULL, CONSTRAINT PK_POSITIONS_CONFIG_CUSTOMER_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-206
CREATE TABLE positions_config_distributor_networks (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NULL, distributor_network_id INT NULL, CONSTRAINT PK_POSITIONS_CONFIG_DISTRIBUTOR_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-207
CREATE TABLE positions_config_networks (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NOT NULL, network_id INT NOT NULL, CONSTRAINT PK_POSITIONS_CONFIG_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-208
CREATE TABLE positions_config_profiles (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NULL, profile_id INT NULL, CONSTRAINT PK_POSITIONS_CONFIG_PROFILES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-209
CREATE TABLE positions_config_regions (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NOT NULL, region_id INT NOT NULL, CONSTRAINT PK_POSITIONS_CONFIG_REGIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-210
CREATE TABLE positions_config_suppliers_categories (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NULL, supplier_category_id INT NULL, CONSTRAINT PK_POSITIONS_CONFIG_SUPPLIERS_CATEGORIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-211
CREATE TABLE positions_config_trading_groups (id INT AUTO_INCREMENT NOT NULL, position_config_id INT NOT NULL, trading_group_id INT NOT NULL, CONSTRAINT PK_POSITIONS_CONFIG_TRADING_GROUPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-212
CREATE TABLE positions_config_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Positions_config_types' NULL, CONSTRAINT PK_POSITIONS_CONFIG_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-213
CREATE TABLE positions_config_types_permissions (id INT AUTO_INCREMENT NOT NULL, position_config_type_id INT NOT NULL, permission_id INT NOT NULL, CONSTRAINT PK_POSITIONS_CONFIG_TYPES_PERMISSIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-214
CREATE TABLE postcode_provinces (id INT AUTO_INCREMENT NOT NULL, postcode VARCHAR(50) NULL, province_code VARCHAR(50) NULL, province_id INT NULL, CONSTRAINT PK_POSTCODE_PROVINCES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-215
CREATE TABLE postcodes (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NULL, CONSTRAINT PK_POSTCODES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-216
CREATE TABLE pricings_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_PRICINGS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-217
CREATE TABLE products (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, name VARCHAR(50) NULL, active BIT(1) NULL, CONSTRAINT PK_PRODUCTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-218
CREATE TABLE products_images (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NULL, type VARCHAR(255) NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NULL, CONSTRAINT PK_PRODUCTS_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-219
CREATE TABLE provinces (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, province_code VARCHAR(6) NOT NULL, country_id INT NOT NULL, active BIT(1) DEFAULT 1 NULL, CONSTRAINT PK_PROVINCES PRIMARY KEY (id), UNIQUE (province_code));

-- changeset LisaCanéSáizSoftecaI:1756821087342-220
CREATE TABLE quotations (id INT AUTO_INCREMENT NOT NULL, id_leadgen INT NOT NULL, quotation_id VARCHAR(255) NULL, network_id INT NOT NULL, garage_id INT NOT NULL, filename VARCHAR(255) NULL, file_guid VARCHAR(255) NULL, deleted BIT(1) DEFAULT 0 NOT NULL, creation_date date NULL, CONSTRAINT PK_QUOTATIONS PRIMARY KEY (id), UNIQUE (file_guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-221
CREATE TABLE quotings_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_QUOTINGS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-222
CREATE TABLE reasons_allowances (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.ReasonsAllowances' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_REASONS_ALLOWANCES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-223
CREATE TABLE reasons_delegates (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.ReasonsDelegates' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_REASONS_DELEGATES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-224
CREATE TABLE reasons_delegates_cancelled (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.ReasonsDelegates' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_REASONS_DELEGATES_CANCELLED PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-225
CREATE TABLE regions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, code VARCHAR(50) NOT NULL, creation_date date NOT NULL, CONSTRAINT PK_REGIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-226
CREATE TABLE requested_changes (id INT AUTO_INCREMENT NOT NULL, table_id INT NOT NULL, field_id INT NULL, user_id INT NOT NULL, garage_id INT NULL, distributor_id INT NULL, date datetime(0) NOT NULL, old_value VARCHAR(255) NULL, new_value VARCHAR(255) NULL, section VARCHAR(255) NULL, `description` TEXT NULL, sent BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_REQUESTED_CHANGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-227
CREATE TABLE requested_changes_images (id INT AUTO_INCREMENT NOT NULL, requested_change_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NOT NULL, CONSTRAINT PK_REQUESTED_CHANGES_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-228
CREATE TABLE review_requests (id INT AUTO_INCREMENT NOT NULL, booking_id INT NULL, aag_region_id INT NOT NULL, request_data TEXT NULL, response_data TEXT NULL, created_at TIMESTAMP(0) DEFAULT NOW() NOT NULL, CONSTRAINT PK_REVIEW_REQUESTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-229
CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Roles' NULL, `role` VARCHAR(255) NOT NULL, CONSTRAINT PK_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-230
CREATE TABLE roles_positions_config_types (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, position_config_type_id INT NOT NULL, CONSTRAINT PK_ROLES_POSITIONS_CONFIG_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-231
CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, conferences_delegates_id INT NULL, weeks_id INT NULL, CONSTRAINT PK_ROOM PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-232
CREATE TABLE room_type (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.RoomType' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_ROOM_TYPE PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-233
CREATE TABLE routes (id INT AUTO_INCREMENT NOT NULL, type INT NULL, name VARCHAR(255) NOT NULL, user_assigned_id INT NOT NULL, creation_date datetime(0) NOT NULL, user_creation_id INT NULL, CONSTRAINT PK_ROUTES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-234
CREATE TABLE sales_areas (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Sale_areas' NULL, aag_region_id INT NULL, CONSTRAINT PK_SALES_AREAS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-235
CREATE TABLE searches_contacts (id INT NOT NULL, search_id INT NOT NULL, contact_id INT NOT NULL, CONSTRAINT PK_SEARCHES_CONTACTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-236
CREATE TABLE searches_distributors (id INT AUTO_INCREMENT NOT NULL, search_id INT NOT NULL, distributor_id INT NOT NULL, CONSTRAINT PK_SEARCHES_DISTRIBUTORS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-237
CREATE TABLE searches_networks (id INT AUTO_INCREMENT NOT NULL, search_id INT NOT NULL, network_id INT NOT NULL, CONSTRAINT PK_SEARCHES_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-238
CREATE TABLE sections_subsections (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Sections_subsections' NULL, image VARCHAR(255) NULL, creation_date datetime(0) NOT NULL, without_networks BIT(1) NULL, without_distributor_networks BIT(1) NULL, without_activity BIT(1) NULL, aag_member_yes BIT(1) NULL, aag_member_no BIT(1) NULL, communication_section_id INT NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_SECTIONS_SUBSECTIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-239
CREATE TABLE sections_subsections_customers_activities (id INT AUTO_INCREMENT NOT NULL, section_subsection_id INT NOT NULL, customer_activity_id INT NOT NULL, CONSTRAINT PK_SECTIONS_SUBSECTIONS_CUSTOMERS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-240
CREATE TABLE sections_subsections_distributors_networks (id INT AUTO_INCREMENT NOT NULL, section_subsection_id INT DEFAULT 0 NOT NULL, distributor_network_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_SECTIONS_SUBSECTIONS_DISTRIBUTORS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-241
CREATE TABLE sections_subsections_networks (id INT AUTO_INCREMENT NOT NULL, section_subsection_id INT DEFAULT 0 NOT NULL, network_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_SECTIONS_SUBSECTIONS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-242
CREATE TABLE sections_subsections_positions (id INT AUTO_INCREMENT NOT NULL, section_subsection_id INT DEFAULT 0 NULL, position_id INT DEFAULT 0 NULL, CONSTRAINT PK_SECTIONS_SUBSECTIONS_POSITIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-243
CREATE TABLE sections_subsections_trading_groups (id INT AUTO_INCREMENT NOT NULL, section_subsection_id INT DEFAULT 0 NOT NULL, trading_group_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_SECTIONS_SUBSECTIONS_TRADING_GROUPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-244
CREATE TABLE sendgrid_email_types_templates (id INT AUTO_INCREMENT NOT NULL, email_type_id INT NOT NULL, sendgrid_template_id VARCHAR(50) NOT NULL COMMENT 'Template ID obtained from Sendgrid', language_id INT NULL, platform_id INT NOT NULL, country_id INT NOT NULL, language_web_id INT NULL, aag_region_id INT NOT NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_SENDGRID_EMAIL_TYPES_TEMPLATES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-245
CREATE TABLE sendgrid_email_types_viewvars (id INT AUTO_INCREMENT NOT NULL, email_type_id INT NOT NULL, sendgrid_viewvar_id INT NOT NULL, CONSTRAINT PK_SENDGRID_EMAIL_TYPES_VIEWVARS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-246
CREATE TABLE sendgrid_licenses_config (id INT AUTO_INCREMENT NOT NULL, guid VARCHAR(36) NULL, api_key VARCHAR(255) NOT NULL, name VARCHAR(50) NULL, from_email VARCHAR(100) NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, platform_id INT NULL, country_id INT NULL, aag_region_id INT NULL, CONSTRAINT PK_SENDGRID_LICENSES_CONFIG PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-247
CREATE TABLE sendgrid_viewvars (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, old_value VARCHAR(100) NULL, CONSTRAINT PK_SENDGRID_VIEWVARS PRIMARY KEY (id), UNIQUE (name));

-- changeset LisaCanéSáizSoftecaI:1756821087342-248
CREATE TABLE services (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Services' NULL, international_code VARCHAR(255) NULL, url VARCHAR(255) NOT NULL COMMENT 'Image address of the service icon', CONSTRAINT PK_SERVICES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-249
CREATE TABLE services_drivers (id INT AUTO_INCREMENT NOT NULL, network_id INT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Services_drivers' NULL, CONSTRAINT PK_SERVICES_DRIVERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-250
CREATE TABLE services_types (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, CONSTRAINT PK_SERVICES_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-251
CREATE TABLE set_distributors_objectives (id INT AUTO_INCREMENT NOT NULL, distributor_array LONGTEXT NULL, distributor_array_updated LONGTEXT NULL, objective_id INT NULL, `from` date NULL, `to` date NULL, creation_date datetime(0) NOT NULL, modification_date datetime(0) NOT NULL, is_set BIT(1) DEFAULT 0 NULL, user_id INT NULL, email_to VARCHAR(255) NULL, CONSTRAINT PK_SET_DISTRIBUTORS_OBJECTIVES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-252
CREATE TABLE shortcuts (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NULL, url VARCHAR(255) NOT NULL, tooltip MEDIUMTEXT NULL, image VARCHAR(255) NOT NULL, shortcut_type_id INT NOT NULL, start_date datetime(0) NULL, end_date datetime(0) NULL, active BIT(1) DEFAULT 1 NOT NULL, is_sso BIT(1) DEFAULT 0 NULL, parameter_name_1 VARCHAR(255) NULL, parameter_name_2 VARCHAR(255) NULL, fixed BIT(1) NULL, code VARCHAR(50) NULL, creation_date datetime(0) NOT NULL, without_networks BIT(1) NULL, without_distributor_networks BIT(1) NULL, without_activity BIT(1) NULL, aag_member_yes BIT(1) NULL, aag_member_no BIT(1) NULL, CONSTRAINT PK_SHORTCUTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-253
CREATE TABLE shortcuts_customers_activities (id INT AUTO_INCREMENT NOT NULL, shortcut_id INT NOT NULL, customer_activity_id INT NOT NULL, CONSTRAINT PK_SHORTCUTS_CUSTOMERS_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-254
CREATE TABLE shortcuts_distributors_networks (id INT AUTO_INCREMENT NOT NULL, shortcut_id INT DEFAULT 0 NOT NULL, distributor_network_id INT DEFAULT 0 NOT NULL, CONSTRAINT PK_SHORTCUTS_DISTRIBUTORS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-255
CREATE TABLE shortcuts_networks (id INT AUTO_INCREMENT NOT NULL, shortcut_id INT NOT NULL, network_id INT NOT NULL, CONSTRAINT PK_SHORTCUTS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-256
CREATE TABLE shortcuts_positions (id INT AUTO_INCREMENT NOT NULL, shortcut_id INT DEFAULT 0 NULL, position_id INT DEFAULT 0 NULL, CONSTRAINT PK_SHORTCUTS_POSITIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-257
CREATE TABLE shortcuts_roles (id INT AUTO_INCREMENT NOT NULL, shortcut_id INT NOT NULL, role_id INT NOT NULL, CONSTRAINT PK_SHORTCUTS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-258
CREATE TABLE shortcuts_trading_groups (id INT AUTO_INCREMENT NOT NULL, shortcut_id INT NOT NULL, trading_group_id INT NOT NULL, CONSTRAINT PK_SHORTCUTS_TRADING_GROUPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-259
CREATE TABLE shortcuts_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Shortcuts_types' NULL, single BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_SHORTCUTS_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-260
CREATE TABLE shortner_url_api (id INT AUTO_INCREMENT NOT NULL, api_key VARCHAR(100) NOT NULL, expire_days INT NULL, expire_at_views INT NULL, domain VARCHAR(100) NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, user_id INT NULL, sms_license_config_id INT NOT NULL, CONSTRAINT PK_SHORTNER_URL_API PRIMARY KEY (id), UNIQUE (sms_license_config_id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-261
CREATE TABLE shortner_url_log (id INT AUTO_INCREMENT NOT NULL, long_url VARCHAR(100) NULL, short_url VARCHAR(100) NULL, domain VARCHAR(100) NULL, short_id VARCHAR(100) NULL, expire_days INT NULL, expire_at_datetime datetime(0) NULL, expire_at_views INT NULL, status BIT(1) DEFAULT 0 NULL, status_code INT NULL, test_config BIT(1) DEFAULT 0 NULL, creation_date datetime(0) NULL, country_id INT NULL, CONSTRAINT PK_SHORTNER_URL_LOG PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-262
CREATE TABLE sms_licenses_config (id INT AUTO_INCREMENT NOT NULL, role_id INT NULL, username_iframe VARCHAR(255) NOT NULL, password_iframe VARCHAR(255) NOT NULL, license_iframe VARCHAR(255) NOT NULL, username_api VARCHAR(255) NOT NULL, password_api VARCHAR(255) NOT NULL, license_api VARCHAR(255) NOT NULL, aag_region_id INT NULL, country_id INT NULL, CONSTRAINT PK_SMS_LICENSES_CONFIG PRIMARY KEY (id), UNIQUE (country_id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-263
CREATE TABLE sms_template_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) NULL, CONSTRAINT PK_SMS_TEMPLATE_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-264
CREATE TABLE sms_templates (id INT AUTO_INCREMENT NOT NULL, template_type_id INT NULL, `description` TEXT NULL, aag_region_id INT NULL, country_id INT NULL, sender VARCHAR(255) NOT NULL, CONSTRAINT PK_SMS_TEMPLATES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-265
CREATE TABLE software (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Software' NULL, aag_region_id INT NULL, CONSTRAINT PK_SOFTWARE PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-266
CREATE TABLE software_manufactures (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Sotware_manufactures' NULL, aag_region_id INT NULL, CONSTRAINT PK_SOFTWARE_MANUFACTURES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-267
CREATE TABLE software_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Software_types' NULL, aag_region_id INT NULL, CONSTRAINT PK_SOFTWARE_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-268
CREATE TABLE stand_size (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.StandSize' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_STAND_SIZE PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-269
CREATE TABLE suppliers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NULL, web VARCHAR(255) NULL, `description` LONGTEXT NULL, url_image VARCHAR(255) NULL, url_video VARCHAR(255) NULL, url_channel VARCHAR(255) NULL, active BIT(1) NOT NULL, phone VARCHAR(255) NULL, fax VARCHAR(255) NULL, email VARCHAR(255) NULL, town VARCHAR(255) NULL, postcode VARCHAR(255) NULL, address1 VARCHAR(255) NULL, address2 VARCHAR(255) NULL, VAT_number VARCHAR(255) NULL, siret VARCHAR(255) NULL, creation_date datetime(0) NOT NULL, edition_date datetime(0) NOT NULL, publication_date datetime(0) NOT NULL, aag_region_id INT NOT NULL, CONSTRAINT PK_SUPPLIERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-270
CREATE TABLE suppliers_categories (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(50) NULL, name_fr VARCHAR(50) NULL, name_de VARCHAR(50) NULL, name_nl VARCHAR(50) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(50) DEFAULT 'Bd.Suppliers_categories' NULL, aag_region_id INT NOT NULL, CONSTRAINT PK_SUPPLIERS_CATEGORIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-271
CREATE TABLE suppliers_files (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, supplier_category_id INT NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NULL, type VARCHAR(255) NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NULL, name VARCHAR(255) NOT NULL, active BIT(1) NULL, CONSTRAINT PK_SUPPLIERS_FILES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-272
CREATE TABLE suppliers_images (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NULL, type VARCHAR(255) NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NULL, CONSTRAINT PK_SUPPLIERS_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-273
CREATE TABLE suppliers_networks (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, network_id INT NOT NULL, CONSTRAINT PK_SUPPLIERS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-274
CREATE TABLE suppliers_trading_groups (id INT AUTO_INCREMENT NOT NULL, supplier_id INT NOT NULL, trading_group_id INT NOT NULL, CONSTRAINT PK_SUPPLIERS_TRADING_GROUPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-275
CREATE TABLE tasks (id INT AUTO_INCREMENT NOT NULL, appointment_id INT NULL, title VARCHAR(255) NULL, body LONGTEXT NOT NULL, task_status_id INT NOT NULL, resolve_date datetime(0) NULL, limit_date date NULL, mandatory BIT(1) DEFAULT 0 NULL, user_assigned_id INT NULL, user_creation_id INT NOT NULL, creation_date datetime(0) NOT NULL, CONSTRAINT PK_TASKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-276
CREATE TABLE tasks_codes (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, office_code VARCHAR(255) NULL, CONSTRAINT PK_TASKS_CODES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-277
CREATE TABLE tasks_contacts_lists (id INT AUTO_INCREMENT NOT NULL, contact_list_id INT NULL, task_id INT NOT NULL, CONSTRAINT PK_TASKS_CONTACTS_LISTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-278
CREATE TABLE tasks_distributors (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, distributor_id INT NOT NULL, completed BIT(1) NULL, completed_date datetime(0) NULL, CONSTRAINT PK_TASKS_DISTRIBUTORS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-279
CREATE TABLE tasks_files (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NOT NULL, source_name VARCHAR(255) NOT NULL, file_guid VARCHAR(255) NULL, CONSTRAINT PK_TASKS_FILES PRIMARY KEY (id), UNIQUE (file_guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-280
CREATE TABLE tasks_garages (id INT AUTO_INCREMENT NOT NULL, task_id INT NOT NULL, garage_id INT NOT NULL, completed BIT(1) NULL, completed_date datetime(0) NULL, CONSTRAINT PK_TASKS_GARAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-281
CREATE TABLE tasks_images (id INT AUTO_INCREMENT NOT NULL, task_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NOT NULL, source_name VARCHAR(255) NOT NULL, CONSTRAINT PK_TASKS_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-282
CREATE TABLE tasks_status (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Tasks_status' NULL, CONSTRAINT PK_TASKS_STATUS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-283
CREATE TABLE tasks_users (id INT AUTO_INCREMENT NOT NULL, user_id INT NULL, task_id INT NOT NULL, CONSTRAINT PK_TASKS_USERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-284
CREATE TABLE trade_show (id INT AUTO_INCREMENT NOT NULL, conferences_delegates_id INT NULL, weeks_id INT NULL, CONSTRAINT PK_TRADE_SHOW PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-285
CREATE TABLE trading_groups (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NULL, image VARCHAR(255) NOT NULL COMMENT 'Image address of the network icon', web VARCHAR(255) NULL COMMENT 'If 1, Trading Group is Independent, if 0 is subsidiary', is_cv BIT(1) NULL COMMENT '0 is LV, 1 is CV , Null are both', independent BIT(1) DEFAULT 1 NULL, primary_color VARCHAR(255) NOT NULL, primary_font_color VARCHAR(255) NOT NULL, primary_background_color VARCHAR(255) NOT NULL, secondary_color VARCHAR(255) NOT NULL, secondary_font_color VARCHAR(255) NOT NULL, secondary_background_color VARCHAR(255) NOT NULL, color_active VARCHAR(255) NOT NULL, tertiary_color VARCHAR(255) NOT NULL, menu_color VARCHAR(255) NOT NULL, menu_background_color VARCHAR(255) NOT NULL, color_exito VARCHAR(255) NULL, color_fallo VARCHAR(255) NOT NULL, color_informacion VARCHAR(255) NOT NULL, color_disabled VARCHAR(255) NOT NULL, creation_date date NOT NULL, aag_region_id INT NOT NULL, CONSTRAINT PK_TRADING_GROUPS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-286
CREATE TABLE trading_groups_distributors_networks (id INT AUTO_INCREMENT NOT NULL, trading_group_id INT NOT NULL, network_id INT NOT NULL, CONSTRAINT PK_TRADING_GROUPS_DISTRIBUTORS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-287
CREATE TABLE trading_groups_networks (id INT AUTO_INCREMENT NOT NULL, trading_group_id INT NOT NULL, network_id INT NOT NULL, CONSTRAINT PK_TRADING_GROUPS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-288
CREATE TABLE trainings_allowances (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NULL, start_date date NULL, end_date date NULL, is_actual INT DEFAULT 0 NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_TRAININGS_ALLOWANCES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-289
CREATE TABLE trainings_courses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NULL, course_type_id INT NULL, training_provider_id INT NULL, duration INT NULL, price INT NULL, price_credit INT NULL, cost_training INT DEFAULT 0 NOT NULL, `description` LONGTEXT NULL, part_number VARCHAR(255) NULL, invoice_number VARCHAR(255) NULL, active BIT(1) NULL, guid VARCHAR(36) NULL, is_online TINYINT(3) DEFAULT 0 NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_TRAININGS_COURSES PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-290
CREATE TABLE trainings_credits (id INT AUTO_INCREMENT NOT NULL, pound DECIMAL(10, 2) NULL, credit DECIMAL(10, 2) NULL, CONSTRAINT PK_TRAININGS_CREDITS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-291
CREATE TABLE trainings_credits_networks (id INT AUTO_INCREMENT NOT NULL, garage_network_id INT NULL, credit_spent INT NULL, credit_given INT NULL, credit INT NULL, contact_id INT NULL, `description` VARCHAR(255) NULL, training_planned_course_id INT NULL, reason_allowance_id INT NULL, training_delegate_id INT NULL, training_allowance_id INT NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_TRAININGS_CREDITS_NETWORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-292
CREATE TABLE trainings_delegates (id INT AUTO_INCREMENT NOT NULL, training_planned_course_id INT NULL, garage_contact_staff_id INT NULL, is_refund_eligible BIT(1) DEFAULT 0 NULL, reason_delegate_id INT NULL, order_number VARCHAR(255) NULL, invoice_number VARCHAR(255) NULL, cancelled BIT(1) DEFAULT 0 NOT NULL, refund_credits INT NULL, manual_credits_calculation INT NULL, paid_separately INT NULL, reason_cancelled_id INT NULL, network_id INT NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_TRAININGS_DELEGATES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-293
CREATE TABLE trainings_planned_courses (id INT AUTO_INCREMENT NOT NULL, training_course_id INT NULL, training_trainer_id INT NULL, venue_id INT NULL, date_from date NULL, date_to date NULL, starting_time time NULL, duration INT NULL, availability INT DEFAULT 0 NOT NULL, status INT DEFAULT 0 NULL, new_imported_name VARCHAR(255) NULL, full BIT(1) NULL, guid VARCHAR(36) NULL, invoice_number VARCHAR(255) NULL, note VARCHAR(255) NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_TRAININGS_PLANNED_COURSES PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-294
CREATE TABLE trainings_providers (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NULL, email VARCHAR(255) NULL, phone VARCHAR(255) NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_TRAININGS_PROVIDERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-295
CREATE TABLE trainings_trainers (id INT AUTO_INCREMENT NOT NULL, training_provider_id INT NULL, name VARCHAR(255) NULL, email VARCHAR(255) NULL, phone VARCHAR(255) NULL, creation_date datetime(0) NULL, modification_date datetime(0) NULL, CONSTRAINT PK_TRAININGS_TRAINERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-296
CREATE TABLE turnovers (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(50) DEFAULT 'Bd.Turnovers' NULL, CONSTRAINT PK_TURNOVERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-297
CREATE TABLE tutorials (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NULL, url VARCHAR(255) NULL, creation_date datetime(0) NOT NULL, user_id INT NOT NULL, `order` INT NOT NULL, CONSTRAINT PK_TUTORIALS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-298
CREATE TABLE tutorials_roles (id INT AUTO_INCREMENT NOT NULL, tutorial_id INT NOT NULL, role_id INT NOT NULL, CONSTRAINT PK_TUTORIALS_ROLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-299
CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, contact_id INT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, username VARCHAR(100) NOT NULL, password VARCHAR(255) DEFAULT '' NOT NULL, role_id INT NOT NULL, language_id INT NOT NULL, garage_id INT NULL, distributor_id INT NULL, garage_type TINYINT(3) NULL, distributor_type TINYINT(3) NULL, active BIT(1) DEFAULT 1 NOT NULL, retries INT DEFAULT 0 NULL, last_login datetime(0) NULL, set_login datetime(0) NULL, repairmaintenance BIT(1) NULL, creation_date datetime(0) NOT NULL, aag_region_id INT NULL, region_id INT NULL, guid VARCHAR(36) NOT NULL, country_id INT NULL, CONSTRAINT PK_USERS PRIMARY KEY (id), UNIQUE (username), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-300
CREATE TABLE users_images (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT 0 NOT NULL, creation_date datetime(0) NOT NULL, file VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, ext VARCHAR(255) NULL, source_name VARCHAR(255) NOT NULL, CONSTRAINT PK_USERS_IMAGES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-301
CREATE TABLE users_master_key (id INT AUTO_INCREMENT NOT NULL, master_key VARCHAR(255) NOT NULL, modification_date datetime(0) NOT NULL, CONSTRAINT PK_USERS_MASTER_KEY PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-302
CREATE TABLE users_preferences (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, preference_id INT NOT NULL, value VARCHAR(255) NULL, CONSTRAINT PK_USERS_PREFERENCES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-303
CREATE TABLE users_reassignments (id INT AUTO_INCREMENT NOT NULL, user_id_origin INT NOT NULL, user_id_destination INT NOT NULL, garage_contact_bdm_id INT NULL, distributor_contact_bdm_id INT NULL, contact_contact_list_id INT NULL, task_id INT NULL, appointment_id INT NULL, route_id INT NULL, date_from date NULL, date_to date NULL, active BIT(1) DEFAULT 0 NULL, permanent BIT(1) DEFAULT 0 NOT NULL, CONSTRAINT PK_USERS_REASSIGNMENTS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-304
CREATE TABLE users_recover_passwords (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, `key` VARCHAR(255) NOT NULL, creation_date datetime(0) NOT NULL, new_user BIT(1) NULL, CONSTRAINT PK_USERS_RECOVER_PASSWORDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-305
CREATE TABLE users_searches (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NULL, user_id INT NULL, route_id INT NULL, network_status_id INT NULL, is_garages INT NULL, city VARCHAR(255) NULL, location VARCHAR(255) NULL, lat DECIMAL(30, 20) NULL, lng DECIMAL(30, 20) NULL, distance INT NULL, last_visit VARCHAR(255) NULL, my_customers INT NULL, last_use datetime(0) NULL, creation_date datetime(0) NULL, CONSTRAINT PK_USERS_SEARCHES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-306
CREATE TABLE users_statistics (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_name VARCHAR(255) NULL, trading_group_id INT NULL, network_id INT NULL, garage_id INT NULL, distributor_id INT NULL, section_id INT NULL, section_name VARCHAR(255) NULL, article_id INT NULL, article_name VARCHAR(255) NULL, date date NOT NULL, created_at datetime(0) NULL, device INT NOT NULL, ip VARCHAR(255) NULL, CONSTRAINT PK_USERS_STATISTICS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-307
CREATE TABLE value_add_supplier_type (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Value_add_supplier_type' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_VALUE_ADD_SUPPLIER_TYPE PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-308
CREATE TABLE value_add_suppliers (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Value_add_suppliers' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_VALUE_ADD_SUPPLIERS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-309
CREATE TABLE value_added_suppliers (id INT AUTO_INCREMENT NOT NULL, guid VARCHAR(50) NOT NULL, aag_region_id INT NOT NULL, logo_image VARCHAR(255) NULL, title VARCHAR(255) NOT NULL, `description` LONGTEXT NULL, supplier_url VARCHAR(255) NOT NULL, updated_at datetime(0) NOT NULL, created_at datetime(0) NOT NULL, CONSTRAINT PK_VALUE_ADDED_SUPPLIERS PRIMARY KEY (id), UNIQUE (guid)) COMMENT='This table is used by Value Added Suppliers Module, not to be confused with values_adds or value_adds_suppliers dynamic lists, used to improve the visibility of suppliers within the GNM platform, allowing users to easily access supplier details and visit their websites.';

-- changeset LisaCanéSáizSoftecaI:1756821087342-310
CREATE TABLE values_adds (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.ValuesAdds' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_VALUES_ADDS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-311
CREATE TABLE vehicle_types (id INT AUTO_INCREMENT NOT NULL, international_code VARCHAR(255) NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Vehicle_types' NULL, url VARCHAR(255) NOT NULL COMMENT 'Image address of the service icon', CONSTRAINT PK_VEHICLE_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-312
CREATE TABLE vehicles (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Vehicles' NULL, CONSTRAINT PK_VEHICLES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-313
CREATE TABLE venues (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NULL, address_1 VARCHAR(255) NULL, address_2 VARCHAR(255) NULL, address_3 VARCHAR(255) NULL, address_4 VARCHAR(255) NULL, sales_area_id INT NULL, latitude DECIMAL(30, 20) NULL, longitude DECIMAL(30, 20) NULL, town VARCHAR(255) NULL, post_code VARCHAR(255) NULL, telephone VARCHAR(255) NULL, active BIT(1) DEFAULT 1 NULL, aag_region_id INT NOT NULL, guid VARCHAR(36) NULL, venue_type_id INT NULL, CONSTRAINT PK_VENUES PRIMARY KEY (id), UNIQUE (guid));

-- changeset LisaCanéSáizSoftecaI:1756821087342-314
CREATE TABLE venues_types (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NOT NULL, name_es VARCHAR(255) NOT NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Venues_types' NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_VENUES_TYPES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-315
CREATE TABLE versions (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, active BIT(1) DEFAULT 1 NOT NULL, CONSTRAINT PK_VERSIONS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-316
CREATE TABLE websites (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, aag_region_id INT NULL, CONSTRAINT PK_WEBSITES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-317
CREATE TABLE weeks (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Weeks' NOT NULL, CONSTRAINT PK_WEEKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-318
CREATE TABLE works (id INT AUTO_INCREMENT NOT NULL, network_id INT NULL, grouping_genart_id INT NULL, active BIT(1) NOT NULL, name_en VARCHAR(255) NULL, name_fr VARCHAR(255) NULL, name_de VARCHAR(255) NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, code VARCHAR(255) NOT NULL, discount DECIMAL(10, 2) NULL, markup DECIMAL(10, 2) NULL, surcharge DECIMAL(10, 2) NULL, CONSTRAINT PK_WORKS PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-319
CREATE TABLE workshop_activities (id INT AUTO_INCREMENT NOT NULL, name_en VARCHAR(255) NOT NULL, name_fr VARCHAR(255) NOT NULL, name_de VARCHAR(255) NOT NULL, name_nl VARCHAR(255) NULL, name_es VARCHAR(255) NULL, name_lc VARCHAR(255) DEFAULT 'Bd.Workshop_activities' NULL, CONSTRAINT PK_WORKSHOP_ACTIVITIES PRIMARY KEY (id));

-- changeset LisaCanéSáizSoftecaI:1756821087342-320
ALTER TABLE garages_networks_works ADD CONSTRAINT UK_garages_networks UNIQUE (garage_network_id, work_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-321
ALTER TABLE garages_networks_fluids ADD CONSTRAINT UK_garages_networks_fluids UNIQUE (garage_network_id, fluid_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-322
ALTER TABLE garages_networks_genarts ADD CONSTRAINT UK_garages_networks_genarts UNIQUE (garage_network_id, genart_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-323
ALTER TABLE garages_networks_genarts_master ADD CONSTRAINT UK_garages_networks_genarts_master_ UNIQUE (garage_network_id, genart_master_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-324
ALTER TABLE garages_networks_services ADD CONSTRAINT UK_garages_networks_services UNIQUE (garage_network_id, service_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-325
ALTER TABLE garages_networks_services_drivers ADD CONSTRAINT UK_garages_networks_services_drivers UNIQUE (garage_network_id, service_driver_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-326
ALTER TABLE garages_networks_vehicle_types ADD CONSTRAINT UK_garages_networks_vehicle_types UNIQUE (garage_network_id, vehicle_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-327
ALTER TABLE garages_networks_vehicles ADD CONSTRAINT UK_garages_networks_vehicles UNIQUE (garage_network_id, vehicle_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-328
ALTER TABLE garages_networks_vehicles_black_list ADD CONSTRAINT UK_garages_networks_vehicles_black_list UNIQUE (garage_network_id, vehicle_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-329
ALTER TABLE garages_networks_works_labours ADD CONSTRAINT UK_garages_networks_works_labours UNIQUE (work_id, garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-330
ALTER TABLE services_drivers ADD CONSTRAINT UK_services_drivers_name_de UNIQUE (name_de, network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-331
ALTER TABLE services_drivers ADD CONSTRAINT UK_services_drivers_name_en UNIQUE (name_en, network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-332
ALTER TABLE services_drivers ADD CONSTRAINT UK_services_drivers_name_fr UNIQUE (name_fr, network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-333
ALTER TABLE languages_webs_networks ADD CONSTRAINT UNIQUE_NETWORK_FLAG_CODE UNIQUE (network_id, code);

-- changeset LisaCanéSáizSoftecaI:1756821087342-334
ALTER TABLE sendgrid_email_types_viewvars ADD CONSTRAINT UNQ_email_type_sendgrid_viewvar UNIQUE (email_type_id, sendgrid_viewvar_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-335
ALTER TABLE sendgrid_licenses_config ADD CONSTRAINT UNQ_platform_country UNIQUE (platform_id, country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-336
ALTER TABLE genarts_master ADD CONSTRAINT code_network_id UNIQUE (code, network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-337
ALTER TABLE fleets ADD CONSTRAINT erp_id_ref_code UNIQUE (erp_id, ref_code);

-- changeset LisaCanéSáizSoftecaI:1756821087342-338
ALTER TABLE fleets_networks ADD CONSTRAINT fleet_id_network_id UNIQUE (fleet_id, network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-339
ALTER TABLE garages_networks_genarts_families ADD CONSTRAINT garage_network_id_genart_family_id UNIQUE (garage_network_id, genart_family_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-340
ALTER TABLE garages_networks_works_prices ADD CONSTRAINT garages_networks_works_prices UNIQUE (garage_network_id, work_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-341
ALTER TABLE grouping_genarts ADD CONSTRAINT network_id_code UNIQUE (network_id, code);

-- changeset LisaCanéSáizSoftecaI:1756821087342-342
ALTER TABLE genarts ADD CONSTRAINT unique_code_grouping UNIQUE (code, grouping_genart_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-343
ALTER TABLE works ADD CONSTRAINT unique_code_network UNIQUE (network_id, code);

-- changeset LisaCanéSáizSoftecaI:1756821087342-344
ALTER TABLE config_modules_regions_roles ADD CONSTRAINT unique_index UNIQUE (config_id, aag_region_id, role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-345
ALTER TABLE networks_contacts_lists ADD CONSTRAINT unique_index UNIQUE (network_id, contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-346
ALTER TABLE sms_templates ADD CONSTRAINT unique_template_type_id_country_id UNIQUE (template_type_id, country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-347
CREATE INDEX FK_BRAND_ID ON brands_images(brand_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-348
CREATE INDEX FK_SUPPLIER_ID ON suppliers_images(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-349
CREATE INDEX FK__contacts ON trainings_credits_networks(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-350
CREATE INDEX FK__courses_types ON trainings_courses(course_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-351
CREATE INDEX FK__garages ON garages_values_adds(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-352
CREATE INDEX FK__garages_b2b ON garages_b2b_postcodes(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-353
CREATE INDEX FK__garages_b2c ON garages_b2c_postcodes(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-354
CREATE INDEX FK__garages_contacts_staff ON trainings_delegates(garage_contact_staff_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-355
CREATE INDEX FK__garages_networks ON trainings_credits_networks(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-356
CREATE INDEX FK__order_products ON garages_products(product_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-357
CREATE INDEX FK__orders ON garages_products(order_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-358
CREATE INDEX FK__postcodes_b2b ON garages_b2b_postcodes(postcode_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-359
CREATE INDEX FK__postcodes_b2c ON garages_b2c_postcodes(postcode_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-360
CREATE INDEX FK__trainings_courses ON trainings_delegates(training_planned_course_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-361
CREATE INDEX FK__trainings_providers ON trainings_courses(training_provider_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-362
CREATE INDEX FK__values_adds ON garages_values_adds(value_add_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-363
CREATE INDEX FK__venues ON conferences(venue_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-364
CREATE INDEX FK_aag_region_id ON config_modules_regions_roles(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-365
CREATE INDEX FK_aag_region_id ON sms_licenses_config(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-366
CREATE INDEX FK_aag_region_id ON sms_templates(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-367
CREATE INDEX FK_aag_region_id_countries ON countries(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-368
CREATE INDEX FK_aag_region_id_garages ON garages(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-369
CREATE INDEX FK_activities_workshops_customer_activities ON distributors_customer_activities_workshops(distributor_customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-370
CREATE INDEX FK_activities_workshops_workshop_activities ON distributors_customer_activities_workshops(workshop_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-371
CREATE INDEX FK_agreements_aag_region_id ON agreements(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-372
CREATE INDEX FK_alerts_alert_type_id ON alerts(alert_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-373
CREATE INDEX FK_alerts_user_id ON alerts(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-374
CREATE INDEX FK_annex_details_aag_region_id ON annex_details(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-375
CREATE INDEX FK_appointments_appointment_feeling_id ON appointments(appointment_feeling_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-376
CREATE INDEX FK_appointments_appointment_status_id ON appointments(appointment_status_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-377
CREATE INDEX FK_appointments_appointment_type_id ON appointments(appointment_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-378
CREATE INDEX FK_appointments_comments_appointment_id ON appointments_comments(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-379
CREATE INDEX FK_appointments_comments_user_id ON appointments_comments(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-380
CREATE INDEX FK_appointments_contacts_appointment_id ON appointments_contacts(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-381
CREATE INDEX FK_appointments_contacts_contact_id ON appointments_contacts(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-382
CREATE INDEX FK_appointments_contacts_lists_appointment_id ON appointments_contacts_lists(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-383
CREATE INDEX FK_appointments_contacts_lists_contact_list_id ON appointments_contacts_lists(contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-384
CREATE INDEX FK_appointments_distributor_id ON appointments(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-385
CREATE INDEX FK_appointments_feedback_user_modification ON appointments(feedback_user_modification);

-- changeset LisaCanéSáizSoftecaI:1756821087342-386
CREATE INDEX FK_appointments_files_appointment_id ON appointments_files(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-387
CREATE INDEX FK_appointments_garage_id ON appointments(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-388
CREATE INDEX FK_appointments_objectives_aag_regions ON appointments_objectives(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-389
CREATE INDEX FK_appointments_user_assigned_id ON appointments(user_assigned_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-390
CREATE INDEX FK_appointments_user_creation_id ON appointments(user_creation_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-391
CREATE INDEX FK_associantions_types_aag_region_id ON associations_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-392
CREATE INDEX FK_billings_schedules_aag_region_id ON billings_schedules(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-393
CREATE INDEX FK_bookings_child_network_id ON bookings(child_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-394
CREATE INDEX FK_bookings_garage_id ON bookings(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-395
CREATE INDEX FK_bookings_network_id ON bookings(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-396
CREATE INDEX FK_bookings_work_id ON bookings(work_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-397
CREATE INDEX FK_brands_supplier_id ON brands(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-398
CREATE INDEX FK_campaign_entries_aag_region_id ON campaign_entries(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-399
CREATE INDEX FK_communications_trading_groups_communication_id ON communications_trading_groups(communication_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-400
CREATE INDEX FK_communications_trading_groups_trading_group_id ON communications_trading_groups(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-401
CREATE INDEX FK_conferences_aag_region_id ON conferences(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-402
CREATE INDEX FK_conferences_delegates_conferences ON conferences_delegates(conferences_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-403
CREATE INDEX FK_conferences_delegates_contacts ON conferences_delegates(delegate_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-404
CREATE INDEX FK_conferences_delegates_distributors ON conferences_delegates(distributors_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-405
CREATE INDEX FK_conferences_delegates_garages ON conferences_delegates(garages_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-406
CREATE INDEX FK_conferences_delegates_room_type ON conferences_delegates(room_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-407
CREATE INDEX FK_conferences_delegates_room_type_2 ON conferences_delegates(guest_separate_room);

-- changeset LisaCanéSáizSoftecaI:1756821087342-408
CREATE INDEX FK_conferences_delegates_stand_size ON conferences_delegates(stand_size_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-409
CREATE INDEX FK_conferences_delegates_suppliers ON conferences_delegates(suppliers_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-410
CREATE INDEX FK_conferences_delegates_venues ON conferences_delegates(venue_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-411
CREATE INDEX FK_config_section_id ON config(section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-412
CREATE INDEX FK_contacts_aag_region_id ON contacts(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-413
CREATE INDEX FK_contacts_contacts_lists_contact_id ON contacts_contacts_lists(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-414
CREATE INDEX FK_contacts_contacts_lists_contact_list_id ON contacts_contacts_lists(contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-415
CREATE INDEX FK_contacts_lists_aag_region_id ON contacts_lists(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-416
CREATE INDEX FK_contacts_position_id ON contacts(position_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-417
CREATE INDEX FK_contacts_regions_contacts ON contacts_regions(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-418
CREATE INDEX FK_contacts_regions_regions ON contacts_regions(region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-419
CREATE INDEX FK_contacts_title_id ON contacts(title_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-420
CREATE INDEX FK_country_id ON sms_templates(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-421
CREATE INDEX FK_courses_types_aag_region_id ON courses_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-422
CREATE INDEX FK_courtesy_car_types_aag_region_id ON courtesy_car_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-423
CREATE INDEX FK_debrief_tasks_contact_list_id ON debrief_tasks(contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-424
CREATE INDEX FK_debrief_tasks_garages_debrief_task_id ON debrief_tasks_garages(debrief_task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-425
CREATE INDEX FK_debrief_tasks_garages_garage_id ON debrief_tasks_garages(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-426
CREATE INDEX FK_debrief_tasks_user_assigned_id ON debrief_tasks(user_assigned_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-427
CREATE INDEX FK_dis_dis_networks_distributors ON distributors_distributors_networks(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-428
CREATE INDEX FK_dis_dis_networks_trading_groups ON distributors_distributors_networks(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-429
CREATE INDEX FK_distributors_aag_region_id ON distributors(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-430
CREATE INDEX FK_distributors_association_id ON distributors(association_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-431
CREATE INDEX FK_distributors_comments_distributor_id ON distributors_comments(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-432
CREATE INDEX FK_distributors_contacts_bdm_contact_id ON distributors_contacts_bdm(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-433
CREATE INDEX FK_distributors_contacts_bdm_distributor_id ON distributors_contacts_bdm(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-434
CREATE INDEX FK_distributors_contacts_general_branch_manager_contact_id ON distributors_contacts_general_branch_manager(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-435
CREATE INDEX FK_distributors_contacts_general_branch_manager_distributor_id ON distributors_contacts_general_branch_manager(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-436
CREATE INDEX FK_distributors_contacts_staff_contact_id ON distributors_contacts_staff(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-437
CREATE INDEX FK_distributors_contacts_staff_distributor_id ON distributors_contacts_staff(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-438
CREATE INDEX FK_distributors_contracts_distributor_id ON distributors_contracts(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-439
CREATE INDEX FK_distributors_contracts_trading_group_id ON distributors_contracts(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-440
CREATE INDEX FK_distributors_customer_activities_customer_activity_id ON distributors_customer_activities(customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-441
CREATE INDEX FK_distributors_customer_activities_distributor_id ON distributors_customer_activities(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-442
CREATE INDEX FK_distributors_distributor_id ON distributors(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-443
CREATE INDEX FK_distributors_distributors_activities_distributor_activity_id ON distributors_distributors_activities(distributor_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-444
CREATE INDEX FK_distributors_distributors_activities_distributor_id ON distributors_distributors_activities(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-445
CREATE INDEX FK_distributors_distributors_networks_distributors_networks ON distributors_distributors_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-446
CREATE INDEX FK_distributors_images_distributor_id ON distributors_images(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-447
CREATE INDEX FK_distributors_labels_distributor_id ON distributors_labels(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-448
CREATE INDEX FK_distributors_labels_label_type_id ON distributors_labels(label_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-449
CREATE INDEX FK_distributors_language_id ON distributors(language_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-450
CREATE INDEX FK_distributors_networks_contacts_bdm_contact_id ON distributors_networks_contacts_bdm(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-451
CREATE INDEX FK_distributors_networks_contacts_bdm_distributor_network_id ON distributors_networks_contacts_bdm(distributor_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-452
CREATE INDEX FK_distributors_primary_activity_id ON distributors(primary_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-453
CREATE INDEX FK_distributors_province_id ON distributors(province_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-454
CREATE INDEX FK_distributors_routes_distributor_id ON distributors_routes(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-455
CREATE INDEX FK_distributors_routes_route_id ON distributors_routes(route_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-456
CREATE INDEX FK_distributors_sales_area_id ON distributors(sales_area_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-457
CREATE INDEX FK_distributors_services_distributor_id ON distributors_services(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-458
CREATE INDEX FK_distributors_services_service_type_id ON distributors_services(service_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-459
CREATE INDEX FK_distributors_software_distributor_id ON distributors_software(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-460
CREATE INDEX FK_distributors_software_software_id ON distributors_software(software_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-461
CREATE INDEX FK_distributors_trading_group_id ON distributors(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-462
CREATE INDEX FK_distributors_user_id ON distributors(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-463
CREATE INDEX FK_email_types_sendgrid_licenses_config ON email_types(sendgrid_license_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-464
CREATE INDEX FK_emails_aag_region_id ON emails(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-465
CREATE INDEX FK_emails_country_id ON emails(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-466
CREATE INDEX FK_emails_email_type_id ON emails(email_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-467
CREATE INDEX FK_emails_language_id ON emails(language_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-468
CREATE INDEX FK_emails_languages_webs_networks ON emails(language_web_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-469
CREATE INDEX FK_emails_platform_id ON emails(platform_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-470
CREATE INDEX FK_employee_types_aag_region_id ON employee_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-471
CREATE INDEX FK_enquiries_child_network_id ON enquiries(child_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-472
CREATE INDEX FK_enquiries_garage_id ON enquiries(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-473
CREATE INDEX FK_enquiries_network_id ON enquiries(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-474
CREATE INDEX FK_enquiries_work_id ON enquiries(work_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-475
CREATE INDEX FK_equipments_aag_region_id ON equipments(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-476
CREATE INDEX FK_equipments_types_aag_region_id ON equipments_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-477
CREATE INDEX FK_erp_aag_region_id ON erp(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-478
CREATE INDEX FK_facilities_aag_region_id ON facilities(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-479
CREATE INDEX FK_fleets_networks_fleets ON fleets_networks(fleet_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-480
CREATE INDEX FK_fleets_networks_networks ON fleets_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-481
CREATE INDEX FK_fluids_network_id ON fluids(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-482
CREATE INDEX FK_garages_agreements_agreements ON garages_agreements(agreement_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-483
CREATE INDEX FK_garages_agreements_garages ON garages_agreements(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-484
CREATE INDEX FK_garages_brands_brand_id ON garages_brands(brand_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-485
CREATE INDEX FK_garages_brands_garage_id ON garages_brands(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-486
CREATE INDEX FK_garages_campaign_garages ON garages_campaign(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-487
CREATE INDEX FK_garages_campaign_garages_campaign ON garages_campaign(garage_campaign_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-488
CREATE INDEX FK_garages_city_id ON garages(city_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-489
CREATE INDEX FK_garages_comments_garage_id ON garages_comments(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-490
CREATE INDEX FK_garages_contacts_bdm_contact_id ON garages_contacts_bdm(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-491
CREATE INDEX FK_garages_contacts_bdm_garage_id ON garages_contacts_bdm(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-492
CREATE INDEX FK_garages_contacts_general_branch_manager_contact_id ON garages_contacts_general_branch_manager(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-493
CREATE INDEX FK_garages_contacts_general_branch_manager_garage_id ON garages_contacts_general_branch_manager(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-494
CREATE INDEX FK_garages_contacts_lists_contacts_lists ON garages_contacts_lists(contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-495
CREATE INDEX FK_garages_contacts_lists_garages ON garages_contacts_lists(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-496
CREATE INDEX FK_garages_contacts_staff_contact_id ON garages_contacts_staff(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-497
CREATE INDEX FK_garages_contacts_staff_garage_id ON garages_contacts_staff(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-498
CREATE INDEX FK_garages_courtesy_car_types_courtesy_car_type_id ON garages_courtesy_car_types(courtesy_car_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-499
CREATE INDEX FK_garages_courtesy_car_types_garage_id ON garages_courtesy_car_types(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-500
CREATE INDEX FK_garages_customers_activities_customer_activity_id ON garages_customers_activities(customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-501
CREATE INDEX FK_garages_customers_activities_garage_id ON garages_customers_activities(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-502
CREATE INDEX FK_garages_distributors_distributor_id ON garages_distributors(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-503
CREATE INDEX FK_garages_distributors_garage_id ON garages_distributors(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-504
CREATE INDEX FK_garages_employees_employee_type_id ON garages_employees(employee_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-505
CREATE INDEX FK_garages_employees_garage_id ON garages_employees(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-506
CREATE INDEX FK_garages_equipments_brand_id ON garages_equipments(brand_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-507
CREATE INDEX FK_garages_equipments_equipment_id ON garages_equipments(equipment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-508
CREATE INDEX FK_garages_equipments_equipment_type_id ON garages_equipments(equipment_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-509
CREATE INDEX FK_garages_equipments_garage_id ON garages_equipments(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-510
CREATE INDEX FK_garages_equipments_supplier_id ON garages_equipments(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-511
CREATE INDEX FK_garages_erp ON garages(erp_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-512
CREATE INDEX FK_garages_facilities_facilities ON garages_facilities(facility_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-513
CREATE INDEX FK_garages_facilities_garages ON garages_facilities(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-514
CREATE INDEX FK_garages_files_garage_id ON garages_files(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-515
CREATE INDEX FK_garages_images_garage_id ON garages_images(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-516
CREATE INDEX FK_garages_language ON garages(language_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-517
CREATE INDEX FK_garages_networks_annex_detail_id ON garages_networks(annex_detail_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-518
CREATE INDEX FK_garages_networks_fluids_fluid_id ON garages_networks_fluids(fluid_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-519
CREATE INDEX FK_garages_networks_fluids_garage_network_id ON garages_networks_fluids(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-520
CREATE INDEX FK_garages_networks_garage_id ON garages_networks(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-521
CREATE INDEX FK_garages_networks_genarts_garage_network_id ON garages_networks_genarts(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-522
CREATE INDEX FK_garages_networks_genarts_genart_id ON garages_networks_genarts(genart_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-523
CREATE INDEX FK_garages_networks_genarts_master_genarts_master ON garages_networks_genarts_master(genart_master_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-524
CREATE INDEX FK_garages_networks_images_garage_network_id ON garages_networks_images(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-525
CREATE INDEX FK_garages_networks_network_contract_type_id ON garages_networks(network_contract_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-526
CREATE INDEX FK_garages_networks_network_id ON garages_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-527
CREATE INDEX FK_garages_networks_reason_leaving_id ON garages_networks(reason_leaving_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-528
CREATE INDEX FK_garages_networks_services_drivers_garage_network_id ON garages_networks_services_drivers(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-529
CREATE INDEX FK_garages_networks_services_drivers_service_driver_id ON garages_networks_services_drivers(service_driver_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-530
CREATE INDEX FK_garages_networks_services_garage_network_id ON garages_networks_services(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-531
CREATE INDEX FK_garages_networks_services_service_id ON garages_networks_services(service_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-532
CREATE INDEX FK_garages_networks_vehicle_types_garage_network_id ON garages_networks_vehicle_types(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-533
CREATE INDEX FK_garages_networks_vehicle_types_vehicle_type_id ON garages_networks_vehicle_types(vehicle_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-534
CREATE INDEX FK_garages_networks_vehicles_black_list_network_id ON garages_networks_vehicles_black_list(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-535
CREATE INDEX FK_garages_networks_vehicles_black_list_vehicle_id ON garages_networks_vehicles_black_list(vehicle_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-536
CREATE INDEX FK_garages_networks_vehicles_garage_network_id ON garages_networks_vehicles(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-537
CREATE INDEX FK_garages_networks_vehicles_vehicle_id ON garages_networks_vehicles(vehicle_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-538
CREATE INDEX FK_garages_networks_works_garage_network_id ON garages_networks_works(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-539
CREATE INDEX FK_garages_networks_works_labours_garage_network_id ON garages_networks_works_labours(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-540
CREATE INDEX FK_garages_networks_works_labours_work_id ON garages_networks_works_labours(work_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-541
CREATE INDEX FK_garages_networks_works_prices_garage_network_id ON garages_networks_works_prices(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-542
CREATE INDEX FK_garages_networks_works_prices_work_id ON garages_networks_works_prices(work_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-543
CREATE INDEX FK_garages_networks_works_work_id ON garages_networks_works(work_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-544
CREATE INDEX FK_garages_province_id ON garages(province_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-545
CREATE INDEX FK_garages_routes_garage_id ON garages_routes(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-546
CREATE INDEX FK_garages_routes_route_id ON garages_routes(route_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-547
CREATE INDEX FK_garages_sales_area_id ON garages(sales_area_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-548
CREATE INDEX FK_garages_services_garage_id ON garages_services(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-549
CREATE INDEX FK_garages_services_service_id ON garages_services(service_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-550
CREATE INDEX FK_garages_software_garage_id ON garages_software(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-551
CREATE INDEX FK_garages_software_software_id ON garages_software(software_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-552
CREATE INDEX FK_garages_specialist_makes_garage_id ON garages_specialist_makes(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-553
CREATE INDEX FK_garages_specialist_makes_vehicle_id ON garages_specialist_makes(vehicle_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-554
CREATE INDEX FK_garages_user_id ON garages(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-555
CREATE INDEX FK_garages_value_add_supplier_garages ON garages_value_add_supplier(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-556
CREATE INDEX FK_garages_value_add_supplier_value_add_supplier_type ON garages_value_add_supplier(value_add_supplier_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-557
CREATE INDEX FK_garages_value_add_supplier_value_add_suppliers ON garages_value_add_supplier(value_add_supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-558
CREATE INDEX FK_garages_values_adds_billings_schedules ON garages_values_adds(billing_schedule_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-559
CREATE INDEX FK_garages_vehicle_types_garage_id ON garages_vehicle_types(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-560
CREATE INDEX FK_garages_vehicle_types_vehicle_type_id ON garages_vehicle_types(vehicle_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-561
CREATE INDEX FK_garages_vehicles_garage_id ON garages_vehicles(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-562
CREATE INDEX FK_garages_vehicles_vehicle_id ON garages_vehicles(vehicle_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-563
CREATE INDEX FK_garages_websites_garage_id ON garages_websites(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-564
CREATE INDEX FK_garages_websites_website_id ON garages_websites(website_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-565
CREATE INDEX FK_genart_family_id ON genarts_master(genart_family_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-566
CREATE INDEX FK_genarts_families_network_id ON genarts_families(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-567
CREATE INDEX FK_genarts_family_id ON garages_networks_genarts_families(genart_family_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-568
CREATE INDEX FK_genarts_grouping_genart_id ON genarts(grouping_genart_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-569
CREATE INDEX FK_general_manager_widgets_roles_panels_widgets ON general_manager_widgets_roles(widget_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-570
CREATE INDEX FK_general_manager_widgets_roles_roles ON general_manager_widgets_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-571
CREATE INDEX FK_grages_networks_contacts_contacts ON garages_networks_contacts(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-572
CREATE INDEX FK_grages_networks_contacts_garages_networks ON garages_networks_contacts(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-573
CREATE INDEX FK_grouping_genarts_network_id ON grouping_genarts(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-574
CREATE INDEX FK_groups_permissions_permissions_groups_permissions ON groups_permissions_permissions(group_permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-575
CREATE INDEX FK_groups_permissions_permissions_permissions ON groups_permissions_permissions(permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-576
CREATE INDEX FK_groups_permissions_position_config_type_id ON groups_permissions(position_config_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-577
CREATE INDEX FK_groups_permissions_roles_groups_permissions ON groups_permissions_roles(group_permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-578
CREATE INDEX FK_groups_permissions_roles_roles ON groups_permissions_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-579
CREATE INDEX FK_groups_permissions_users_groups_permissions ON groups_permissions_users(group_permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-580
CREATE INDEX FK_groups_permissions_users_users ON groups_permissions_users(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-581
CREATE INDEX FK_hold_reason_types_aag_region_id ON hold_reason_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-582
CREATE INDEX FK_languages_webs_networks_languages_webs_flags ON languages_webs_networks(language_web_flag_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-583
CREATE INDEX FK_languages_webs_networks_networks ON languages_webs_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-584
CREATE INDEX FK_leaving_reason_types_aag_region_id ON leaving_reason_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-585
CREATE INDEX FK_leaving_reasons_comments_garage_network_id ON leaving_reason_comments(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-586
CREATE INDEX FK_lists_aag_regions ON lists(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-587
CREATE INDEX FK_lists_networks ON lists(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-588
CREATE INDEX FK_logs_changes_distributor_id ON logs_changes(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-589
CREATE INDEX FK_logs_changes_field_id ON logs_changes(field_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-590
CREATE INDEX FK_logs_changes_garage_id ON logs_changes(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-591
CREATE INDEX FK_logs_changes_group_permission_id ON logs_changes(group_permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-592
CREATE INDEX FK_logs_changes_position_id ON logs_changes(position_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-593
CREATE INDEX FK_logs_changes_table_id ON logs_changes(table_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-594
CREATE INDEX FK_logs_changes_user_id ON logs_changes(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-595
CREATE INDEX FK_logs_fields_table_id ON logs_fields(table_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-596
CREATE INDEX FK_logs_logins_user_id ON logs_login(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-597
CREATE INDEX FK_network_cities_cities ON network_cities(city_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-598
CREATE INDEX FK_network_cities_networks ON network_cities(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-599
CREATE INDEX FK_network_id ON genarts_master(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-600
CREATE INDEX FK_networks_aag_region_id ON networks(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-601
CREATE INDEX FK_networks_contact_lists_contacts_litsts ON networks_contacts_lists(contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-602
CREATE INDEX FK_networks_contacts_bdm_contact_id ON networks_contacts_bdm(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-603
CREATE INDEX FK_networks_contacts_bdm_distributor_id ON networks_contacts_bdm(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-604
CREATE INDEX FK_networks_contacts_bdm_garage_id ON networks_contacts_bdm(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-605
CREATE INDEX FK_networks_contacts_bdm_network_id ON networks_contacts_bdm(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-606
CREATE INDEX FK_networks_contract_types_aag_region_id ON networks_contract_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-607
CREATE INDEX FK_networks_distance_unit_id ON networks(distance_unit_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-608
CREATE INDEX FK_networks_email_booking_contact_id ON networks(email_booking_contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-609
CREATE INDEX FK_networks_email_enquiry_contact_id ON networks(email_enquiry_contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-610
CREATE INDEX FK_networks_parent_network_id ON networks(parent_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-611
CREATE INDEX FK_networks_pricing_type_id ON networks(pricing_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-612
CREATE INDEX FK_networks_quoting_type_id ON networks(quoting_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-613
CREATE INDEX FK_networks_recommended_internal_network_id ON networks_recommended(internal_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-614
CREATE INDEX FK_networks_recommended_network_id ON networks_recommended(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-615
CREATE INDEX FK_order_order_type ON orders(order_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-616
CREATE INDEX FK_order_product_id ON order_types_products(order_product_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-617
CREATE INDEX FK_order_products_aag_region_id ON order_products(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-618
CREATE INDEX FK_order_type_id ON order_types_products(order_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-619
CREATE INDEX FK_order_types_aag_region_id ON order_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-620
CREATE INDEX FK_order_types_products_aag_region_id ON order_types_products(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-621
CREATE INDEX FK_orders_garage_id ON orders(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-622
CREATE INDEX FK_panel_gm_widgets_roles_panels_widgets ON panel_gm_widgets_roles(widget_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-623
CREATE INDEX FK_panel_gm_widgets_roles_roles ON panel_gm_widgets_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-624
CREATE INDEX FK_panels_widgets_roles_panels_widgets ON panels_widgets_roles(widget_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-625
CREATE INDEX FK_panels_widgets_roles_roles ON panels_widgets_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-626
CREATE INDEX FK_panels_widgets_users_panels_widgets ON panels_widgets_users(widget_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-627
CREATE INDEX FK_panels_widgets_users_users ON panels_widgets_users(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-628
CREATE INDEX FK_permissions_groupings_permissions ON permissions(grouping_permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-629
CREATE INDEX FK_permissions_position_config_type_id ON permissions(position_config_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-630
CREATE INDEX FK_permissions_roles_permissions ON permissions_roles(permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-631
CREATE INDEX FK_permissions_roles_roles ON permissions_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-632
CREATE INDEX FK_permissions_users_permissions ON permissions_users(permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-633
CREATE INDEX FK_permissions_users_users ON permissions_users(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-634
CREATE INDEX FK_permissions_versions_permissions ON permissions_versions(permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-635
CREATE INDEX FK_permissions_versions_versions ON permissions_versions(version_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-636
CREATE INDEX FK_platforms_aag_regions ON platforms(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-637
CREATE INDEX FK_platforms_networks ON platforms(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-638
CREATE INDEX FK_positions_config_aag_members_position_config_id ON positions_config_aag_members(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-639
CREATE INDEX FK_positions_config_bdms_position_config_id ON positions_config_bdms(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-640
CREATE INDEX FK_positions_config_bdms_user_id ON positions_config_bdms(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-641
CREATE INDEX FK_positions_config_networks_network_id ON positions_config_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-642
CREATE INDEX FK_positions_config_networks_position_config_id ON positions_config_networks(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-643
CREATE INDEX FK_positions_config_position_config_type_id ON positions_config(position_config_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-644
CREATE INDEX FK_positions_config_regions_position_config_id ON positions_config_regions(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-645
CREATE INDEX FK_positions_config_regions_region_id ON positions_config_regions(region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-646
CREATE INDEX FK_positions_config_trading_groups_position_config_id ON positions_config_trading_groups(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-647
CREATE INDEX FK_positions_config_trading_groups_trading_group_id ON positions_config_trading_groups(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-648
CREATE INDEX FK_positions_role_id ON positions(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-649
CREATE INDEX FK_postcode_provinces_provinces ON postcode_provinces(province_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-650
CREATE INDEX FK_province_id_cities ON cities(province_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-651
CREATE INDEX FK_provinces_country_id ON provinces(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-652
CREATE INDEX FK_quotations_garage_id ON quotations(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-653
CREATE INDEX FK_quotations_network_id ON quotations(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-654
CREATE INDEX FK_reasons_allowances_aag_region_id ON reasons_allowances(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-655
CREATE INDEX FK_reasons_delegates_aag_region_id ON reasons_delegates(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-656
CREATE INDEX FK_reasons_delegates_cancelled_aag_region_id ON reasons_delegates_cancelled(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-657
CREATE INDEX FK_review_requests_aag_region_id ON review_requests(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-658
CREATE INDEX FK_review_requests_booking_id ON review_requests(booking_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-659
CREATE INDEX FK_role_id ON config_modules_regions_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-660
CREATE INDEX FK_role_id ON sms_licenses_config(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-661
CREATE INDEX FK_room_conferences_delegates ON dinner(conferences_delegates_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-662
CREATE INDEX FK_room_conferences_delegates ON room(conferences_delegates_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-663
CREATE INDEX FK_room_conferences_delegates ON trade_show(conferences_delegates_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-664
CREATE INDEX FK_room_type_aag_region_id ON room_type(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-665
CREATE INDEX FK_room_weeks ON dinner(weeks_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-666
CREATE INDEX FK_room_weeks ON room(weeks_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-667
CREATE INDEX FK_room_weeks ON trade_show(weeks_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-668
CREATE INDEX FK_sale_areas_aag_region_id ON sales_areas(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-669
CREATE INDEX FK_sections_subsections_communication_section_id ON sections_subsections(communication_section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-670
CREATE INDEX FK_sendgrid_config_Aag_regions ON sendgrid_licenses_config(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-671
CREATE INDEX FK_sendgrid_email_types_templates_aag_region_id ON sendgrid_email_types_templates(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-672
CREATE INDEX FK_sendgrid_email_types_templates_country_id ON sendgrid_email_types_templates(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-673
CREATE INDEX FK_sendgrid_email_types_templates_email_type_id ON sendgrid_email_types_templates(email_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-674
CREATE INDEX FK_sendgrid_email_types_templates_email_type_id ON sendgrid_email_types_viewvars(email_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-675
CREATE INDEX FK_sendgrid_email_types_templates_language_id ON sendgrid_email_types_templates(language_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-676
CREATE INDEX FK_sendgrid_email_types_templates_languages_webs_networks ON sendgrid_email_types_templates(language_web_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-677
CREATE INDEX FK_sendgrid_email_types_templates_platform_id ON sendgrid_email_types_templates(platform_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-678
CREATE INDEX FK_sendgrid_licenses_config_countries ON sendgrid_licenses_config(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-679
CREATE INDEX FK_sendgrid_licenses_config_platforms ON sendgrid_licenses_config(platform_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-680
CREATE INDEX FK_sendgrid_viewvars_templates_sendgrid_viewvar_id ON sendgrid_email_types_viewvars(sendgrid_viewvar_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-681
CREATE INDEX FK_services_drivers_network_id ON services_drivers(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-682
CREATE INDEX FK_set_distributors_objectives_appointments_objectives ON set_distributors_objectives(objective_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-683
CREATE INDEX FK_set_distributors_objectives_users ON set_distributors_objectives(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-684
CREATE INDEX FK_shortcuts_trading_groups_shortcut_id ON shortcuts_trading_groups(shortcut_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-685
CREATE INDEX FK_shortcuts_trading_groups_trading_group_id ON shortcuts_trading_groups(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-686
CREATE INDEX FK_shortner_api_users ON shortner_url_api(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-687
CREATE INDEX FK_shortner_url_log_countries ON shortner_url_log(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-688
CREATE INDEX FK_software_aag_region_id ON software(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-689
CREATE INDEX FK_software_manufactures_aag_region_id ON software_manufactures(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-690
CREATE INDEX FK_software_types_aag_region_id ON software_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-691
CREATE INDEX FK_stand_size_aag_region_id ON stand_size(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-692
CREATE INDEX FK_suppliers_aag_region_id ON suppliers(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-693
CREATE INDEX FK_suppliers_categories_aag_region_id ON suppliers_categories(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-694
CREATE INDEX FK_suppliers_files_supplier_id ON suppliers_files(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-695
CREATE INDEX FK_suppliers_networks_network_id ON suppliers_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-696
CREATE INDEX FK_suppliers_networks_supplier_id ON suppliers_networks(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-697
CREATE INDEX FK_suppliers_trading_groups_supplier_id ON suppliers_trading_groups(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-698
CREATE INDEX FK_suppliers_trading_groups_trading_group_id ON suppliers_trading_groups(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-699
CREATE INDEX FK_tasks_appointment_id ON tasks(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-700
CREATE INDEX FK_tasks_contacts_lists_contact_list_id ON tasks_contacts_lists(contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-701
CREATE INDEX FK_tasks_contacts_lists_task_id ON tasks_contacts_lists(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-702
CREATE INDEX FK_tasks_files_task_id ON tasks_files(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-703
CREATE INDEX FK_tasks_user_assigned_id ON tasks(user_assigned_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-704
CREATE INDEX FK_tasks_user_creation_id ON tasks(user_creation_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-705
CREATE INDEX FK_tasks_users_task_id ON tasks_users(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-706
CREATE INDEX FK_tasks_users_user_id ON tasks_users(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-707
CREATE INDEX FK_template_type_id ON sms_templates(template_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-708
CREATE INDEX FK_trading_groups_aag_region_id ON trading_groups(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-709
CREATE INDEX FK_trading_groups_distributors_networks_network_id ON trading_groups_distributors_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-710
CREATE INDEX FK_trading_groups_distributors_networks_trading_group_id ON trading_groups_distributors_networks(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-711
CREATE INDEX FK_trading_groups_networks_network_id ON trading_groups_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-712
CREATE INDEX FK_trading_groups_networks_trading_group_id ON trading_groups_networks(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-713
CREATE INDEX FK_trainings_allowances_garages_networks ON trainings_allowances(garage_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-714
CREATE INDEX FK_trainings_credits_networks_reasons_allowances ON trainings_credits_networks(reason_allowance_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-715
CREATE INDEX FK_trainings_credits_networks_trainings_allowances ON trainings_credits_networks(training_allowance_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-716
CREATE INDEX FK_trainings_credits_networks_trainings_delegates ON trainings_credits_networks(training_delegate_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-717
CREATE INDEX FK_trainings_credits_networks_trainings_planned_courses ON trainings_credits_networks(training_planned_course_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-718
CREATE INDEX FK_trainings_delegates_networks ON trainings_delegates(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-719
CREATE INDEX FK_trainings_delegates_reasons_delegates ON trainings_delegates(reason_delegate_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-720
CREATE INDEX FK_trainings_delegates_reasons_delegates_cancelled ON trainings_delegates(reason_cancelled_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-721
CREATE INDEX FK_trainings_planned_courses_trainings_courses ON trainings_planned_courses(training_course_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-722
CREATE INDEX FK_trainings_planned_courses_trainings_trainers ON trainings_planned_courses(training_trainer_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-723
CREATE INDEX FK_trainings_planned_courses_venues ON trainings_planned_courses(venue_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-724
CREATE INDEX FK_tutorials_roles_role_id ON tutorials_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-725
CREATE INDEX FK_tutorials_roles_tutorial_id ON tutorials_roles(tutorial_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-726
CREATE INDEX FK_users_aag_region_id ON users(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-727
CREATE INDEX FK_users_contact_id ON users(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-728
CREATE INDEX FK_users_country_id ON users(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-729
CREATE INDEX FK_users_distributors ON users(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-730
CREATE INDEX FK_users_garages ON users(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-731
CREATE INDEX FK_users_images_user_id ON users_images(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-732
CREATE INDEX FK_users_language_id ON users(language_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-733
CREATE INDEX FK_users_reassignments_appointments ON users_reassignments(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-734
CREATE INDEX FK_users_reassignments_contacts_contacts_lists ON users_reassignments(contact_contact_list_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-735
CREATE INDEX FK_users_reassignments_routes ON users_reassignments(route_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-736
CREATE INDEX FK_users_reassignments_tasks ON users_reassignments(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-737
CREATE INDEX FK_users_reassignments_users ON users_reassignments(user_id_origin);

-- changeset LisaCanéSáizSoftecaI:1756821087342-738
CREATE INDEX FK_users_reassignments_users_2 ON users_reassignments(user_id_destination);

-- changeset LisaCanéSáizSoftecaI:1756821087342-739
CREATE INDEX FK_users_searches ON users_searches(network_status_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-740
CREATE INDEX FK_value_add_supplier_type_aag_region_id ON value_add_supplier_type(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-741
CREATE INDEX FK_value_add_suppliers_aag_region_id ON value_add_suppliers(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-742
CREATE INDEX FK_values_adds_aag_region_id ON values_adds(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-743
CREATE INDEX FK_venues_aag_region_id ON venues(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-744
CREATE INDEX FK_venues_sales_area_id ON venues(sales_area_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-745
CREATE INDEX FK_venues_types_aag_region_id ON venues_types(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-746
CREATE INDEX FK_venues_venues_types ON venues(venue_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-747
CREATE INDEX FK_websites_aag_region_id ON websites(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-748
CREATE INDEX FK_works_grouping_genart_id ON works(grouping_genart_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-749
CREATE INDEX FK_works_network_id ON works(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-750
CREATE INDEX aag_region_id ON communications(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-751
CREATE INDEX aag_region_id ON communications_sections(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-752
CREATE INDEX aag_region_id ON distributors_networks(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-753
CREATE INDEX aag_region_id ON fleets(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-754
CREATE INDEX aag_region_id ON sections_subsections(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-755
CREATE INDEX aag_region_id ON value_added_suppliers(aag_region_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-756
CREATE INDEX account_number ON distributors(account_number);

-- changeset LisaCanéSáizSoftecaI:1756821087342-757
CREATE INDEX active ON email_types(active);

-- changeset LisaCanéSáizSoftecaI:1756821087342-758
CREATE INDEX active ON fleets(active);

-- changeset LisaCanéSáizSoftecaI:1756821087342-759
CREATE INDEX active ON genarts(active);

-- changeset LisaCanéSáizSoftecaI:1756821087342-760
CREATE INDEX active ON genarts_master(active);

-- changeset LisaCanéSáizSoftecaI:1756821087342-761
CREATE INDEX active ON permissions(active);

-- changeset LisaCanéSáizSoftecaI:1756821087342-762
CREATE INDEX active ON venues(active);

-- changeset LisaCanéSáizSoftecaI:1756821087342-763
CREATE INDEX appointment_id ON appointments_objective_comments(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-764
CREATE INDEX article_id ON users_statistics(article_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-765
CREATE INDEX association_type_id ON distributors(association_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-766
CREATE INDEX billing_schedule_id ON garages_equipments(billing_schedule_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-767
CREATE INDEX billing_schedule_id ON garages_software(billing_schedule_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-768
CREATE INDEX brand_id ON products(brand_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-769
CREATE INDEX code ON genarts(code);

-- changeset LisaCanéSáizSoftecaI:1756821087342-770
CREATE INDEX communication_id ON communications_customers_activities(communication_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-771
CREATE INDEX communication_id ON communications_distributors_networks(communication_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-772
CREATE INDEX communication_id ON communications_files(communication_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-773
CREATE INDEX communication_id ON communications_networks(communication_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-774
CREATE INDEX communication_id ON communications_positions(communication_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-775
CREATE INDEX communication_id ON communications_users(communication_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-776
CREATE INDEX communication_section_id ON communications(communication_section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-777
CREATE INDEX communication_section_id ON communications_sections_customers_activities(communication_section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-778
CREATE INDEX communication_section_id ON communications_sections_distributors_networks(communication_section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-779
CREATE INDEX communication_section_id ON communications_sections_networks(communication_section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-780
CREATE INDEX communication_section_id ON communications_sections_positions(communication_section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-781
CREATE INDEX communication_section_id ON communications_sections_trading_groups(communication_section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-782
CREATE INDEX contact_id ON emails_daily(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-783
CREATE INDEX contact_id ON searches_contacts(contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-784
CREATE INDEX country_id ON fleets(country_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-785
CREATE INDEX customer_activity_id ON communications_customers_activities(customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-786
CREATE INDEX customer_activity_id ON communications_sections_customers_activities(customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-787
CREATE INDEX customer_activity_id ON positions_config_customer_activities(customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-788
CREATE INDEX customer_activity_id ON sections_subsections_customers_activities(customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-789
CREATE INDEX customer_activity_id ON shortcuts_customers_activities(customer_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-790
CREATE INDEX date ON appointments(date);

-- changeset LisaCanéSáizSoftecaI:1756821087342-791
CREATE INDEX debrief_task_id ON debrief_tasks_distributors(debrief_task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-792
CREATE INDEX distributor_id ON contacts(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-793
CREATE INDEX distributor_id ON debrief_tasks_distributors(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-794
CREATE INDEX distributor_id ON distributors_networks_contacts_bdm(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-795
CREATE INDEX distributor_id ON distributors_objectives(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-796
CREATE INDEX distributor_id ON garages_distributors_shortcuts(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-797
CREATE INDEX distributor_id ON messages_distributors(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-798
CREATE INDEX distributor_id ON requested_changes(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-799
CREATE INDEX distributor_id ON searches_distributors(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-800
CREATE INDEX distributor_id ON tasks_distributors(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-801
CREATE INDEX distributor_id ON users_statistics(distributor_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-802
CREATE INDEX distributor_network_id ON communications_distributors_networks(distributor_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-803
CREATE INDEX distributor_network_id ON communications_sections_distributors_networks(distributor_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-804
CREATE INDEX distributor_network_id ON distributors_contracts(distributor_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-805
CREATE INDEX distributor_network_id ON positions_config_distributor_networks(distributor_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-806
CREATE INDEX distributor_network_id ON sections_subsections_distributors_networks(distributor_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-807
CREATE INDEX distributor_network_id ON shortcuts_distributors_networks(distributor_network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-808
CREATE INDEX distributor_type_id ON distributors(distributor_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-809
CREATE INDEX erp_id ON fleets(erp_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-810
CREATE INDEX field_id ON requested_changes(field_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-811
CREATE INDEX g_number_id ON garages(g_number_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-812
CREATE INDEX garage_id ON contacts(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-813
CREATE INDEX garage_id ON distributors_networks_contacts_bdm(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-814
CREATE INDEX garage_id ON garages_distributors_shortcuts(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-815
CREATE INDEX garage_id ON garages_oils(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-816
CREATE INDEX garage_id ON garages_workshop_activities(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-817
CREATE INDEX garage_id ON messages_garages(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-818
CREATE INDEX garage_id ON requested_changes(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-819
CREATE INDEX garage_id ON tasks_garages(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-820
CREATE INDEX garage_id ON users_statistics(garage_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-821
CREATE INDEX global ON contacts_lists(global);

-- changeset LisaCanéSáizSoftecaI:1756821087342-822
CREATE INDEX guid ON garages(guid);

-- changeset LisaCanéSáizSoftecaI:1756821087342-823
CREATE INDEX insurance_agreement_id ON garages(insurance_agreement_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-824
CREATE INDEX is_labour_time ON genarts(is_labour_time);

-- changeset LisaCanéSáizSoftecaI:1756821087342-825
CREATE INDEX is_labour_time ON genarts_master(is_labour_time);

-- changeset LisaCanéSáizSoftecaI:1756821087342-826
CREATE INDEX latitude ON distributors(latitude);

-- changeset LisaCanéSáizSoftecaI:1756821087342-827
CREATE INDEX leaving_reason_id ON distributors_contracts(leaving_reason_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-828
CREATE INDEX logistic_center_id ON contacts(logistic_center_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-829
CREATE INDEX longitude ON distributors(longitude);

-- changeset LisaCanéSáizSoftecaI:1756821087342-830
CREATE INDEX message_id ON messages_distributors(message_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-831
CREATE INDEX message_id ON messages_files(message_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-832
CREATE INDEX message_id ON messages_garages(message_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-833
CREATE INDEX name ON cities(name);

-- changeset LisaCanéSáizSoftecaI:1756821087342-834
CREATE INDEX name ON distributors(name);

-- changeset LisaCanéSáizSoftecaI:1756821087342-835
CREATE INDEX network_id ON communications_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-836
CREATE INDEX network_id ON communications_sections_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-837
CREATE INDEX network_id ON distributors_contracts(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-838
CREATE INDEX network_id ON garages_distributors_shortcuts(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-839
CREATE INDEX network_id ON searches_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-840
CREATE INDEX network_id ON sections_subsections_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-841
CREATE INDEX network_id ON shortcuts_networks(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-842
CREATE INDEX network_id ON users_statistics(network_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-843
CREATE INDEX objective_id ON appointments_objective_comments(objective_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-844
CREATE INDEX objective_id ON distributors_objectives(objective_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-845
CREATE INDEX oil_id ON garages_oils(oil_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-846
CREATE INDEX old ON emails(old);

-- changeset LisaCanéSáizSoftecaI:1756821087342-847
CREATE INDEX permission_id ON positions_config_types_permissions(permission_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-848
CREATE INDEX position_config_id ON positions_config_customer_activities(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-849
CREATE INDEX position_config_id ON positions_config_distributor_networks(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-850
CREATE INDEX position_config_id ON positions_config_profiles(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-851
CREATE INDEX position_config_id ON positions_config_suppliers_categories(position_config_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-852
CREATE INDEX position_config_type_id ON positions_config_types_permissions(position_config_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-853
CREATE INDEX position_config_type_id ON roles_positions_config_types(position_config_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-854
CREATE INDEX position_id ON communications_positions(position_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-855
CREATE INDEX position_id ON communications_sections_positions(position_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-856
CREATE INDEX position_id ON sections_subsections_positions(position_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-857
CREATE INDEX position_id ON shortcuts_positions(position_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-858
CREATE INDEX postcode ON postcode_provinces(postcode);

-- changeset LisaCanéSáizSoftecaI:1756821087342-859
CREATE INDEX product_id ON products_images(product_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-860
CREATE INDEX profile_id ON positions_config_profiles(profile_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-861
CREATE INDEX province_id ON fleets(province_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-862
CREATE INDEX reason_hold_id ON garages_agreements(reason_hold_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-863
CREATE INDEX reason_leaving_id ON distributors_distributors_networks(reason_leaving_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-864
CREATE INDEX reason_leaving_id ON garages_agreements(reason_leaving_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-865
CREATE INDEX ref_code ON garages(ref_code);

-- changeset LisaCanéSáizSoftecaI:1756821087342-866
CREATE INDEX repairmaintenance ON fleets(repairmaintenance);

-- changeset LisaCanéSáizSoftecaI:1756821087342-867
CREATE INDEX repairmaintenance ON garages(repairmaintenance);

-- changeset LisaCanéSáizSoftecaI:1756821087342-868
CREATE INDEX requested_change_id ON requested_changes_images(requested_change_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-869
CREATE INDEX role_id ON roles_positions_config_types(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-870
CREATE INDEX role_id ON shortcuts_roles(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-871
CREATE INDEX role_id ON users(role_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-872
CREATE INDEX route_id ON users_searches(route_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-873
CREATE INDEX search_id ON searches_contacts(search_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-874
CREATE INDEX search_id ON searches_distributors(search_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-875
CREATE INDEX search_id ON searches_networks(search_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-876
CREATE INDEX section_id ON users_statistics(section_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-877
CREATE INDEX section_subsection_id ON communications(section_subsection_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-878
CREATE INDEX section_subsection_id ON sections_subsections_customers_activities(section_subsection_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-879
CREATE INDEX section_subsection_id ON sections_subsections_distributors_networks(section_subsection_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-880
CREATE INDEX section_subsection_id ON sections_subsections_networks(section_subsection_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-881
CREATE INDEX section_subsection_id ON sections_subsections_positions(section_subsection_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-882
CREATE INDEX section_subsection_id ON sections_subsections_trading_groups(section_subsection_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-883
CREATE INDEX sendgrid_template_id ON sendgrid_email_types_templates(sendgrid_template_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-884
CREATE INDEX sent ON emails(sent);

-- changeset LisaCanéSáizSoftecaI:1756821087342-885
CREATE INDEX shortcut_id ON garages_distributors_shortcuts(shortcut_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-886
CREATE INDEX shortcut_id ON shortcuts_customers_activities(shortcut_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-887
CREATE INDEX shortcut_id ON shortcuts_distributors_networks(shortcut_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-888
CREATE INDEX shortcut_id ON shortcuts_networks(shortcut_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-889
CREATE INDEX shortcut_id ON shortcuts_positions(shortcut_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-890
CREATE INDEX shortcut_id ON shortcuts_roles(shortcut_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-891
CREATE INDEX shortcut_type_id ON shortcuts(shortcut_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-892
CREATE INDEX slug ON garages(slug);

-- changeset LisaCanéSáizSoftecaI:1756821087342-893
CREATE INDEX software_manufacture_id ON distributors_software(software_manufacture_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-894
CREATE INDEX software_manufacture_id ON garages_software(software_manufacture_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-895
CREATE INDEX software_type_id ON distributors_software(software_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-896
CREATE INDEX software_type_id ON garages_software(software_type_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-897
CREATE INDEX status ON conferences(status);

-- changeset LisaCanéSáizSoftecaI:1756821087342-898
CREATE INDEX status ON distributors(status);

-- changeset LisaCanéSáizSoftecaI:1756821087342-899
CREATE INDEX status ON garages(status);

-- changeset LisaCanéSáizSoftecaI:1756821087342-900
CREATE INDEX status ON garages_networks(status);

-- changeset LisaCanéSáizSoftecaI:1756821087342-901
CREATE INDEX status ON shortner_url_log(status);

-- changeset LisaCanéSáizSoftecaI:1756821087342-902
CREATE INDEX supplier_category_id ON positions_config_suppliers_categories(supplier_category_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-903
CREATE INDEX supplier_id ON distributors_software(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-904
CREATE INDEX supplier_id ON garages_software(supplier_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-905
CREATE INDEX table_id ON requested_changes(table_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-906
CREATE INDEX task_id ON appointments_topics(appointment_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-907
CREATE INDEX task_id ON tasks_codes(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-908
CREATE INDEX task_id ON tasks_distributors(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-909
CREATE INDEX task_id ON tasks_garages(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-910
CREATE INDEX task_id ON tasks_images(task_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-911
CREATE INDEX task_status_id ON tasks(task_status_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-912
CREATE INDEX topic_id ON appointments_topics(topic_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-913
CREATE INDEX town ON distributors(town);

-- changeset LisaCanéSáizSoftecaI:1756821087342-914
CREATE INDEX town ON garages(name);

-- changeset LisaCanéSáizSoftecaI:1756821087342-915
CREATE INDEX trading_group_id ON communications_sections_trading_groups(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-916
CREATE INDEX trading_group_id ON garages_networks(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-917
CREATE INDEX trading_group_id ON sections_subsections_trading_groups(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-918
CREATE INDEX trading_group_id ON users_statistics(trading_group_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-919
CREATE INDEX training_provider_id ON trainings_trainers(training_provider_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-920
CREATE INDEX type ON messages(type);

-- changeset LisaCanéSáizSoftecaI:1756821087342-921
CREATE INDEX user_assigned_id ON routes(user_assigned_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-922
CREATE INDEX user_id ON communications_users(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-923
CREATE INDEX user_id ON contacts_lists(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-924
CREATE INDEX user_id ON distributors_comments(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-925
CREATE INDEX user_id ON garages_distributors_shortcuts(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-926
CREATE INDEX user_id ON messages(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-927
CREATE INDEX user_id ON requested_changes(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-928
CREATE INDEX user_id ON tutorials(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-929
CREATE INDEX user_id ON users_recover_passwords(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-930
CREATE INDEX user_id ON users_searches(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-931
CREATE INDEX user_id ON users_statistics(user_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-932
CREATE INDEX visit_contact_id ON appointments(visit_contact_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-933
CREATE INDEX workshop_activity_id ON garages_workshop_activities(workshop_activity_id);

-- changeset LisaCanéSáizSoftecaI:1756821087342-934
ALTER TABLE communications_sections_customers_activities ADD CONSTRAINT FK_CS_customers_activities_communications_sections FOREIGN KEY (communication_section_id) REFERENCES communications_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-935
ALTER TABLE communications_sections_customers_activities ADD CONSTRAINT FK_CS_customers_activities_customers_activities FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-936
ALTER TABLE sections_subsections_customers_activities ADD CONSTRAINT FK_SS_customers_activities FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-937
ALTER TABLE sections_subsections_customers_activities ADD CONSTRAINT FK_SS_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-938
ALTER TABLE communications_distributors_networks ADD CONSTRAINT FK__communications FOREIGN KEY (communication_id) REFERENCES communications (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-939
ALTER TABLE trainings_credits_networks ADD CONSTRAINT FK__contacts FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-940
ALTER TABLE trainings_courses ADD CONSTRAINT FK__courses_types FOREIGN KEY (course_type_id) REFERENCES courses_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-941
ALTER TABLE tasks_distributors ADD CONSTRAINT FK__distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-942
ALTER TABLE communications_distributors_networks ADD CONSTRAINT FK__distributors_networks FOREIGN KEY (distributor_network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-943
ALTER TABLE garages_values_adds ADD CONSTRAINT FK__garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-944
ALTER TABLE garages_b2b_postcodes ADD CONSTRAINT FK__garages_b2b FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-945
ALTER TABLE garages_b2c_postcodes ADD CONSTRAINT FK__garages_b2c FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-946
ALTER TABLE trainings_delegates ADD CONSTRAINT FK__garages_contacts_staff FOREIGN KEY (garage_contact_staff_id) REFERENCES garages_contacts_staff (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-947
ALTER TABLE trainings_credits_networks ADD CONSTRAINT FK__garages_networks FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-948
ALTER TABLE garages_products ADD CONSTRAINT FK__order_products FOREIGN KEY (product_id) REFERENCES order_products (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-949
ALTER TABLE garages_products ADD CONSTRAINT FK__orders FOREIGN KEY (order_id) REFERENCES orders (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-950
ALTER TABLE tasks_distributors ADD CONSTRAINT FK__tasks FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-951
ALTER TABLE trainings_delegates ADD CONSTRAINT FK__trainings_courses FOREIGN KEY (training_planned_course_id) REFERENCES trainings_planned_courses (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-952
ALTER TABLE trainings_courses ADD CONSTRAINT FK__trainings_providers FOREIGN KEY (training_provider_id) REFERENCES trainings_providers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-953
ALTER TABLE garages_values_adds ADD CONSTRAINT FK__values_adds FOREIGN KEY (value_add_id) REFERENCES values_adds (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-954
ALTER TABLE conferences ADD CONSTRAINT FK__venues FOREIGN KEY (venue_id) REFERENCES venues (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-955
ALTER TABLE sections_subsections_customers_activities ADD CONSTRAINT FK_aa_customers_activities FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-956
ALTER TABLE sections_subsections_customers_activities ADD CONSTRAINT FK_aa_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-957
ALTER TABLE config_modules_regions_roles ADD CONSTRAINT FK_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-958
ALTER TABLE countries ADD CONSTRAINT FK_aag_region_id_countries FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-959
ALTER TABLE garages ADD CONSTRAINT FK_aag_region_id_garages FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-960
ALTER TABLE sms_licenses_config ADD CONSTRAINT FK_aag_region_id_sms FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-961
ALTER TABLE distributors_customer_activities_workshops ADD CONSTRAINT FK_activities_workshops_customer_activities FOREIGN KEY (distributor_customer_activity_id) REFERENCES distributors_customer_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-962
ALTER TABLE distributors_customer_activities_workshops ADD CONSTRAINT FK_activities_workshops_workshop_activities FOREIGN KEY (workshop_activity_id) REFERENCES workshop_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-963
ALTER TABLE agreements ADD CONSTRAINT FK_agreements_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-964
ALTER TABLE alerts ADD CONSTRAINT FK_alerts_alert_type_id FOREIGN KEY (alert_type_id) REFERENCES alerts_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-965
ALTER TABLE alerts ADD CONSTRAINT FK_alerts_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-966
ALTER TABLE annex_details ADD CONSTRAINT FK_annex_details_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-967
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_appointment_feeling_id FOREIGN KEY (appointment_feeling_id) REFERENCES appointments_feelings (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-968
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_appointment_status_id FOREIGN KEY (appointment_status_id) REFERENCES appointments_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-969
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_appointment_type_id FOREIGN KEY (appointment_type_id) REFERENCES appointments_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-970
ALTER TABLE appointments_comments ADD CONSTRAINT FK_appointments_comments_appointment_id FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-971
ALTER TABLE appointments_comments ADD CONSTRAINT FK_appointments_comments_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-972
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_contacts FOREIGN KEY (visit_contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-973
ALTER TABLE appointments_contacts ADD CONSTRAINT FK_appointments_contacts_appointment_id FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-974
ALTER TABLE appointments_contacts ADD CONSTRAINT FK_appointments_contacts_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-975
ALTER TABLE appointments_contacts_lists ADD CONSTRAINT FK_appointments_contacts_lists_appointment_id FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-976
ALTER TABLE appointments_contacts_lists ADD CONSTRAINT FK_appointments_contacts_lists_contact_list_id FOREIGN KEY (contact_list_id) REFERENCES contacts_lists (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-977
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-978
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_feedback_user_modification FOREIGN KEY (feedback_user_modification) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-979
ALTER TABLE appointments_files ADD CONSTRAINT FK_appointments_files_appointment_id FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-980
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-981
ALTER TABLE appointments_objective_comments ADD CONSTRAINT FK_appointments_objective_comments_appointments FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-982
ALTER TABLE appointments_objective_comments ADD CONSTRAINT FK_appointments_objective_comments_appointments_objectives FOREIGN KEY (objective_id) REFERENCES appointments_objectives (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-983
ALTER TABLE appointments_objectives ADD CONSTRAINT FK_appointments_objectives_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-984
ALTER TABLE appointments_topics ADD CONSTRAINT FK_appointments_topics_appointments FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-985
ALTER TABLE appointments_topics ADD CONSTRAINT FK_appointments_topics_debrief_topics FOREIGN KEY (topic_id) REFERENCES debrief_topics (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-986
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_user_assigned_id FOREIGN KEY (user_assigned_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-987
ALTER TABLE appointments ADD CONSTRAINT FK_appointments_user_creation_id FOREIGN KEY (user_creation_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-988
ALTER TABLE associations_types ADD CONSTRAINT FK_associantions_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-989
ALTER TABLE billings_schedules ADD CONSTRAINT FK_billings_schedules_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-990
ALTER TABLE bookings ADD CONSTRAINT FK_bookings_child_network_id FOREIGN KEY (child_network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-991
ALTER TABLE bookings ADD CONSTRAINT FK_bookings_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-992
ALTER TABLE bookings ADD CONSTRAINT FK_bookings_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-993
ALTER TABLE bookings ADD CONSTRAINT FK_bookings_work_id FOREIGN KEY (work_id) REFERENCES works (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-994
ALTER TABLE brands_images ADD CONSTRAINT FK_brand_image_brand FOREIGN KEY (brand_id) REFERENCES brands (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-995
ALTER TABLE brands ADD CONSTRAINT FK_brands_supplier_id FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-996
ALTER TABLE campaign_entries ADD CONSTRAINT FK_campaign_entries_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-997
ALTER TABLE communications ADD CONSTRAINT FK_communications_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-998
ALTER TABLE communications ADD CONSTRAINT FK_communications_communications_sections FOREIGN KEY (communication_section_id) REFERENCES communications_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-999
ALTER TABLE communications_customers_activities ADD CONSTRAINT FK_communications_customers_activities_communications FOREIGN KEY (communication_id) REFERENCES communications (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1000
ALTER TABLE communications_customers_activities ADD CONSTRAINT FK_communications_customers_activities_customers_activities FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1001
ALTER TABLE communications_files ADD CONSTRAINT FK_communications_files_communications FOREIGN KEY (communication_id) REFERENCES communications (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1002
ALTER TABLE communications_networks ADD CONSTRAINT FK_communications_networks_communications FOREIGN KEY (communication_id) REFERENCES communications (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1003
ALTER TABLE communications_networks ADD CONSTRAINT FK_communications_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1004
ALTER TABLE communications_positions ADD CONSTRAINT FK_communications_positions_communications FOREIGN KEY (communication_id) REFERENCES communications (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1005
ALTER TABLE communications_positions ADD CONSTRAINT FK_communications_positions_positions FOREIGN KEY (position_id) REFERENCES positions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1006
ALTER TABLE communications_sections ADD CONSTRAINT FK_communications_sections_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1007
ALTER TABLE communications_sections_networks ADD CONSTRAINT FK_communications_sections_networks_communications_sections FOREIGN KEY (communication_section_id) REFERENCES communications_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1008
ALTER TABLE communications_sections_networks ADD CONSTRAINT FK_communications_sections_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1009
ALTER TABLE communications_sections_positions ADD CONSTRAINT FK_communications_sections_positions_communications_sections FOREIGN KEY (communication_section_id) REFERENCES communications_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1010
ALTER TABLE communications_sections_positions ADD CONSTRAINT FK_communications_sections_positions_positions FOREIGN KEY (position_id) REFERENCES positions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1011
ALTER TABLE communications ADD CONSTRAINT FK_communications_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1012
ALTER TABLE communications_sections_trading_groups ADD CONSTRAINT FK_communications_sections_tg_communications_sections FOREIGN KEY (communication_section_id) REFERENCES communications_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1013
ALTER TABLE communications_sections_trading_groups ADD CONSTRAINT FK_communications_sections_trading_groups_trading_groups FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1014
ALTER TABLE communications_trading_groups ADD CONSTRAINT FK_communications_trading_groups_communication_id FOREIGN KEY (communication_id) REFERENCES communications (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1015
ALTER TABLE communications_trading_groups ADD CONSTRAINT FK_communications_trading_groups_trading_group_id FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1016
ALTER TABLE communications_users ADD CONSTRAINT FK_communications_users_communications FOREIGN KEY (communication_id) REFERENCES communications (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1017
ALTER TABLE communications_users ADD CONSTRAINT FK_communications_users_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1018
ALTER TABLE conferences ADD CONSTRAINT FK_conferences_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1019
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_conferences FOREIGN KEY (conferences_id) REFERENCES conferences (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1020
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_contacts FOREIGN KEY (delegate_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1021
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_distributors FOREIGN KEY (distributors_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1022
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_garages FOREIGN KEY (garages_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1023
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_room_type FOREIGN KEY (room_type_id) REFERENCES room_type (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1024
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_room_type_2 FOREIGN KEY (guest_separate_room) REFERENCES room_type (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1025
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_stand_size FOREIGN KEY (stand_size_id) REFERENCES stand_size (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1026
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_suppliers FOREIGN KEY (suppliers_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1027
ALTER TABLE conferences_delegates ADD CONSTRAINT FK_conferences_delegates_venues FOREIGN KEY (venue_id) REFERENCES venues (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1028
ALTER TABLE config_modules_regions_roles ADD CONSTRAINT FK_config_id FOREIGN KEY (config_id) REFERENCES config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1029
ALTER TABLE config ADD CONSTRAINT FK_config_section_id FOREIGN KEY (section_id) REFERENCES config_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1030
ALTER TABLE contacts ADD CONSTRAINT FK_contacts_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1031
ALTER TABLE contacts_contacts_lists ADD CONSTRAINT FK_contacts_contacts_lists_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1032
ALTER TABLE contacts_contacts_lists ADD CONSTRAINT FK_contacts_contacts_lists_contact_list_id FOREIGN KEY (contact_list_id) REFERENCES contacts_lists (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1033
ALTER TABLE contacts ADD CONSTRAINT FK_contacts_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1034
ALTER TABLE contacts ADD CONSTRAINT FK_contacts_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1035
ALTER TABLE contacts_lists ADD CONSTRAINT FK_contacts_lists_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1036
ALTER TABLE contacts_lists ADD CONSTRAINT FK_contacts_lists_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1037
ALTER TABLE contacts ADD CONSTRAINT FK_contacts_logistic_centers FOREIGN KEY (logistic_center_id) REFERENCES logistic_centers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1038
ALTER TABLE contacts ADD CONSTRAINT FK_contacts_position_id FOREIGN KEY (position_id) REFERENCES positions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1039
ALTER TABLE contacts_regions ADD CONSTRAINT FK_contacts_regions_contacts FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1040
ALTER TABLE contacts_regions ADD CONSTRAINT FK_contacts_regions_regions FOREIGN KEY (region_id) REFERENCES regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1041
ALTER TABLE contacts ADD CONSTRAINT FK_contacts_title_id FOREIGN KEY (title_id) REFERENCES contacts_titles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1042
ALTER TABLE sms_licenses_config ADD CONSTRAINT FK_country_id_sms FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1043
ALTER TABLE courses_types ADD CONSTRAINT FK_courses_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1044
ALTER TABLE courtesy_car_types ADD CONSTRAINT FK_courtesy_car_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1045
ALTER TABLE debrief_tasks_distributors ADD CONSTRAINT FK_debrief_debrief_tasks FOREIGN KEY (debrief_task_id) REFERENCES debrief_tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1046
ALTER TABLE debrief_tasks_distributors ADD CONSTRAINT FK_debrief_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1047
ALTER TABLE debrief_tasks ADD CONSTRAINT FK_debrief_tasks_contact_list_id FOREIGN KEY (contact_list_id) REFERENCES contacts_lists (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1048
ALTER TABLE debrief_tasks_garages ADD CONSTRAINT FK_debrief_tasks_garages_debrief_task_id FOREIGN KEY (debrief_task_id) REFERENCES debrief_tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1049
ALTER TABLE debrief_tasks_garages ADD CONSTRAINT FK_debrief_tasks_garages_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1050
ALTER TABLE debrief_tasks ADD CONSTRAINT FK_debrief_tasks_user_assigned_id FOREIGN KEY (user_assigned_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1051
ALTER TABLE dinner ADD CONSTRAINT FK_dinner_conferences_delegates FOREIGN KEY (conferences_delegates_id) REFERENCES conferences_delegates (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1052
ALTER TABLE dinner ADD CONSTRAINT FK_dinner_weeks FOREIGN KEY (weeks_id) REFERENCES weeks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1053
ALTER TABLE distributors_distributors_networks ADD CONSTRAINT FK_dis_dis_networks_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1054
ALTER TABLE distributors_distributors_networks ADD CONSTRAINT FK_dis_dis_networks_trading_groups FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1055
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1056
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_associantions_types FOREIGN KEY (association_type_id) REFERENCES associations_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1057
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_association_id FOREIGN KEY (association_id) REFERENCES associations (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1058
ALTER TABLE distributors_comments ADD CONSTRAINT FK_distributors_comments_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1059
ALTER TABLE distributors_comments ADD CONSTRAINT FK_distributors_comments_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1060
ALTER TABLE distributors_contacts_bdm ADD CONSTRAINT FK_distributors_contacts_bdm_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1061
ALTER TABLE distributors_contacts_bdm ADD CONSTRAINT FK_distributors_contacts_bdm_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1062
ALTER TABLE distributors_contacts_general_branch_manager ADD CONSTRAINT FK_distributors_contacts_general_branch_manager_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1063
ALTER TABLE distributors_contacts_general_branch_manager ADD CONSTRAINT FK_distributors_contacts_general_branch_manager_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1064
ALTER TABLE distributors_contacts_staff ADD CONSTRAINT FK_distributors_contacts_staff_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1065
ALTER TABLE distributors_contacts_staff ADD CONSTRAINT FK_distributors_contacts_staff_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1066
ALTER TABLE distributors_contracts ADD CONSTRAINT FK_distributors_contracts_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1067
ALTER TABLE distributors_contracts ADD CONSTRAINT FK_distributors_contracts_distributors_networks FOREIGN KEY (distributor_network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1068
ALTER TABLE distributors_contracts ADD CONSTRAINT FK_distributors_contracts_leaving_reason_types FOREIGN KEY (leaving_reason_id) REFERENCES leaving_reason_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1069
ALTER TABLE distributors_contracts ADD CONSTRAINT FK_distributors_contracts_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1070
ALTER TABLE distributors_contracts ADD CONSTRAINT FK_distributors_contracts_trading_group_id FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1071
ALTER TABLE distributors_customer_activities ADD CONSTRAINT FK_distributors_customer_activities_customer_activity_id FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1072
ALTER TABLE distributors_customer_activities ADD CONSTRAINT FK_distributors_customer_activities_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1073
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1074
ALTER TABLE distributors_distributors_activities ADD CONSTRAINT FK_distributors_distributors_activities_distributor_activity_id FOREIGN KEY (distributor_activity_id) REFERENCES distributors_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1075
ALTER TABLE distributors_distributors_activities ADD CONSTRAINT FK_distributors_distributors_activities_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1076
ALTER TABLE distributors_distributors_networks ADD CONSTRAINT FK_distributors_distributors_networks_distributors_networks FOREIGN KEY (network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1077
ALTER TABLE distributors_distributors_networks ADD CONSTRAINT FK_distributors_distributors_networks_leaving_reason_types FOREIGN KEY (reason_leaving_id) REFERENCES leaving_reason_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1078
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_distributors_types FOREIGN KEY (distributor_type_id) REFERENCES distributors_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1079
ALTER TABLE distributors_images ADD CONSTRAINT FK_distributors_images_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1080
ALTER TABLE distributors_labels ADD CONSTRAINT FK_distributors_labels_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1081
ALTER TABLE distributors_labels ADD CONSTRAINT FK_distributors_labels_label_type_id FOREIGN KEY (label_type_id) REFERENCES labels_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1082
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_language_id FOREIGN KEY (language_id) REFERENCES languages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1083
ALTER TABLE shortcuts_distributors_networks ADD CONSTRAINT FK_distributors_networks FOREIGN KEY (distributor_network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1084
ALTER TABLE distributors_networks ADD CONSTRAINT FK_distributors_networks_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1085
ALTER TABLE distributors_networks_contacts_bdm ADD CONSTRAINT FK_distributors_networks_contacts_bdm_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1086
ALTER TABLE distributors_networks_contacts_bdm ADD CONSTRAINT FK_distributors_networks_contacts_bdm_distributor_network_id FOREIGN KEY (distributor_network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1087
ALTER TABLE distributors_networks_contacts_bdm ADD CONSTRAINT FK_distributors_networks_contacts_bdm_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1088
ALTER TABLE distributors_networks_contacts_bdm ADD CONSTRAINT FK_distributors_networks_contacts_bdm_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1089
ALTER TABLE distributors_objectives ADD CONSTRAINT FK_distributors_objectives_appointments_objectives FOREIGN KEY (objective_id) REFERENCES appointments_objectives (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1090
ALTER TABLE distributors_objectives ADD CONSTRAINT FK_distributors_objectives_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1091
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_primary_activity_id FOREIGN KEY (primary_activity_id) REFERENCES distributors_activities_primary (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1092
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_province_id FOREIGN KEY (province_id) REFERENCES provinces (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1093
ALTER TABLE distributors_routes ADD CONSTRAINT FK_distributors_routes_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1094
ALTER TABLE distributors_routes ADD CONSTRAINT FK_distributors_routes_route_id FOREIGN KEY (route_id) REFERENCES routes (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1095
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_sales_area_id FOREIGN KEY (sales_area_id) REFERENCES sales_areas (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1096
ALTER TABLE distributors_services ADD CONSTRAINT FK_distributors_services_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1097
ALTER TABLE distributors_services ADD CONSTRAINT FK_distributors_services_service_type_id FOREIGN KEY (service_type_id) REFERENCES services_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1098
ALTER TABLE distributors_software ADD CONSTRAINT FK_distributors_software_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1099
ALTER TABLE distributors_software ADD CONSTRAINT FK_distributors_software_software_id FOREIGN KEY (software_id) REFERENCES software (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1100
ALTER TABLE distributors_software ADD CONSTRAINT FK_distributors_software_software_manufactures FOREIGN KEY (software_manufacture_id) REFERENCES software_manufactures (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1101
ALTER TABLE distributors_software ADD CONSTRAINT FK_distributors_software_software_types FOREIGN KEY (software_type_id) REFERENCES software_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1102
ALTER TABLE distributors_software ADD CONSTRAINT FK_distributors_software_suppliers FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1103
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_trading_group_id FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1104
ALTER TABLE distributors ADD CONSTRAINT FK_distributors_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1105
ALTER TABLE email_types ADD CONSTRAINT FK_email_types_sendgrid_licenses_config FOREIGN KEY (sendgrid_license_config_id) REFERENCES sendgrid_licenses_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1106
ALTER TABLE emails ADD CONSTRAINT FK_emails_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1107
ALTER TABLE emails ADD CONSTRAINT FK_emails_country_id FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1108
ALTER TABLE emails_daily ADD CONSTRAINT FK_emails_daily_contacts FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1109
ALTER TABLE emails ADD CONSTRAINT FK_emails_email_type_id FOREIGN KEY (email_type_id) REFERENCES email_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1110
ALTER TABLE emails ADD CONSTRAINT FK_emails_language_id FOREIGN KEY (language_id) REFERENCES languages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1111
ALTER TABLE emails ADD CONSTRAINT FK_emails_languages_webs_networks FOREIGN KEY (language_web_id) REFERENCES languages_webs_networks (id) ON UPDATE RESTRICT ON DELETE SET NULL;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1112
ALTER TABLE emails ADD CONSTRAINT FK_emails_platform_id FOREIGN KEY (platform_id) REFERENCES platforms (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1113
ALTER TABLE employee_types ADD CONSTRAINT FK_employee_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1114
ALTER TABLE enquiries ADD CONSTRAINT FK_enquiries_child_network_id FOREIGN KEY (child_network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1115
ALTER TABLE enquiries ADD CONSTRAINT FK_enquiries_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1116
ALTER TABLE enquiries ADD CONSTRAINT FK_enquiries_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1117
ALTER TABLE enquiries ADD CONSTRAINT FK_enquiries_work_id FOREIGN KEY (work_id) REFERENCES works (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1118
ALTER TABLE equipments ADD CONSTRAINT FK_equipments_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1119
ALTER TABLE equipments_types ADD CONSTRAINT FK_equipments_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1120
ALTER TABLE erp ADD CONSTRAINT FK_erp_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1121
ALTER TABLE facilities ADD CONSTRAINT FK_facilities_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1122
ALTER TABLE communications_sections_distributors_networks ADD CONSTRAINT FK_fk_communications_sections FOREIGN KEY (communication_section_id) REFERENCES communications_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1123
ALTER TABLE communications_sections_distributors_networks ADD CONSTRAINT FK_fk_distributors_networks FOREIGN KEY (distributor_network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1124
ALTER TABLE fleets ADD CONSTRAINT FK_fleets_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1125
ALTER TABLE fleets ADD CONSTRAINT FK_fleets_countries FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1126
ALTER TABLE fleets ADD CONSTRAINT FK_fleets_erp FOREIGN KEY (erp_id) REFERENCES erp (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1127
ALTER TABLE fleets_networks ADD CONSTRAINT FK_fleets_networks_fleets FOREIGN KEY (fleet_id) REFERENCES fleets (id) ON UPDATE RESTRICT ON DELETE CASCADE;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1128
ALTER TABLE fleets_networks ADD CONSTRAINT FK_fleets_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE CASCADE;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1129
ALTER TABLE fleets ADD CONSTRAINT FK_fleets_provinces FOREIGN KEY (province_id) REFERENCES provinces (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1130
ALTER TABLE fluids ADD CONSTRAINT FK_fluids_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1131
ALTER TABLE garages_networks_genarts_families ADD CONSTRAINT FK_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1132
ALTER TABLE garages_agreements ADD CONSTRAINT FK_garages_agreements_agreements FOREIGN KEY (agreement_id) REFERENCES agreements (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1133
ALTER TABLE garages_agreements ADD CONSTRAINT FK_garages_agreements_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1134
ALTER TABLE garages_agreements ADD CONSTRAINT FK_garages_agreements_hold_reason_types FOREIGN KEY (reason_hold_id) REFERENCES hold_reason_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1135
ALTER TABLE garages_agreements ADD CONSTRAINT FK_garages_agreements_leaving_reason_types FOREIGN KEY (reason_leaving_id) REFERENCES leaving_reason_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1136
ALTER TABLE garages_b2b_postcodes ADD CONSTRAINT FK_garages_b2b_postcodes_postcode_provinces FOREIGN KEY (postcode_id) REFERENCES postcode_provinces (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1137
ALTER TABLE garages_b2c_postcodes ADD CONSTRAINT FK_garages_b2c_postcodes_postcode_provinces FOREIGN KEY (postcode_id) REFERENCES postcode_provinces (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1138
ALTER TABLE garages_brands ADD CONSTRAINT FK_garages_brands_brand_id FOREIGN KEY (brand_id) REFERENCES brands (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1139
ALTER TABLE garages_brands ADD CONSTRAINT FK_garages_brands_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1140
ALTER TABLE garages_campaign ADD CONSTRAINT FK_garages_campaign_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1141
ALTER TABLE garages_campaign ADD CONSTRAINT FK_garages_campaign_garages_campaign FOREIGN KEY (garage_campaign_id) REFERENCES campaign_entries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1142
ALTER TABLE garages ADD CONSTRAINT FK_garages_city_id FOREIGN KEY (city_id) REFERENCES cities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1143
ALTER TABLE garages_comments ADD CONSTRAINT FK_garages_comments_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1144
ALTER TABLE garages_contacts_bdm ADD CONSTRAINT FK_garages_contacts_bdm_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1145
ALTER TABLE garages_contacts_bdm ADD CONSTRAINT FK_garages_contacts_bdm_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1146
ALTER TABLE garages_contacts_general_branch_manager ADD CONSTRAINT FK_garages_contacts_general_branch_manager_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1147
ALTER TABLE garages_contacts_general_branch_manager ADD CONSTRAINT FK_garages_contacts_general_branch_manager_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1148
ALTER TABLE garages_contacts_lists ADD CONSTRAINT FK_garages_contacts_lists_contacts_lists FOREIGN KEY (contact_list_id) REFERENCES contacts_lists (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1149
ALTER TABLE garages_contacts_lists ADD CONSTRAINT FK_garages_contacts_lists_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1150
ALTER TABLE garages_contacts_staff ADD CONSTRAINT FK_garages_contacts_staff_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1151
ALTER TABLE garages_contacts_staff ADD CONSTRAINT FK_garages_contacts_staff_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1152
ALTER TABLE garages_courtesy_car_types ADD CONSTRAINT FK_garages_courtesy_car_types_courtesy_car_type_id FOREIGN KEY (courtesy_car_type_id) REFERENCES courtesy_car_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1153
ALTER TABLE garages_courtesy_car_types ADD CONSTRAINT FK_garages_courtesy_car_types_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1154
ALTER TABLE garages_customers_activities ADD CONSTRAINT FK_garages_customers_activities_customer_activity_id FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1155
ALTER TABLE garages_customers_activities ADD CONSTRAINT FK_garages_customers_activities_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1156
ALTER TABLE garages_distributors ADD CONSTRAINT FK_garages_distributors_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1157
ALTER TABLE garages_distributors ADD CONSTRAINT FK_garages_distributors_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1158
ALTER TABLE garages_distributors_shortcuts ADD CONSTRAINT FK_garages_distributors_shortcuts_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1159
ALTER TABLE garages_distributors_shortcuts ADD CONSTRAINT FK_garages_distributors_shortcuts_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1160
ALTER TABLE garages_distributors_shortcuts ADD CONSTRAINT FK_garages_distributors_shortcuts_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1161
ALTER TABLE garages_distributors_shortcuts ADD CONSTRAINT FK_garages_distributors_shortcuts_shortcuts FOREIGN KEY (shortcut_id) REFERENCES shortcuts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1162
ALTER TABLE garages_distributors_shortcuts ADD CONSTRAINT FK_garages_distributors_shortcuts_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1163
ALTER TABLE garages_employees ADD CONSTRAINT FK_garages_employees_employee_type_id FOREIGN KEY (employee_type_id) REFERENCES employee_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1164
ALTER TABLE garages_employees ADD CONSTRAINT FK_garages_employees_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1165
ALTER TABLE garages_equipments ADD CONSTRAINT FK_garages_equipments_billings_schedules FOREIGN KEY (billing_schedule_id) REFERENCES billings_schedules (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1166
ALTER TABLE garages_equipments ADD CONSTRAINT FK_garages_equipments_brand_id FOREIGN KEY (brand_id) REFERENCES brands (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1167
ALTER TABLE garages_equipments ADD CONSTRAINT FK_garages_equipments_equipment_id FOREIGN KEY (equipment_id) REFERENCES equipments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1168
ALTER TABLE garages_equipments ADD CONSTRAINT FK_garages_equipments_equipment_type_id FOREIGN KEY (equipment_type_id) REFERENCES equipments_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1169
ALTER TABLE garages_equipments ADD CONSTRAINT FK_garages_equipments_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1170
ALTER TABLE garages_equipments ADD CONSTRAINT FK_garages_equipments_supplier_id FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1171
ALTER TABLE garages ADD CONSTRAINT FK_garages_erp FOREIGN KEY (erp_id) REFERENCES erp (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1172
ALTER TABLE garages_facilities ADD CONSTRAINT FK_garages_facilities_facilities FOREIGN KEY (facility_id) REFERENCES facilities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1173
ALTER TABLE garages_facilities ADD CONSTRAINT FK_garages_facilities_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1174
ALTER TABLE garages_files ADD CONSTRAINT FK_garages_files_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1175
ALTER TABLE garages_images ADD CONSTRAINT FK_garages_images_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1176
ALTER TABLE garages ADD CONSTRAINT FK_garages_insurance_agreements FOREIGN KEY (insurance_agreement_id) REFERENCES insurance_agreements (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1177
ALTER TABLE garages ADD CONSTRAINT FK_garages_language_id FOREIGN KEY (language_id) REFERENCES languages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1178
ALTER TABLE garages_networks ADD CONSTRAINT FK_garages_networks_annex_detail_id FOREIGN KEY (annex_detail_id) REFERENCES annex_details (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1179
ALTER TABLE garages_networks_fluids ADD CONSTRAINT FK_garages_networks_fluids_fluid_id FOREIGN KEY (fluid_id) REFERENCES fluids (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1180
ALTER TABLE garages_networks_fluids ADD CONSTRAINT FK_garages_networks_fluids_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1181
ALTER TABLE garages_networks ADD CONSTRAINT FK_garages_networks_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1182
ALTER TABLE garages_networks_genarts ADD CONSTRAINT FK_garages_networks_genarts_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1183
ALTER TABLE garages_networks_genarts ADD CONSTRAINT FK_garages_networks_genarts_genart_id FOREIGN KEY (genart_id) REFERENCES genarts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1184
ALTER TABLE garages_networks_genarts_master ADD CONSTRAINT FK_garages_networks_genarts_master_garages_networks FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1185
ALTER TABLE garages_networks_genarts_master ADD CONSTRAINT FK_garages_networks_genarts_master_genarts_master FOREIGN KEY (genart_master_id) REFERENCES genarts_master (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1186
ALTER TABLE garages_networks_images ADD CONSTRAINT FK_garages_networks_images_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1187
ALTER TABLE garages_networks ADD CONSTRAINT FK_garages_networks_network_contract_type_id FOREIGN KEY (network_contract_type_id) REFERENCES networks_contract_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1188
ALTER TABLE garages_networks ADD CONSTRAINT FK_garages_networks_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1189
ALTER TABLE garages_networks ADD CONSTRAINT FK_garages_networks_reason_leaving_id FOREIGN KEY (reason_leaving_id) REFERENCES leaving_reason_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1190
ALTER TABLE garages_networks_services_drivers ADD CONSTRAINT FK_garages_networks_services_drivers_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1191
ALTER TABLE garages_networks_services_drivers ADD CONSTRAINT FK_garages_networks_services_drivers_service_driver_id FOREIGN KEY (service_driver_id) REFERENCES services_drivers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1192
ALTER TABLE garages_networks_services ADD CONSTRAINT FK_garages_networks_services_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1193
ALTER TABLE garages_networks_services ADD CONSTRAINT FK_garages_networks_services_service_id FOREIGN KEY (service_id) REFERENCES services (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1194
ALTER TABLE garages_networks_vehicle_types ADD CONSTRAINT FK_garages_networks_vehicle_types_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1195
ALTER TABLE garages_networks_vehicle_types ADD CONSTRAINT FK_garages_networks_vehicle_types_vehicle_type_id FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1196
ALTER TABLE garages_networks_vehicles_black_list ADD CONSTRAINT FK_garages_networks_vehicles_black_list_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1197
ALTER TABLE garages_networks_vehicles_black_list ADD CONSTRAINT FK_garages_networks_vehicles_black_list_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1198
ALTER TABLE garages_networks_vehicles ADD CONSTRAINT FK_garages_networks_vehicles_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1199
ALTER TABLE garages_networks_vehicles ADD CONSTRAINT FK_garages_networks_vehicles_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1200
ALTER TABLE garages_networks_works ADD CONSTRAINT FK_garages_networks_works_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1201
ALTER TABLE garages_networks_works_labours ADD CONSTRAINT FK_garages_networks_works_labours_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1202
ALTER TABLE garages_networks_works_labours ADD CONSTRAINT FK_garages_networks_works_labours_work_id FOREIGN KEY (work_id) REFERENCES works (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1203
ALTER TABLE garages_networks_works_prices ADD CONSTRAINT FK_garages_networks_works_prices__garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1204
ALTER TABLE garages_networks_works_prices ADD CONSTRAINT FK_garages_networks_works_prices_work_id FOREIGN KEY (work_id) REFERENCES works (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1205
ALTER TABLE garages_networks_works ADD CONSTRAINT FK_garages_networks_works_work_id FOREIGN KEY (work_id) REFERENCES works (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1206
ALTER TABLE garages_oils ADD CONSTRAINT FK_garages_oils_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1207
ALTER TABLE garages_oils ADD CONSTRAINT FK_garages_oils_oils FOREIGN KEY (oil_id) REFERENCES oils (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1208
ALTER TABLE garages ADD CONSTRAINT FK_garages_province_id FOREIGN KEY (province_id) REFERENCES provinces (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1209
ALTER TABLE garages_routes ADD CONSTRAINT FK_garages_routes_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1210
ALTER TABLE garages_routes ADD CONSTRAINT FK_garages_routes_route_id FOREIGN KEY (route_id) REFERENCES routes (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1211
ALTER TABLE garages ADD CONSTRAINT FK_garages_sales_area_id FOREIGN KEY (sales_area_id) REFERENCES sales_areas (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1212
ALTER TABLE garages_services ADD CONSTRAINT FK_garages_services_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1213
ALTER TABLE garages_services ADD CONSTRAINT FK_garages_services_service_id FOREIGN KEY (service_id) REFERENCES services (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1214
ALTER TABLE garages_software ADD CONSTRAINT FK_garages_software_billings_schedules FOREIGN KEY (billing_schedule_id) REFERENCES billings_schedules (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1215
ALTER TABLE garages_software ADD CONSTRAINT FK_garages_software_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1216
ALTER TABLE garages_software ADD CONSTRAINT FK_garages_software_software_id FOREIGN KEY (software_id) REFERENCES software (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1217
ALTER TABLE garages_software ADD CONSTRAINT FK_garages_software_software_manufactures FOREIGN KEY (software_manufacture_id) REFERENCES software_manufactures (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1218
ALTER TABLE garages_software ADD CONSTRAINT FK_garages_software_software_types FOREIGN KEY (software_type_id) REFERENCES software_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1219
ALTER TABLE garages_software ADD CONSTRAINT FK_garages_software_suppliers FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1220
ALTER TABLE garages_specialist_makes ADD CONSTRAINT FK_garages_specialist_makes_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1221
ALTER TABLE garages_specialist_makes ADD CONSTRAINT FK_garages_specialist_makes_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1222
ALTER TABLE garages ADD CONSTRAINT FK_garages_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1223
ALTER TABLE garages_value_add_supplier ADD CONSTRAINT FK_garages_value_add_supplier_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1224
ALTER TABLE garages_value_add_supplier ADD CONSTRAINT FK_garages_value_add_supplier_value_add_supplier_type FOREIGN KEY (value_add_supplier_type_id) REFERENCES value_add_supplier_type (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1225
ALTER TABLE garages_value_add_supplier ADD CONSTRAINT FK_garages_value_add_supplier_value_add_suppliers FOREIGN KEY (value_add_supplier_id) REFERENCES value_add_suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1226
ALTER TABLE garages_values_adds ADD CONSTRAINT FK_garages_values_adds_billings_schedules FOREIGN KEY (billing_schedule_id) REFERENCES billings_schedules (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1227
ALTER TABLE garages_vehicle_types ADD CONSTRAINT FK_garages_vehicle_types_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1228
ALTER TABLE garages_vehicle_types ADD CONSTRAINT FK_garages_vehicle_types_vehicle_type_id FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1229
ALTER TABLE garages_vehicles ADD CONSTRAINT FK_garages_vehicles_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1230
ALTER TABLE garages_vehicles ADD CONSTRAINT FK_garages_vehicles_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1231
ALTER TABLE garages_websites ADD CONSTRAINT FK_garages_websites_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1232
ALTER TABLE garages_websites ADD CONSTRAINT FK_garages_websites_website_id FOREIGN KEY (website_id) REFERENCES websites (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1233
ALTER TABLE garages_workshop_activities ADD CONSTRAINT FK_garages_workshop_activities_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1234
ALTER TABLE garages_workshop_activities ADD CONSTRAINT FK_garages_workshop_activities_workshop_activities FOREIGN KEY (workshop_activity_id) REFERENCES workshop_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1235
ALTER TABLE genarts_master ADD CONSTRAINT FK_genart_family_id FOREIGN KEY (genart_family_id) REFERENCES genarts_families (id) ON UPDATE RESTRICT ON DELETE SET NULL;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1236
ALTER TABLE genarts_families ADD CONSTRAINT FK_genarts_families_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1237
ALTER TABLE garages_networks_genarts_families ADD CONSTRAINT FK_genarts_family_id FOREIGN KEY (genart_family_id) REFERENCES genarts_families (id) ON UPDATE RESTRICT ON DELETE CASCADE;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1238
ALTER TABLE genarts ADD CONSTRAINT FK_genarts_grouping_genart_id FOREIGN KEY (grouping_genart_id) REFERENCES grouping_genarts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1239
ALTER TABLE general_manager_widgets_roles ADD CONSTRAINT FK_general_manager_widgets_roles_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1240
ALTER TABLE general_manager_widgets_roles ADD CONSTRAINT FK_general_manager_widgets_roles_widget_id FOREIGN KEY (widget_id) REFERENCES panels_widgets (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1241
ALTER TABLE garages_networks_contacts ADD CONSTRAINT FK_grages_networks_contacts_contacts FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1242
ALTER TABLE garages_networks_contacts ADD CONSTRAINT FK_grages_networks_contacts_garages_networks FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1243
ALTER TABLE grouping_genarts ADD CONSTRAINT FK_grouping_genarts_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1244
ALTER TABLE groups_permissions_permissions ADD CONSTRAINT FK_groups_permissions_permissions_group_permission_id FOREIGN KEY (group_permission_id) REFERENCES groups_permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1245
ALTER TABLE groups_permissions_permissions ADD CONSTRAINT FK_groups_permissions_permissions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1246
ALTER TABLE groups_permissions ADD CONSTRAINT FK_groups_permissions_position_config_type_id FOREIGN KEY (position_config_type_id) REFERENCES positions_config_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1247
ALTER TABLE groups_permissions_roles ADD CONSTRAINT FK_groups_permissions_roles_group_permission_id FOREIGN KEY (group_permission_id) REFERENCES groups_permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1248
ALTER TABLE groups_permissions_roles ADD CONSTRAINT FK_groups_permissions_roles_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1249
ALTER TABLE groups_permissions_users ADD CONSTRAINT FK_groups_permissions_users_group_permission_id FOREIGN KEY (group_permission_id) REFERENCES groups_permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1250
ALTER TABLE groups_permissions_users ADD CONSTRAINT FK_groups_permissions_users_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1251
ALTER TABLE hold_reason_types ADD CONSTRAINT FK_hold_reason_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1252
ALTER TABLE languages_webs_networks ADD CONSTRAINT FK_languages_webs_networks_languages_webs_flags FOREIGN KEY (language_web_flag_id) REFERENCES languages_webs_flags (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1253
ALTER TABLE languages_webs_networks ADD CONSTRAINT FK_languages_webs_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1254
ALTER TABLE leaving_reason_types ADD CONSTRAINT FK_leaving_reason_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1255
ALTER TABLE leaving_reason_comments ADD CONSTRAINT FK_leaving_reasons_comments_garage_network_id FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1256
ALTER TABLE lists ADD CONSTRAINT FK_lists_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1257
ALTER TABLE lists ADD CONSTRAINT FK_lists_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1258
ALTER TABLE logs_changes ADD CONSTRAINT FK_logs_changes_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1259
ALTER TABLE logs_changes ADD CONSTRAINT FK_logs_changes_field_id FOREIGN KEY (field_id) REFERENCES logs_fields (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1260
ALTER TABLE logs_changes ADD CONSTRAINT FK_logs_changes_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1261
ALTER TABLE logs_changes ADD CONSTRAINT FK_logs_changes_group_permission_id FOREIGN KEY (group_permission_id) REFERENCES groups_permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1262
ALTER TABLE logs_changes ADD CONSTRAINT FK_logs_changes_position_id FOREIGN KEY (position_id) REFERENCES positions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1263
ALTER TABLE logs_changes ADD CONSTRAINT FK_logs_changes_table_id FOREIGN KEY (table_id) REFERENCES logs_tables (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1264
ALTER TABLE logs_changes ADD CONSTRAINT FK_logs_changes_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1265
ALTER TABLE logs_fields ADD CONSTRAINT FK_logs_fields_table_id FOREIGN KEY (table_id) REFERENCES logs_tables (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1266
ALTER TABLE logs_login ADD CONSTRAINT FK_logs_logins_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE CASCADE;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1267
ALTER TABLE messages_distributors ADD CONSTRAINT FK_messages_distributors_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1268
ALTER TABLE messages_distributors ADD CONSTRAINT FK_messages_distributors_messages FOREIGN KEY (message_id) REFERENCES messages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1269
ALTER TABLE messages_files ADD CONSTRAINT FK_messages_files_messages FOREIGN KEY (message_id) REFERENCES messages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1270
ALTER TABLE messages_garages ADD CONSTRAINT FK_messages_garages_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1271
ALTER TABLE messages_garages ADD CONSTRAINT FK_messages_garages_messages FOREIGN KEY (message_id) REFERENCES messages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1272
ALTER TABLE messages ADD CONSTRAINT FK_messages_messages_types FOREIGN KEY (type) REFERENCES messages_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1273
ALTER TABLE messages ADD CONSTRAINT FK_messages_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1274
ALTER TABLE network_cities ADD CONSTRAINT FK_network_cities_cities FOREIGN KEY (city_id) REFERENCES cities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1275
ALTER TABLE network_cities ADD CONSTRAINT FK_network_cities_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1276
ALTER TABLE genarts_master ADD CONSTRAINT FK_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1277
ALTER TABLE networks ADD CONSTRAINT FK_networks_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1278
ALTER TABLE networks_contacts_lists ADD CONSTRAINT FK_networks_contact_lists_contacts_litsts FOREIGN KEY (contact_list_id) REFERENCES contacts_lists (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1279
ALTER TABLE networks_contacts_bdm ADD CONSTRAINT FK_networks_contacts_bdm_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1280
ALTER TABLE networks_contacts_bdm ADD CONSTRAINT FK_networks_contacts_bdm_distributor_id FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1281
ALTER TABLE networks_contacts_bdm ADD CONSTRAINT FK_networks_contacts_bdm_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1282
ALTER TABLE networks_contacts_bdm ADD CONSTRAINT FK_networks_contacts_bdm_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1283
ALTER TABLE networks_contacts_lists ADD CONSTRAINT FK_networks_contacts_lists_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1284
ALTER TABLE networks_contract_types ADD CONSTRAINT FK_networks_contract_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1285
ALTER TABLE networks ADD CONSTRAINT FK_networks_distance_unit_id FOREIGN KEY (distance_unit_id) REFERENCES distance_units (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1286
ALTER TABLE networks ADD CONSTRAINT FK_networks_email_booking_contact_id FOREIGN KEY (email_booking_contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1287
ALTER TABLE networks ADD CONSTRAINT FK_networks_email_enquiry_contact_id FOREIGN KEY (email_enquiry_contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1288
ALTER TABLE networks ADD CONSTRAINT FK_networks_parent_network_id FOREIGN KEY (parent_network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1289
ALTER TABLE networks ADD CONSTRAINT FK_networks_pricing_type_id FOREIGN KEY (pricing_type_id) REFERENCES pricings_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1290
ALTER TABLE networks ADD CONSTRAINT FK_networks_quoting_type_id FOREIGN KEY (quoting_type_id) REFERENCES quotings_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1291
ALTER TABLE networks_recommended ADD CONSTRAINT FK_networks_recommended_internal_network_id FOREIGN KEY (internal_network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1292
ALTER TABLE networks_recommended ADD CONSTRAINT FK_networks_recommended_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1293
ALTER TABLE orders ADD CONSTRAINT FK_order_order_type FOREIGN KEY (order_type_id) REFERENCES order_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1294
ALTER TABLE order_types_products ADD CONSTRAINT FK_order_product_id FOREIGN KEY (order_product_id) REFERENCES order_products (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1295
ALTER TABLE order_products ADD CONSTRAINT FK_order_products_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1296
ALTER TABLE order_types_products ADD CONSTRAINT FK_order_type_id FOREIGN KEY (order_type_id) REFERENCES order_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1297
ALTER TABLE order_types ADD CONSTRAINT FK_order_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1298
ALTER TABLE order_types_products ADD CONSTRAINT FK_order_types_products_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1299
ALTER TABLE orders ADD CONSTRAINT FK_orders_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1300
ALTER TABLE panel_gm_widgets_roles ADD CONSTRAINT FK_panel_gm_widgets_roles_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1301
ALTER TABLE panel_gm_widgets_roles ADD CONSTRAINT FK_panel_gm_widgets_roles_widget_id FOREIGN KEY (widget_id) REFERENCES panels_widgets (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1302
ALTER TABLE panels_widgets_roles ADD CONSTRAINT FK_panels_widgets_roles_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1303
ALTER TABLE panels_widgets_roles ADD CONSTRAINT FK_panels_widgets_roles_widget_id FOREIGN KEY (widget_id) REFERENCES panels_widgets (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1304
ALTER TABLE panels_widgets_users ADD CONSTRAINT FK_panels_widgets_users_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1305
ALTER TABLE panels_widgets_users ADD CONSTRAINT FK_panels_widgets_users_widget_id FOREIGN KEY (widget_id) REFERENCES panels_widgets (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1306
ALTER TABLE permissions ADD CONSTRAINT FK_permissions_grouping_permission_id FOREIGN KEY (grouping_permission_id) REFERENCES groupings_permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1307
ALTER TABLE permissions ADD CONSTRAINT FK_permissions_position_config_type_id FOREIGN KEY (position_config_type_id) REFERENCES positions_config_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1308
ALTER TABLE permissions_roles ADD CONSTRAINT FK_permissions_roles_permission_id FOREIGN KEY (permission_id) REFERENCES permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1309
ALTER TABLE permissions_roles ADD CONSTRAINT FK_permissions_roles_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1310
ALTER TABLE permissions_users ADD CONSTRAINT FK_permissions_users_permission_id FOREIGN KEY (permission_id) REFERENCES permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1311
ALTER TABLE permissions_users ADD CONSTRAINT FK_permissions_users_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1312
ALTER TABLE permissions_versions ADD CONSTRAINT FK_permissions_versions_permission_id FOREIGN KEY (permission_id) REFERENCES permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1313
ALTER TABLE permissions_versions ADD CONSTRAINT FK_permissions_versions_version_id FOREIGN KEY (version_id) REFERENCES versions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1314
ALTER TABLE platforms ADD CONSTRAINT FK_platforms_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1315
ALTER TABLE platforms ADD CONSTRAINT FK_platforms_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1316
ALTER TABLE positions_config_aag_members ADD CONSTRAINT FK_positions_config_aag_members_position_config_id FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1317
ALTER TABLE positions_config_bdms ADD CONSTRAINT FK_positions_config_bdms_position_config_id FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1318
ALTER TABLE positions_config_bdms ADD CONSTRAINT FK_positions_config_bdms_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1319
ALTER TABLE positions_config_customer_activities ADD CONSTRAINT FK_positions_config_customer_activities_customers_activities FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1320
ALTER TABLE positions_config_customer_activities ADD CONSTRAINT FK_positions_config_customer_activities_positions_config FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1321
ALTER TABLE positions_config_distributor_networks ADD CONSTRAINT FK_positions_config_distributor_networks_distributors_networks FOREIGN KEY (distributor_network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1322
ALTER TABLE positions_config_distributor_networks ADD CONSTRAINT FK_positions_config_distributor_networks_positions_config FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1323
ALTER TABLE positions_config_networks ADD CONSTRAINT FK_positions_config_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1324
ALTER TABLE positions_config_networks ADD CONSTRAINT FK_positions_config_networks_positions_config FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1325
ALTER TABLE positions_config ADD CONSTRAINT FK_positions_config_position_config_type_id FOREIGN KEY (position_config_type_id) REFERENCES positions_config_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1326
ALTER TABLE positions_config_profiles ADD CONSTRAINT FK_positions_config_profiles_positions_config FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1327
ALTER TABLE positions_config_suppliers_categories ADD CONSTRAINT FK_positions_config_suppliers_categories_positions_config FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1328
ALTER TABLE positions_config_suppliers_categories ADD CONSTRAINT FK_positions_config_suppliers_categories_suppliers_categories FOREIGN KEY (supplier_category_id) REFERENCES suppliers_categories (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1329
ALTER TABLE positions_config_trading_groups ADD CONSTRAINT FK_positions_config_trading_groups_positions_config FOREIGN KEY (position_config_id) REFERENCES positions_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1330
ALTER TABLE positions_config_trading_groups ADD CONSTRAINT FK_positions_config_trading_groups_trading_groups FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1331
ALTER TABLE positions_config_types_permissions ADD CONSTRAINT FK_positions_config_types_permissions_permissions FOREIGN KEY (permission_id) REFERENCES permissions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1332
ALTER TABLE positions_config_types_permissions ADD CONSTRAINT FK_positions_config_types_permissions_positions_config_types FOREIGN KEY (position_config_type_id) REFERENCES positions_config_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1333
ALTER TABLE positions ADD CONSTRAINT FK_positions_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1334
ALTER TABLE postcode_provinces ADD CONSTRAINT FK_postcode_provinces_provinces FOREIGN KEY (province_id) REFERENCES provinces (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1335
ALTER TABLE products ADD CONSTRAINT FK_products_brand FOREIGN KEY (brand_id) REFERENCES brands (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1336
ALTER TABLE products_images ADD CONSTRAINT FK_products_images_products FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1337
ALTER TABLE cities ADD CONSTRAINT FK_province_id_cities FOREIGN KEY (province_id) REFERENCES provinces (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1338
ALTER TABLE provinces ADD CONSTRAINT FK_provinces_country_id FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1339
ALTER TABLE quotations ADD CONSTRAINT FK_quotations_garage_id FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1340
ALTER TABLE quotations ADD CONSTRAINT FK_quotations_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1341
ALTER TABLE reasons_allowances ADD CONSTRAINT FK_reasons_allowances_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1342
ALTER TABLE reasons_delegates ADD CONSTRAINT FK_reasons_delegates_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1343
ALTER TABLE reasons_delegates_cancelled ADD CONSTRAINT FK_reasons_delegates_cancelled_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1344
ALTER TABLE requested_changes ADD CONSTRAINT FK_requested_changes_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1345
ALTER TABLE requested_changes ADD CONSTRAINT FK_requested_changes_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1346
ALTER TABLE requested_changes_images ADD CONSTRAINT FK_requested_changes_images_requested_changes FOREIGN KEY (requested_change_id) REFERENCES requested_changes (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1347
ALTER TABLE requested_changes ADD CONSTRAINT FK_requested_changes_logs_fields FOREIGN KEY (field_id) REFERENCES logs_fields (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1348
ALTER TABLE requested_changes ADD CONSTRAINT FK_requested_changes_logs_tables FOREIGN KEY (table_id) REFERENCES logs_tables (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1349
ALTER TABLE requested_changes ADD CONSTRAINT FK_requested_changes_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1350
ALTER TABLE review_requests ADD CONSTRAINT FK_review_requests_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1351
ALTER TABLE review_requests ADD CONSTRAINT FK_review_requests_booking_id FOREIGN KEY (booking_id) REFERENCES bookings (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1352
ALTER TABLE config_modules_regions_roles ADD CONSTRAINT FK_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1353
ALTER TABLE sms_licenses_config ADD CONSTRAINT FK_role_id_sms FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1354
ALTER TABLE roles_positions_config_types ADD CONSTRAINT FK_roles_positions_config_types_positions_config_types FOREIGN KEY (position_config_type_id) REFERENCES positions_config_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1355
ALTER TABLE roles_positions_config_types ADD CONSTRAINT FK_roles_positions_config_types_roles FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1356
ALTER TABLE room ADD CONSTRAINT FK_room_conferences_delegates FOREIGN KEY (conferences_delegates_id) REFERENCES conferences_delegates (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1357
ALTER TABLE room_type ADD CONSTRAINT FK_room_type_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1358
ALTER TABLE room ADD CONSTRAINT FK_room_weeks FOREIGN KEY (weeks_id) REFERENCES weeks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1359
ALTER TABLE routes ADD CONSTRAINT FK_routes_users FOREIGN KEY (user_assigned_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1360
ALTER TABLE sales_areas ADD CONSTRAINT FK_sale_areas_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1361
ALTER TABLE searches_contacts ADD CONSTRAINT FK_searches_contacts FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1362
ALTER TABLE searches_contacts ADD CONSTRAINT FK_searches_contacts_users_searches FOREIGN KEY (search_id) REFERENCES users_searches (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1363
ALTER TABLE searches_distributors ADD CONSTRAINT FK_searches_distributors_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1364
ALTER TABLE searches_distributors ADD CONSTRAINT FK_searches_distributors_users_searches FOREIGN KEY (search_id) REFERENCES users_searches (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1365
ALTER TABLE searches_networks ADD CONSTRAINT FK_searches_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1366
ALTER TABLE searches_networks ADD CONSTRAINT FK_searches_networks_users_searches FOREIGN KEY (search_id) REFERENCES users_searches (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1367
ALTER TABLE sections_subsections_distributors_networks ADD CONSTRAINT FK_section_sub_distributors_networks FOREIGN KEY (distributor_network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1368
ALTER TABLE sections_subsections_distributors_networks ADD CONSTRAINT FK_section_sub_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1369
ALTER TABLE sections_subsections ADD CONSTRAINT FK_sections_subsections_aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1370
ALTER TABLE sections_subsections ADD CONSTRAINT FK_sections_subsections_communication_section_id FOREIGN KEY (communication_section_id) REFERENCES communications_sections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1371
ALTER TABLE sections_subsections_networks ADD CONSTRAINT FK_sections_subsections_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1372
ALTER TABLE sections_subsections_networks ADD CONSTRAINT FK_sections_subsections_networks_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1373
ALTER TABLE sections_subsections_positions ADD CONSTRAINT FK_sections_subsections_positions_positions FOREIGN KEY (position_id) REFERENCES positions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1374
ALTER TABLE sections_subsections_positions ADD CONSTRAINT FK_sections_subsections_positions_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1375
ALTER TABLE sections_subsections_trading_groups ADD CONSTRAINT FK_sections_subsections_trading_groups_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1376
ALTER TABLE sections_subsections_trading_groups ADD CONSTRAINT FK_sections_subsections_trading_groups_trading_groups FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1377
ALTER TABLE sendgrid_licenses_config ADD CONSTRAINT FK_sendgrid_config_Aag_regions FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1378
ALTER TABLE sendgrid_email_types_templates ADD CONSTRAINT FK_sendgrid_email_types_templates_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1379
ALTER TABLE sendgrid_email_types_templates ADD CONSTRAINT FK_sendgrid_email_types_templates_country_id FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1380
ALTER TABLE sendgrid_email_types_templates ADD CONSTRAINT FK_sendgrid_email_types_templates_email_type_id FOREIGN KEY (email_type_id) REFERENCES email_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1381
ALTER TABLE sendgrid_email_types_templates ADD CONSTRAINT FK_sendgrid_email_types_templates_language_id FOREIGN KEY (language_id) REFERENCES languages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1382
ALTER TABLE sendgrid_email_types_templates ADD CONSTRAINT FK_sendgrid_email_types_templates_languages_webs_networks FOREIGN KEY (language_web_id) REFERENCES languages_webs_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1383
ALTER TABLE sendgrid_email_types_templates ADD CONSTRAINT FK_sendgrid_email_types_templates_platform_id FOREIGN KEY (platform_id) REFERENCES platforms (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1384
ALTER TABLE sendgrid_licenses_config ADD CONSTRAINT FK_sendgrid_licenses_config_countries FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1385
ALTER TABLE sendgrid_licenses_config ADD CONSTRAINT FK_sendgrid_licenses_config_platforms FOREIGN KEY (platform_id) REFERENCES platforms (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1386
ALTER TABLE sendgrid_email_types_viewvars ADD CONSTRAINT FK_sendgrid_viewvars_templates_email_type_id FOREIGN KEY (email_type_id) REFERENCES email_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1387
ALTER TABLE sendgrid_email_types_viewvars ADD CONSTRAINT FK_sendgrid_viewvars_templates_sendgrid_viewvar_id FOREIGN KEY (sendgrid_viewvar_id) REFERENCES sendgrid_viewvars (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1388
ALTER TABLE services_drivers ADD CONSTRAINT FK_services_drivers_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1389
ALTER TABLE set_distributors_objectives ADD CONSTRAINT FK_set_distributors_objectives_appointments_objectives FOREIGN KEY (objective_id) REFERENCES appointments_objectives (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1390
ALTER TABLE set_distributors_objectives ADD CONSTRAINT FK_set_distributors_objectives_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1391
ALTER TABLE shortcuts_distributors_networks ADD CONSTRAINT FK_shortcut FOREIGN KEY (shortcut_id) REFERENCES shortcuts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1392
ALTER TABLE shortcuts_customers_activities ADD CONSTRAINT FK_shortcuts_customers_activities_customers_activities FOREIGN KEY (customer_activity_id) REFERENCES customers_activities (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1393
ALTER TABLE shortcuts_customers_activities ADD CONSTRAINT FK_shortcuts_customers_activities_shortcuts FOREIGN KEY (shortcut_id) REFERENCES shortcuts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1394
ALTER TABLE shortcuts_networks ADD CONSTRAINT FK_shortcuts_networks_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1395
ALTER TABLE shortcuts_networks ADD CONSTRAINT FK_shortcuts_networks_shortcuts FOREIGN KEY (shortcut_id) REFERENCES shortcuts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1396
ALTER TABLE shortcuts_positions ADD CONSTRAINT FK_shortcuts_positions_positions FOREIGN KEY (position_id) REFERENCES positions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1397
ALTER TABLE shortcuts_positions ADD CONSTRAINT FK_shortcuts_positions_shortcuts FOREIGN KEY (shortcut_id) REFERENCES shortcuts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1398
ALTER TABLE shortcuts_roles ADD CONSTRAINT FK_shortcuts_roles_roles FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1399
ALTER TABLE shortcuts_roles ADD CONSTRAINT FK_shortcuts_roles_shortcuts FOREIGN KEY (shortcut_id) REFERENCES shortcuts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1400
ALTER TABLE shortcuts ADD CONSTRAINT FK_shortcuts_shortcuts_types FOREIGN KEY (shortcut_type_id) REFERENCES shortcuts_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1401
ALTER TABLE shortcuts_trading_groups ADD CONSTRAINT FK_shortcuts_trading_groups_shortcut_id FOREIGN KEY (shortcut_id) REFERENCES shortcuts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1402
ALTER TABLE shortcuts_trading_groups ADD CONSTRAINT FK_shortcuts_trading_groups_trading_group_id FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1403
ALTER TABLE shortner_url_api ADD CONSTRAINT FK_shortner_api_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1404
ALTER TABLE shortner_url_api ADD CONSTRAINT FK_shortner_url_api_sms_licenses_config FOREIGN KEY (sms_license_config_id) REFERENCES sms_licenses_config (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1405
ALTER TABLE shortner_url_log ADD CONSTRAINT FK_shortner_url_log_countries FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1406
ALTER TABLE software ADD CONSTRAINT FK_software_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1407
ALTER TABLE software_manufactures ADD CONSTRAINT FK_software_manufactures_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1408
ALTER TABLE software_types ADD CONSTRAINT FK_software_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1409
ALTER TABLE sections_subsections_positions ADD CONSTRAINT FK_ss_positions_positions FOREIGN KEY (position_id) REFERENCES positions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1410
ALTER TABLE sections_subsections_positions ADD CONSTRAINT FK_ssubsections_positions_sections_subsections FOREIGN KEY (section_subsection_id) REFERENCES sections_subsections (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1411
ALTER TABLE stand_size ADD CONSTRAINT FK_stand_size_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1412
ALTER TABLE suppliers ADD CONSTRAINT FK_suppliers_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1413
ALTER TABLE suppliers_categories ADD CONSTRAINT FK_suppliers_categories_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1414
ALTER TABLE suppliers_files ADD CONSTRAINT FK_suppliers_files_supplier_id FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1415
ALTER TABLE suppliers_images ADD CONSTRAINT FK_suppliers_images_supplier_id FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1416
ALTER TABLE suppliers_networks ADD CONSTRAINT FK_suppliers_networks_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1417
ALTER TABLE suppliers_networks ADD CONSTRAINT FK_suppliers_networks_supplier_id FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1418
ALTER TABLE suppliers_trading_groups ADD CONSTRAINT FK_suppliers_trading_groups_supplier_id FOREIGN KEY (supplier_id) REFERENCES suppliers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1419
ALTER TABLE suppliers_trading_groups ADD CONSTRAINT FK_suppliers_trading_groups_trading_group_id FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1420
ALTER TABLE tasks_garages ADD CONSTRAINT FK_task_garage_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1421
ALTER TABLE tasks_garages ADD CONSTRAINT FK_task_garage_tasks FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1422
ALTER TABLE tasks ADD CONSTRAINT FK_tasks_appointment_id FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1423
ALTER TABLE tasks_codes ADD CONSTRAINT FK_tasks_codes_tasks FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1424
ALTER TABLE tasks_contacts_lists ADD CONSTRAINT FK_tasks_contacts_lists_contact_list_id FOREIGN KEY (contact_list_id) REFERENCES contacts_lists (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1425
ALTER TABLE tasks_contacts_lists ADD CONSTRAINT FK_tasks_contacts_lists_task_id FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1426
ALTER TABLE tasks_files ADD CONSTRAINT FK_tasks_files_task_id FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1427
ALTER TABLE tasks_images ADD CONSTRAINT FK_tasks_images_tasks FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1428
ALTER TABLE tasks ADD CONSTRAINT FK_tasks_tasks_status FOREIGN KEY (task_status_id) REFERENCES tasks_status (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1429
ALTER TABLE tasks ADD CONSTRAINT FK_tasks_user_assigned_id FOREIGN KEY (user_assigned_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1430
ALTER TABLE tasks ADD CONSTRAINT FK_tasks_user_creation_id FOREIGN KEY (user_creation_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1431
ALTER TABLE tasks_users ADD CONSTRAINT FK_tasks_users_task_id FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1432
ALTER TABLE tasks_users ADD CONSTRAINT FK_tasks_users_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1433
ALTER TABLE sms_templates ADD CONSTRAINT FK_template_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1434
ALTER TABLE sms_templates ADD CONSTRAINT FK_template_country_id FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1435
ALTER TABLE sms_templates ADD CONSTRAINT FK_template_template_type_id FOREIGN KEY (template_type_id) REFERENCES sms_template_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1436
ALTER TABLE trade_show ADD CONSTRAINT FK_trade_show_conferences_delegates FOREIGN KEY (conferences_delegates_id) REFERENCES conferences_delegates (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1437
ALTER TABLE trade_show ADD CONSTRAINT FK_trade_show_weeks FOREIGN KEY (weeks_id) REFERENCES weeks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1438
ALTER TABLE trading_groups ADD CONSTRAINT FK_trading_groups_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1439
ALTER TABLE trading_groups_distributors_networks ADD CONSTRAINT FK_trading_groups_distributors_networks_network_id FOREIGN KEY (network_id) REFERENCES distributors_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1440
ALTER TABLE trading_groups_distributors_networks ADD CONSTRAINT FK_trading_groups_distributors_networks_trading_group_id FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1441
ALTER TABLE trading_groups_networks ADD CONSTRAINT FK_trading_groups_networks_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1442
ALTER TABLE trading_groups_networks ADD CONSTRAINT FK_trading_groups_networks_trading_group_id FOREIGN KEY (trading_group_id) REFERENCES trading_groups (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1443
ALTER TABLE trainings_allowances ADD CONSTRAINT FK_trainings_allowances_garages_networks FOREIGN KEY (garage_network_id) REFERENCES garages_networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1444
ALTER TABLE trainings_credits_networks ADD CONSTRAINT FK_trainings_credits_networks_reasons_allowances FOREIGN KEY (reason_allowance_id) REFERENCES reasons_allowances (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1445
ALTER TABLE trainings_credits_networks ADD CONSTRAINT FK_trainings_credits_networks_trainings_allowances FOREIGN KEY (training_allowance_id) REFERENCES trainings_allowances (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1446
ALTER TABLE trainings_credits_networks ADD CONSTRAINT FK_trainings_credits_networks_trainings_delegates FOREIGN KEY (training_delegate_id) REFERENCES trainings_delegates (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1447
ALTER TABLE trainings_credits_networks ADD CONSTRAINT FK_trainings_credits_networks_trainings_planned_courses FOREIGN KEY (training_planned_course_id) REFERENCES trainings_planned_courses (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1448
ALTER TABLE trainings_delegates ADD CONSTRAINT FK_trainings_delegates_networks FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1449
ALTER TABLE trainings_delegates ADD CONSTRAINT FK_trainings_delegates_reasons_delegates FOREIGN KEY (reason_delegate_id) REFERENCES reasons_delegates (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1450
ALTER TABLE trainings_delegates ADD CONSTRAINT FK_trainings_delegates_reasons_delegates_cancelled FOREIGN KEY (reason_cancelled_id) REFERENCES reasons_delegates_cancelled (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1451
ALTER TABLE trainings_planned_courses ADD CONSTRAINT FK_trainings_planned_courses_trainings_courses FOREIGN KEY (training_course_id) REFERENCES trainings_courses (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1452
ALTER TABLE trainings_planned_courses ADD CONSTRAINT FK_trainings_planned_courses_trainings_trainers FOREIGN KEY (training_trainer_id) REFERENCES trainings_trainers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1453
ALTER TABLE trainings_planned_courses ADD CONSTRAINT FK_trainings_planned_courses_venues FOREIGN KEY (venue_id) REFERENCES venues (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1454
ALTER TABLE trainings_trainers ADD CONSTRAINT FK_trainings_trainers_trainings_providers FOREIGN KEY (training_provider_id) REFERENCES trainings_providers (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1455
ALTER TABLE tutorials_roles ADD CONSTRAINT FK_tutorials_roles_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1456
ALTER TABLE tutorials_roles ADD CONSTRAINT FK_tutorials_roles_tutorial_id FOREIGN KEY (tutorial_id) REFERENCES tutorials (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1457
ALTER TABLE tutorials ADD CONSTRAINT FK_tutorials_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1458
ALTER TABLE users ADD CONSTRAINT FK_users_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1459
ALTER TABLE users ADD CONSTRAINT FK_users_contact_id FOREIGN KEY (contact_id) REFERENCES contacts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1460
ALTER TABLE users ADD CONSTRAINT FK_users_country_id FOREIGN KEY (country_id) REFERENCES countries (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1461
ALTER TABLE users ADD CONSTRAINT FK_users_distributors FOREIGN KEY (distributor_id) REFERENCES distributors (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1462
ALTER TABLE users ADD CONSTRAINT FK_users_garages FOREIGN KEY (garage_id) REFERENCES garages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1463
ALTER TABLE users_images ADD CONSTRAINT FK_users_images_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1464
ALTER TABLE users ADD CONSTRAINT FK_users_language_id FOREIGN KEY (language_id) REFERENCES languages (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1465
ALTER TABLE users_reassignments ADD CONSTRAINT FK_users_reassignments_appointments FOREIGN KEY (appointment_id) REFERENCES appointments (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1466
ALTER TABLE users_reassignments ADD CONSTRAINT FK_users_reassignments_contacts_contacts_lists FOREIGN KEY (contact_contact_list_id) REFERENCES contacts_contacts_lists (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1467
ALTER TABLE users_reassignments ADD CONSTRAINT FK_users_reassignments_routes FOREIGN KEY (route_id) REFERENCES routes (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1468
ALTER TABLE users_reassignments ADD CONSTRAINT FK_users_reassignments_tasks FOREIGN KEY (task_id) REFERENCES tasks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1469
ALTER TABLE users_reassignments ADD CONSTRAINT FK_users_reassignments_users FOREIGN KEY (user_id_origin) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1470
ALTER TABLE users_reassignments ADD CONSTRAINT FK_users_reassignments_users_2 FOREIGN KEY (user_id_destination) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1471
ALTER TABLE users_recover_passwords ADD CONSTRAINT FK_users_recover_passwords_user_id FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1472
ALTER TABLE users ADD CONSTRAINT FK_users_role_id FOREIGN KEY (role_id) REFERENCES roles (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1473
ALTER TABLE users_searches ADD CONSTRAINT FK_users_searches FOREIGN KEY (network_status_id) REFERENCES networks_statuses (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1474
ALTER TABLE users_searches ADD CONSTRAINT FK_users_searches_routes FOREIGN KEY (route_id) REFERENCES routes (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1475
ALTER TABLE users_searches ADD CONSTRAINT FK_users_searches_users FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1476
ALTER TABLE value_add_supplier_type ADD CONSTRAINT FK_value_add_supplier_type_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1477
ALTER TABLE value_add_suppliers ADD CONSTRAINT FK_value_add_suppliers_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1478
ALTER TABLE values_adds ADD CONSTRAINT FK_values_adds_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1479
ALTER TABLE venues ADD CONSTRAINT FK_venues_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1480
ALTER TABLE venues ADD CONSTRAINT FK_venues_sales_area_id FOREIGN KEY (sales_area_id) REFERENCES sales_areas (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1481
ALTER TABLE venues_types ADD CONSTRAINT FK_venues_types_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1482
ALTER TABLE venues ADD CONSTRAINT FK_venues_venues_types FOREIGN KEY (venue_type_id) REFERENCES venues_types (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1483
ALTER TABLE websites ADD CONSTRAINT FK_websites_aag_region_id FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1484
ALTER TABLE works ADD CONSTRAINT FK_works_grouping_genart_id FOREIGN KEY (grouping_genart_id) REFERENCES grouping_genarts (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1485
ALTER TABLE works ADD CONSTRAINT FK_works_network_id FOREIGN KEY (network_id) REFERENCES networks (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1486
ALTER TABLE value_added_suppliers ADD CONSTRAINT aag_region FOREIGN KEY (aag_region_id) REFERENCES aag_regions (id) ON UPDATE RESTRICT ON DELETE RESTRICT;

-- changeset LisaCanéSáizSoftecaI:1756821087342-1487
CREATE VIEW networks_recommended_view AS select `gn`.`network_id` AS `network_id`,`gn`.`garage_id` AS `garage_id` from (((`garages_networks` `gn` join `garages_networks` `gn_others` on(((`gn`.`garage_id` = `gn_others`.`garage_id`) and (`gn`.`status` = 5) and (`gn_others`.`status` = 5) and (`gn`.`network_id` <> `gn_others`.`network_id`)))) join `networks` `networks_others` on(((`networks_others`.`id` = `gn_others`.`network_id`) and (`networks_others`.`internal` = 1)))) join `networks_recommended` on(((`gn`.`network_id` = `networks_recommended`.`network_id`) and (`gn_others`.`network_id` = `networks_recommended`.`internal_network_id`) and (`networks_recommended`.`recommended` = 1) and (`networks_recommended`.`recommended_label` = 1)))) group by `gn`.`network_id`,`gn`.`garage_id`;


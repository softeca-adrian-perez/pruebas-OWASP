<?php

class ConstantesIdiomasPrefijo
{
    const ENGLISH = 'en';
    const FRENCH = 'fr';
    const GERMAN = 'de';
}

class ConstantsValidation
{
    const MAX_LENGTH_VARCHAR = 255;
    const MAX_LENGTH_VARCHAR_CORTO = 50;
    const MAX_LENGTH_TEXT = 65535;
    const MAX_LENGTH_INT = 10;
    const MAX_LENGTH_PHONE = 30;
    const MAX_LENGTH_TINYINT = 1;
    const MAX_LENGTH_SMS = 1600;
    const MAX_DECIMALES = 2;
    const MAX_CODE = 4;
    const MAX_CODE_PROVINCE = 6;
    const MAX_VALUE_INT = 2147483647;
    const MAX_LENGTH_VARCHAR_MEDIO = 100;
    const MAX_LENGTH_VARCHAR_SMALL = 20;
}

class ConstantsMessages
{
    const NO_PERMISSION = 'Constants.No_permission';
    const WRONG_EXTENSION = 'Constants.Wrong_extension';
    const NO_EXTENSION = 'Constants.No_extension';
    const INCORRECT_LOGIN = 'Constants.Incorrect_login';
    const RESTRICTED_ACCESS = 'Constants.Restricted_access';
    const WELL_SAVED = 'Constants.Message_well_saved';
    const BAD_SAVED = 'Constants.Message_bad_saved';
    const BAD_SAVED_2 = 'Constants.Message_bad_saved_2';
    const WELL_DELETED = 'Constants.Message_well_deleted';
    const BAD_DELETED = 'Constants.Message_bad_deleted';
    const BAD_DELETED_PERMISSION_GROUP = 'Constants.Bad_deleted_permission_group';
    const NOT_EXIST_VISIT = 'Constants.Not_existing_visit';
    const NOT_EXIST_TASK = 'Constants.Not_existing_task';
    const NOT_EXIST_GARAGE = 'Constants.Not_existing_garage';
    const NOT_EXIST_DISTRIBUTOR = 'Constants.Not_existing_distributor';
    const NOT_EXIST_ALERT = 'Constants.Not_existing_alert';
    const MAX_FILE_SIZE = 'General.File_size';
    const NULL_RETRIES = 'Constants.Null_retries';
    const BAD_DELETED_BRAND_PRODUCT = 'Constants.Brand_associated_product';
    const VIRUS_FILE = 'Constants.Virus_file';
    const NOT_EXIST_VENUE = 'Constants.Not_existing_venue';
    const NOT_EXIST_CONFERENCE = 'Constants.Not_existing_conference';
    const NOT_EXIST_SERVICE = 'Constants.Not_existing_service';
    const NOT_EXIST_PROVIDER = 'Constants.Not_existing_provider';
    const NOT_EXIST_TRAINER = 'Constants.Not_existing_trainer';
    const NOT_EXIST_COURSE = 'Constants.Not_existing_course';
    const NOT_EXIST_PLANNED_COURSE = 'Constants.Not_existing_planned_course';
    const NOT_EXIST_DELEGATE = 'Constants.Not_existing_delegate';
    const NOT_EXIST_NETWORK = 'Constants.Not_existing_network';
    const FILE_ERROR_EXTENSION = 'General.File_extensions_error';
    const IMAGE_ERROR_EXTENSION = 'General.Image_extensions_error';
}

class ConstantsPagination
{
    const SIZE_PAGE_SMALL = 10;
    const SIZE_PAGE_MEDIUM = 20;
    const SIZE_PAGE_LARGE = 30;
    const SIZE_ADVANCED_SEARCH = 5;
    const SIZE_COMMENTS = 5;
    const SIZE_TUTORIALS = 12;
    const SIZE_PLANNING = 100;
    const FIRST_PAGE = 1;
    const NUMBER_PAGES = 4;
}

class ConstantsCommunicationPosition
{
    const CARROUSEL = 0;
    const FIXED1 = 1;
    const FIXED2 = 2;
}

class ConstantsShortcutPosition
{
    const POSITION1 = 1;
    const POSITION2 = 2;
    const POSITION3 = 3;
}

class ConstantsBackContactUser
{
    const BACK_CONTACTS = 1;
    const BACK_USERS = 2;
    const BACK_GARAGES = 3;
    const BACK_DISTRIBUTORS = 4;
    const BACK_GARAGES_ID = 5;
}

define('DIR_APP_ABSOLUTE', realpath(dirname(__FILE__) . DS . '..'));

class ConstantsPath
{
    const DIR_GARAGE_IMAGES = '../files/Garages/Images';
    const DIR_GARAGE_NETWORK_IMAGES = '../files/GaragesNetworks/Images';
    const DIR_DISTRIBUTOR_IMAGES = '../files/Distributors/Images';
    const DIR_APPOINTMENT_FILES = '../files/Appointments/Files';
    const DIR_GARAGE_FILES = '../files/Garages/Files';
    const DIR_GARAGE_UPDATES_ABSOLUTE = DIR_APP_ABSOLUTE . DS . 'files' . DS . 'Garages' . DS . 'Updates';
    const DIR_COMMUNICATIONS_FILES = '../files/Communications/Files';
    const DIR_MESSAGES_FILES = '../files/Messages/Files';
    const DIR_SUPPLIERS_FILES = '../files/Suppliers/Files';
    const DIR_TASK_FILES = '../files/Tasks/Files';
    const DIR_TASK_IMAGES = '../files/Tasks/Images';
    const DIR_TRANSLATIONS = '../App/Lib';
    const DIR_TRANSLATIONS_JS = 'webroot/js/translations';
    const DIR_USER_IMAGE = '../files/Users/Images';
    const DIR_USER_IMAGES_ORIGINAL = '../files/Profiles/Original';
    const DIR_QUOTATION_DETAILS_PDFS = '/files/Quotations';

    const DIR_COMMUNICATIONS_IMAGE = '/img/communications';
    const DIR_USER_IMAGES_CROP = '/img/profiles';
    const ADD_IMAGE_IMAGE = '/img/upload_pic.png';
    const ADD_DEFAULT_IMAGE = '/img/silueta.png';
    const ADD_DEFAULT_IMAGE_BIG = '/img/silueta_big.png';

    const DIR_CSV_FILES_ABSOLUTE = DIR_APP_ABSOLUTE . DS . 'files' . DS . 'Csv';
    const DIR_CSV_EXPORT_FILES_ABSOLUTE = DIR_APP_ABSOLUTE . DS . 'files' . DS . 'Csv' . DS . 'Export';
    const DIR_CSV_OBJECTIVES_FILES_ABSOLUTE = DIR_APP_ABSOLUTE . DS . 'files' . DS . 'Csv' . DS . 'Objectives';
    const DIR_SUPPLIERS_FILES_ABSOLUTE = DIR_APP_ABSOLUTE . DS . 'files' . DS . 'Suppliers' . DS . 'Files' . DS;
    const DIR_MESSAGES_FILES_ABSOLUTE = DIR_APP_ABSOLUTE . DS . 'files' . DS . 'Messages' . DS . 'Files' . DS;
    const DIR_COMMUNICATIONS_FILES_ABSOLUTE = DIR_APP_ABSOLUTE . DS . 'files' . DS . 'Communications' . DS . 'Files' . DS;
}

class FilePaths
{
    const TRADING_GROUP_IMAGES_RELATIVE = '/img/trading_groups/';
    const GARAGES_IMAGES_RELATIVE = '/img/garages/';
    const SHORTCUT_IMAGES_RELATIVE = '/img/shortcuts/';
    const COMMUNICATIONS_IMAGES_RELATIVE = '/img/communications/';
    const SUPPLIERS_IMAGES_RELATIVE = '/img/suppliers/';
    const BRANDS_IMAGES_RELATIVE = '/img/brands/';
    const PRODUCTS_IMAGES_RELATIVE = '/img/products/';
    const PINS_IMAGES_REALTIVE = '/img/pins/';
    const NETWORKS_IMAGES_RELATIVE = '/img/networks/';
    const VALUE_ADDED_SUPPLIERS_LOGO_IMAGES_RELATIVE = '/img/value-add-suppliers-images/';
    const REGIONS_IMAGES_RELATIVE = '/img/regions/';
    const COUNTRIES_IMAGES_RELATIVE = '/img/countries/';
    const ICONS_IMAGES_RELATIVE = '/img/iconos/';
    const NETWORKS_RECOMMENDED_IMAGES = '/img/networks_recommended/';
    const LANGUAGES_WEBS_FLAGS_IMAGES_RELATIVE = '/img/flags/';
}

define('GARAGES_IMAGES_ABSOLUTE', APP . 'files/Garages/Images/');
define('GARAGES_IMAGES_RELATIVE', "files" . DS . "Garages" . DS . "Images" . DS);
define('GARAGES_NETWORKS_IMAGES_ABSOLUTE', APP . 'files/GaragesNetworks/Images/');
define('GARAGES_NETWORKS_IMAGES_RELATIVE', "files" . DS . "GaragesNetworks" . DS . "Images" . DS);
define('DISTRIBUTORS_IMAGES_ABSOLUTE', APP . 'files/Distributors/Images/');
define('DISTRIBUTORS_IMAGES_RELATIVE', "files" . DS . "Distributors" . DS . "Images" . DS);
define('GARAGES_REQUEST_IMAGES_ABSOLUTE', APP . 'files/Garages/RequestImages/');
define('GARAGES_REQUEST_IMAGES_RELATIVE', "files" . DS . "Garages" . DS . "RequestImages" . DS);
define('DISTRIBUTORS_REQUEST_IMAGES_ABSOLUTE', APP . 'files/Distributors/RequestImages/');
define('DISTRIBUTORS_REQUEST_IMAGES_RELATIVE', "files" . DS . "Distributors" . DS . "RequestImages" . DS);
define('DIR_APPOINTMENT_ICS_ABSOLUTE', APP . 'files/Appointments/Ics');
define('DIR_APPOINTMENT_ICS_RELATIVE', "files" . DS . "Appointments" . DS . "Ics" . DS);
define('DIR_APPOINTMENT_FILES_ABSOLUTE', dirname(__FILE__) . '/../files/Appointments/Files/');
define('DIR_APPOINTMENT_FILES_RELATIVE', "files" . DS . "Appointments" . DS . "Files" . DS);
define('DIR_TASK_FILES_ABSOLUTE', dirname(__FILE__) . '/../files/Tasks/Files/');
define('DIR_TASK_FILES_RELATIVE', "files" . DS . "Tasks" . DS . "Files" . DS);
define('PROFILE_IMAGES_ABSOLUTE', WWW_ROOT . 'img' . DS . 'profiles' . DS);
define('PROFILE_IMAGES_RELATIVE', "img" . DS . "profiles" . DS);
define('TINYMCE_IMAGES_ABSOLUTE', WWW_ROOT . 'img' . DS . 'tinymce' . DS);
define('TINYMCE_IMAGES_RELATIVE', 'img' . DS . 'tinymce' . DS);
define('TRADING_GROUP_IMAGES_ABSOLUTE', WWW_ROOT . 'img' . DS . 'trading_groups' . DS);
define('TRADING_GROUP_IMAGES_RELATIVE', 'img' . DS . 'trading_groups' . DS);
define('NETWORK_IMAGES_ABSOLUTE', WWW_ROOT . 'img' . DS . 'networks' . DS);
define('NETWORK_IMAGES_RELATIVE', 'img' . DS . 'networks' . DS);
define('QUOTATION_DETAILS_PDFS_ABSOLUTE', APP . 'files' . DS . 'Quotations');
define('QUOTATION_DETAILS_PDFS_RELATIVE', 'files' . DS . 'Quotations' . DS);
define('NETWORKS_RECOMMENDED_IMAGES_ABSOLUTE', WWW_ROOT . 'img' . DS . 'networks_recommended' . DS);
define('NETWORKS_RECOMMENDED_IMAGES_RELATIVE', 'img' . DS . 'networks_recommended' . DS);
define('ICONS_IMAGES_RELATIVE', 'files' . DS . 'Icons' . DS);
define('BRANDS_IMAGES_RELATIVE', 'img' . DS . 'brands' . DS);
define('NETWORKS_RECOMMENDED_IMAGES', WWW_ROOT . 'img' . DS . 'networks_recommended' . DS);

class ConstantsFilePaths
{
    const GARAGES_IMAGES_ABSOLUTE = GARAGES_IMAGES_ABSOLUTE;
    const GARAGES_IMAGES_RELATIVE = GARAGES_IMAGES_RELATIVE;
    const GARAGES_NETWORKS_IMAGES_ABSOLUTE = GARAGES_NETWORKS_IMAGES_ABSOLUTE;
    const GARAGES_NETWORKS_IMAGES_RELATIVE = GARAGES_NETWORKS_IMAGES_RELATIVE;
    const DISTRIBUTORS_IMAGES_ABSOLUTE = DISTRIBUTORS_IMAGES_ABSOLUTE;
    const DISTRIBUTORS_IMAGES_RELATIVE = DISTRIBUTORS_IMAGES_RELATIVE;
    const GARAGES_REQUEST_IMAGES_ABSOLUTE = GARAGES_REQUEST_IMAGES_ABSOLUTE;
    const GARAGES_REQUEST_IMAGES_RELATIVE = GARAGES_REQUEST_IMAGES_RELATIVE;
    const DISTRIBUTORS_REQUEST_IMAGES_ABSOLUTE = DISTRIBUTORS_REQUEST_IMAGES_ABSOLUTE;
    const DISTRIBUTORS_REQUEST_IMAGES_RELATIVE = DISTRIBUTORS_REQUEST_IMAGES_RELATIVE;
    const PROFILE_IMAGES_ABSOLUTE = PROFILE_IMAGES_ABSOLUTE;
    const PROFILE_IMAGES_RELATIVE = PROFILE_IMAGES_RELATIVE;
    const TINYMCE_IMAGES_ABSOLUTE = TINYMCE_IMAGES_ABSOLUTE;
    const TINYMCE_IMAGES_RELATIVE = TINYMCE_IMAGES_RELATIVE;
    const TRADING_GROUP_IMAGES_ABSOLUTE = TRADING_GROUP_IMAGES_ABSOLUTE;
    const TRADING_GROUP_IMAGES_RELATIVE = TRADING_GROUP_IMAGES_RELATIVE;
    const DEFAULT_IMAGE = 'upload_pic.png';
    const DIR_TASK_FILES_RELATIVE = DIR_TASK_FILES_RELATIVE;
    const DIR_APPOINTMENT_FILES_RELATIVE = DIR_APPOINTMENT_FILES_RELATIVE;
    const NETWORK_IMAGES_ABSOLUTE = NETWORK_IMAGES_ABSOLUTE;
    const NETWORK_IMAGES_RELATIVE = NETWORK_IMAGES_RELATIVE;
    const QUOTATION_DETAILS_PDFS_ABSOLUTE = QUOTATION_DETAILS_PDFS_ABSOLUTE;
    const QUOTATION_DETAILS_PDFS_RELATIVE = QUOTATION_DETAILS_PDFS_RELATIVE;
    const NETWORKS_RECOMMENDED_IMAGES_ABSOLUTE = NETWORKS_RECOMMENDED_IMAGES_ABSOLUTE;
    const NETWORKS_RECOMMENDED_IMAGES_RELATIVE = NETWORKS_RECOMMENDED_IMAGES_RELATIVE;
}

define('VERSION_CACHE_NETWORK', dirname(__FILE__) . '/../webroot/css/version_cache_network.txt');

class ConstantsUserImage
{
    const CONST_DELETE_IMAGE = 'delete_image';
}

// IMAGEN
class ConstantsImageParams
{
    const IMAGE_EXTENSION = '.jpg';
    const IMAGE_WIDTH = 255;
    const IMAGE_HEIGHT = 255;
    const IMAGE_QUALITY = 100;
    const SIZE_NULL = 0;
}

class ConstantsBooleans
{
    const NO_ACTIVE = 0;
    const ACTIVE = 1;
    const NO = 0;
    const YES = 1;
}

class ConstantsStatusTasks
{
    const COMPLETED = 1;
    const PENDING = 2;
    const EXPIRED = 3;
    //    const LOCKED = 3;
}

class ConstantsTasksExistingViews
{
    const INDEX_ASSIGNED_TO_ME = 1;
    const INDEX_CREATED_BY_ME = 2;
    const INDEX_ASSIGNED_TO_MY_GROUP = 3;
    const INDEX_ASSIGNED_TO_MY_CUSTOMERS = 4;
    const INDEX_ALL = 5;

    const ASSIGNED_TO_ME = 'Task.Assigned_to_me';
    const CREATED_BY_ME = 'Task.Created_by_me';
    const ASSIGNED_TO_MY_GROUP = 'Task.Assigned_to_my_group';
    const ASSIGNED_TO_MY_CUSTOMERS = 'Task.Assigned_to_my_customers';
    const ALL = 'Task.All';
}


class ConstantsActiveHomePage
{
    const PAGE_1 = 1;
    const PAGE_2 = 2;
    const PAGE_3 = 3;
    const PAGE_4 = 4;
    const PAGE_5 = 5;
    const PAGE_6 = 6;
}

class ConstantsRoles
{
    const ADMIN = 1;
    const EMPLOYEE = 2;
    const AAG_DIRECTOR = 3;
    const AAG_MANAGER = 4;
    const GENERIC_STAFF = 5;
    const BDM_AAG = 6;
    const GARAGE = 7;
    const GENERAL_BRANCH_MANAGER = 8;
    const TG_DIRECTOR = 9;
    const GARAGE_NETWORK_MANAGER = 10;
    const BDM_TG = 11;
    const DISTRIBUTOR = 12;
    const GPC_LOGISTICS_DIRECTOR = 13;
    const GPC_LOGISTICS_RM = 14;
    const GPC_LOGISTICS_BDM = 15;
    const SUPER_ADMIN = 16;
}

Configure::write('Roles_Directory', array(
    ConstantsRoles::AAG_DIRECTOR => ConstantsRoles::AAG_DIRECTOR,
    ConstantsRoles::AAG_MANAGER => ConstantsRoles::AAG_MANAGER,
    ConstantsRoles::BDM_AAG => ConstantsRoles::BDM_AAG,
    ConstantsRoles::TG_DIRECTOR => ConstantsRoles::TG_DIRECTOR,
    ConstantsRoles::GARAGE_NETWORK_MANAGER => ConstantsRoles::GARAGE_NETWORK_MANAGER,
    ConstantsRoles::BDM_TG => ConstantsRoles::BDM_TG,
    ConstantsRoles::GENERAL_BRANCH_MANAGER => ConstantsRoles::GENERAL_BRANCH_MANAGER,
));

class ConstantsPermissions
{
    const USUARIOS_ADMIN = 1;
    const USUARIOS_ADMIN_CLAVE_MAESTRA = 2;
    const TODOS = 3;
}

class ConstantsPermissionsGrouping
{
    const CREATE_GARAGE = 1;
    const VIEW_GARAGE = 2;
    const EDIT_GARAGE = 3;
    //const DELETE_GARAGE = 4;
    //const CREATE_VISIT = 5;// No se utiliza
    const CRM = 6;
    //const EDIT_VISIT = 7;// No se utiliza
    //const DELETE_VISIT = 8;// No se utiliza
    //const CREATE_CALENDAR = 9;// No se utiliza
    const VIEW_CALENDAR = 10;
    //const EDIT_CALENDAR = 11; // No se utiliza
    //const DELETE_CALENDAR = 12; // No se utiliza
    const CREATE_DISTRIBUTOR = 13;
    const VIEW_DISTRIBUTOR = 14;
    const EDIT_DISTRIBUTOR = 15;
    //const DELETE_DISTRIBUTOR = 16;// No se utiliza
    const CREATE_USER = 18;
    const VIEW_USER = 19;
    const MAINTENANCE = 20;
    const CREATE_TRADING_GROUP = 21;
    const VIEW_TRADING_GROUP = 22;
    const CREATE_NETWORK = 23;
    const VIEW_NETWORK = 24;
    const CREATE_CONTACT_LIST = 25;
    const VIEW_CONTACT_LIST = 26;
    const CREATE_USER_GUIDE = 27;
    const VIEW_USER_GUIDE = 28;
    const VIEW_ALERT = 29;
    const CREATE_CONTACT = 30;
    const VIEW_CONTACT = 31;
    const VIEW_PERMISSION = 32;
    const VIEW_EMAILS = 33;
    const DELETE_CONTACT = 34;
    const SERVICES = 35;
    const VEHICLES = 36;
    const VEHICLE_TYPES = 37;

    const WEBSITE = 40;
    const POSITIONS = 41;
    const PERMISSIONS = 43;
    const ASSOCIATIONS = 44;
    const EVENTS = 45;
    const SHORTCUTS = 46;
    const COMMUNICATIONS = 47;
    const DELETE_USER = 48;
    const SUPPLIERS = 49;
    const MAINTENANCE_CUSTOMERS = 50;
    const MAINTENANCE_CRM = 51;
    const MAINTENANCE_CONTACT_USER = 52;
    const REASSIGNMENT = 53;
    const TASKS = 54;
    const TOPICS = 55;
    const REGIONS = 56;
    const CONFIGURATION = 57;
    const VIEW_SHORTCUTS = 58;
    const VIEW_COMMUNICATIONS = 59;
    const VIEW_SUPPLIERS = 60;
    const EDIT_TRADING_GROUP = 61;
    const EDIT_NETWORK = 62;
    const CREATE_DISTRIBUTOR_NETWORK = 63;
    const VIEW_DISTRIBUTOR_NETWORK = 64;
    const EDIT_DISTRIBUTOR_NETWORK = 65;
    const CREATE_COMMUNICATION = 67;
    const VIEW_COMMUNICATION = 68;
    const EDIT_COMMUNICATION = 69;
    const DELETE_COMMUNICATION = 70;
    const CREATE_SHORTCUT = 71;
    const VIEW_SHORTCUT = 72;
    const EDIT_SHORTCUT = 73;
    const DELETE_SHORTCUT = 74;
    const REQUEST_CHANGE_GARAGE = 75;
    const REQUEST_CHANGE_DISTRIBUTOR = 76;
    const TRANSLATIONS = 77;
    const CREATE_GARAGE_USER = 78;
    const CREATE_DISTRIBUTOR_USER = 79;
    const VIEW_DIRECTORY = 80;
    const VIEW_MAILBOX = 81;
    const CREATE_GARAGE_USER_D = 83;
    const STATISTICS = 84;
    const VIEW_AGREEMENTS = 86;
    const EDIT_AGREEMENTS = 87;
    const CREATE_AGREEMENTS = 85;
    const VIEW_SMS = 88;
    const CREATE_SMS_CONFIGURATION = 89;
    const VIEW_FLEET = 90;
    const VENUES = 91;
}

class ConstantsConfig
{
    const START_VISIT = 1;
    const START_VISIT_LOCATION = 2;
    const PENDING_VISIT = 3;
    const EVENT_OFFICE = 4;
    const DAILY_REPORT = 5;
    const INSTANT_REPORT = 6;
    const CHECK_SEND_NOTIFICATION = 7;
    const ASSIGN_TO_GROUP = 8;
    const TASK_DEADLINE = 9;
    const DISTRIBUTOR_SALES = 10;
    const SUPPLIERS_SALES = 11;
    const DETAX = 13;
    const SIRET = 14;
    const LEAD_SOURCE = 16;
    const INTEREST = 17;
    const SPEND = 18;
    const DETAX_DISTRIBUTOR = 32;
    const SIRET_DISTRIBUTOR = 33;
    const AUTO_SAVE = 34;
    const MOT = 35;
    const TURNOVER = 36;
    const FLAT_RATE = 37;
    const COURTESY_CAR_TYPE = 38;
    const EQUIPMENT = 39;
    const MARKETING_EMAIL = 40;
    const PARTS_BRANDS = 41;
    const AFFILIATION_ASSEMBLY = 42;
    const DOCUMENTS_LEGAL = 43;
    const DIESEL_LIABILITY = 44;
    const INSURANCE_AGREEMENT = 45;
    const LABEL = 46;
    const CREDIT_WATCH = 47;
    const AAG_SERVICES = 48;
    const WORKSHOP_ACTIVITIES = 49;
    const ACTIVITY_DETAILS = 50;
    const ACTIVITY_TYPE = 51;
    const SOFTWARE_USER_PASSWORD_DISTRIBUTOR = 52;
    const SOFTWARE_USER_GARAGE = 53;
    const SOFTWARE_PASSWORD_GARAGE = 54;
    const COUNTY_COUNTRY_GARAGE = 55;
    const COUNTY_COUNTRY_DISTRIBUTOR = 56;
    const STATISTICS_IP = 57;
}

class ConstantsConfigModules
{
    const TRADING_GROUPS = 62;
    const GARAGES = 63;
    const GARAGES_NETWORKS = 64;
    const DISTRIBUTORS = 65;
    const DISTRIBUTORS_NETWORKS = 66;
    const AGREEMENTS = 67;
    const USERS = 68;
    const ALERTS = 69;
    const MAINTENANCE = 70;
    const USER_GUIDES = 71;
    const SUPPLIERS = 72;
    const SUPPLIERS_CATEGORIES = 73;
    const BRANDS = 74;
    const PRODUCTS = 75;
    const EMAILS = 76;
    const CONTACTS = 77;
    const CONTACTS_LIST = 78;
    const STATISTICS = 79;
    const TRAINING = 80;
    const CRM = 81;
    const SMS = 82;
    const FLEET = 83;
    const VENUES = 84;
    const CONFERENCES = 85;
    const VALUE_ADDED_SUPPLIER = 86;
}

class ConstantsPreferences
{
    const CALENDAR_VIEW = 1;
    const PAGINATION = 2;
}

class ConstantsEmail
{
    const NOT_SEND = 0;
    const SEND = 1;

    const NUM_RETRIES = 10;
    const NUM_EMAIL_SEND = 40;
    const ERROR_OFFICE = 'An error related with office 365 has occurred';
}

class ConstantsStatusEmail
{
    const SENT = 1;
    const PENDING = 2;
    const ERROR = 3;
}

Configure::write('email_status', array(
    ConstantsStatusEmail::SENT => 'Email.Sent',
    ConstantsStatusEmail::PENDING => 'General.Pending',
    ConstantsStatusEmail::ERROR => 'General.Fail',
));

class ConstantsEmailTypes
{
    const APPOINTMENT = 1;
    const TASK = 2;
    const EVENT = 3;
    const RECOVER_PASSWORD = 4;
    const ERROR = 6; //inactive
    const NEW_USER_PASSWORD = 9;
    const APPOINTMENT_REMINDER = 11;
    const ENQUIRY_GARAGE = 13;
    const ENQUIRY_CUSTOMER = 14;
    const ENQUIRY_GARAGE_ANSWER = 15;
    const BOOKING_GARAGE = 16;
    const BOOKING_CUSTOMER = 17;
    const APPROVED_DATE = 18;
    const BOOKING_DISTRIBUTOR = 19;
    const BOOKING_REMINDER = 20;
    const LOG_JSON_FORMAT = 21;
    const ONBOARDING = 22;
    const DATA_EXPORT = 27;
    const DISTRIBUTOR_OBJECTIVES = 28;
    const IMPORT_DISTRIBUTOR = 29;
}

Configure::write('request_fields_send_all', array(
    245,
    244,
    206,
    216,
    219,
    221,
    231,
    204
));

class ConstantsEmailAction
{
    const ADD = 1;
    const EDIT = 2;
    const DELETE = 3;
}

class ConstantsFiles
{
    const DIR_ICONOS = 'iconos/';

    const IMAGEN_MAX_WIDTH = 800;
    const IMAGEN_MAX_HEIGHT = 600;
    const IMAGEN_MINIATURA_MAX_WIDTH = 100;
    const IMAGEN_MINIATURA_MAX_HEIGHT = 100;

    const MAX_FILE_SIZE = 5242880;
}

class ConstantsSessionVariables
{
    const PERMISOS_DEL_USUARIO = 'Auth.User.Permissions';
}

class ConstantsPrefixNameFieldPermit
{
    const PERMITIR = 'permitir';
    const DENEGAR = 'denegar';
    const INCLUIR = 'incluir';
}

class ConstantsNetworks
{
    const LIGHT_COMMERCIAL_VEHICLE = 1;
    const HEAVY_COMMERCIAL_VEHICLE = 2;
}

class ConstantsTradingGroupsNames
{
    const GROUPAUTO_FRANCE = 1;
    const PARTNERS = 2;
    const PRECISIUM = 3;
    const GEF_AUTO = 4;
    const SAS_FRANCE = 5;
    const AAG_FRANCE = 6;
}

class ConstantsDistributorActivity
{
    const LV = 1;
    const CV = 2;
}

class ConstantsServiceImageDimension
{
    const WIDTH = 68;
    const HEIGHT = 32;
}

class ConstantsNetworkImageDimension
{
    const WIDTH_CLUSTER = 61;
    const HEIGHT_CLUSTER = 31;
    const WIDTH_PIN = 21;
    const HEIGHT_PIN = 26;
}

Configure::write('network_types', array(
    ConstantsNetworks::HEAVY_COMMERCIAL_VEHICLE => 'CV',
    ConstantsNetworks::LIGHT_COMMERCIAL_VEHICLE => 'LV',
));

class ConstantsAlertsErrors
{
    const ERROR_NAME_EMPTY = 'Constants.Error_alert_name_empty';
    const ERROR_GENERAL = 'Constants.Error_alert_general';
    const ERROR_DIMENSIONS = 'Constants.Error_alert_dimensions';
    const ERROR_EXTENSION = 'Constants.Error_alert_extensions';
    const ERROR_NOT_FILE = 'Constants.Error_alert_not_file';
    const ERROR_DELETE = 'Constants.Error_alert_delete';
    const ERROR_POSITION = 'Constants.Error_position';
    const ERROR_POSITION_ROLE = 'Constants.Error_position_role';
    const ERROR_ROLE = 'Constants.Error_role';
    const ERROR_PIN = 'Constants.Error_pin';
    const ERROR_LANGUAGE = 'Constants.Fill_languages';
    const ERROR_DELETE_ASSOCIATIONS = 'Constants.Delete_associations';
}

class ConstantsFlag
{
    const ERROR_NO = 0;
    const ERROR_NAME_EMPTY = 1;
    const ERROR_DIMENSIONS = 2;
    const ERROR_EXTENSION = 3;
    const ERROR_NOT_FILE = 4;
    const ERROR_POSITION = 5;
    const ERROR_POSITION_ROLE = 6;
}

class ConstantsLeadSource
{
    const MEMBER_NOMINATION = 1;
    const OUTSIDE_ENQUIRY = 2;
    const SHOW_ENQUIRY = 3;
    const WEB_ENQUIRY = 4;
    const TELEPHONE_ENQUIRY = 5;
}

Configure::write('lead_source', array(
    ConstantsLeadSource::MEMBER_NOMINATION => 'Garage.Member_nomination',
    ConstantsLeadSource::OUTSIDE_ENQUIRY => 'Garage.Outside_enquiry',
    ConstantsLeadSource::SHOW_ENQUIRY => 'Garage.Show_enquiry',
    ConstantsLeadSource::TELEPHONE_ENQUIRY => 'Garage.Telephone_enquiry',
    ConstantsLeadSource::WEB_ENQUIRY => 'Garage.Web_enquiry',
));

class ConstantsGarageStatus
{
    //const PROSPECT = 1;// No se utiliza
    const ACTIVE = 2;
    const ONSTOP = 3;
    const INACTIVE = 4;
}
class ConstantsDistributorStatus
{
    //const PROSPECT = 1;// No se utiliza
    const ACTIVE = 2;
    const INACTIVE = 3;
}

class ConstantsTasks
{
    const USER = 0;
    const GARAGE = 1;
    const DISTRIBUTOR = 2;
}

Configure::write('Garage_Status', array(
    ConstantsGarageStatus::ACTIVE => 'General.Active',
    ConstantsGarageStatus::ONSTOP => 'General.Inactive',
    ConstantsGarageStatus::INACTIVE => 'Garage.Inactive',
    //ConstantsGarageStatus::PROSPECT => 'Prospect'// No se utiliza
));

Configure::write('Distributor_Status', array(
    ConstantsDistributorStatus::ACTIVE => 'General.Active',
    ConstantsDistributorStatus::INACTIVE => 'General.Inactive',
    //ConstantsDistributorStatus::PROSPECT => 'Prospect'// No se utiliza
));

class ConstantsGarageStatusDe
{
    const POTENTIAL = 4;
}

Configure::write('Garage_Status_De', array(
    ConstantsGarageStatusDe::POTENTIAL => 'Garage.Potential',
));

class ConstantsNetworksStatus
{
    const PROSPECT = 1;
    const AWAITING_VISIT = 2;
    const AWAITING_DECISION = 3;
    const NOT_CONVERTED = 4;
    const LIVE = 5;
    const UNSUBSCRIBE = 6;
    const ON_HOLD = 7;
    const LEFT = 8;
}

Configure::write('Network_Status', array(
    ConstantsNetworksStatus::AWAITING_VISIT => 'Network.Awaiting_visit',
    ConstantsNetworksStatus::AWAITING_DECISION => 'Network.Awaiting_decision',
    ConstantsNetworksStatus::LIVE => 'Network.Live',
    ConstantsNetworksStatus::NOT_CONVERTED => 'Network.Not_converted',
    ConstantsNetworksStatus::PROSPECT => 'Network.Prospect',
    ConstantsNetworksStatus::UNSUBSCRIBE => 'Network.Unsubscribe',
    ConstantsNetworksStatus::ON_HOLD => 'Network.On_hold',
    ConstantsNetworksStatus::LEFT => 'Network.Left',
));

class ConstantsExclude
{
    const MODIFICATION_GARAGE = 'modification_date';
    const ID = 'id';
    const GARAGE_ID = 'garage_id';
    const DISTRIBUTOR_ID = 'distributor_id';
    const DISTRIBUTOR_ACTIVITY_ID = 'distributor_activity_id';
    const REQUESTED_CHANGE_SENT = 'sent';
    const SPEND_THIS_MONTH = 'spend_this_month';
    const SPEND_LAST_MONTH = 'spend_last_month';
    const SPEND_12_MONTH = 'spend_12_month';
    const SPEND_PROJECTED = 'spend_projected';
    const LOCO = 'name_lc';
}

Configure::write('Exclude_logs', array(
    ConstantsExclude::MODIFICATION_GARAGE => ConstantsExclude::MODIFICATION_GARAGE,
    ConstantsExclude::ID => ConstantsExclude::ID,
    ConstantsExclude::GARAGE_ID => ConstantsExclude::GARAGE_ID,
    ConstantsExclude::DISTRIBUTOR_ID => ConstantsExclude::DISTRIBUTOR_ID,
    ConstantsExclude::DISTRIBUTOR_ACTIVITY_ID => ConstantsExclude::DISTRIBUTOR_ACTIVITY_ID
));

Configure::write('Exclude_requested_change', array(
    ConstantsExclude::MODIFICATION_GARAGE => ConstantsExclude::MODIFICATION_GARAGE,
    ConstantsExclude::ID => ConstantsExclude::ID,
    ConstantsExclude::GARAGE_ID => ConstantsExclude::GARAGE_ID,
    ConstantsExclude::DISTRIBUTOR_ID => ConstantsExclude::DISTRIBUTOR_ID,
    ConstantsExclude::REQUESTED_CHANGE_SENT => ConstantsExclude::REQUESTED_CHANGE_SENT,
    ConstantsExclude::SPEND_THIS_MONTH => ConstantsExclude::SPEND_THIS_MONTH,
    ConstantsExclude::SPEND_LAST_MONTH => ConstantsExclude::SPEND_LAST_MONTH,
    ConstantsExclude::SPEND_12_MONTH => ConstantsExclude::SPEND_12_MONTH,
    ConstantsExclude::SPEND_PROJECTED => ConstantsExclude::SPEND_PROJECTED,

));

class ConstantsPositions
{
    const BDM_AAG_ID = 1;
    const BDM_TG_ID = 29;
    const BDM_GPC_ID = 36;
    //const DISTRIBUTOR_ID = 30;// No se utiliza
    const DISTRIBUTOR_MANAGER_ID = 33;
    const GENERAL_BRANCH_MANAGER_ID = 22;
    const GENERIC_STAFF_ID = 2;
    const GARAGE_MANAGER_ID = 32;
    const NATIONAL_SALES_MANAGER_CV_ID = 18;
    const NATIONAL_SALES_MANAGER_LV_ID = 19;
    const REGIONAL_SALES_MANAGER_LV_ID = 21;
    const REGIONAL_SALES_MANAGER_CV_ID = 20;
    const TRADING_GROUP_DIRECTOR_ID = 37;
}

class ConstantsStatusAppointments
{
    const PLANNED = 1;
    const ACCOMPLISHED = 2;
    const CANCELED = 3;
    const RESCHEDULED = 4;
    const EVENT = 5;
    const PENDING = 7;
}

class ConstantsStatusAppointmentsDe
{
    const RUNNING = 6;
}

Configure::write('Appointment_Status_De', array(
    ConstantsStatusAppointmentsDe::RUNNING => 'Running',
));

class ConstantsTypesAppointments
{
    const VISIT = 1;
    const PROSPECT_GARAGE_VISIT = 17;
    const TEAMS = 20;
}

class ConstantsStatusColorAppointments
{
    const PLANNED = 'rgb(71,134,255)';
    const ACCOMPLISHED = 'rgb(132,193,91)';
    const CANCELED = 'rgb(255,86,86)';
    const RESCHEDULED = 'rgb(232,149,59)';
    const EVENT = 'rgb(195,195,195)';
    const PENDING = 'rgb(221, 198, 20)';
    const RUNNING = 'rgb(222, 123, 57)';
}

class ConstantsLanguages
{
    const ENGLISH = 1;
    const FRENCH = 2;
    const GERMAN = 3;
    const ENGLISH_CODE = 'en';
    const FRENCH_CODE = 'fr';
    const GERMAN_CODE = 'de';
    const LOCO_CODE = 'lc';
}

class ConstantsAlerts
{
    const APPOINTMENT = 1;
    const TASK = 2;
    const EVENT = 3;
    const GARAGE = 4;
    const RM = 5;
    const ARTICLE = 6;
}

class ConstantsMessagesTypes
{
    const GARAGE = 1;
    const DISTRIBUTOR = 2;
}

class ConstantsImageType
{
    const TYPE_JPG = 'jpg';
    const TYPE_JPEG = 'jpeg';
    const TYPE_PNG = 'png';
    const TYPE_GIF = 'gif';
}

class ConstantsServiceInternationalCode
{
    // GARAGES
    const BODYWORK = 'bodywork';
    const MECHANIC = 'mechanic';
    const ELECTRICITY = 'electricity';
    const PAINT = 'paint';
    const FAST_FIT = 'fast_fit';
    const COOL_HEATING = 'cool_heating';
    const INJECTION = 'injection';
    const BRAKES = 'brakes';
    const TYRES = 'tyres';
    const ELECTRONICS = 'electronics';
    const DIAGNOSIS = 'diagnosis';

    const TECHNICAL_INSPECTION = 'technical_inspection';
    const TOWING = 'towing';
    const COURTESY_CAR = 'courtesy_car';
    const PICK_DELIVER = 'pick_and_deliver';

    const TACHYMETER = 'tachymeter';
    const GEARBOX = 'gearbox';
    const SUSPENSION = 'suspension';
    const ENGINE = 'engine';
    const STEERING_CONTROL = 'steering_control';
    const REFRIGERATED_VEHICLE = 'refrigerated_vehicle';
    const REPAIR_PUNCTURE = 'repair_puncture';
    const LIGHTING = 'lighting';
    const OZONE_CLEANING = 'ozone_cleaning';

    const WAITING_ROOM = 'waiting_room';
    const WASHING_CLEANING = 'washing_cleaning';
    const RENTAL_VEHICLE = 'rental_vehicle';

    const PARKING = null;
    // SHOPS
    const BODY_PARTS_AND_MIRRORS = 'body_parts_and_mirrors';
    const MIRRORS = 'mirrors';
    const HEADLIGHTS_LIGHTS_AND_BATTEIES = 'headlights_lights_and_batteries';
    const ENGINE_AND_DRIVETRAIN = 'engine_and_drivetrain';
    const BRAKES_SUSPENSION_AND_STEERING = 'brakes_suspension_and_steering';
    const WHEELS_TYRES = 'wheels_and_tyres';
    const LUBRICANTS_FILTERS = 'lubricants_and_filters';
    const EXHAUST_PIPES = 'exhaust_pipes';
    const INTERIOR_ACCESSORIES = 'interior_accessories';
    const EXTERIOR_ACCESSORIES = 'exterior_accessories';

    const EQUIPMENT = 'equipment';
    const TOOLS_ELECTRONICS = 'tools_electronics';
    const CLEANING_CARE = 'cleaning_care';
    const GARMENTS = 'garments';

    // DISTRIBUTORS
    const HEADQUARTERS = 'headquarters';
    const CV = 'cv';
    const LV = 'lv';
    const OUTLET = 'outlet';
    const LOGISTIC_PLATFORM = 'logistic_platform';
    const TECHNICAL_PLATFORM = 'technical_platform';
    const TRUCKS = 'trucks';
}

class ConstantsVehicleTypeInternationalCode
{
    const PASSENGER_CAR = 'passenger_car';
    const MOTORCYCLES = 'motorcycles';
    const VANS = 'vans';
    const ALL_TERRAIN = '4x4';
    const LIGHT_COMMERCIAL_VEHICLE = 'light_commercial_vehicle';
    const HEAVY_COMMERCIAL_VEHICLE = 'heavy_commercial_vehicle';

    const BUS_COACH = 'bus_coach';
    const TRAILER = 'trailer';
    const TRUCKS = 'tractors';
    const LIGHT_VEHICLES = 'van';
    const INDUSTRIAL_VAN = 'industrial_van';
    const HEAVY_DUTY = 'heavy_equipment';
    const AGRICULTURAL = 'agricultural_machinery';
}

class ConstantsOauth
{
    const AUTHORIZATION_ENDPOINT = 'https://login.microsoftonline.com/organizations/oauth2/v2.0/authorize';
    const TOKEN_ENDPOINT = 'https://login.microsoftonline.com/organizations/oauth2/v2.0/token';
    const URL_EVENT = 'https://graph.microsoft.com/v1.0/me/calendar/events';
    const SCOPE_WRITE_CALENDAR = 'calendars.readwrite';
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';
}

class ConstantsHttpMethods
{
    const POST = 'POST';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
}

class ConstantsLimitDashboard
{
    const APPOINTMENT = 5;
    const TASK = 5;
    const FLOP_FIVE = 5;
}

class ConstantsCustomerType
{
    const A = 'A';
    //const B = 'B';// No se utiliza
    //const C = 'C';// No se utiliza
    //const D = 'D';// No se utiliza
}

Configure::write('Customer_Types', array(
    ConstantsCustomerType::A => 'A',
    //ConstantsCustomerType::B => 'B',
    //ConstantsCustomerType::C => 'C',
    //ConstantsCustomerType::D => 'D',
));

class ConstantsLastVisit
{
    const PLUS_THREE_MONTHS = 'Red';
    const PLUS_SIX_MONTHS = 'Red';
    const THREE_MONTHS_TWO_WEEKS = 'Orange';
    const THREE_MONTHS_SIX_MONTHS = 'Orange';
    const MINUS_TWO_WEEKS = 'Green';
    const MINUS_THREE_MONTHS = 'Green';
    const NO_VISIT = 'Grey';
}

class ConstantsDistanceType
{
    const KM = 'KM';
    const MILES = 'MI';
}

class ConstantsDistanceOptions
{
    const SHORT_KM = 10;
    const MEDIUM_KM = 25;
    const LARGE_KM = 50;
    const LARGE2_KM = 80;
    const LARGE3_KM = 160;

    const SHORT_MILE = 5;
    const MEDIUM_MILE = 15;
    const LARGE_MILE = 30;
    const LARGE2_MILE = 50;
    const LARGE3_MILE = 100;
}

Configure::write('Distance_Options_KM', array(
    ConstantsDistanceOptions::SHORT_KM => ConstantsDistanceOptions::SHORT_KM . ' ' . ConstantsDistanceType::KM,
    ConstantsDistanceOptions::MEDIUM_KM => ConstantsDistanceOptions::MEDIUM_KM  . ' ' . ConstantsDistanceType::KM,
    ConstantsDistanceOptions::LARGE_KM => ConstantsDistanceOptions::LARGE_KM . ' ' . ConstantsDistanceType::KM,
    ConstantsDistanceOptions::LARGE2_KM => ConstantsDistanceOptions::LARGE2_KM . ' ' . ConstantsDistanceType::KM,
    ConstantsDistanceOptions::LARGE3_KM => ConstantsDistanceOptions::LARGE3_KM . ' ' . ConstantsDistanceType::KM,
));

Configure::write('Distance_Options_Miles', array(
    ConstantsDistanceOptions::SHORT_MILE => ConstantsDistanceOptions::SHORT_MILE  . ' ' . ConstantsDistanceType::MILES,
    ConstantsDistanceOptions::MEDIUM_MILE => ConstantsDistanceOptions::MEDIUM_MILE . ' ' . ConstantsDistanceType::MILES,
    ConstantsDistanceOptions::LARGE_MILE => ConstantsDistanceOptions::LARGE_MILE . ' ' . ConstantsDistanceType::MILES,
    ConstantsDistanceOptions::LARGE2_MILE => ConstantsDistanceOptions::LARGE2_MILE . ' ' . ConstantsDistanceType::MILES,
    ConstantsDistanceOptions::LARGE3_MILE => ConstantsDistanceOptions::LARGE3_MILE . ' ' . ConstantsDistanceType::MILES,
));

class ConstantsVisitType
{
    const GARAGE = 1;
    const DISTRIBUTOR = 2;
}

class ConstantsIframeVideo
{
    const WIDTH = '350';
    const HEIGHT = '250';
}

class ConstantsLengthPostcode
{
    const POSTCODE_FR = '5';
    const POSTCODE_DE = '5';
}

class ConstantsIncrease
{
    const DEFAULT_INCREASE = 1;
}

class ConstantsLogsTranslations
{
    const CV = 'CV';
    const LV = 'LV';
    const PRINCIPAL = 'Garage.Principal';
    const NOT_PRINCIPAL = 'Garage.Not_principal';
}

class ConstantsCommunicationSections
{
    const TOP = 0;
    const LEFT = 1;
    const RIGHT = 2;
}

class ConstantsMaintenance
{
    const CUSTOMER = 1;
    const CONTACT_USER = 2;
    const SHORTCUTS = 3;
    const COMMUNICATIONS = 4;
    const CRM = 5;
    const SUPPLIERS = 6;
    const REGIONS = 7;
    const CONFIGURATION = 8;
}

class ConstantsCalendarView
{
    const DAY = 1;
    const WORKWEEK = 2;
    const WEEK = 3;
    const MONTH = 4;
}

class ConstantsLogType
{
    const GARAGE = 1;
    const DISTRIBUTOR = 2;
    const CONTACT = 3;
    const GROUP_PERMISSION = 4;
    const PERMISSION = 5;
}

class ConstantsPositionConfigType
{
    const GARAGE = 1;
    const DISTRIBUTOR = 2;
    const DEFAULT_TYPE = 3;
    const COMMUNICATION_SSO = 4;
    const SUPPLIER_CATEGORY = 5;
}

class ConstantsAagMember
{
    const YES = 1;
    const NO = 2;
}

class ConstantsConfigSelect
{
    const ALL = -2;
    const WITHOUT = -1;
}

class ConstantsFileType
{
    const IMAGE = 'img';
    const PDF = 'pdf';
    const FILE = 'file';
}

class ConstantsTypeSearch
{
    const GARAGE = 1;
    const CLIENT = 2;
    const DISTRIBUTOR = 3;
    const DISTRIBUTOR_CLIENT = 4;
    const TRAINING_LIST_DELEGATES = 5;

    const GARAGE_PATH_HOME = '../Garages/home';
    const GARAGE_CLIENT_PATH_HOME = '../Clients/home';
    const GARAGE_PATH_AJAX = '../Garages/Elements/ajax_search_home';
    const GARAGE_CLIENT_PATH_AJAX = '../Clients/Elements/ajax_search_home';

    const DISTRIBUTOR_PATH_HOME = '../Distributors/home';
    const DISTRIBUTOR_PATH_AJAX = '../Distributors/Elements/ajax_search_home';
    const DISTRIBUTOR_CLIENT_PATH_HOME = '../Clients/home_distributors';
    const DISTRIBUTOR_CLIENT_PATH_AJAX = '../Clients/Elements/ajax_search_distributors';

    const TRAINING_LIST_DELEGATES_PATH_HOME = '../TrainingsListDelegates/home';
    const TRAINING_LIST_DELEGATES_PATH_AJAX = '../TrainingsListDelegates/Elements/ajax_search_home';
}

Configure::write('TypeSearch', array(
    ConstantsTypeSearch::GARAGE => ConstantsTypeSearch::GARAGE_PATH_HOME,
    ConstantsTypeSearch::CLIENT => ConstantsTypeSearch::GARAGE_CLIENT_PATH_HOME,
    ConstantsTypeSearch::DISTRIBUTOR => ConstantsTypeSearch::DISTRIBUTOR_PATH_HOME,
    ConstantsTypeSearch::DISTRIBUTOR_CLIENT => ConstantsTypeSearch::DISTRIBUTOR_CLIENT_PATH_HOME,
    ConstantsTypeSearch::TRAINING_LIST_DELEGATES => ConstantsTypeSearch::TRAINING_LIST_DELEGATES_PATH_HOME
));

Configure::write('TypeSearchAjax', array(
    ConstantsTypeSearch::GARAGE => ConstantsTypeSearch::GARAGE_PATH_AJAX,
    ConstantsTypeSearch::CLIENT => ConstantsTypeSearch::GARAGE_CLIENT_PATH_AJAX,
    ConstantsTypeSearch::DISTRIBUTOR => ConstantsTypeSearch::DISTRIBUTOR_PATH_AJAX,
    ConstantsTypeSearch::DISTRIBUTOR_CLIENT => ConstantsTypeSearch::DISTRIBUTOR_CLIENT_PATH_AJAX,
    ConstantsTypeSearch::TRAINING_LIST_DELEGATES => ConstantsTypeSearch::TRAINING_LIST_DELEGATES_PATH_AJAX
));

class ConstantsDevices
{
    const DESKTOP = 1;
    const MOBILE = 2;
    const TABLET = 3;
}

class ConstantsLogsOptions
{
    const CONTACT_ID = 'contact_id';
    const DISTRIBUTOR_ACTIVITY_ID = 'distributor_activity_id'; // Check
    const DISTRIBUTOR_PRIMARY_ACTIVITY_ID = 'primary_activity_id';
    const DISTRIBUTOR_ID = 'distributor_id'; // Check
    const GARAGE_ID = 'garage_id';
    const NETWORK_CONTRACT_TYPE_ID = 'network_contract_type_id';
    const NETWORK_ID = 'network_id';

    const PRIMARY_ACTIVITY_ID = 'primary_activity_id'; // Check
    const PROVINCE_ID = 'province_id';
    const SERVICE_ID = 'service_id';
    const SOFTWARE_ID = 'software_id';
    const USER_ID = 'user_id';
    const VEHICLE_ID = 'vehicle_id';
    const VEHICLE_TYPE_ID = 'vehicle_type_id';
    const WEBSITE_ID = 'website_id';
    // const ASSOCIATION_ID = 'association_id';// No se utiliza
    const STATUS = 'status';
    const IS_CV = 'is_cv';
    const PRINCIPAL = 'principal';
    const LEAD_SOURCE = 'lead_source';
    const TRADING_GROUP = 'trading_group_id';
    const GARAGE_SERVICE = 'garages_services';
    const GARAGE_VEHICLE = 'garages_vehicles';
    const GARAGE_VEHICLE_TYPE = 'garages_vehicle_types';
    const SALES_AREA_ID = 'sales_area_id';
    const INSURANCE_AGREEMENT_ID = 'insurance_agreement_id';
    const GARAGE_SPECIALIST_MAKES = 'garages_specialist_makes';
    const VISIT_FREQUENCY_ID = 'visit_frequency';
    const VISIT_MONDAY = 'visit_monday';
    const VISIT_TUESDAY = 'visit_tuesday';
    const VISIT_WEDNESDAY = 'visit_wednesday';
    const VISIT_THURSDAY = 'visit_thursday';
    const VISIT_FRIDAY = 'visit_friday';
    const AFFILIANTION_ASSEMBLY = 'affiliation_assembly';
    const DOCUMENTS_LEGAL = 'documents_legal';
    const DIESEL_LIABILITY = 'diesel_liability';
    const SOFTWARE_TYPE_ID = 'software_type_id';
    const SUPPLIER_ID = 'supplier_id';
    const SOFTWARE_MANUFACTURE_ID = 'software_manufacture_id';
    const PART_BRAND = 'garages_parts_brands';
    const EQUIPMENT_ID = 'equipment_id';
    const EQUIPMENT_TYPE_ID = 'equipment_type_id';
    const EQUIPMENT_SUPPLIER_ID = 'supplier_id';
    const EQUIPMENT_BRAND_ID = 'brand_id';
    const EMPLOYEES_TYPE_ID = 'employee_type_id';
    const CREDIT_WATCH = 'credit_watch';
    const LEAVING_REASON = 'reason_leaving_id';
    const HOLD_REASON = 'reason_hold_id';
    const FLAT_RATE = 'flat_rate';
    const AAG_MEMBER = 'aag_member';
    const BILLED_BY_AAG = 'billed_by_aag';
    const TURNOVER = 'turnover_id';
    const COURTESY_CAR_TYPE = 'courtesy_car_type_id';
    const WORKSHOP_ACTIVITY = 'workshop_activity_id';
    const CUSTOMER_ACTIVITY = 'customer_activity_id';
    const POSITION_ID = 'position_id';
    const PERMISSION_ID = 'permission_id';
    const POSITION_CONFIG_TYPE_ID = 'position_config_type_id';
    const ROLE_ID = 'role_id';
    const LEAVING_REASON_ID = 'leaving_reason_id';
    const SERVICE_TYPE_ID = 'service_type_id';
    const LABEL_TYPE_ID = 'label_type_id';
    const DISTRIBUTOR_TYPE_ID = 'distributor_type_id';
    const TYPE = 'type';
    const BILLING_SCHEDULE_ID = 'billing_schedule_id';
    const NUMBER_SUBSCRIPTION = 'number_subscription';
    const ONLINE_ORDERING = 'online_ordering';
    const DD_ACTIVE = 'dd_active';
    const ANNEX_DETAIL = 'annex_detail_id';
    const FLEET_WORK_DIRECTION = 'fleet_work_direction';
    const COLLECTION_DELIVERY_B2B = 'collection_delivery_b2b';
    const COLLECTION_DELIVERY_B2C = 'collection_delivery_b2c';
    const POSTCODE_B2B = 'postcode_id_b2b';
    const POSTCODE_B2C = 'postcode_id_b2c';
    const EMPLOYEE_TYPE = 'employee_type_id';
    const GARAGE_CAMPAIGN = 'garage_campaign_id';
    const FACILITIES = 'facility_id';
    const VALUE_ADD_SUPPLIER = 'value_add_supplier_id';
    const VALUE_ADD_SUPPLIER_TYPE = 'value_add_supplier_type_id';
    const ORDERS_ID = 'id';
    const VALUE_ADD = 'value_add_id';
}

class ConstantsLogsOptionsModels
{
    const CONTACT_MODEL = 'Contact';
    const DISTRIBUTOR_MODEL = 'Distributor';
    const DISTRIBUTOR_ACTIVITY_PRIMARY_MODEL = 'DistributorActivityPrimary';
    const GARAGE_MODEL = 'Garage';
    const NETWORK_CONTRACT_TYPE_MODEL = 'NetworkContractType';
    const NETWORK_MODEL = 'Network';
    const PROVINCE_MODEL = 'Province';
    const SERVICE_MODEL = 'Service';
    const SOFTWARE_MODEL = 'Software';
    const USER_MODEL = 'User';
    const VEHICLE_MODEL = 'Vehicle';
    const VEHICLE_TYPE_MODEL = 'VehicleType';
    const ASSOCIATION_MODEL = 'Association';
    const WEBSITE_MODEL = 'Website';
    const TRADING_GROUP_MODEL = 'TradingGroup';
    const DISTRIBUTOR_PRIMARY_ACTIVITY_MODEL = 'DistributorActivity';
    const SERVICE = 'Service';
    const VEHICLE = 'Vehicle';
    const VEHICLE_TYPE = 'VehicleType';
    const SALES_AREA_MODEL = 'SalesArea';
    const INSURANCE_AGREEMENT_MODEL = 'InsuranceAgreement';
    const VISIT_FREQUENCY_MODEL = 'GarageVisitFrequency';
    const SOFTWARE_TYPE_MODEL = 'SoftwareType';
    const SUPPLIER_MODEL = 'Supplier';
    const SOFTWARE_MANUFACTURE_MODEL = 'SoftwareManufacture';
    const PART_BRAND_MODEL = 'PartBrand';
    const EQUIPMENT_MODEL = 'Equipment';
    const EQUIPMENT_TYPE_MODEL = 'EquipmentType';
    const EQUIPMENT_SUPPLIER_MODEL = 'Supplier';
    const EQUIPMENT_BRAND_MODEL = 'Brand';
    const EMPLOYEES_TYPE_MODEL = 'EmployeeType';
    const SUPPLIERS_MODEL = 'Supplier';
    const LEAVING_REASON_TYPE_MODEL = 'LeavingReasonType';
    const HOLD_REASON_TYPE_MODEL = 'HoldReasonType';
    const TURNOVER_MODEL = 'Turnover';
    const GARAGE_BRAND_MODEL = 'GarageBrand';
    const COURTESY_CAR_TYPE_MODEL = 'CourtesyCarType';
    const WORKSHOP_ACTIVITY_MODEL = 'WorkshopActivity';
    const CUSTOMER_ACTIVITY_MODEL = 'CustomerActivity';
    const POSITION_MODEL = 'Position';
    const PERMISSION_MODEL = 'Permission';
    const POSITION_CONFIG_TYPE_MODEL = 'PositionConfigType';
    const ROLE_MODEL = 'Role';
    const SERVICE_TYPE_MODEL = 'ServiceType';
    const LABEL_TYPE_MODEL = 'LabelType';
    const DISTRIBUTOR_TYPE_MODEL = 'DistributorType';
    const BILLING_SCHEDULE_MODEL = 'BillingSchedule';
    const ANNEX_DETAIL_MODEL = 'AnnexDetail';
    const POSTCODE_B2B_MODEL = 'GarageB2bPostcode';
    const POSTCODE_B2C_MODEL = 'GarageB2cPostcode';
    const FACILITIES_MODEL = 'Facility';
    const VALUE_ADD_SUPPLIER_MODEL = 'ValueAddSupplier';
    const VALUE_ADD_SUPPLIER_TYPE_MODEL = 'ValueAddSuppliertype';

    const EMPLOYEE_TYPE_MODEL = 'EmployeeType';
    const GARAGE_CAMPAIGN_MODEL = 'CampaignEntry';
    const ORDERS_MODEL = 'Order';
    const VALUE_ADD_MODEL = 'ValueAdd';
}

Configure::write(
    'ConstantsFileTypes',
    array(
        'txt',
        'pdf',
        'doc',
        'rtf',
        'xls',
        'ppt',
        'docx',
        'xlsx',
        'pptx',
        'zip',
        'rar',
        'png',
        'jpe',
        'jpeg',
        'jpg',
        'gif',
        'bmp',
        'ico',
        'tiff',
        'tif',
        'svg',
        'svgz',
        'webp'
    )
);
Configure::write('ConstantsLogsOptionsChecks', array(
    ConstantsLogsOptions::VISIT_MONDAY,
    ConstantsLogsOptions::VISIT_TUESDAY,
    ConstantsLogsOptions::VISIT_WEDNESDAY,
    ConstantsLogsOptions::VISIT_THURSDAY,
    ConstantsLogsOptions::VISIT_FRIDAY,
    ConstantsLogsOptions::AFFILIANTION_ASSEMBLY,
    ConstantsLogsOptions::DOCUMENTS_LEGAL,
    ConstantsLogsOptions::DIESEL_LIABILITY,
    ConstantsLogsOptions::CREDIT_WATCH,
    ConstantsLogsOptions::FLAT_RATE,
    ConstantsLogsOptions::AAG_MEMBER,
    ConstantsLogsOptions::DD_ACTIVE,
    ConstantsLogsOptions::FLEET_WORK_DIRECTION,
    ConstantsLogsOptions::COLLECTION_DELIVERY_B2B,
    ConstantsLogsOptions::COLLECTION_DELIVERY_B2C,
    ConstantsLogsOptions::BILLED_BY_AAG,
    ConstantsLogsOptions::ONLINE_ORDERING,
));

Configure::write('Constants_Logs_Options_Translations', array(
    ConstantsLogsOptions::NETWORK_CONTRACT_TYPE_ID => ConstantsLogsOptionsModels::NETWORK_CONTRACT_TYPE_MODEL,
    ConstantsLogsOptions::SOFTWARE_ID => ConstantsLogsOptionsModels::SOFTWARE_MODEL,
    ConstantsLogsOptions::DISTRIBUTOR_ACTIVITY_ID => ConstantsLogsOptionsModels::DISTRIBUTOR_ACTIVITY_PRIMARY_MODEL,
    ConstantsLogsOptions::DISTRIBUTOR_PRIMARY_ACTIVITY_ID => ConstantsLogsOptionsModels::DISTRIBUTOR_PRIMARY_ACTIVITY_MODEL,
    ConstantsLogsOptions::SOFTWARE_TYPE_ID => ConstantsLogsOptionsModels::SOFTWARE_TYPE_MODEL,
    ConstantsLogsOptions::EQUIPMENT_ID => ConstantsLogsOptionsModels::EQUIPMENT_MODEL,
    ConstantsLogsOptions::EQUIPMENT_TYPE_ID => ConstantsLogsOptionsModels::EQUIPMENT_TYPE_MODEL,
    ConstantsLogsOptions::EMPLOYEES_TYPE_ID => ConstantsLogsOptionsModels::EMPLOYEES_TYPE_MODEL,
    ConstantsLogsOptions::LEAVING_REASON => ConstantsLogsOptionsModels::LEAVING_REASON_TYPE_MODEL,
    ConstantsLogsOptions::HOLD_REASON => ConstantsLogsOptionsModels::HOLD_REASON_TYPE_MODEL,
    ConstantsLogsOptions::LEAVING_REASON_ID => ConstantsLogsOptionsModels::LEAVING_REASON_TYPE_MODEL,
    ConstantsLogsOptions::TURNOVER => ConstantsLogsOptionsModels::TURNOVER_MODEL,
    ConstantsLogsOptions::COURTESY_CAR_TYPE => ConstantsLogsOptionsModels::COURTESY_CAR_TYPE_MODEL,
    ConstantsLogsOptions::WORKSHOP_ACTIVITY => ConstantsLogsOptionsModels::WORKSHOP_ACTIVITY_MODEL,
    ConstantsLogsOptions::CUSTOMER_ACTIVITY => ConstantsLogsOptionsModels::CUSTOMER_ACTIVITY_MODEL,
    ConstantsLogsOptions::VEHICLE_ID => ConstantsLogsOptionsModels::VEHICLE_MODEL,
    ConstantsLogsOptions::VEHICLE_TYPE_ID => ConstantsLogsOptionsModels::VEHICLE_TYPE_MODEL,
    ConstantsLogsOptions::POSITION_ID => ConstantsLogsOptionsModels::POSITION_MODEL,
    ConstantsLogsOptions::PERMISSION_ID => ConstantsLogsOptionsModels::PERMISSION_MODEL,
    ConstantsLogsOptions::POSITION_CONFIG_TYPE_ID => ConstantsLogsOptionsModels::POSITION_CONFIG_TYPE_MODEL,
    ConstantsLogsOptions::SOFTWARE_MANUFACTURE_ID => ConstantsLogsOptionsModels::SOFTWARE_MANUFACTURE_MODEL,
    ConstantsLogsOptions::ROLE_ID => ConstantsLogsOptionsModels::ROLE_MODEL,
    ConstantsLogsOptions::DISTRIBUTOR_TYPE_ID => ConstantsLogsOptionsModels::DISTRIBUTOR_TYPE_MODEL,
    ConstantsLogsOptions::BILLING_SCHEDULE_ID => ConstantsLogsOptionsModels::BILLING_SCHEDULE_MODEL,
    ConstantsLogsOptions::ANNEX_DETAIL => ConstantsLogsOptionsModels::ANNEX_DETAIL_MODEL,
    ConstantsLogsOptions::GARAGE_CAMPAIGN => ConstantsLogsOptionsModels::GARAGE_CAMPAIGN_MODEL,
    ConstantsLogsOptions::FACILITIES => ConstantsLogsOptionsModels::FACILITIES_MODEL,
    ConstantsLogsOptions::VALUE_ADD_SUPPLIER => ConstantsLogsOptionsModels::VALUE_ADD_SUPPLIER_MODEL,
    ConstantsLogsOptions::VALUE_ADD_SUPPLIER_TYPE => ConstantsLogsOptionsModels::VALUE_ADD_SUPPLIER_TYPE_MODEL,
    ConstantsLogsOptions::ORDERS_ID => ConstantsLogsOptionsModels::ORDERS_MODEL,
    ConstantsLogsOptions::VALUE_ADD => ConstantsLogsOptionsModels::VALUE_ADD_MODEL,
    ConstantsLogsOptions::SALES_AREA_ID => ConstantsLogsOptionsModels::SALES_AREA_MODEL,
));

Configure::write('Constants_Logs_Options', array(
    ConstantsLogsOptions::CONTACT_ID => ConstantsLogsOptionsModels::CONTACT_MODEL,
    ConstantsLogsOptions::DISTRIBUTOR_ID => ConstantsLogsOptionsModels::DISTRIBUTOR_MODEL,
    ConstantsLogsOptions::GARAGE_ID => ConstantsLogsOptionsModels::GARAGE_MODEL,
    ConstantsLogsOptions::NETWORK_ID => ConstantsLogsOptionsModels::NETWORK_MODEL,
    ConstantsLogsOptions::PROVINCE_ID => ConstantsLogsOptionsModels::PROVINCE_MODEL,
    ConstantsLogsOptions::USER_ID => ConstantsLogsOptionsModels::USER_MODEL,
    ConstantsLogsOptions::VEHICLE_ID => ConstantsLogsOptionsModels::VEHICLE_MODEL,
    ConstantsLogsOptions::VEHICLE_TYPE_ID => ConstantsLogsOptionsModels::VEHICLE_TYPE_MODEL,
    ConstantsLogsOptions::WEBSITE_ID => ConstantsLogsOptionsModels::WEBSITE_MODEL,
    ConstantsLogsOptions::TRADING_GROUP => ConstantsLogsOptionsModels::TRADING_GROUP_MODEL,
    // ConstantsLogsOptions::ASSOCIATION_ID => ConstantsLogsOptionsModels::ASSOCIATION_MODEL,
    ConstantsLogsOptions::GARAGE_SERVICE => ConstantsLogsOptionsModels::SERVICE,
    ConstantsLogsOptions::GARAGE_VEHICLE => ConstantsLogsOptionsModels::VEHICLE,
    ConstantsLogsOptions::GARAGE_VEHICLE_TYPE => ConstantsLogsOptionsModels::VEHICLE_TYPE,
    ConstantsLogsOptions::INSURANCE_AGREEMENT_ID => ConstantsLogsOptionsModels::INSURANCE_AGREEMENT_MODEL,
    ConstantsLogsOptions::VISIT_FREQUENCY_ID => ConstantsLogsOptionsModels::VISIT_FREQUENCY_MODEL,
    ConstantsLogsOptions::SUPPLIER_ID => ConstantsLogsOptionsModels::SUPPLIER_MODEL,
    ConstantsLogsOptions::PART_BRAND => ConstantsLogsOptionsModels::PART_BRAND_MODEL,
    ConstantsLogsOptions::EQUIPMENT_SUPPLIER_ID => ConstantsLogsOptionsModels::EQUIPMENT_SUPPLIER_MODEL,
    ConstantsLogsOptions::EQUIPMENT_BRAND_ID => ConstantsLogsOptionsModels::EQUIPMENT_BRAND_MODEL,
    ConstantsLogsOptions::SUPPLIER_ID => ConstantsLogsOptionsModels::SUPPLIERS_MODEL,
    ConstantsLogsOptions::SERVICE_TYPE_ID => ConstantsLogsOptionsModels::SERVICE_TYPE_MODEL,
    ConstantsLogsOptions::LABEL_TYPE_ID => ConstantsLogsOptionsModels::LABEL_TYPE_MODEL,
    ConstantsLogsOptions::POSTCODE_B2B => ConstantsLogsOptionsModels::POSTCODE_B2B_MODEL,
    ConstantsLogsOptions::POSTCODE_B2C => ConstantsLogsOptionsModels::POSTCODE_B2C_MODEL,
));

class ConstantsGroupingPermissions
{
    const GARAGE = 1;
    const DISTRIBUTOR = 4;
    const TRADING_GROUP = 8;
}

class ConstantsLists
{
    const EQUIPMENT_NAME = 1;
    const EQUIPMENT_TYPE = 2;
    const SOFTWARE_NAME = 3;
    const SOFTWARE_TYPE = 4;
    const SOFTWARE_PROVIDER = 5;
    const EQUIPMENT_SOFTWARE_BILLING_SCHEDULE = 6;
    const WEBSITE_NAME = 7;
    const ADDITIONAL_TURNOVER = 8;
    const COURTESY_CAR_TYPE = 9;
    const LEAVING_REASON = 10;
    const HOLD_REASON = 11;
    const ANNEX_DETAILS = 12;
    const EMPLOYEE_TYPES = 13;
    const POSTCODES = 14;
    const FACILITIES = 15;
    const VALUE_ADD_SUPPLIER = 16;
    const VALUE_ADD_SUPPLIER_TYPE = 17;
    const CAMPAING_ENTRIES = 18;
    const ORDER_PRODUCTS = 19;
    const ROOM_TYPE = 20;
    const STAND_SIZE = 21;
    const CITIES = 22;
    const COURSE_TYPE = 23;
    // const VEHICLES = 24;
    const VALUES_ADDS = 25;
    const SERVICES_DRIVERS = 26;
    const REASON_DELEGATE = 27;
    const REASON_DELEGATE_CANCELLED = 28;
    const VENUES_TYPES = 29;
    const REASON_ALLOWANCE = 30;
    const ASSOCIATION_TYPE = 31;
    const NETWORK_CONTRACT_TYPE = 32;
    const ORDER_TYPES_PRODUCTS = 33;
    const ORDER_TYPES = 34;
    const APPROVED_GARAGE_CITY = 35;
    const APPROVED_GARAGE_SERVICE_TO_DRIVER = 36;
    const GARAGEVERGELIJKER_CITY = 37;
    const GARAGEVERGELIJKER_SERVICE_TO_DRIVER = 38;
    const GARAGE_CHECKER_CITY = 39;
    const GARAGE_CHECKER_SERVICE_TO_DRIVER = 40;
    const SALES_AREA = 41;
}

//ID TABLE CONFIG
class ConstantsTabs
{
    const GENERAL_BRANCH_MANAGER = 58;
    const EMPLOYEES = 59;
    const BDM = 60;
}

//ID TABLE CONFIG_SECTION
class ConstantsSections
{
    const GARAGES = 1;
    const DISTRIBUTORS = 2;
    const VISIT_PAGE = 3;
    const TASKS = 4;
    const DASHBOARD = 5;
    const STATISTICS = 6;
    const TABS = 7;
    const MODULES = 8;
}

// ID TABLA WEEK
class ConstantsWeeks
{
    const MONDAY = 1;
    const TUSDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;
    const SUNDAY = 7;
}

// TYPE TABLE AGREEMENT
class ConstantsTypeAgreement
{
    const PERSONAL = 1;
    const MANAGEMENT = 2;
}

// FLASH SECONDS OUT
class ConstantsFlashSeconds
{
    const FLASH_SECONDS = 15;
}

class ConstantsPlannnedCourseStatus
{
    const INACTIVE = 0;
    const ACTIVE = 1;
    const CANCELED = 2;
}

class ConstantsPlannnedCourseStatusName
{
    const INACTIVE = 'Inactive';
    const ACTIVE = 'Active';
    const CANCELED = 'Canceled';
}

class ConstantsAvailabilityName
{
    const UNAVAILABLE = 'Unavailable';
    const AVAILABLE = 'Available';
    const COMPLETE = 'Complete';
}

class ConstantsDescriptionCredits
{
    const EXTRA_GIVEN = 'Extra Given';
    const YEARLY_RENEW = 'Yearly Renew';
    const REFUNDED_CREDITS = 'Refunded Credits';
}

class ConstantsQuotingPricingTypes
{
    const NETWORK_QUOTING = 1;
    const GARAGE_SPECIFIC_QUOTING = 2;
    const LIST_PRICE = 1;
    const NET_PRICE = 2;
}

class ConstantesSizeWidget
{
    const SMALL = 1;
    const BIG = 2;
}

class ConstantesGraphicReports
{
    const GRAPHIC_SERVICES = 1;
    const GRAPHIC_STATISTICS = 2;
}

class ConstantsUpdateFromLeadgenInfoTypes
{
    const SERVICES = 'services';
    const FLUIDS = 'fluids';
    const GROUPINGS_DATA = 'groupingsData';
}

class ConstantsTypesGenartsLeadGen
{
    const FLUID = 'F';
    const DUMMY = 'D';
    const STANDARD = 'S';
}
class ConstantsUpdateImage
{
    const EXTENSIONS_FILES = ['jpg', 'jpeg', 'png', 'svg', 'webp'];
    const MIME_TYPE_FILES = ['image/jpg', 'image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
    const SIZE_FILES_UPLOAD = 4000000; // 1 MB = 1000000 bytes
    const SIZE_FILES_UPLOAD_TEXT = '4 MB';
}

class ConstantsUpdateFile
{
    const EXTENSIONS_FILES = ['pdf'];
    const MIME_TYPE_FILES = ['application/pdf'];
    const SIZE_FILES_UPLOAD = 4000000; // 1 MB = 1000000 bytes
    const SIZE_FILES_UPLOAD_TEXT = '4 MB';
}

class ConstantsFileErrorTypes
{
    const OK = 1;
    const SIZE_ERROR = 2;
    const IMAGE_EXTENSION_ERROR = 3;
    const FILE_EXTENSION_ERROR = 4;
}

class ConstantsBookingsStatus
{
    const PENDING = 1;
    const CANCELLED = 2;
    const CONFIRMED = 3;
    const COMPLETED = 4;
    const EXPIRED = 5;
}

class ConstantsAvailabilityLenght
{
    const LENGHT = 30;
}

class ConstantsAAGRegionId
{
    const BENELUX = 1;
    const UK = 2;
}

class ConstantsSoftwareValues
{
    const CALLTRACKS = 'Calltracks';
}

class ConstantsSoftwareId
{
    const TECHNICAL_HELPLINE = 6;
    const MAM = 7;
}

class ConstantesPaginacion
{
    const TAM_PAGINA = 20;
    const TAM_PAGINA_GRANDE = 100;
    const TAM_LISTADO_EXPORTACION = 30000;
    const TAM_LIMITE_EXPORTACION_OPERACIONES = 101;
    const TAM_LIMITE_EXPORTACION_VEHICULOS = 1001;
    const TAM_LIMITE_EXPORTACION_FACTURAS = 51;
    const TAM_10 = 10;
    const TAM_20 = 20;
    const TAM_35 = 35;
    const TAM_50 = 50;
    const TAM_100 = 100;
    const TAM_120 = 120;
    const TAM_200 = 200;
    const TAM_1000 = 1000;
}

class ConstantesFtp
{
    const LIMITE_CONEXION = 1480;
    const GARAGE_NAME = '_GARAGES';
    const CSV_GARAGE_PATH = '../files/Csv/';
    const CSV_DB_PATH = '../files/CsvDB/';
    const DBZIP_NAME = '_DBZIP';
    const AGREEMENT_NAME = '_AGREEMENT';
    const ANNEX_DETAILS_NAME = '_ANNEX_DETAILS';
    const ASSOCIATIONS_TYPES_NAME = '_ASSOCIATIONS_TYPES';
    const ASSOCIATIONS_NAME = '_ASSOCIATIONS';
    const APPOINTMENTS_NAME = '_APPOINTMENTS';
    const BILLINGS_SCHEDULES_NAME = '_BILLINGS_SCHEDULES';
    const BOOKINGS_NAME = '_BOOKINGS';
    const CITIES_NAME = '_CITIES';
    const CONTACTS_NAME = '_CONTACTS';
    const CONTACTS_TITLES_NAME = '_CONTACTS_TITLES';
    const COUNTRIES_NAME = '_COUNTRIES';
    const COURSES_TYPES_NAME = '_COURSES_TYPES';
    const COURTESY_CAR_TYPES_NAME = '_COURTESY_CAR_TYPES';
    const CUSTOMERS_ACTIVITIES_NAME = '_CUSTOMERS_ACTIVITIES';
    const DISTRIBUTORS_NAME = '_DISTRIBUTORS';
    const DISTRIBUTORS_ACTIVITIES_NAME = '_DISTRIBUTORS_ACTIVITIES';
    const DISTRIBUTORS_ACTIVITIES_PRIMARY_NAME = '_DISTRIBUTORS_ACTIVITIES_PRIMARY';
    const DISTRIBUTORS_COMMENTS_NAME = '_DISTRIBUTORS_COMMENTS';
    const DISTRIBUTORS_CONTACTS_BDM_NAME = '_DISTRIBUTORS_CONTACTS_BDM';
    const DISTRIBUTORS_CONTACTS_GENERAL_BRANCH_MANAGER_NAME = '_DISTRIBUTORS_CONTACTS_GENERAL_BRANCH_MANAGER';
    const DISTRIBUTORS_CONTACTS_STAFF_NAME = '_DISTRIBUTORS_CONTACTS_STAFF';
    const DISTRIBUTORS_CONTRACTS_NAME = '_DISTRIBUTORS_CONTRACTS';
    const DISTRIBUTORS_CUSTOMER_ACTIVITIES_NAME = '_DISTRIBUTORS_CUSTOMER_ACTIVITIES';
    const DISTRIBUTORS_CUSTOMER_ACTIVITIES_WORKSHOPS_NAME = '_DISTRIBUTORS_CUSTOMER_ACTIVITIES_WORKSHOPS';
    const DISTRIBUTORS_DISTRIBUTORS_ACTIVITIES_NAME = '_DISTRIBUTORS_DISTRIBUTORS_ACTIVITIES';
    const DISTRIBUTORS_DISTRIBUTORS_NETWORKS_NAME = '_DISTRIBUTORS_DISTRIBUTORS_NETWORKS';
    const DISTRIBUTORS_FIGURES_NAME = '_DISTRIBUTORS_FIGURES';
    const DISTRIBUTORS_FIGURES_DETAILS_NAME = '_DISTRIBUTORS_FIGURES_DETAILS';
    const DISTRIBUTORS_IMAGES_NAME = '_DISTRIBUTORS_IMAGES';
    const DISTRIBUTORS_KPIS_NAME = '_DISTRIBUTORS_KPIS';
    const DISTRIBUTORS_LABELS_NAME = '_DISTRIBUTORS_LABELS';
    const DISTRIBUTORS_NETWORKS_NAME = '_DISTRIBUTORS_NETWORKS';
    const DISTRIBUTORS_NETWORKS_CONTACTS_BDM_NAME = '_DISTRIBUTORS_NETWORKS_CONTACTS_BDM';
    const DISTRIBUTORS_OBJECTIVES_NAME = '_DISTRIBUTORS_OBJECTIVES';
    const DISTRIBUTORS_ROUTES_NAME = '_DISTRIBUTORS_ROUTES';
    const DISTRIBUTORS_SERVICES_NAME = '_DISTRIBUTORS_SERVICES';
    const DISTRIBUTORS_SOFTWARE_NAME = '_DISTRIBUTORS_SOFTWARE';
    const DISTRIBUTORS_TYPES_NAME = '_DISTRIBUTORS_TYPES';
    const EMPLOYEE_TYPES_NAME = '_EMPLOYEE_TYPES';
    const ENQUIRIES_NAME = '_ENQUIRIES';
    const EQUIPMENTS_NAME = '_EQUIPMENTS';
    const EQUIPMENTS_TYPES_NAME = '_EQUIPMENTS_TYPES';
    const FACILITIES_NAME = '_FACILITIES';
    const FLUID_NAME = '_FLUIDS';
    const GARAGES_NAME = '_GARAGES';
    const GARAGES_AGREEMENTS_NAME = '_GARAGES_AGREEMENTS';
    const GARAGES_B2B_POSTCODES_NAME = '_GARAGES_B2B_POSTCODES';
    const GARAGES_B2C_POSTCODES_NAME = '_GARAGES_B2C_POSTCODES';
    const GARAGES_BRANDS_NAME = '_GARAGES_BRANDS';
    const GARAGES_CAMPAIGN_NAME = '_GARAGES_CAMPAIGN';
    const GARAGES_COMMENTS_NAME = '_GARAGES_COMMENTS';
    const GARAGES_CONTACTS_BDM_NAME = '_GARAGES_CONTACTS_BDM';
    const GARAGES_CONTACTS_GENERAL_BRANCH_MANAGER_NAME = '_GARAGES_CONTACTS_GENERAL_BRANCH_MANAGER';
    const GARAGES_CONTACTS_LISTS_NAME = '_GARAGES_CONTACTS_LISTS';
    const GARAGES_CONTACTS_STAFF_NAME = '_GARAGES_CONTACTS_STAFF';
    const GARAGES_COURTESY_CAR_TYPES_NAME = '_GARAGES_COURTESY_CAR_TYPES';
    const GARAGES_CUSTOMERS_ACTIVITIES_NAME = '_GARAGES_CUSTOMERS_ACTIVITIES';
    const GARAGES_DISTRIBUTORS_NAME = '_GARAGES_DISTRIBUTORS';
    const GARAGES_DISTRIBUTORS_SHORTCUTS_NAME = '_GARAGES_DISTRIBUTORS_SHORTCUTS';
    const GARAGES_EMPLOYEES_NAME = '_GARAGES_EMPLOYEES';
    const GARAGES_EQUIPMENTS_NAME = '_GARAGES_EQUIPMENTS';
    const GARAGES_FACILITIES_NAME = '_GARAGES_FACILITIES';
    const GARAGES_FIGURES_NAME = '_GARAGES_FIGURES';
    const GARAGES_FIGURES_DETAILS_NAME = '_GARAGES_FIGURES_DETAILS';
    const GARAGES_FILES_NAME = '_GARAGES_FILES';
    const GARAGES_IMAGES_NAME = '_GARAGES_IMAGES';
    const GARAGES_KPIS_NAME = '_GARAGES_KPIS';
    const GARAGES_NETWORKS_NAME = '_GARAGES_NETWORKS';
    const GARAGES_NETWORKS_CONTACTS_NAME = '_GARAGES_NETWORKS_CONTACTS';
    const GARAGES_NETWORKS_FLUIDS_NAME = '_GARAGES_NETWORKS_FLUIDS';
    const GARAGES_NETWORKS_GENARTS_NAME = '_GARAGES_NETWORKS_GENARTS';
    const GARAGES_NETWORKS_GENARTS_FAMILIES_NAME = '_GARAGES_NETWORKS_GENARTS_FAMILIES';
    const GARAGES_NETWORKS_GENARTS_MASTER_NAME = '_GARAGES_NETWORKS_GENARTS_MASTER';
    const GARAGES_NETWORKS_IMAGES_NAME = '_GARAGES_NETWORKS_IMAGES';
    const GARAGES_NETWORKS_SERVICES_NAME = '_GARAGES_NETWORKS_SERVICES';
    const GARAGES_NETWORKS_SERVICES_DRIVERS_NAME = '_GARAGES_NETWORKS_SERVICES_DRIVERS';
    const GARAGES_NETWORKS_VEHICLES_NAME = '_GARAGES_NETWORKS_VEHICLES';
    const GARAGES_NETWORKS_VEHICLES_BLACK_LIST_NAME = '_GARAGES_NETWORKS_VEHICLES_BLACK_LIST';
    const GARAGES_NETWORKS_VEHICLE_TYPES_NAME = '_GARAGES_NETWORKS_VEHICLE_TYPES';
    const GARAGES_NETWORKS_WORKS_NAME = '_GARAGES_NETWORKS_WORKS';
    const GARAGES_NETWORKS_WORKS_LABOURS_NAME = '_GARAGES_NETWORKS_WORKS_LABOURS';
    const GARAGES_NETWORKS_WORKS_PRICES_NAME = '_GARAGES_NETWORKS_WORKS_PRICES';
    const GARAGES_OILS_NAME = '_GARAGES_OILS';
    const GARAGES_PRODUCTS_NAME = '_GARAGES_PRODUCTS';
    const GARAGES_ROUTES_NAME = '_GARAGES_ROUTES';
    const GARAGES_SERVICES_NAME = '_GARAGES_SERVICES';
    const GARAGES_SOFTWARE_NAME = '_GARAGES_SOFTWARE';
    const GARAGES_SPECIALIST_MAKES_NAME = '_GARAGES_SPECIALIST_MAKES';
    const GARAGES_STATUSES_NAME = '_GARAGES_STATUSES';
    const GARAGES_VALUES_ADDS_NAME = '_GARAGES_VALUES_ADDS';
    const GARAGES_VALUE_ADD_SUPPLIER_NAME = '_GARAGES_VALUE_ADD_SUPPLIER';
    const GARAGES_VEHICLES_NAME = '_GARAGES_VEHICLES';
    const GARAGES_VEHICLE_TYPES_NAME = '_GARAGES_VEHICLE_TYPES';
    const GARAGES_VISIT_FREQUENCIES_NAME = '_GARAGES_VISIT_FREQUENCIES';
    const GARAGES_WEBSITES_NAME = '_GARAGES_WEBSITES';
    const GARAGES_WORKSHOP_ACTIVITIES_NAME = '_GARAGES_WORKSHOP_ACTIVITIES';
    const GENARTS_NAME = '_GENARTS';
    const GENARTS_FAMILIES_NAME = '_GENARTS_FAMILIES';
    const GENARTS_MASTER_NAME = '_GENARTS_MASTER';
    const GROUPING_GENARTS_NAME = '_GROUPING_GENARTS';
    const HOLD_REASON_TYPES_NAME = '_HOLD_REASON_TYPES';
    const LEAVING_REASON_TYPES_NAME = '_LEAVING_REASON_TYPES';
    const LISTS_NAME = '_LISTS';
    const NETWORKS_NAME = '_NETWORKS';
    const NETWORKS_CONTACTS_BDM_NAME = '_NETWORKS_CONTACTS_BDM';
    const NETWORKS_CONTACTS_LISTS_NAME = '_NETWORKS_CONTACTS_LISTS';
    const NETWORKS_CONTRACT_TYPES_NAME = '_NETWORKS_CONTRACT_TYPES';
    const NETWORKS_STATUSES_NAME = '_NETWORKS_STATUSES';
    const ORDERS_NAME = '_ORDERS';
    const ORDER_PRODUCTS_NAME = '_ORDER_PRODUCTS';
    const ORDER_TYPES_NAME = '_ORDER_TYPES';
    const POSITIONS_NAME = '_POSITIONS';
    const POSTCODES_NAME = '_POSTCODES';
    const POSTCODE_PROVINCES_NAME = '_POSTCODE_PROVINCES';
    const PROVINCES_NAME = '_PROVINCES';
    const REASONS_DELEGATES_NAME = '_REASONS_DELEGATES';
    const REGIONS_NAME = '_REGIONS';
    const ROLES_NAME = '_ROLES';
    const SERVICES_NAME = '_SERVICES';
    const SERVICES_DRIVERS_NAME = '_SERVICES_DRIVERS';
    const SERVICES_TYPES_NAME = '_SERVICES_TYPES';
    const SOFTWARE_NAME = '_SOFTWARE';
    const SOFTWARE_MANUFACTURES_NAME = '_SOFTWARE_MANUFACTURES';
    const SOFTWARE_TYPES_NAME = '_SOFTWARE_TYPES';
    const TASKS_NAME = '_TASKS';
    const TRADING_GROUPS_NAME = '_TRADING_GROUPS';
    const TRADING_GROUPS_DISTRIBUTORS_NETWORKS_NAME = '_TRADING_GROUPS_DISTRIBUTORS_NETWORKS';
    const TRADING_GROUPS_NETWORKS_NAME = '_TRADING_GROUPS_NETWORKS';
    const TRAININGS_COURSES_NAME = '_TRAININGS_COURSES';
    const TRAININGS_CREDITS_NAME = '_TRAININGS_CREDITS';
    const TRAININGS_CREDITS_NETWORKS_NAME = '_TRAININGS_CREDITS_NETWORKS';
    const TRAININGS_DELEGATES_NAME = '_TRAININGS_DELEGATES';
    const TRAININGS_PLANNED_COURSES_NAME = '_TRAININGS_PLANNED_COURSES';
    const TRAININGS_PROVIDERS_NAME = '_TRAININGS_PROVIDERS';
    const TRAININGS_TRAINERS_NAME = '_TRAININGS_TRAINERS';
    const USERS_NAME = '_USERS';
    const VALUES_ADDS_NAME = '_VALUES_ADDS';
    const VALUE_ADD_SUPPLIERS_NAME = '_VALUE_ADD_SUPPLIERS';
    const VALUE_ADD_SUPPLIER_TYPE_NAME = '_VALUE_ADD_SUPPLIER_TYPE';
    const VEHICLE_TYPES_NAME = '_VEHICLE_TYPES';
    const VENUES_NAME = '_VENUES';
    const WORKS_NAME = '_WORKS';
    const WORKSHOP_ACTIVITIES_NAME = '_WORKSHOP_ACTIVITIES';
    const DISTRIBUTORS_JSON_NAME = 'Distributors_';
}

class ConstantsContactAction
{
    const ADD_STAFF = 'add_contacts_staff';
}


class ConstantsErpCodes
{
    const AX = 'AX';
    const SAP = 'SAP';
    const MAM = 'MAM';
}

class ConstantsCountries
{
    const NETHERLANDS = 7;
    const UNITED_KINGDOM = 8;
}

class ConstantsTiny
{
    const URL_API_TINY = 'https://api.tinify.com/shrink';
}

class ConstantsVenuesTypes
{
    const HOTEL = 1;
}

class ConstantsSmsTemplateTypes
{
    const BOOKING = 1;
    const ENQUIRY = 2;
}

class ConstantsStatusCode
{
    const OK = 200;
    const ACCEPTED = 202;
    const FOUND = 302;
    const BAD_REQUEST = 400;
    const UNPROCESSABLE_CONTENT = 422;
}

Configure::write('STATUS_CODE', array(
    ConstantsStatusCode::OK => 'StatusCode.Ok',
    ConstantsStatusCode::FOUND => 'StatusCode.Found',
    ConstantsStatusCode::BAD_REQUEST => 'StatusCode.Bad_request',
    ConstantsStatusCode::UNPROCESSABLE_CONTENT => 'StatusCode.Unprocessable_content',
));

class ConstantsPlannnedCourseLabelName
{
    const DEACTIVATE = 'Deactivate';
    const ACTIVATE = 'Activate';
    const CANCEL = 'Cancel';
}
class MenuAction
{
    const DASHBOARD = 0;
    const GARAGES = 1;
    // const SUBSCRIPTIONS = 2;
    // const AUDITS = 3;
    // const AGREEMENTS = 4;
    // const PROMOTIONS = 5;
    // const TRAINING = 6;
    // const CALENDAR = 7;
    const ALERTS = 8;
    //const WEB = 9;
    //const CONTENTS = 10;
    //const MAINTENANCE = 11;
    //const WIDGETS = 12;
}
class ConstantsReasonCancelled
{
    const COURSE_CANCELLED = 7;
}
class ConstantsErrorUrl
{
    const BAD_APIKEY = 'error_apikey';
    const BAD_DOMAIN = 'error_domain';
}

class ConstantsQueryTypes
{
    const COUNT = 'count';
    const ALL = 'all';
    const LIST = 'list';
    const FIRST = 'first';
}

class ConstantsPrivateFilesTypes
{
    const QUOTATION_PDF = '1';
    const APPOINTMENT_FILE = '2';
    const TASK_FILE = '3';
}

class ConstantsPrivateFilesAccessAllowance
{
    const QUOTATION_PDF = array(
        ConstantsRoles::ADMIN,
        ConstantsRoles::SUPER_ADMIN,
        ConstantsRoles::GARAGE
    );

    const TASK_FILE = array(
        ConstantsRoles::ADMIN,
        ConstantsRoles::SUPER_ADMIN,
        ConstantsRoles::BDM_AAG,
        ConstantsRoles::BDM_TG,
        ConstantsRoles::GPC_LOGISTICS_BDM
    );

    const APPOINTMENT_FILE = array(
        ConstantsRoles::ADMIN,
        ConstantsRoles::SUPER_ADMIN,
        ConstantsRoles::BDM_AAG,
        ConstantsRoles::BDM_TG,
        ConstantsRoles::GPC_LOGISTICS_BDM
    );
}

class ConstantsFleet
{
    const LOBSTERCODE = '5200';
}

class ConstantsObjectives
{
    const LIMIT_CREATE = 1000;
    const TIME_SCHEDULED_TASK_CREATE = 2;
    const LIMIT_DELETE = 2000;
    const TIME_SCHEDULED_TASK_DELETE = 2;
}

class ConstantsDeleteAll
{
    const DISTRIBUTOR_OBJECTIVES = 'distributors_objectives';
}

class DefaultGarageSearchRadius
{
    const AGN = 10;
    const GENERAL = 5;
}

class ConstantsDistanceUnit
{
    const KM = 1;
    const MILES = 2;
}

class ConstantsReasonsLeaving
{
    const INACTIVE = 'Inactive';
}

class ConstantsPlatform
{
    const GNM = 1;
    const AGN = 2;
    const GV = 3;
    const GC = 4;
    const P360 = 5;
}

class ConstantsModulesStatuses
{
    const ACTIVE = 'Active';
    const INACTIVE = 'Inactive';
    const MULTIVALUE = 'Multivalue';
}

class ConstantsHTTP
{
    const HTTPS = 'https://';
}

class ConstantsReasonDelegate
{
    const UNCANCELED = 6;
}
class ConstantsActions
{
    const ADD = 1;
    const EDIT = 2;
    const COMMENT = 3;
    const DELETE = 4;
}

class ConstantsActionsNames
{
    const ADD = 'add';
    const CREATE = 'create';
    const EDIT = 'edit';
    const UPDATE = 'update';
    const COMMENT = 'comment';
    const DELETE = 'delete';
}

class ConstantsDistributorsAssociationTypes
{
    const SUBSIDIARY = 1;
    const INDEPENDENT = 2;
}

class ConstantsNetworksPins
{
    const DEFAULT = 1;
    const CUSTOM = 'custom';
}

class ConstantsTypesTinyMce
{
    const TYPES_TINY_MCE = array(
        'ae384663-5de7-4a65-832d-6f954efd96a5' => 'training-images',
        'fde6d88a-804b-4edc-a809-a70915c0fa9c' => 'value-add-suppliers-images',
    );

    const TRAINING_COURSE = 'ae384663-5de7-4a65-832d-6f954efd96a5';
    const VALUE_ADD_SUPPLIER = 'fde6d88a-804b-4edc-a809-a70915c0fa9c';
}

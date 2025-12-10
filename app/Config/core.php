<?php

App::uses('Texto', 'Lib');
require_once(__DIR__ . '/configuration.php');

/**
 * This is core configuration file.
 *
 * Use it to configure core behavior of Cake.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 ** Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * CakePHP Debug Level:
 *
 * Production Mode:
 * 	0: No error messages, errors, or warnings shown. Flash messages redirect.
 *
 * Development Mode:
 * 	1: Errors and warnings shown, model caches refreshed, flash messages halted.
 * 	2: As in 1, but also with full debug messages and SQL output.
 *
 * In production mode, flash messages redirect after a time interval.
 * In development mode, you need to click the flash message to continue.
 */
Configure::write('debug', DEBUG);

/**
 * Configure the Error handler used to handle errors for your application. By default
 * ErrorHandler::handleError() is used. It will display errors using Debugger, when debug > 0
 * and log errors with CakeLog when debug = 0.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle errors. You can set this to any callable type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `level` - integer - The level of errors you are interested in capturing.
 * - `trace` - boolean - Include stack traces for errors in log files.
 *
 * @see ErrorHandler for more information on error handling and configuration.
 */
Configure::write('Error', array(
	'handler' => 'ErrorHandler::handleError',
	'level' => E_ALL & ~E_DEPRECATED,
	'trace' => true
));

/**
 * Configure the Exception handler used for uncaught exceptions. By default,
 * ErrorHandler::handleException() is used. It will display a HTML page for the exception, and
 * while debug > 0, framework errors like Missing Controller will be displayed. When debug = 0,
 * framework errors will be coerced into generic HTTP errors.
 *
 * Options:
 *
 * - `handler` - callback - The callback to handle exceptions. You can set this to any callback type,
 *   including anonymous functions.
 *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
 * - `renderer` - string - The class responsible for rendering uncaught exceptions. If you choose a custom class you
 *   should place the file for that class in app/Lib/Error. This class needs to implement a render method.
 * - `log` - boolean - Should Exceptions be logged?
 * - `skipLog` - array - list of exceptions to skip for logging. Exceptions that
 *   extend one of the listed exceptions will also be skipped for logging.
 *   Example: `'skipLog' => array('NotFoundException', 'UnauthorizedException')`
 *
 * @see ErrorHandler for more information on exception handling and configuration.
 */
Configure::write('Exception', array(
	'handler' => 'ErrorHandler::handleException',
	'renderer' => 'ExceptionRenderer',
	'log' => true
));

/**
 * Application wide charset encoding
 */
Configure::write('App.encoding', 'UTF-8');

/**
 * To configure CakePHP *not* to use mod_rewrite and to
 * use CakePHP pretty URLs, remove these .htaccess
 * files:
 *
 * /.htaccess
 * /app/.htaccess
 * /app/webroot/.htaccess
 *
 * And uncomment the App.baseUrl below. But keep in mind
 * that plugin assets such as images, CSS and JavaScript files
 * will not work without URL rewriting!
 * To work around this issue you should either symlink or copy
 * the plugin assets into you app's webroot directory. This is
 * recommended even when you are using mod_rewrite. Handling static
 * assets through the Dispatcher is incredibly inefficient and
 * included primarily as a development convenience - and
 * thus not recommended for production applications.
 */
//Configure::write('App.baseUrl', env('SCRIPT_NAME'));

/**
 * To configure CakePHP to use a particular domain URL
 * for any URL generation inside the application, set the following
 * configuration variable to the http(s) address to your domain. This
 * will override the automatic detection of full base URL and can be
 * useful when generating links from the CLI (e.g. sending emails)
 */
//Configure::write('App.fullBaseUrl', 'http://example.com');

/**
 * Web path to the public images directory under webroot.
 * If not set defaults to 'img/'
 */
//Configure::write('App.imageBaseUrl', 'img/');

/**
 * Web path to the CSS files directory under webroot.
 * If not set defaults to 'css/'
 */
//Configure::write('App.cssBaseUrl', 'css/');

/**
 * Web path to the js files directory under webroot.
 * If not set defaults to 'js/'
 */
//Configure::write('App.jsBaseUrl', 'js/');

/**
 * Uncomment the define below to use CakePHP prefix routes.
 *
 * The value of the define determines the names of the routes
 * and their associated controller actions:
 *
 * Set to an array of prefixes you want to use in your application. Use for
 * admin or other prefixed routes.
 *
 * 	Routing.prefixes = array('admin', 'manager');
 *
 * Enables:
 *	`admin_index()` and `/admin/controller/index`
 *	`manager_index()` and `/manager/controller/index`
 *
 */
//Configure::write('Routing.prefixes', array('admin'));

/**
 * Turn off all caching application-wide.
 *
 */
//Configure::write('Cache.disable', true);

/**
 * Enable cache checking.
 *
 * If set to true, for view caching you must still use the controller
 * public $cacheAction inside your controllers to define caching settings.
 * You can either set it controller-wide by setting public $cacheAction = true,
 * or in each action using $this->cacheAction = true.
 *
 */
//Configure::write('Cache.check', true);

/**
 * Enable cache view prefixes.
 *
 * If set it will be prepended to the cache name for view file caching. This is
 * helpful if you deploy the same application via multiple subdomains and languages,
 * for instance. Each version can then have its own view cache namespace.
 * Note: The final cache file name will then be `prefix_cachefilename`.
 */
//Configure::write('Cache.viewPrefix', 'prefix');

/**
 * Session configuration.
 *
 * Contains an array of settings to use for session configuration. The defaults key is
 * used to define a default preset to use for sessions, any settings declared here will override
 * the settings of the default config.
 *
 * ## Options
 *
 * - `Session.cookie` - The name of the cookie to use. Defaults to 'CAKEPHP'
 * - `Session.timeout` - The number of minutes you want sessions to live for. This timeout is handled by CakePHP
 * - `Session.cookieTimeout` - The number of minutes you want session cookies to live for.
 * - `Session.checkAgent` - Do you want the user agent to be checked when starting sessions? You might want to set the
 *    value to false, when dealing with older versions of IE, Chrome Frame or certain web-browsing devices and AJAX
 * - `Session.defaults` - The default configuration set to use as a basis for your session.
 *    There are four builtins: php, cake, cache, database.
 * - `Session.handler` - Can be used to enable a custom session handler. Expects an array of callables,
 *    that can be used with `session_save_handler`. Using this option will automatically add `session.save_handler`
 *    to the ini array.
 * - `Session.autoRegenerate` - Enabling this setting, turns on automatic renewal of sessions, and
 *    sessionids that change frequently. See CakeSession::$requestCountdown.
 * - `Session.ini` - An associative array of additional ini values to set.
 *
 * The built in defaults are:
 *
 * - 'php' - Uses settings defined in your php.ini.
 * - 'cake' - Saves session files in CakePHP's /tmp directory.
 * - 'database' - Uses CakePHP's database sessions.
 * - 'cache' - Use the Cache class to save sessions.
 *
 * To define a custom session handler, save it at /app/Model/Datasource/Session/<name>.php.
 * Make sure the class implements `CakeSessionHandlerInterface` and set Session.handler to <name>
 *
 * To use database sessions, run the app/Config/Schema/sessions.php schema using
 * the cake shell command: cake schema create Sessions
 *
 */
Configure::write('Session', array(
	'defaults' => 'cake', // para guarde la sesion en app/tmp/sessions
	'timeout' => 60 * 2, // session.gc_maxlifetime en min = 1 día (y cookie_lifetime si no se pone cookieTimeout)
	'cookieTimeout' => 0, // cookie_lifetime -> si es 0 Expires: At end of session
	'ini' => [
		'session.cookie_path' => '/; SameSite=Strict',
	],
));

/**
 * A random string used in security hashing methods.
 */
Configure::write('Security.salt', 'DYhG54ask3120JfIxff289j3eUubWwvniEkjas45TnKaC9mi');

/**
 * A random numeric string (digits only) used to encrypt/decrypt strings.
 */
Configure::write('Security.cipherSeed', '7612339585657453852316749668472346875');

/**
 * Apply timestamps with the last modified time to static assets (js, css, images).
 * Will append a query string parameter containing the time the file was modified. This is
 * useful for invalidating browser caches.
 *
 * Set to `true` to apply timestamps when debug > 0. Set to 'force' to always enable
 * timestamping regardless of debug value.
 */
//Configure::write('Asset.timestamp', true);

/**
 * Compress CSS output by removing comments, whitespace, repeating tags, etc.
 * This requires a/var/cache directory to be writable by the web server for caching.
 * and /vendors/csspp/csspp.php
 *
 * To use, prefix the CSS link URL with '/ccss/' instead of '/css/' or use HtmlHelper::css().
 */
//Configure::write('Asset.filter.css', 'css.php');

/**
 * Plug in your own custom JavaScript compressor by dropping a script in your webroot to handle the
 * output, and setting the config below to the name of the script.
 *
 * To use, prefix your JavaScript link URLs with '/cjs/' instead of '/js/' or use JsHelper::link().
 */
//Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');

/**
 * The class name and database used in CakePHP's
 * access control lists.
 */
Configure::write('Acl.classname', 'IniAcl');
//Configure::write('Acl.classname', 'DbAcl');
//Configure::write('Acl.database', 'default');

/**
 * Uncomment this line and correct your server timezone to fix
 * any date & time related errors.
 */
date_default_timezone_set('Europe/London');

/**
 * `Config.timezone` is available in which you can set users' timezone string.
 * If a method of CakeTime class is called with $timezone parameter as null and `Config.timezone` is set,
 * then the value of `Config.timezone` will be used. This feature allows you to set users' timezone just
 * once instead of passing it each time in function calls.
 */
Configure::write('Config.timezone', 'Europe/London');

/**
 * Cache Engine Configuration
 * Default settings provided below
 *
 * File storage engine.
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'File', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'path' => CACHE, //[optional] use system tmp directory - remember to use absolute path
 * 		'prefix' => 'cake_', //[optional]  prefix every cache file with this string
 * 		'lock' => false, //[optional]  use file locking
 * 		'serialize' => true, //[optional]
 * 		'mask' => 0664, //[optional]
 *	));
 *
 * APC (http://pecl.php.net/package/APC)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Apc', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *	));
 *
 * Xcache (http://xcache.lighttpd.net/)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Xcache', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional] prefix every cache file with this string
 *		'user' => 'user', //user from xcache.admin.user settings
 *		'password' => 'password', //plaintext password (xcache.admin.pass)
 *	));
 *
 * Memcached (http://www.danga.com/memcached/)
 *
 * Uses the memcached extension. See http://php.net/memcached
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Memcached', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 * 		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 * 		'servers' => array(
 * 			'127.0.0.1:11211' // localhost, default port 11211
 * 		), //[optional]
 * 		'persistent' => 'my_connection', // [optional] The name of the persistent connection.
 * 		'compress' => false, // [optional] compress data in Memcached (slower, but uses less memory)
 *	));
 *
 *  Wincache (http://php.net/wincache)
 *
 * 	 Cache::config('default', array(
 *		'engine' => 'Wincache', //[required]
 *		'duration' => 3600, //[optional]
 *		'probability' => 100, //[optional]
 *		'prefix' => Inflector::slug(APP_DIR) . '_', //[optional]  prefix every cache file with this string
 *	));
 */

/**
 * Configure the cache handlers that CakePHP will use for internal
 * metadata like class maps, and model schema.
 *
 * By default File is used, but for improved performance you should use APC.
 *
 * Note: 'default' and other application caches should be configured in app/Config/bootstrap.php.
 *       Please check the comments in bootstrap.php for more info on the cache engines available
 *       and their settings.
 */
$engine = 'File';

// In development mode, caches should expire quickly.
$duration = '+999 days';
if (Configure::read('debug') > 0) {
	$duration = '+10 seconds';
}

// Prefix each application on the same server with a different string, to avoid Memcache and APC conflicts.
$prefix = 'probase_';

/**
 * Configure the cache used for general framework caching. Path information,
 * object listings, and translation cache files are stored with this configuration.
 */
Cache::config('_cake_core_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_core_',
	'path' => CACHE . 'persistent' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));

/**
 * Configure the cache for model and datasource caches. This cache configuration
 * is used to store schema descriptions, and table listings in connections.
 */
Cache::config('_cake_model_', array(
	'engine' => $engine,
	'prefix' => $prefix . 'cake_model_',
	'path' => CACHE . 'models' . DS,
	'serialize' => ($engine === 'File'),
	'duration' => $duration
));

Configure::write('VERSION_CACHE', VERSION_CACHE);

Configure::write('AAG_REGION_ID_BENELUX', '1');
Configure::write('AAG_REGION_ID_UK_IRELAND', '2');

Configure::write('Email.configuracion', GNMAAG_EMAIL_CONFIGURATION_NO_REGION);
Configure::write('Email.configuracionRegion.' . Configure::read('AAG_REGION_ID_BENELUX'), GNMAAG_EMAIL_CONFIGURATION_BENELUX);
Configure::write('Email.configuracionRegion.' . Configure::read('AAG_REGION_ID_UK_IRELAND'), GNMAAG_EMAIL_CONFIGURATION_UK_IRELAND);

Configure::write('Email.notification', array(
	GNMAAG_ERROR_NOTIFICATION_EMAIL_1,
	GNMAAG_ERROR_NOTIFICATION_EMAIL_2,
	GNMAAG_ERROR_NOTIFICATION_EMAIL_3
));

Configure::write('max_login_retries', '3');

Configure::write('DefaultConfig', array( // Germany
	1 =>  0, //BDMs will need to press the Start Visit button as soon as they start their visit and the visit status changes to Running. No other visits (by the same BDM) can be started until the Running visit is Ended.
	2 =>  0, //When the Start Visit button is pressed, the location is saved.
	3 =>  1, //Before the visit has passed, GNM sets the date to Planned by default. After the visit time, the status is automatically changed to Pending Feedback. Then once the feedback is entered, the status changes to Completed.
	4 =>  1, //When a visit is created in GNM, automatically create the event in the Outlook calendar of the "Assigned To" BDM.
	5 =>  0, //RSMs and selected recipients receive a daily report with all visit feedback
	6 =>  1, //RSMs and selected recipients receive instant report with individual visits
	7 =>  0, //Checkbox to send (or not) an email notification - Feedback will be included in the Notifications
	8 =>  0, //Possibility to Assign a Task to a Group of Users.
	9 =>  0, // See to deadline
	10 =>  1, //Add sales info for distributors
	11 => 1, //Add sales info per supplier (brand)
	//12 => 1, //Ref code
	13 => 0, //Detax code
	14 => 0, //Siret
	16 => 1, //Lead source
	17 => 1, //Interest
	18 => 0, //Spend this month, spend last month, spend 12 month, spend projected
	//19 => 1, //Address 2
	//20 => 1, //Address 3
	//21 => 1, //Address 4
	//22 => 1, //Abbreviation
	//23 => 1, //Trading as
	//24 => 1, //Address 2
	//25 => 1, //Address 3
	//27 => 1, //Reg number
	//28 => 1, //Rebate name
	//29 => 1, //Association with
	//30 => 1, //Currency
	//31 => 1, //MAMID
	32 => 0, //Detax code
	33 => 0, //Siret
	34 => 1, //Autosave
	35 => 1, //MOT
	//36 => 1, //Turnover
	//37 => 0, //Flat rate
	//38 => 0, //Courtesy car type,
	39 => 1, //Equipment,
	40 => 1, //Marqueting Email,
	41 => 1, //Part Brands
	42 => 0, //Affiliation assembly
	43 => 0, //Documents legal
	44 => 0, //Diesel liability
	45 => 0, //Insurance Agreement,
	46 => 0, //Label
	47 => 1, //Credit Watch
	48 => 0, //AAG Service
	49 => 0, //Workshop activities
	50 => 0, //activities details
	51 => 0, //activity type
	52 => 1, //software username / password
	53 => 1, //garage software username
	54 => 1, //garage software password
	55 => 1, //County Country Garages
	56 => 1, //County Country Distributors
	61 => 1, //Campaign Entries
));

Configure::write('Presets', array(
	1 => array('Cluster-1.png', 'Pin-1.png'),
	2 => array('Cluster-2.png', 'Pin-2.png'),
	3 => array('Cluster-3.png', 'Pin-3.png'),
	4 => array('Cluster-4.png', 'Pin-4.png'),
	5 => array('Cluster-5.png', 'Pin-5.png'),
	6 => array('Cluster-6.png', 'Pin-6.png'),
	7 => array('Cluster-7.png', 'Pin-7.png'),
	8 => array('Cluster-8.png', 'Pin-8.png'),
	9 => array('Cluster-9.png', 'Pin-9.png'),
	10 => array('Cluster-10.png', 'Pin-10.png'),
	11 => array('Cluster-11.png', 'Pin-11.png'),
	12 => array('Cluster-12.png', 'Pin-12.png'),
	13 => array('Cluster-13.png', 'Pin-13.png'),
	14 => array('Cluster-14.png', 'Pin-14.png'),
	15 => array('Cluster-15.png', 'Pin-15.png'),
	16 => array('Cluster-16.png', 'Pin-16.png'),
	17 => array('Cluster-17.png', 'Pin-17.png'),
	18 => array('Cluster-18.png', 'Pin-18.png'),
	19 => array('Cluster-19.png', 'Pin-19.png'),
	20 => array('Cluster-20.png', 'Pin-20.png'),
	21 => array('Cluster-21.png', 'Pin-21.png'),
	22 => array('Cluster-22.png', 'Pin-22.png'),
	23 => array('Cluster-23.png', 'Pin-23.png'),
	24 => array('Cluster-24.png', 'Pin-24.png'),
));


Configure::write(
	array(
		'webservice' => array(
			'password' => Texto::encryptDecryptText(WEBSERVICE_PASSWORD, false),
			'username' => WEBSERVICE_USERNAME,
			'wsdl' => WEBSERVICE_WSDL
		)
	)
);

require_once dirname(__DIR__) . '/Vendor/autoload.php';
require_once __DIR__ . '/constantes.php';

Configure::write(
	array(
		'repair-maintenance' => array(
			'url' => 'usuarios/login_externo',
			'servicios' => array(
				'nuevo_usuario' => "usuarios/nuevo_usuario_externo/",
				'actualizar_redes_garage' => "proceso_manual/pm_talleres/actualizar_redes_taller/",
				'actualizar_garage' => "proceso_manual/pm_talleres/actualizar_taller_gnm_aag/",
				'add_fleet' => "gestion_cliente_corporativo/gcc_clientes_corporativos/add_fleet_gnm_aag/",
				'edit_fleet' => "gestion_cliente_corporativo/gcc_clientes_corporativos/edit_fleet_gnm_aag/"
			),
			'url_24h' => 'BreakdownService/bs_serviciowebapps/is_a24h/',
			'gnm_code' => 'et',
			'rm_red_id' => '8',
			'url_save_in_rm' => 'repairs_maintenances/information/',
			'gnm_comunication_url' => 'repairs_maintenances/acceso_externo/',
			'url_garage_agreement' => 'repairs_maintenances/garage_agreement/',
		)
	)
);

Configure::write('AZURE_FILES', GNMAAG_AZURE_STORAGE); // false = local, true = azure
Configure::write('AZURE_CONFIG', array(
	'account_name' => GNMAAG_AZURE_STORAGE_CONFIG_ACCOUNT_NAME,
	'account_key' => Texto::encryptDecryptText(GNMAAG_AZURE_STORAGE_CONFIG_ACCOUNT_KEY, false),
	'container' => GNMAAG_AZURE_STORAGE_CONFIG_CONTAINER,
	'private_container' => GNMAAG_AZURE_STORAGE_CONFIG_PRIVATE_CONTAINER,
	'sas_duration' => GNMAAG_AZURE_STORAGE_CONFIG_SAS_DURATION,
));

Configure::write('SCANII_ANTIVIRUS_CONFIG', array(
	'active' => GNMAAG_SCANII_ANTIVIRUS,
	'key' => GNMAAG_SCANII_ANTIVIRUS_CONFIG_KEY,
	'secret' => Texto::encryptDecryptText(GNMAAG_SCANII_ANTIVIRUS_CONFIG_SECRET, false),
));

// [PERMISOS] Incluido aquí en el proyecto base. En los proyectos, ubicar donde corresponda.
Configure::write('Version', 1);

Configure::write('URL_BASE', GNMAAG_URL_BASE);

Configure::write(
	array(
		'leadgen_api' => array(
			'login_endpoint' => '/auth/login',
			'works_endpoint' => '/gnm/services',
			'fluids_endpoint' => '/gnm/fluids',
			'prices_endpoint' => '/gnm/groupingsData',
			'quotations_endpoint' => '/gnm/quotations',
			'quotations_document' => '/public/quotation_document',
			'quotations_pdf_endpoint' => '/gnm/quotationsPDF',
			'quotations_statistics' => '/gnm/statisticsQuotationsPerService',
			'emails_endpoint' => '/gnm/email',
			'quotation_details_endpoint' => '/gnm/quotationDetails'
		)
	)
);

Configure::write(
	array(
		'lobster_api' => array(
			'update_garage_endpoint' => '/updategarageinfo'
		)
	)
);

Configure::write('ANNEX_DETAIL_AUTOCARE_UNBRANDED', '1');

Configure::write('REGION_ID_BENELUX', '2');
Configure::write('REGION_ID_UK_IRELAND', '3');

Configure::write('KIYOH_GET_REVIEWS_URL', 'https://kiyoh.com/v1/publication/review/locations/latest');
Configure::write('KIYOH_LATEST_REVIEWS_URL', 'https://kiyoh.com/v1/publication/review/external/');
Configure::write('KIYOH_REQUEST_REVIEW_URL', 'https://kiyoh.com/v1/invite/external/');

Configure::write('GOOGLE_TITLE_REVIEWS_URL', 'https://maps.googleapis.com/maps/api/place/nearbysearch/json');
Configure::write('GOOGLE_GARAGE_REVIEWS_URL', 'https://maps.googleapis.com/maps/api/place/details/json');
Configure::write('GOOGLE_REVIEWS_FORM_URL', 'https://search.google.com/local/writereview');
Configure::write('GOOGLE_REVIEWS_LINK', 'https://www.google.com/maps/contrib/');

Configure::write(
	array(
		'network_language' => array(
			NETWORK_ID_AGN => 'en',
			NETWORK_ID_GV => 'nl',
			NETWORK_ID_GC => 'nl'
		)
	)
);

Configure::write(
	array(
		'agn_api' => array(
			'login_endpoint' => '/auth/login',
			'login_seo_admin_zone_endpoint' => '/gnm/getdatalogin',
			'update_loco_translations_endpoint' => '/api/gnm/updateLocoTranslations',
			'create_loco_language_endpoint' => '/api/gnm/createLocoLanguage',
			'delete_loco_language_endpoint' => '/api/gnm/deleteLocoLanguage',
			'purge_cache_location_data' => '/gnm/purgeCacheLocationData',
			'purge_cache_reviews_data' => '/gnm/purgeCacheReviewsData',
		)
	)
);

Configure::write(
	array(
		'networks_web_login_prefix' => array(
			NETWORK_ID_AGN => NETWORKS_WEB_LOGIN_PREFIX_AGN,
			NETWORK_ID_GV => NETWORKS_WEB_LOGIN_PREFIX_GV,
			NETWORK_ID_GC => NETWORKS_WEB_LOGIN_PREFIX_GC,
		)
	)
);

/* En desarrollo poner las claves que corresponden a cada uno.
	En pre y produccion poner las correspondientes claves, tener en cuenta que seran distintas
	también en GNMUK y en GNMAAG ya que llevan dominios distintos.

	Poner CLOUDFLARE_CONFIG_ACTIVE = 1 para activar verificacion en captcha si se tienen las keys
*/

Configure::write('CLOUDFLARE_URL', 'https://challenges.cloudflare.com/turnstile/v0/siteverify');

Configure::write('WEBHOST_DOMAIN', GNMAAG_URL_BASE);

Configure::write(
	array(
		'autopart' => array(
			'FTP' => array(
				'Host' => 'aagsftp.blob.core.windows.net',
				'Port' => '22',
			)
		)
	)
);

// Privacy notice configuration settings
Configure::write('ENVIRONMENT_PRO', ENVIRONMENT_PRO);
Configure::write(
	array(
		'privacy_notice' => array(
			'url' => 'https://privacyportal-cdn.onetrust.com/9e3840d9-d3d2-4665-ac3b-b1cdce104745/privacy-notices/',
			'draftUrlSegment' => 'draft/',
			'defaultCode' => PRIVACY_NOTICE_CODE_UK,
			'regionsCodes' => array(
				Configure::read('AAG_REGION_ID_BENELUX') => PRIVACY_NOTICE_CODE_BEN,
				Configure::read('AAG_REGION_ID_UK_IRELAND') => PRIVACY_NOTICE_CODE_UK,
			)
		)
	)
);

Configure::write(
	array(
		'SMS' => array(
			'remitente' => '',
			'codigo' => '',
			'usuario' => '',
			'contrasena' => '',
			'url' => 'http://www.mensagrafia.es/api'
		)
	)
);

Configure::write(
	array(
		'ERP_IDS' => array(
			ConstantsErpCodes::AX => 1,
			ConstantsErpCodes::SAP => 2,
			ConstantsErpCodes::MAM => 3
		)
	)
);
Configure::write('SMS_URL', 'https://www.mensagrafia.es/acceso-externo-alliance');
Configure::write('MAINTENANCES_DEFAULT_LANGUAGE', 'en');

Configure::write(
	array(
		'shortner_url_api' => array(
			'base_url' => 'https://t.ly/api/v1/link',
			'shorten_endpoint' => '/shorten',
		)
	)
);
Configure::write('LANGUAGE_CODE_DEFAULT', 'en');
Configure::write('Security.useOpenSsl', true);

Configure::write('Session', array(
	'defaults' => 'php'
));

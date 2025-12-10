<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @property      AccesoComponent $Acceso
 * @property      AuthUserComponent $AuthUser
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $helpers = array(
        'Acceso',
        'Form' => array('className' => 'CustomForm'),
        'Html' => array('className' => 'CustomHtml'),
    );

    public $components = array(
        'Session' => array('className' => 'CustomSession'),
        //'DebugKit.Toolbar',
        'Acceso',
        'AuthUser',
        'Acl',
        'Auth' => array(
            'loginAction' => array(
                'controller' => 'users',
                'action' => 'login',
            ),
            'loginRedirect' => array(
                'controller' => 'paginas',
                'action' => 'home'
            ),
            'logoutRedirect' => array(
                'controller' => 'users',
                'action' => 'login'
            ),
            'authError' => ConstantsMessages::RESTRICTED_ACCESS,
            'authenticate' => array(
                'Form' => array(
                    'passwordHasher' => array(
                        'className' => 'Simple',
                        'hashType' => 'sha256'
                    ),
                    'userModel' => 'User',
                    'fields' => array(
                        'username' => 'username',
                        'password' => 'password'
                    ),
                    'scope' => array(
                        'active' => ConstantsBooleans::ACTIVE
                    ),
                )
            ),
        ),
    );

    public function beforeRender()
    {
        if ($this->name == 'CakeError') {

            $exception = isset($this->viewVars['error']) ? $this->viewVars['error'] : null;

            if ($exception instanceof UnauthorizedException) {
                $this->layout = 'error_unauthorized';
            } else {
                if (Configure::read('debug') >= 1) {
                    $this->layout = 'error';
                } else {
                    $this->layout = 'error_template';
                }
            }
        }
    }

    private function add_csrf()
    {
        $add = false;

        if ($this->params['controller'] == 'brands' && in_array($this->params['action'], array('add', 'edit', 'add_opening_garage', 'add_aditional_info_and_other_details_garage'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'contacts' && in_array($this->params['action'], array('add', 'add_contact_and_user', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'contacts_lists' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'distributors' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'distributors_networks' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'garages' && in_array($this->params['action'], array('add', 'edit', 'add_opening_garage', 'add_aditional_info_and_other_details_garage'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'networks' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'products' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'suppliers' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'tasks' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'trading_groups' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'tutorials' && in_array($this->params['action'], array('add', 'edit'))) {
            $add = true;
        }

        if ($this->params['controller'] == 'users' && in_array($this->params['action'], array('add_user_to_contact', 'edit'))) {
            $add = true;
        }

        if ($add) {
            $this->Security = $this->Components->load('Security');
            $this->Security->csrfUseOnce = false;
            $this->Security->validatePost = false;
        }
    }

    public function beforeFilter()
    {
        if ($_SERVER['HTTP_HOST'] != Configure::read('WEBHOST_DOMAIN')) {
            $this->redirect('https://' . Configure::read('WEBHOST_DOMAIN'));
        }

        $this->response->header('X-Frame-Options', 'SAMEORIGIN', false);

        $this->response->disableCache();

        $this->add_csrf();

        if ($this->params['controller'] != 'languages' && (FULL_BASE_URL . $this->here) != $this->referer()) {
            CakeSession::write('url_referer', $this->referer()); // T001 SECURITY - It is not changed
        }

        if ($this->params['controller'] == 'webservices') {
            $this->Auth->allow();
        }

        if ($this->params['controller'] == 'garages_agreements') {
            $this->Auth->allow('set_garages_agreement_from_rm');
        }

        // TODO: remove and move to API plugin
        if ($this->params['controller'] == 'garages_networks') {
            $this->Auth->allow(array("garage_card"));
            $this->Auth->allow(array("garage_dates"));
            $this->Auth->allow(array("work_prices"));
            $this->Auth->allow(array("garage_info"));
            $this->Auth->allow(array("garage_search"));
            $this->Auth->allow(array("image"));
            $this->Auth->allow(array("get_first_available_date"));
        }

        if ($this->params['controller'] == 'networks') {
            $this->Auth->allow(array("get_network_list"));
            $this->Auth->allow(array("get_network_search_list"));
            $this->Auth->allow(array("update_quoting_pricing"));
            $this->Auth->allow(array("get_network_erp_account_code"));
            $this->Auth->allow(array("update_leadgen_information"));
            $this->Auth->allow(array("get_languages_flags"));
        }

        if ($this->params['controller'] == 'vehicle_types') {
            $this->Auth->allow(array("get_vehicle_types"));
        }

        if ($this->params['controller'] == 'fluids') {
            $this->Auth->allow(array("get_fluids_garages"));
        }

        if ($this->params['controller'] == 'bookings') {
            $this->Auth->allow(array("create_booking"));
        }

        if ($this->params['controller'] == 'enquiries') {
            $this->Auth->allow(array("create_enquiry"));
        }

        if ($this->params['controller'] == 'api') {
            $this->Auth->allow(array("get_authentication"));
        }

        if ($this->params['controller'] == 'vehicles') {
            $this->Auth->allow(array("get_vehicles"));
            $this->Auth->allow(array("get_locations_vehicle"));
        }

        if ($this->params['controller'] == 'works') {
            $this->Auth->allow(array("get_works"));
            $this->Auth->allow(array("get_locations_work"));
        }

        if ($this->params['controller'] == 'services') {
            $this->Auth->allow(array("get_services"));
            $this->Auth->allow(array("get_locations_service"));
        }

        if ($this->params['controller'] == 'services_drivers') {
            $this->Auth->allow(array("get_services_drivers"));
        }

        if ($this->params['controller'] == 'cities') {
            $this->Auth->allow(array("get_network_locations"));
        }

        if ($this->params['controller'] == 'reviews') {
            $this->Auth->allow(array("review_info"));
            $this->Auth->allow(array("review_network"));
            $this->Auth->allow(array("review_network_city"));
            $this->Auth->allow(array("review_network_work"));
            $this->Auth->allow(array("review_network_work_city"));
            $this->Auth->allow(array("review_network_service"));
            $this->Auth->allow(array("review_network_service_city"));
            $this->Auth->allow(array("review_network_vehicle"));
            $this->Auth->allow(array("review_network_vehicle_city"));
        }

        if ($this->params['controller'] == 'redirect') {
            $this->Auth->allow();
        }

        if ($this->params['controller'] == 'sms') {
            $this->Auth->allow(array('get_info_sms_country'));
        }

        // get all DB languages and write them in session
        $this->Language = ClassRegistry::init('Language');
        $validLanguageCodes = $this->Language->find('all', array('fields' => 'Language.code'));
        $validLanguageCodes =  Hash::extract($validLanguageCodes, '{n}.Language.code');

        CakeSession::write('Config.valid_languages_codes', $validLanguageCodes);

        if (CakeSession::started()) {
            // user's language. Can be null if there is no user connected
            $language_code = $this->AuthUser->getLanguageCode();
        }
        if (!isset($language_code)) {
            $language_code = Configure::read('LANGUAGE_CODE_DEFAULT');
        }
        CakeSession::write('Config.language', $language_code);

        FileManager::clean_problems_upload();

        global $translations;
        global $translations_languages;
        if (!is_array($translations)) {
            $translations_languages = array();
            $translations_filename = __DIR__ . '/../Lib/Translation_' . $language_code . '.php';
            if (file_exists($translations_filename)) {
                require_once $translations_filename;
                $translations = $translations_languages[$language_code];
            } else {
                $translations_filename = __DIR__ . '/../Lib/Translation_' . 'en' . '.php';
                require_once $translations_filename;
                $translations = $translations_languages['en'];
            }
        }
    }

    public function begin()
    {
        $this->{$this->modelClass}->getDataSource()->begin();
    }

    public function commit()
    {
        $this->{$this->modelClass}->getDataSource()->commit();
    }

    public function havePermission($permisos)
    {
        //TODO: Eliminar porque ya no se utiliza.
        if ($this->Acceso->havePermission($permisos)) {
            return true;
        } else {
            $this->Session->setFlashError(__t(ConstantsMessages::RESTRICTED_ACCESS));
            $this->redirect(
                array(
                    'controller' => 'paginas',
                    'action' => 'home',
                )
            );
        }
    }

    public function haveDefaultPermission($permisos)
    {
        if ($this->Acceso->haveDefaultPermission($permisos)) {
            return true;
        } else {
            $this->Session->setFlashError(__t(ConstantsMessages::RESTRICTED_ACCESS));
            $this->redirect(
                array(
                    'controller' => 'paginas',
                    'action' => 'home',
                )
            );
        }
    }

    public function haveNetworkRegionPermission($permiso)
    {
        if ($this->Acceso->haveNetworkRegionPermission($permiso)) {
            return true;
        } else {
            return false;
        }
    }

    public function custom_pagination($param__query, $conditions, $limit =  ConstantsPagination::SIZE_PAGE_SMALL, $model = null, $paginatorHelper = null, $paginatorComponent = null)
    {
        $auth = CakeSession::read('Auth');
        if (isset($auth['Paginator'])) {
            $limit = $auth['Paginator']['paginator_size'];
        }
        $this->Paginator = $this->Components->load(
            $paginatorComponent ? $paginatorComponent : 'Paginator',
            Hash::merge(
                $param__query,
                array(
                    'limit' => $limit,
                )
            )
        );
        if (isset($model)) {
            if ($paginatorHelper) {
                $this->helpers['Paginator'] = array(
                    'className' => $paginatorHelper
                );
            }
            return $this->Paginator->paginate($model, $conditions);
        } else {
            return $this->Paginator->paginate($conditions);
        }
    }

    public function paginator_size($controller, $action, $pass = null)
    {
        $data = $this->request->data;
        $this->Auth->Session->write('Auth.Paginator', array('paginator_size' => $data['Paginator']['pagination_size']));
        if (is_null($pass)) {
            $this->redirect(
                array(
                    'controller' => $controller,
                    'action' => $action
                )
            );
        } else {
            $redirect_url = array(
                'controller' => $controller,
                'action' => $action
            );
            $params = explode(",", $pass);
            foreach ($params as $param) {
                array_push($redirect_url, $param);
            }

            $this->redirect(
                $redirect_url
            );
        }
    }

    protected function downloadFile($guid, $type)
    {
        $user = $this->Acceso->user();
        if (!isset($user['role_id']) || empty($user['role_id'])) {
            $this->redirect($this->referer());
            exit;
        }
        $fileName = null;
        $relativePath = null;
        $download = false;
        $isPrivateContainer = false;
        switch ($type) {
            case ConstantsPrivateFilesTypes::QUOTATION_PDF:
                if (!in_array($user['role_id'], ConstantsPrivateFilesAccessAllowance::QUOTATION_PDF)) {
                    $this->redirect($this->referer());
                    exit;
                }
                $quotationClass = ClassRegistry::init('Quotation');
                $file = $quotationClass->findByFileGuid($guid);
                if (isset($file['Quotation']['filename']) && !empty($file['Quotation']['filename'])) {
                    $fileName = $file['Quotation']['filename'];
                    $relativePath = ConstantsFilePaths::QUOTATION_DETAILS_PDFS_RELATIVE;
                    $download = true;
                    $isPrivateContainer = true;
                }
                break;
            case ConstantsPrivateFilesTypes::APPOINTMENT_FILE:
                if (!in_array($user['role_id'], ConstantsPrivateFilesAccessAllowance::APPOINTMENT_FILE)) {
                    $this->redirect($this->referer());
                    exit;
                }
                $appointmentFileClass = ClassRegistry::init('AppointmentFile');
                $file = $appointmentFileClass->findByFileGuid($guid);
                if (isset($file['AppointmentFile']['file']) && !empty($file['AppointmentFile']['file'])) {
                    $fileName = $file['AppointmentFile']['file'];
                    $relativePath = ConstantsFilePaths::DIR_APPOINTMENT_FILES_RELATIVE;
                    $download = true;
                    $isPrivateContainer = true;
                }
                break;
            case ConstantsPrivateFilesTypes::TASK_FILE:
                if (!in_array($user['role_id'], ConstantsPrivateFilesAccessAllowance::TASK_FILE)) {
                    $this->redirect($this->referer());
                    exit;
                }
                $taskFileClass = ClassRegistry::init('TaskFile');
                $file = $taskFileClass->findByFileGuid($guid);
                if (isset($file['TaskFile']['file']) && !empty($file['TaskFile']['file'])) {
                    $fileName = $file['TaskFile']['file'];
                    $relativePath = ConstantsFilePaths::DIR_TASK_FILES_RELATIVE;
                    $download = true;
                    $isPrivateContainer = true;
                }
                break;
            default:
                break;
        }

        if (isset($fileName) && !empty($fileName)) {
            $this->download_file_name(
                $fileName,
                $fileName,
                $relativePath,
                $download,
                $isPrivateContainer
            );
        } else {
            $this->redirect($this->referer());
        }
    }

    protected function download_file_name($source_name, $server_name, $path, $download = true, $isPrivateContainer = false)
    {
        if (\Configure::read('AZURE_FILES')) {
            $path = substr($path, 0, -1);
            $resultado = FileManager::get_url($path . '/' . $server_name, $isPrivateContainer);
            $this->redirect($resultado);
            exit;
        } else {
            $this->viewClass = 'Media';
            $pathInfo = pathinfo($path . $server_name);
            $pos_point = strrchr($source_name, '.');
            if ($pos_point !== false) {
                $source_name = substr($source_name, 0, strlen($source_name) - strlen($pos_point));
            }
            $params = array(
                'id'        => $server_name,
                'name'      => $source_name,
                'extension' => $pathInfo['extension'],
                'download'  => $download,
                'path'      => $path,
            );
            $this->set($params);
        }
    }

    protected function returnJsonResult($result)
    {
        $this->response->type('json');
        $this->autoRender = false;
        return json_encode($result);
    }

    /**
     * Show view to download the file obtained from base 64.
     */
    public function download_base64_file($base64, $filename)
    {
        $base64Decoded = base64_decode($base64);
        $size = strlen($base64Decoded);
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $size);
        echo $base64Decoded;
        exit;
    }

    public function verify_captcha($response, $remoteip)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, Configure::read('CLOUDFLARE_URL'));

        curl_setopt($curl, CURLOPT_POST, true);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            array(
                'secret' => Texto::encryptDecryptText(CLOUDFLARE_SECRET_KEY, false),
                'response' => $response,
                'remoteip' => $remoteip,
            )
        );

        $curlData = curl_exec($curl);

        curl_close($curl);
        $verificar_captcha = JSON_decode($curlData);

        if (CLOUDFLARE_CONFIG_ACTIVE == ConstantsBooleans::ACTIVE && isset($verificar_captcha->success)) {
            return $verificar_captcha->success;
        } else {
            return true;
        }
    }

    public function verify_ajax($request)
    {
        if ($request->is('ajax')) {
            return true;
        } else {
            header('HTTP/1.0 405 Method Not Allowed');
            exit;
        }
    }

    public function api_auth_and_log($headers, $dataReceived)
    {
        try {
            $resultAuth = ApiUtil::checkAuthentication($headers);

            $apiUser = isset($resultAuth['result']) ? $resultAuth['result'] : null;

            // api log data
            $ip = $this->request->clientIp();
            $date = date('Y-m-d H:i:s');
            $uri = $_SERVER['REQUEST_URI'];

            $log_api = array(
                'LogApi' => array(
                    'user_email' => $apiUser,
                    'ip' => $ip,
                    'date' => $date,
                    'uri' => $uri,
                    'request' => json_encode($dataReceived, JSON_PRETTY_PRINT),
                    //'response' => null
                    //'notes' => null
                )
            );
            $logApiClass = ClassRegistry::init('LogApi');
            $logApiClass->save($log_api);
            return $resultAuth ? true : false;
        } catch (Exception $e) {
            CakeLog::debug(print_r("Api auth and log - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    public function api_log($dataReceived, $authOk)
    {
        try {
            // api log data
            $ip = $this->request->clientIp();
            $date = date('Y-m-d H:i:s');
            $uri = $_SERVER['REQUEST_URI'];

            $log_api = array(
                'LogApi' => array(
                    'user_email' => null,
                    'ip' => $ip,
                    'date' => $date,
                    'uri' => $uri,
                    'request' => $authOk ? 'auth ok' : $dataReceived,
                    //'response' => null
                    'notes' => $authOk ? 'auth ok' : 'auth ko'
                )
            );
            $logApiClass = ClassRegistry::init('LogApi');
            $logApiClass->save($log_api);
        } catch (Exception $e) {
            CakeLog::debug(print_r("Api log - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    /**
     * When exporting garages via email:
     * Verifies email format of the given address.
     */
    public function ajax_verify_email()
    {
        $this->verify_ajax($this->request);
        $this->layout = $this->autoRender = false;
        $email = $this->request->data['contact-email'];
        $emailCheck = filter_var($email, FILTER_VALIDATE_EMAIL) ? 1 : 0;
        return json_encode($emailCheck);
    }

    /**
     * Generates an email with csv file to be sent to the verified email address
     */
    public static function generateEmail($filename, $fileFullName, $user, $email, $controller)
    {
        $emailClass = ClassRegistry::init('Email');
        $controller = ucfirst($controller);
        $emailClass->newEmailExport($filename, serialize(array($fileFullName)), $email, $user, $controller);
    }
}

    <?php
    class MessagesFilesController extends AppController
    {
        public $uses = array(
            'MessageFile'
        );

        public function download_file($id_param = null)
        {
            if (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
            ) {
                if ($id_param == null) {
                    $id = $this->request->query['id'];
                } else {
                    $id = $id_param;
                }
                $files = $this->MessageFile->findAllById($id);
                foreach ($files as $file) {
                    $path = substr(ConstantsPath::DIR_MESSAGES_FILES, 3) . DS;
                    if(Configure::read('AZURE_FILES')) {
                        $filePath = FileManager::get_url($path . $file['MessageFile']['file'], false);
                        $headers = get_headers($filePath, 1);
                    } else {
                        $filePath = ConstantsPath::DIR_MESSAGES_FILES_ABSOLUTE . $file['MessageFile']['file'];
                    }
                    if ((!Configure::read('AZURE_FILES') && file_exists($filePath)) || (Configure::read('AZURE_FILES') && $headers && (strpos($headers[0], '200') !== false))) {
                        $this->download_file_name($file['MessageFile']['source_name'], $file['MessageFile']['file'], $path);
                    } else {
                        $msg = h(sprintf(__t('General.File_does_not_exists')));
                        $this->Session->setFlashError($msg);
                        $this->redirect(
                            array(
                                'controller' => 'home',
                                'action' => 'home_page2',
                            )
                        );
                    }
                }

                $this->AutoRender = false;
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        }

        /**
         * AJAX delete MessageFile.
         */
        public function ajax_delete_file()
        {
            $this->verify_ajax($this->request);

            if (
                CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
                $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::VIEW_MAILBOX)
            ) {
                $id = $this->request->data['id'];
                $messageFile = $this->MessageFile->findById($id);

                if (!$this->request->is('get')) {
                    $delete = $this->MessageFile->deleteMessageFile($id);
                    if (!$delete) {
                        $this->Session->setFlashError('Can\'t delete the file');
                    }
                }

                $messageFiles = $this->MessageFile->findAllByMessageId($messageFile['MessageFile']['message_id']);
                $this->set(
                    array(
                        'message_files' => $messageFiles,
                    )
                );

                $this->layout = null;
                $this->render('/Messages/Elements/form_attached_files');
            } else {
                header('HTTP/1.0 401 Unauthorized');
                exit;
            }
        }
    }

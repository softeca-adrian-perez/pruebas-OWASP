<?php
class ShortnerUrlLogController extends AppController
{
    public $uses = array(
        'ShortnerUrlLog',
        'ShortnerUrl',
        'Sms',
    );

    /**
     * Shortner URL log page.
     */
    public function home()
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            (
                $this->Acceso->haveReversePermission(ConstantsPermissionsGrouping::VIEW_SMS) &&
                $this->Acceso->haveModulePermission(ConstantsConfigModules::SMS)
            )
        ) {
            $user = $this->Acceso->user();
            $aagRegionId = $user['aag_region_id'];

            $status = array(
                ConstantsBooleans::NO => __t('General.Ko'),
                ConstantsBooleans::YES => __t('General.Ok')
            );

            $statusCodes = null;
            $statusCodeTmp = Configure::read('STATUS_CODE');
            foreach ($statusCodeTmp as $key => $status_code) {
                $statusCodes[$key] = $key . ' - ' . __t($status_code);
            }

            $testConfig = array(
                ConstantsBooleans::NO => __t('General.No'),
                ConstantsBooleans::YES => __t('General.Yes')
            );

            $searcher = $this->request->query;
            $this->request->data['Search'] = $searcher;

            $shortnerUrlLogs = $this->custom_pagination(
                $this->ShortnerUrlLog->_query($aagRegionId),
                $this->ShortnerUrlLog->conditions($searcher),
                ConstantsPagination::SIZE_PAGE_SMALL
            );

            $this->set(array(
                'shortner_url_logs' => $shortnerUrlLogs,
                'status' => $status,
                'status_codes' => $statusCodes,
                'test_config' => $testConfig,
            ));
        } else {
            throw new UnauthorizedException();
        }
    }
}

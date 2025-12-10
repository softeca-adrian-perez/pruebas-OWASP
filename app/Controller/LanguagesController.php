<?php
class LanguagesController extends AppController
{
    /**
     * Change web language.
     */
    public function change($code)
    {
        if (
            CakeSession::read('Auth.User.role_id') == ConstantsRoles::SUPER_ADMIN ||
            ($code != 'lc' || ($code == 'lc' && $this->Acceso->haveDefaultPermission(ConstantsPermissionsGrouping::TRANSLATIONS)))
        ) {
            $this->AuthUser->changeLanguageCode($code);
            CakeSession::write('GOOGLE_MAPS_API', 'https://maps.googleapis.com/maps/api/js?libraries=places&language=' . $code . '&key=' . Texto::encryptDecryptText(GOOGLE_API_KEY, false));
            $this->redirect($this->referer());
        } else {
            throw new UnauthorizedException();
        }
    }
}

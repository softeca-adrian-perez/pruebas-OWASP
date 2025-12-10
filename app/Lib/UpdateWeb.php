<?php
class UpdateWeb
{
    public function setEmailGuid()
    {
        $emailClass = ClassRegistry::init('Email');

        $emailClass->generateGuidForEveryEntranceWithoutIt();
        echo "END email GUIDS\n";
    }

    public function setSendgridLicenseConfigGuid()
    {
        $licenseClass = ClassRegistry::init('SendgridLicenseConfig');

        $licenseClass->generateGuidForEveryEntranceWithoutIt();
        echo "END sendgrid license config GUIDS\n";
    }

    public function setEmailTypeGuid()
    {
        $emailTypeClass = ClassRegistry::init('EmailType');

        $emailTypeClass->generateGuidForEveryEntranceWithoutIt();
        echo "END email type GUIDS\n";
    }
}

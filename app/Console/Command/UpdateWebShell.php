<?php

App::uses('UpdateWeb', 'Lib');

class UpdateWebShell extends Shell
{
    /**
     * Set email guid.
     *
     * console\cake updateweb addGuidToEmail
     */
    public function addGuidToEmail()
    {
        // will only be executed in qa, pre and pro once only
        $updateWeb = new UpdateWeb();
        $updateWeb->setEmailGuid();
    }

    /**
     * Set sendrgrid license config guid.
     *
     * console\cake updateweb addGuidToSendgridLicenseConfig
     */
    public function addGuidToSendgridLicenseConfig()
    {
        // will only be executed in qa, pre and pro once only
        $updateWeb = new UpdateWeb();
        $updateWeb->setSendgridLicenseConfigGuid();
    }

    /**
     * Set email type config guid.
     *
     * console\cake updateweb addGuidToEmailType
     */
    public function addGuidToEmailType()
    {
        // will only be executed in qa, pre and pro once only
        $updateWeb = new UpdateWeb();
        $updateWeb->setEmailTypeGuid();
    }
}

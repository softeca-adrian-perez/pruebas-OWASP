<?php

class MessagingSMS
{
    public static function enviar_sms($phones, $text, $sender, $prefix = null, $sms_code, $sms_user, $sms_password)
    {
        try {
            if (!SMS_ACTIVE) {
                return false;
            }

            if (empty($phones) || empty($text) || empty($sender)) {
                return false;
            }

            $curl = curl_init(Configure::read('SMS.url'));
            if ($curl === false) {
                return false;
            } else {
                $options = array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => MessagingSMS::obtener_xml_envio_mensaje($phones, $text, $sender, $prefix, $sms_code, $sms_user, $sms_password),
                );

                if (!curl_setopt_array($curl, $options)) {
                    return false;
                }

                $response = curl_exec($curl);
                curl_close($curl);
                return $response;
            }
        } catch (Exception $e) {
            CakeLog::debug(print_r("SMS - Send SMS - An error has occured: " . $e->getMessage(), true));
            return false;
        }
    }

    public static function obtener_xml_envio_mensaje($phones, $text, $sender, $prefix = null, $sms_code, $sms_user, $sms_password)
    {
        try {
            $xml = '<?xml version="1.0" encoding="UTF-8"?>
                <sms version="1.7.0">
                    <licencia>
                        <codigo>' . $sms_code . '</codigo>
                        <usuario>' . $sms_user . '</usuario>
                        <contrasena>' . $sms_password . '</contrasena>
                    </licencia>
                    <peticion servicio="envio-extendido">
                        <mensaje codificacion="LATIN">
                        <remitente>' . $sender . '</remitente>
                        <texto>' . htmlspecialchars($text) . '</texto>
                        <telefonos>';

            if (!is_array($phones)) {
                $phones = array(0 => $phones);
            }

            foreach ($phones as $phone) {
                if (strlen($phone) > 0) {
                    $xml .= '<telefono>
                            <numero>' . $prefix . $phone . '</numero>
                            <nombre>Rubén</nombre>
                        </telefono>';
                }
            }
            $xml .= '</telefonos>
                        </mensaje>
                    </peticion>
                </sms>';
            return $xml;
        } catch (Exception $e) {
            CakeLog::debug(print_r("SMS - Get XML sms - An error has occured: " . $e->getMessage(), true));
            return null;
        }
    }
}

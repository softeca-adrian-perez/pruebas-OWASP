<?php

class Texto
{

    public static function toFormatoBdNifCif($cif)
    {
        $str = trim($cif);
        $str = str_replace("-", "", $str);
        $str = str_replace(" ", "", $str);
        $str = strtoupper($str);
        return $str;
    }

    public static function esNifCif($cif)
    {
        $str = self::toFormatoBdNifCif($cif);
        return self::_validaNifCifNie($str) > 0;
    }

    /**
     * @returns 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF error, -2 = CIF error, -3 = NIE error, 0 = ??? error
     */
    private static function _validaNifCifNie($cif)
    {
        //si no tiene un formato valido devuelve error
        if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif)) {
            return 0;
        }
        $num = array();
        for ($i = 0; $i < 9; $i++) {
            $num[$i] = substr($cif, $i, 1);
        }

        //comprobacion de NIFs estandar
        if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $cif)) {
            if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1)) {
                return 1;
            } else {
                return -1;
            }
        }

        //algoritmo para comprobacion de codigos tipo CIF
        $suma = $num[2] + $num[4] + $num[6];
        for ($i = 1; $i < 8; $i += 2) {
            $suma += substr((2 * $num[$i]), 0, 1) + substr((2 * $num[$i]), 1, 1);
        }
        $n = 10 - substr($suma, strlen($suma) - 1, 1);

        //comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
        if (preg_match('/^[KLM]{1}/', $cif)) {
            if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 1, 8) % 23, 1)) {
                return 1;
            } else {
                return -1;
            }
        }
        //comprobacion de CIFs
        if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif)) {
            if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1)) {
                return 2;
            } else {
                return -2;
            }
        }

        //comprobacion de NIEs - T
        if (preg_match('/^[T]{1}/', $cif)) {
            if ($num[8] == preg_match('/^[T]{1}[A-Z0-9]{8}$/', $cif)) {
                return 3;
            } else {
                return -3;
            }
        }

        //comprobacion de NIEs - XYZ
        if (preg_match('/^[XYZ]{1}/', $cif)) {
            if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X', 'Y', 'Z'), array('0', '1', '2'), $cif), 0, 8) % 23, 1)) {
                return 3;
            } else {
                return -3;
            }
        }

        return 0;
    }

    public static function encryptDecryptText($text, $encrypt = false, $data = GNMAAG_DATA1, $data2 = GNMAAG_DATA2)
    {
        $encrypt_method = 'AES-256-CBC';
        $secret_key = hash('sha256', $data);
        $secret_iv = substr(hash('sha256', $data2), 0, 16);
        $string = trim(strval($text));

        return $encrypt ? openssl_encrypt($string, $encrypt_method, $secret_key, 0, $secret_iv)
            : openssl_decrypt($string, $encrypt_method, $secret_key, 0, $secret_iv);
    }

    public static function is_utf8($str)
    {
        $strlen = strlen($str);
        for ($i = 0; $i < $strlen; $i++) {
            $ord = ord($str[$i]);
            if ($ord < 0x80) continue; // 0bbbbbbb
            elseif (($ord & 0xE0) === 0xC0 && $ord > 0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
            elseif (($ord & 0xF0) === 0xE0) $n = 2; // 1110bbbb
            elseif (($ord & 0xF8) === 0xF0 && $ord < 0xF5) $n = 3; // 11110bbb (exkl F5-FF)
            else return false; // invalid UTF-8-Zeichen
            for ($c = 0; $c < $n; $c++) // $n following bytes? // 10bbbbbb
                if (++$i === $strlen || (ord($str[$i]) & 0xC0) !== 0x80)
                    return false; // invalid UTF-8 char
        }
        return true; // didn't find any invalid characters
    }

    /**
     * Decrypts the viewvar value for SendGrid emails if necessary by email type and variable name.
     */
    public static function decryptSendGridViewVarValue($emailTypeId, $viewVarName, $viewVarValue)
    {
        $emailTypeIdsDecrypt = array(
            ConstantsEmailTypes::BOOKING_CUSTOMER,
            ConstantsEmailTypes::BOOKING_GARAGE,
            ConstantsEmailTypes::BOOKING_REMINDER,
            ConstantsEmailTypes::ENQUIRY_CUSTOMER,
            ConstantsEmailTypes::ENQUIRY_GARAGE,
            ConstantsEmailTypes::ONBOARDING
        );

        $emailViewVarsDecrypt = array(
            'customer_email',
            'customer_name',
            'customer_phone',
            'gnm_password',
        );

        $emailTypeIdsDecrypt2 = array(
            ConstantsEmailTypes::ENQUIRY_CUSTOMER,
            ConstantsEmailTypes::ENQUIRY_GARAGE,
            ConstantsEmailTypes::BOOKING_DISTRIBUTOR,
            ConstantsEmailTypes::BOOKING_REMINDER
        );

        $emailViewVarsDecrypt2 = array(
            'vehicle_plate',
            'vehicle_vin',
        );

        // some variables are encrypted, so they have to be decrypted before been added
        if (
            in_array($emailTypeId, $emailTypeIdsDecrypt) && in_array($viewVarName, $emailViewVarsDecrypt) ||
            in_array($emailTypeId, $emailTypeIdsDecrypt2) && in_array($viewVarName, $emailViewVarsDecrypt2)
        ) {
            $viewVarValue = Texto::encryptDecryptText($viewVarValue, false);
        }

        return $viewVarValue;
    }
}

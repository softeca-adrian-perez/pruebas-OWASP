<?php

/**
 * Created by PhpStorm.
 * User: David
 * Date: 18/08/2015
 * Time: 18:32
 */
abstract class CodeGenerator {
    /**
     * Generates a random string using alphanumeric characters (upper and lowercase
     * and numbers).
     * @param type $length the length for the string (default 32)
     * @return string random string obtained
     */
    static function generateRandomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function createToken($id){
        return substr(md5($id.'tokengnm'), 0, 8);
    }
}
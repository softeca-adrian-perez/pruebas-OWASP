<?php

/**
 * Translates text.
 */
function __t($variable, $language_code = '')
{
    global $translations_languages;
    if ($language_code == '') {
        global $translations;
        if (isset($translations[$variable])) {
            return h($translations[$variable]);
        }
        return h($variable);
    } else {
        $translations_filename = __DIR__ . '/Translation_' . $language_code . '.php';
        if (file_exists($translations_filename)) {
            require_once $translations_filename;
            if (isset($translations_languages[$language_code][$variable])) {
                return h($translations_languages[$language_code][$variable]);
            }
            return h($variable);
        } else {
            $translations_filename = __DIR__ . '/Translation_en.php';
            require_once $translations_filename;
            if (isset($translations_languages['en'][$variable])) {
                return h($translations_languages['en'][$variable]);
            }
            return h($variable);
        }
    }
}

/**
 * Gets language code.
 */
function __l()
{
    if (CakeSession::started()) {
        $language_code = CakeSession::read('Config.language');
    }

    $validLanguageCodes = CakeSession::read('Config.valid_languages_codes');

    // Validate the language code using a whitelist
    if (!isset($language_code) || empty($language_code) || !in_array($language_code, $validLanguageCodes)) {
        $language_code = Configure::read('LANGUAGE_CODE_DEFAULT');
    }

    return $language_code;
}

/**
 * Gets language code as a suffix for database.
 */
function __s()
{
    $language_code = __l();
    if ($language_code == 'en') {
        return '_en';
    }
    return '_' . $language_code;
}

/**
 * @return string Current locale as expected by jQuery UI and compatible libraries, e.g.: "es" or "en-GB"
 */
function __get_locale_jquery_ui()
{
    $map = array(
        'en' => 'en-GB',
        'ar' => 'es',
    );
    $internal_code = __l();
    return array_key_exists($internal_code, $map) ? $map[$internal_code] : $internal_code;
}

function translateDataErrors(&$errors)
{
    foreach ($errors as &$error) {
        $error = __t(implode($error));
    }
    return $errors;
}

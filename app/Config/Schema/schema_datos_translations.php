<?php
ini_set('memory_limit', '-1');
class AppSchema extends CakeSchema
{

    public $connection = 'default';

    public function __construct($options = array())
    {
        $this->_insertarDatosIniciales();
        parent::__construct($options);
    }

    private function _insertarDatosIniciales()
    {
        $this->_translations();
        $this->_translations_js();
    }

    private function _translations(){
        App::import('Vendor', 'PHPExcel', array('file' => 'phpoffice/phpexcel/Classes/PHPExcel.php'));
        $file_tmp = "/Files/Translations.xlsx";
        $path = dirname(__FILE__) . $file_tmp;
        $reader = PHPExcel_IOFactory::load($path);
        $translations = $reader->setActiveSheetIndex(0)->toArray(null, true, true, true);

        $languages_codes = array(
            $translations[1]['C'], //en English
            $translations[1]['D'], //fr Frances
            $translations[1]['E'], //de Alemán
        );

        $files = array();
        foreach($languages_codes as $language_code){
            $files[$language_code] = fopen(ConstantsPath::DIR_TRANSLATIONS.'/Translation_'.$language_code.'.php', 'w');
            fputs($files[$language_code], '<?php'."\n".'$translations_languages[\''.$language_code.'\'] = array(');
        }
        unset($translations[1]);
        foreach($translations as $translation){
            // sample: 'User.Name' => 'Nname',

            fputs($files['en'], "\n\t".sprintf('\'%s.%s\' => \'%s\',',
                    $translation['A'],
                    $translation['B'],
                    $translation['C'] //en English
            ));

            //////////////////////////////////////////////////////////////

            if(empty($translation['D'])){
                $translationFrench = $translation['C'];
            }
            else{
                $translationFrench = $translation['D'];
            }

            fputs($files['fr'], "\n\t".sprintf('\'%s.%s\' => \'%s\',',
                    $translation['A'],
                    $translation['B'],
                    $translationFrench //fr French
            ));

            //////////////////////////////////////////////////////////////

            if(empty($translation['E'])){
                $translationGerman = $translation['C'];
            }
            else{
                $translationGerman = $translation['E'];
            }

            fputs($files['de'], "\n\t".sprintf('\'%s.%s\' => \'%s\',',
                    $translation['A'],
                    $translation['B'],
                    $translationGerman //de German
            ));

            //////////////////////////////////////////////////////////////

        }
        foreach($files as $file){
            fputs($file, "\n);");
            fclose($file);
        }
    }

    private function _translations_js(){
        $file_tmp = "/Files/Translations.xlsx";
        $path = dirname(__FILE__) . $file_tmp;
        $reader = PHPExcel_IOFactory::load($path);
        $translations = $reader->setActiveSheetIndex(0)->toArray(null, true, true, true);

        $languages_codes = array(
            $translations[1]['C'], //en English
            $translations[1]['D'], //fr Frances
            $translations[1]['E'], //de Alemán
        );
        $files = array();

        $js_translations = array(
            'en' => array(),
            'fr' => array(),
            'de' => array()
        );

        unset($translations[1]);
        foreach($translations as $translation){
            $translation = str_replace("\'", "'",$translation);
            $js_translations['en'][$translation['A'] . '.' . $translation['B']] = $translation['C'];
            $js_translations['fr'][$translation['A'] . '.' . $translation['B']] = $translation['D'];
            $js_translations['de'][$translation['A'] . '.' . $translation['B']] = $translation['E'];
        }

        foreach($languages_codes as $language_code){
            $files[$language_code] = fopen(dirname(__FILE__).'/../../'.ConstantsPath::DIR_TRANSLATIONS_JS.'/Translation_'.$language_code.'.js', 'w');
            $js_file = "$(document).ready(function () {
                i18n_" . $language_code . ".load_" . $language_code . "();
            });

            var i18n_" . $language_code . " = (function () {

                var language_" . $language_code . " = function () {
                    var myDictionary = " . json_encode($js_translations[$language_code]) . ";
                    $.i18n.load(myDictionary);
                };

                return {
                    load_" . $language_code . ": function () {
                        language_" . $language_code . "();
                    }
                }
            })();";
            fputs($files[$language_code], $js_file);
        }

    }

}
?>
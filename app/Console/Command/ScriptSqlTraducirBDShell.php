<?php
class ScriptSqlTraducirBDShell extends Shell {

    private static $mbd;

    // Establecer conexión con la BD
    private static function inicializar() {
        $host = '127.0.0.1';
        $nombre_bd = 'gnmaag';
        $usuario = 'gnmaag';
        $contrasenna = 'gnmaag';

        self::$mbd = new PDO('mysql:host=' . $host . ';dbname=' . $nombre_bd, $usuario, $contrasenna);
        self::$mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$mbd->exec('SET NAMES UTF8');
    }

    protected $idiomas = array(
        //ConstantesIdiomasPrefijo::ENGLISH,
        //ConstantesIdiomasPrefijo::FRENCH,
        ConstantesIdiomasPrefijo::GERMAN,
    );

    // Tablas donde hay que traducir textos
    protected $tables = array(
        array('config_sections','name'),
        array('config','name'),
        array('config','tooltip'),
        array('positions_config_types','name'),
        array('distributors_types','name'),
        array('logs_fields','name'),
        array('logs_tables','name'),
        array('groupings_permissions','name'),
        array('groups_permissions','name'),
        array('turnovers','name'),
        array('courtesy_car_types','name'),
        array('workshop_activities','name'),
        array('tasks_status','name'),
        array('shortcuts_types','name'),
        array('sections_subsections','name'),
        array('messages_types','name'),
        array('communications_sections','name'),
        array('associations_types','name'),
        array('distributors_activities_primary','name'),
        array('networks_contract_types','name'),
        array('services','name'),
        array('vehicle_types','name'),
        array('vehicles','name'),
        array('software_manufactures','name'),
        array('software_types','name'),
        array('software','name'),
        array('equipments','name'),
        array('equipments_types','name'),
        array('employee_types','name'),
        array('customers_activities','name'),
        array('leaving_reason_types','name'),
        array('logistic_centers','name'),
        array('sales_families','name'),
        array('appointments_types','name'),
        array('appointments_status','name'),
        array('appointments_feelings','name'),
        array('languages','name'),
        array('positions','name'),
        array('roles','name'),
        array('alerts_types','name'),
        array('suppliers_categories','name'),
        array('debrief_topics','name'),
        array('debrief_tasks','title'),
        array('debrief_tasks','description'),
    );

    /**
     * Crear CSV de todos los textos de las tablas indicadas en el array '$tables'
     */
    // console\cake ScriptSqlTraducirBD crear_csv_textos
    public function crear_csv_textos(){
        self::inicializar();
        foreach($this->idiomas as $locale){
            $datos = array();
            foreach($this->tables as $table){
                $table_name = $table[0];
                $table_field = $table[1];
                $resultados = self::$mbd->query("SELECT CONCAT('" . $table_name . ".', " . $table_name . ".id) AS asset_id, " . $table_name. "." . $table_field. "_".$locale. " AS name FROM " . $table_name)->fetchAll();
                foreach($resultados as $resultado){
                    $datos[] = array(
                        $resultado['asset_id'],
                        $resultado['name'],
                    );
                }
            }
            $this->crear_csv($datos, $locale);
        }
       
    }

    private function crear_csv($datos, $locale){
        $path = dirname(__FILE__) . '/../../tmp/logs/idiomas/';
        $file_name = $locale . '.csv';
        $fp = fopen($path . $file_name, 'w+');
        fputcsv($fp, array("Asset ID", $locale));
        foreach ($datos as $data) {
            fputcsv($fp, $data);
        }
        fclose($fp);
        echo 'File create: ' . $file_name . "\n";
    }

    /**
     * Leer los textos traducidos del .csv y actualizarlos en las tablas de BD
     */
    // console\cake ScriptSqlTraducirBD actualizar_textos_bd
    public function actualizar_textos_bd(){
        $sqls = array();
        foreach($this->idiomas as $locale){
            $traducciones_tablas = $this->leer_csv($locale);
            foreach($this->tables as $table){
                $table_name = $table[0];
                $table_field = $table[1];
                $table_field = $table_field.'_'.$locale;
                if(!empty($traducciones_tablas[$table_name])){
                    foreach($traducciones_tablas[$table_name] as $id=>$translation){
                        $translation = str_replace('"', '\"', $translation);
                        $sqls[$table_name][] = 'UPDATE ' . $table_name . ' SET ' . $table_field . '="' . $translation . '" WHERE  id = ' . $id . ';';
                    }
                }
            }
        }
        $this->crear_sql($sqls, 'script_traducciones.sql');
    }

    private function leer_csv($locale){
        $path = dirname(__FILE__) . '/../../tmp/logs/subir_idioma/';
        $file_name = $locale . '.csv';

        $fp = fopen($path . $file_name, 'r');
        $datos = array();
        $first_line = true;
        while (($data = fgetcsv($fp, 1000, ',')) !== FALSE) {
            if ($first_line) {
                $first_line = false;
                continue;
            }
            //$partes = explode(',', $data[0]);
            $text_table_and_id = $data[0];
            $text_table_and_id = explode('.', $text_table_and_id);
            $text_table = $text_table_and_id[0];
            $text_id = $text_table_and_id[1];
            $text_translation = trim(str_replace('"','',$data[1]));
            if (!isset($datos[$text_table])) {
                $datos[$text_table] = array();
            }
            $datos[$text_table][$text_id] = $text_translation;
        }
        fclose($fp);
        echo 'File read: ' . $file_name . "\n";
        return $datos;
    }

    private function crear_sql($sqls, $file_name)
    {
        $path = dirname(__FILE__) . '/../../tmp/logs/idiomas/';
        $fp = fopen($path . $file_name, 'w+');
        foreach ($sqls as $sqls_tabla) {
            foreach($sqls_tabla as $sql){
                fputs($fp, $sql.PHP_EOL);
            }
        }
        fclose($fp);
        echo 'File create: ' . $file_name . "\n";
    }

    /**
     * Crear .sql de todas las columnas necesarias para cada idioma en cada tabla del array '$tables'
     */
    // console\cake ScriptSqlTraducirBD crear_add_columns
    public function crear_add_columns(){
        $sqls = array();
        foreach($this->tables as $table){
            foreach($this->idiomas as $locale){
                $table_name = $table[0];
                $table_field = $table[1];
                $new_table_field = $table_field.'_'.$locale;
                $sqls[$table_name][] = 'ALTER TABLE ' . $table_name . ' ADD COLUMN ' . $new_table_field . ' VARCHAR(255) NULL DEFAULT NULL AFTER ' . $table_field . ';';
            }
        }
        $this->crear_sql($sqls, 'script_add_columns.sql');
    }

    //[No tiene nada que ver con el resto de cosas del fichero, fué una funcionalidad que se ejcuto una sola vez en DEMO] Ejemplo para crear matriculas aleatorias
    // console\cake ScriptSqlTraducirBD matriculasAleatorias
    /* public function matriculasAleatorias(){
        $array_letras_1 = array ('B', 'C', 'D', 'F', 'G', 'H', 'J');
        $array_letras_2 = array ('A', 'B', 'C', 'D', 'F', 'G', 'H', 'J', 'K', 'P', 'Q', 'T', 'V', 'W', 'X');
        $array_letras_3 = array ('A', 'C', 'F', 'G', 'H', 'I', 'J', 'M', 'N', 'P', 'R', 'S', 'V', 'X', 'Z');

        for($i = 1; $i < 333; $i++){
            $table_name = 'vehiculos';
            $table_field = 'matricula';

            $num_matricula = rand(2564, 9856);
            $matricula = $num_matricula . $array_letras_1[rand(0, sizeof($array_letras_1) - 1)] . $array_letras_2[rand(0, sizeof($array_letras_2) - 1)] . $array_letras_3[rand(0, sizeof($array_letras_3) - 1)];

            $sqls['vehiculos'][] = 'UPDATE ' . $table_name . ' SET ' . $table_field . '="' . $matricula . '" WHERE  id = ' . $i . ';';
        }
        for($i = 381; $i < 498; $i++){
            $table_name = 'vehiculos';
            $table_field = 'matricula';

            $num_matricula = rand(2564, 9856);
            $matricula = $num_matricula . $array_letras_1[rand(0, sizeof($array_letras_1) - 1)] . $array_letras_2[rand(0, sizeof($array_letras_2) - 1)] . $array_letras_3[rand(0, sizeof($array_letras_3) - 1)];

            $sqls['vehiculos'][] = 'UPDATE ' . $table_name . ' SET ' . $table_field . '="' . $matricula . '" WHERE  id = ' . $i . ';';
        }
        //debug($sqls);exit;
        $this->crear_sql($sqls, 'script_new_matriculas.sql');
    } */
}
?>
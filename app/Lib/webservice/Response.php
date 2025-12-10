<?php
if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;


/**
 * Respose of webservice
 *
 * @pw_element string $Status A string with result of call
 * @pw_set minoccurs=0
 * @pw_element string $StatusMessage A string with a message of call
 * @pw_set minoccurs=0
 * @pw_element OutletDataArray $OutletDataArray A string with a value
 *
 * @pw_complex Response Response of webservice
 */
class Response{

    public function Response($status=false, $status_message=false, $garage_array=false){
        $this->Status = $status;

        if($garage_array==false){
            $this->StatusMessage = $status_message;
        }else{
            $this->OutletDataArray = $garage_array;
        }
    }

}

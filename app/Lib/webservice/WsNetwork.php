<?php
if(basename($_SERVER['SCRIPT_FILENAME'])==basename(__FILE__))
    exit;

/**
 * Network item
 *
 * @pw_element string $id Network id
 * @pw_element string $name Network name
 * @pw_element string $network_type Network type
 *
 * @pw_complex WsNetwork
 */

class WsNetwork{
    public $id;
    public $name;
    public $network_type;

    public function WsNetwork($network){
        $this->id = $network['id'];
        $this->name = $network['name'];
        $this->network_type = $network['network_type'];
    }
}

<?php
class RepairsMaintenancesController extends AppController
{
    public $uses = array(
        'Alert',
        'Communication',
        'RepairMaintenance'
    );

    public function information()
    {
        if (!$this->request->is('get')) {
            $data = $this->request->input('json_decode');
            if (isset($data->Alerta)) {
                $result = $this->Alert->edit_alert_rm($data);
            } elseif (isset($data->PmCita)) {
                $result = $this->Communication->edit_comunication_rm($data);
            } else {
                $result = $this->RepairMaintenance->error();
            }
        } else {
            $result = $this->RepairMaintenance->error();
        }
        $this->returnResult($result);
    }

    private function returnResult($result)
    {
        if (is_array($result['data']) && isset($result['data']['auth_error']) && $result['data']['auth_error']) {
            header('HTTP/1.0 401 Unauthorized');
        }
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        $json = json_encode($result);
        echo isset($_GET['callback']) ? "{$_GET['callback']}($json)" : $json;
        exit;
    }
}

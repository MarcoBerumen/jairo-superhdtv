<?php
namespace Imx;
/**
 * Classe para loguear en bitacora 
 */

use mysqli;
class logger{
    public $module="generic";
    public $user = "user";
    public $module_id = 1;
    public $action ="default";
    public $history_id = "";
    function storeEvent()
    {
        $date = date('Y-m-d H:i:s');
        $module = $this->module;
        $user = $this->user;
        $module_id = $this->module_id;
        $action = $this->action;
        $history_id = $this->history_id;
        $ip_address = $_SERVER['REMOTE_ADDR'];

        db::iquery("
        INSERT INTO `bitacora` (`module`, `user`, `module_id`, `date`, `action`, `history_id`,ip_address) 
        VALUES ( '$module', '$user', '$module_id', '$date', '$action', '$history_id','$ip_address');
        ");
    }
}
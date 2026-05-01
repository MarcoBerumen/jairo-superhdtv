<?php

namespace Imx;

use Latte;

class sidebar2
{


    static public function sidebar()
    {

         $latte = new Latte\Engine;

        $parts = explode("/", $_SERVER['REQUEST_URI']);
        $profile = db::rquery("select name from profiles where profile_id ='{$_SESSION['user']['profile_id']}'");
        $params = [
            "name" => $_SESSION['user']['name'],
            "profile" => $profile,
            "menu" => $_SESSION['menu'],
            "root"=> $parts[0]
        ];
            echo  $latte->renderToString('../app/templates/html/sidebar.latte', $params);

    }
}

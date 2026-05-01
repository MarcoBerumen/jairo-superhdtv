<?php

namespace Imx;

use Latte;

class sidebar
{


    static public function sidebar()
    {

        // $latte = new Latte\Engine;

        // $params = [
        //     "mame" => $_SESSION['user']['name'],
        //     "menu" => $_SESSION['menu']
        // ];
        // echo  $latte->renderToString('../app/templates/html/begincontent.latte');

        // return "";
        // print_r($_SESSION);
        $items = $_SESSION['menu'];
        $_SESSION['user']['puesto'] = $_SESSION['user']['puesto'] ?? "";
        $menui = "";
        $parts = explode("/", $_SERVER['REQUEST_URI']);

        if ($parts[0] == "")
            $parts = array_splice($parts, 1);


        foreach ($items as $item) {
            if($item['enabled'] == false ) continue;

            if ($item['sub']) {
                $active =   "";
                if ($item['name'] == $parts[0])
                    $active = "active";

                $menui .= "<li class=\"has-sub $active\">
        <a href=\"{$item['href']}\">
            <b class=\"caret\"></b>
            <i class=\"fa fa-{$item['icon']}\"></i>
            <span>{$item['module']}</span>
        </a>
        <ul class=\"sub-menu\">";
                foreach ($item['items'] as $sub) {
                    if($sub['enabled'] == false ) continue;
                    $active = "";
                    if ($sub['sub'] ?? "" != "") {
                        if ($sub['name']  == $parts[1] ?? "" && $parts[1] ?? "" != "") {

                            $active = "active";
                        }
                        $menui .= "       
                        <li class=\"has-sub $active\"><a href=\"{$sub['href']}\"> {$sub['module']}</a>
                        <ul class=\"sub-menu\">";
                        foreach ($sub['items'] ?? [] as $subsub) {
                            $active = "";
                            if (strpos($_SERVER['REQUEST_URI'], $subsub['href']) !== false) $active = "class=\"active\"";
                            $menui .= "       
                     <li {$active}><a href=\"{$subsub['href']}\">{$subsub['module']}</a></li>";
                        }

                        $menui .= "</ul></li> ";
                    } else {
                        if (strpos($_SERVER['REQUEST_URI'], $sub['href']) !== false) $active = "class=\"active\"";
                        $menui .= "       
                 <li {$active}><a href=\"{$sub['href']}\">{$sub['module']}</a></li>
    ";
                    }
                }
                $menui .= "
        </ul>
        </li>
        ";
            } else {
                $active =   "";
                if ($item['name'] == $parts[0])
                    $active = "active";

                $menui .= "<li $active>
        <a href=\"{$item['href']}\">
            <i class=\"fa fa-{$item['icon']}\"></i>
            <span>{$item['module']}</span>
        </a>
        </li>
        ";
            }
        }


        $menu = "
<!-- begin #sidebar -->
<div id=\"sidebar\" class=\"sidebar\">
    <!-- begin sidebar scrollbar -->
    <div data-scrollbar=\"true\" data-height=\"100%\">
        <!-- begin sidebar user -->
        <ul class=\"nav\">
            <li class=\"nav-profile\">

                <div class=\"cover with-shadow\"></div>

                <div class=\"info\">
                    {$_SESSION['user']['nombre']}
                    <small>{$_SESSION['user']['puesto']}</small>
                </div>

            </li>
        </ul>
        <!-- end sidebar user -->
        <!-- begin sidebar nav -->
        <ul class=\"nav\">
            <li class=\"nav-header\">Options Menu</li>
            $menui
            <!-- begin sidebar minify button -->
            <li><a href=\"javascript:;\" class=\"sidebar-minify-btn\" data-click=\"sidebar-minify\"><i class=\"fa
                        fa-angle-double-left\"></i></a></li>
            <!-- end sidebar minify button -->
        </ul>
        <!-- end sidebar nav -->
    </div>
    <!-- end sidebar scrollbar -->
</div>
<div class=\"sidebar-bg\"></div>
<!-- end #sidebar -->";
        echo $menu;
        return "";
    }
}

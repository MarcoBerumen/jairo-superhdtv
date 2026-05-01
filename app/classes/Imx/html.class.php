<?php

namespace Imx;

use Latte;

class html extends sidebar
{

    static public    function head($title, $styles = [])
    {

        $params = [
            'app' => APP_NAME,
            'name' => $title,

        ];
        $latte = new Latte\Engine;

        // echo  $latte->renderToString('../app/templates/html/head.latte', $params);
        // return "";

        $template =  "
        
<!DOCTYPE html>
<html lang=\"en\">

<head>
    <meta charset=\"utf-8\" />
    <title> " . APP_NAME . " | {$title}</title>
    <meta content=\"width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no\" name=\"viewport\" />
    <meta content=\"\" name=\"description\" />
    <meta content=\"\" name=\"author\" />

    <!-- ================== BEGIN BASE CSS STYLE ================== -->
    <link href=\"https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700\" rel=\"stylesheet\" />
    <link href=\"/assets/css/default/app.min.css\" rel=\"stylesheet\" />
    <link href=\"/assets/css/vendor.css\" rel=\"stylesheet\" />
    <!-- ================== END BASE CSS STYLE ================== -->

    <!-- ================== BEGIN PAGE LEVEL STYLE ================== -->
    <link href=\"/assets/plugins/jvectormap-next/jquery-jvectormap.css\" rel=\"stylesheet\" />
    <link href=\"/assets/plugins/bootstrap-datepicker/dist/css/bootstrap-datepicker.css\" rel=\"stylesheet\" />
    <link href=\"/assets/plugins/gritter/css/jquery.gritter.css\" rel=\"stylesheet\" />
    <!-- ================== END PAGE LEVEL STYLE ================== -->
	<link href=\"/assets/plugins/blueimp-gallery/css/blueimp-gallery.min.css\" rel=\"stylesheet\" />
	<link href=\"/assets/plugins/blueimp-file-upload/css/jquery.fileupload.css\" rel=\"stylesheet\" />
	<link href=\"/assets/plugins/blueimp-file-upload/css/jquery.fileupload-ui.css\" rel=\"stylesheet\" />
	<link href=\"/assets/plugins/select2/dist/css/select2.min.css\" rel=\"stylesheet\" />
	<link href=\"/assets/plugins/lightbox2/dist/css/lightbox.css\" rel=\"stylesheet\" />


    <link rel=\"apple-touch-icon\" sizes=\"57x57\" href=\"/assets/favicon/apple-icon-57x57.png\">
<link rel=\"apple-touch-icon\" sizes=\"60x60\" href=\"/assets/favicon/apple-icon-60x60.png\">
<link rel=\"apple-touch-icon\" sizes=\"72x72\" href=\"/assets/favicon/apple-icon-72x72.png\">
<link rel=\"apple-touch-icon\" sizes=\"76x76\" href=\"/assets/favicon/apple-icon-76x76.png\">
<link rel=\"apple-touch-icon\" sizes=\"114x114\" href=\"/assets/favicon/apple-icon-114x114.png\">
<link rel=\"apple-touch-icon\" sizes=\"120x120\" href=\"/assets/favicon/apple-icon-120x120.png\">
<link rel=\"apple-touch-icon\" sizes=\"144x144\" href=\"/assets/favicon/apple-icon-144x144.png\">
<link rel=\"apple-touch-icon\" sizes=\"152x152\" href=\"/assets/favicon/apple-icon-152x152.png\">
<link rel=\"apple-touch-icon\" sizes=\"180x180\" href=\"/assets/favicon/apple-icon-180x180.png\">
<link rel=\"icon\" type=\"image/png\" sizes=\"192x192\"  href=\"/assets/favicon/android-icon-192x192.png\">
<link rel=\"icon\" type=\"image/png\" sizes=\"32x32\" href=\"/assets/favicon/favicon-32x32.png\">
<link rel=\"icon\" type=\"image/png\" sizes=\"96x96\" href=\"/assets/favicon/favicon-96x96.png\">
<link rel=\"icon\" type=\"image/png\" sizes=\"16x16\" href=\"/assets/favicon/favicon-16x16.png\">
<link rel=\"manifest\" href=\"/assets/favicon/manifest.json\">
<meta name=\"msapplication-TileColor\" content=\"#ffffff\">
<meta name=\"msapplication-TileImage\" content=\"/assets/favicon/ms-icon-144x144.png\">
<meta name=\"theme-color\" content=\"#ffffff\">
<script src=\"/assets/plugins/vue/vue.js\"></script>
<!-- Taginput -->
<link href=\"/assets/plugins/taginput/tagsinput.css\" rel=\"stylesheet\" type=\"text/css\">


<!-- FLATPICKR -->
<link href=\"/assets/plugins/flatpickr/flatpickr.min.css\" rel=\"stylesheet\" />
<script src=\"/assets/plugins/flatpickr/flatpickr.min.js\"></script>
";
        foreach ($styles as $style) {
            $template .= "<link href=\"$style\" rel=\"stylesheet\" />";
        }
        $template .= "
<!-- Datatables -->
<link href=\"/assets/plugins/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css\" rel=\"stylesheet\" />
<style>
#loader {
    position: fixed;
    margin: 0;
    padding: 0;
    left: 0;
    top: 0;
    bottom: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.6);
    display:none;
    z-index:100000;
    overflow: hidden; /* or auto or scroll */
  }
  	#gritter-notice-wrapper {
    	        z-index:9999999999 !important;
    	}
  </style>
</head>";
        echo $template;
        return "";
    }
    static public function bodyInit()
    {
        $latte = new Latte\Engine;

        // echo  $latte->renderToString('../app/templates/html/body.latte');
        // return "";

        $body = "<body>
        <div id=\"loader\" class=\"app-loader\">
		<span class=\"spinner\"></span>
        
	</div>
    <!-- begin #page-loader -->
    <div id=\"page-loader\" class=\"fade show\">
        <span class=\"spinner\"></span>
    </div>
    <!-- end #page-loader -->

    <!-- begin #page-container -->
    <div id=\"page-container\" class=\"fade page-sidebar-fixed page-header-fixed\">
    <!-- begin #header -->

    ";
        echo $body;
        return "";
    }

    static public function header($items)
    {
        $latte = new Latte\Engine;

        $params = [
            "name" => $_SESSION['user']['name'],
            "menu" => $_SESSION['menu']
        ];
        // echo  $latte->renderToString('../app/templates/html/header.latte', $params);
        // return "";

        $usuario = $_SESSION['user']['id'];

        $notificaciones = db::dataQueryMultiple("select * from notificaciones
        where usuario = '$usuario' and estatus = 1");
        $notificaciones =  count($notificaciones) ?? 0;

        $header = "        <div id=\"header\" class=\"header navbar-default\">
    <!-- begin navbar-header -->
    <div class=\"navbar-header\">
        <a href=\"index.html\" class=\"navbar-brand\"><span class=\"navbar-logo\"></span> <b> " . APP_NAME . " </b>  &nbsp; " . APP_VERSION . " </a>
        <button type=\"button\" class=\"navbar-toggle\" data-click=\"sidebar-toggled\">
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
            <span class=\"icon-bar\"></span>
        </button>
    </div>
    <!-- end navbar-header -->
    <!-- begin header-nav -->
    <ul class=\"navbar-nav navbar-right\">

        <li class=\"dropdown\">
            <a href=\"#\" data-toggle=\"dropdown\" class=\"dropdown-toggle f-s-14\">
                <i class=\"fa fa-bell\"></i>
                <span class=\"label\">$notificaciones</span>
            </a>
            <div class=\"dropdown-menu media-list dropdown-menu-right\">
                <div class=\"dropdown-header\">$notificaciones Notificaciones</div>
        
                <div class=\"dropdown-footer text-center\">
                    <a href=\"/notificaciones\">Ver</a>
                </div>
            </div>
        </li>
        <li class=\"dropdown navbar-user\">
            <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">
                <span class=\"d-none d-md-inline\">{$_SESSION['user']['nombre']}</span> <b class=\"caret\"></b>
            </a>
            <div class=\"dropdown-menu dropdown-menu-right\">
                <a href=\"javascript:;\" class=\"dropdown-item\"><span
                        class=\"badge badge-danger pull-right\">2</span> Buzon</a>
                <a href=\"javascript:;\" class=\"dropdown-item\">Setting</a>
                <div class=\"dropdown-divider\"></div>
                <a href=\"/logout\" class=\"dropdown-item\">Salir</a>
            </div>
        </li>
    </ul>
    <!-- end header-nav -->
</div>
<!-- end #header -->
";
        // print_r($_SESSION);
        // exit;
        echo $header;
        return "";
    }
    static public function containerEnd()
    {
        $latte = new Latte\Engine;

        // echo  $latte->renderToString('../app/templates/html/containerend.latte');
        // return "";

        $html = "    <!-- begin scroll to top btn -->
    <a href=\"javascript:;\" class=\"btn btn-icon btn-circle btn-success btn-scroll-to-top fade\"
        data-click=\"scroll-top\"><i class=\"fa fa-angle-up\"></i></a>
    <!-- end scroll to top btn -->
</div>
<!-- end page container -->
";
        echo $html;
        return "";
    }
    static public function scripts($charts = false, $extra = "")
    {

        $latte = new Latte\Engine;

        // echo  $latte->renderToString('../app/templates/html/scripts.latte');
        // return "";

        $date = date('ymdhisu');
        $scripts = "   
    <!-- ================== BEGIN BASE JS ================== -->
    <script src=\"/assets/js/app.min.js\"></script>
    <script src=\"/imx-assets/imx-core.js?expire=$date\"></script>
    <script src=\"/assets/plugins/axios/axios.min.js\"></script>
    <script src=\"/imx-assets/utils.js\"></script>
	<!-- ================== BEGIN PAGE LEVEL JS ================== -->
	<script src=\"/assets/plugins/gritter/js/jquery.gritter.js\"></script>
	<script src=\"/assets/plugins/sweetalert/dist/sweetalert.min.js\"></script>
	<!-- ================== THEME SETTINGS ================== -->
    <script src=\"/assets/js/theme/default.min.js\"></script>
    <!-- ================== SELECT2 ================== -->
	<script src=\"/assets/plugins/select2/dist/js/select2.min.js\"></script>
    <!-- ================== DATEPICKER VECTOR  ================== -->
    <script src=\"/assets/plugins/jquery-sparkline/jquery.sparkline.min.js\"></script>
    <script src=\"/assets/plugins/jvectormap-next/jquery-jvectormap.min.js\"></script>
    <script src=\"/assets/plugins/jvectormap-next/jquery-jvectormap-world-mill.js\"></script>
    <script src=\"/assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js\"></script>
    <!-- ================== DATATABLES ================== -->
    <script src=\"/assets/plugins/datatables.net/js/jquery.dataTables.min.js\"></script>
    <script src=\"/assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js\"></script>
    <script src=\"/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js\"></script>
    <script src=\"/assets/plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js\"></script>
    <script src=\"/assets/plugins/datatables.net-fixedheader-bs4/js/fixedHeader.bootstrap4.min.js\"></script>
    <script src=\"/assets/plugins/parsleyjs/dist/parsley.min.js\" type=\"text/javascript\"></script>
    <script src=\"/assets/plugins/highlight.js/highlight.min.js\" type=\"text/javascript\"></script>
	<script src=\"/assets/plugins/isotope-layout/dist/isotope.pkgd.min.js\"></script>
	<script src=\"/assets/plugins/lightbox2/dist/js/lightbox.min.js\"></script>
    <script src=\"/assets/plugins/taginput/tagsinput.js\"></script>

   
";
        $locale = $_ENV['APP_LOCALE'] ?? "es";
        if ($locale == "es") {
            $scripts .= "    <script src=\"/assets/plugins/parsleyjs/dist/i18n/es.js\" type=\"text/javascript\"></script>";
        }
        if ($charts) {
            $scripts .= "
    <script src=\"/assets/plugins/gritter/js/jquery.gritter.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.canvaswrapper.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.colorhelpers.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.saturated.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.browser.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.drawSeries.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.uiConstants.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.time.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.resize.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.pie.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.crosshair.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.categories.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.navigate.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.touchNavigate.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.hover.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.touch.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.selection.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.symbol.js\"></script>
    <script src=\"/assets/plugins/flot/source/jquery.flot.legend.js\"></script>

    ";
        }
        $scripts .= $extra;
        echo $scripts;
        return "";
    }
    static public function bodyEnd()
    {
        $latte = new Latte\Engine;

        // echo  $latte->renderToString('../app/templates/html/bodyend.latte');
        // return "";

        $return = "
<script>
window.ParsleyValidator
    .addValidator('minSelect', function(value, requirement) {
        return value.split(',').length >= parseInt(requirement, 10);
    }, 32)
    .addMessage('en', 'minSelect', 'You must select at least %s.');

    </script>
</body>

</html>
";
        echo $return;
        return "";
    }


    static public function beginContent($breadcrumbs = [])
    {
        $latte = new Latte\Engine;

        // echo  $latte->renderToString('../app/templates/html/begincontent.latte');
        // return "";
        $content = "<!-- begin #content -->
    <div id=\"content\" class=\"content\">
  
";
        $items = "";
        if (count($breadcrumbs ?? []) > 0) {
            foreach ($breadcrumbs as $b) {
                $b['link'] = $b['link'] ?? "javascript:;";
                $active = "";
                if ($b['text'] == $breadcrumbs[count($breadcrumbs) - 1]['text']) {
                    $active = "active";
                }
                $items .= "<li class=\"breadcrumb-item {$active}\"><a href=\"{$b['link']}\">{$b['text']}</a></li>";
            }
            $content .= "
    <!-- begin breadcrumb -->
    <dic class=\"row\">
    <ol class=\"breadcrumb float-xl-left\">
        <li class=\"breadcrumb-item\"><a href=\"/\">Home</a></li>
        $items
    </ol>
    </dic>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <!-- end page-header -->
    ";
        }
        echo $content;

        return "";
    }
    static public function endContent()
    {
        $html =  "    </div>
    <!-- end theme-panel -->
";
        echo $html;
        return "";
    }

    static public function themePanel()
    {

        $html = `       <!-- begin theme-panel -->
<div class="theme-panel theme-panel-lg">
    <a href="javascript:;" data-click="theme-panel-expand" class="theme-collapse-btn"><i
            class="fa fa-cog"></i></a>
    <div class="theme-panel-content">
        <h5>App Settings</h5>
        <ul class="theme-list clearfix">
            <li><a href="javascript:;" class="bg-red" data-theme="red"
                    data-theme-file="/assets/css/default/theme/red.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body" data-title="Red">&nbsp;</a>
            </li>
            <li><a href="javascript:;" class="bg-pink" data-theme="pink"
                    data-theme-file="/assets/css/default/theme/pink.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Pink">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-orange" data-theme="orange"
                    data-theme-file="/assets/css/default/theme/orange.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Orange">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-yellow" data-theme="yellow"
                    data-theme-file="/assets/css/default/theme/yellow.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Yellow">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-lime" data-theme="lime"
                    data-theme-file="/assets/css/default/theme/lime.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Lime">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-green" data-theme="green"
                    data-theme-file="/assets/css/default/theme/green.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Green">&nbsp;</a></li>
            <li class="active"><a href="javascript:;" class="bg-teal" data-theme="default" data-theme-file=""
                    data-click="theme-selector" data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Default">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-aqua" data-theme="aqua"
                    data-theme-file="/assets/css/default/theme/aqua.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Aqua">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-blue" data-theme="blue"
                    data-theme-file="/assets/css/default/theme/blue.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Blue">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-purple" data-theme="purple"
                    data-theme-file="/assets/css/default/theme/purple.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Purple">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-indigo" data-theme="indigo"
                    data-theme-file="/assets/css/default/theme/indigo.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Indigo">&nbsp;</a></li>
            <li><a href="javascript:;" class="bg-black" data-theme="black"
                    data-theme-file="/assets/css/default/theme/black.min.css" data-click="theme-selector"
                    data-toggle="tooltip" data-trigger="hover" data-container="body"
                    data-title="Black">&nbsp;</a></li>
        </ul>
        <div class="divider"></div>
        <div class="row m-t-10">
            <div class="col-6 control-label text-inverse f-w-600">Header Fixed</div>
            <div class="col-6 d-flex">
                <div class="custom-control custom-switch ml-auto">
                    <input type="checkbox" class="custom-control-input" name="header-fixed" id="headerFixed"
                        value="1" checked />
                    <label class="custom-control-label" for="headerFixed">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="row m-t-10">
            <div class="col-6 control-label text-inverse f-w-600">Header Inverse</div>
            <div class="col-6 d-flex">
                <div class="custom-control custom-switch ml-auto">
                    <input type="checkbox" class="custom-control-input" name="header-inverse" id="headerInverse"
                        value="1" />
                    <label class="custom-control-label" for="headerInverse">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="row m-t-10">
            <div class="col-6 control-label text-inverse f-w-600">Sidebar Fixed</div>
            <div class="col-6 d-flex">
                <div class="custom-control custom-switch ml-auto">
                    <input type="checkbox" class="custom-control-input" name="sidebar-fixed" id="sidebarFixed"
                        value="1" checked />
                    <label class="custom-control-label" for="sidebarFixed">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="row m-t-10">
            <div class="col-6 control-label text-inverse f-w-600">Sidebar Grid</div>
            <div class="col-6 d-flex">
                <div class="custom-control custom-switch ml-auto">
                    <input type="checkbox" class="custom-control-input" name="sidebar-grid" id="sidebarGrid"
                        value="1" />
                    <label class="custom-control-label" for="sidebarGrid">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="row m-t-10">
            <div class="col-md-6 control-label text-inverse f-w-600">Sidebar Gradient</div>
            <div class="col-md-6 d-flex">
                <div class="custom-control custom-switch ml-auto">
                    <input type="checkbox" class="custom-control-input" name="sidebar-gradient"
                        id="sidebarGradient" value="1" />
                    <label class="custom-control-label" for="sidebarGradient">&nbsp;</label>
                </div>
            </div>
        </div>
        <div class="divider"></div>
        <h5>Admin Design (5)</h5>
        <div class="theme-version">
            <a href="/template_html/index_v2.html" class="active">
                <span style="background-image: url(/assets/img/theme/default.jpg);"></span>
            </a>
            <a href="/template_transparent/index_v2.html">
                <span style="background-image: url(/assets/img/theme/transparent.jpg);"></span>
            </a>
        </div>
        <div class="theme-version">
            <a href="/template_apple/index_v2.html">
                <span style="background-image: url(/assets/img/theme/apple.jpg);"></span>
            </a>
            <a href="/template_material/index_v2.html">
                <span style="background-image: url(/assets/img/theme/material.jpg);"></span>
            </a>
        </div>
        <div class="theme-version">
            <a href="/template_facebook/index_v2.html">
                <span style="background-image: url(/assets/img/theme/facebook.jpg);"></span>
            </a>
            <a href="/template_google/index_v2.html">
                <span style="background-image: url(/assets/img/theme/google.jpg);"></span>
            </a>
        </div>
        <div class="divider"></div>
        <h5>Language Version (7)</h5>
        <div class="theme-version">
            <a href="/template_html/index.html" class="active">
                <span style="background-image: url(/assets/img/version/html.jpg);"></span>
            </a>
            <a href="/template_ajax/index.html">
                <span style="background-image: url(/assets/img/version/ajax.jpg);"></span>
            </a>
        </div>
        <div class="theme-version">
            <a href="/template_angularjs/index.html">
                <span style="background-image: url(/assets/img/version/angular1x.jpg);"></span>
            </a>
            <a href="/template_angularjs10/index.html">
                <span style="background-image: url(/assets/img/version/angular10x.jpg);"></span>
            </a>
        </div>
        <div class="theme-version">
            <a href="javascript:alert('Laravel Version only available in downloaded version.');">
                <span style="background-image: url(/assets/img/version/laravel.jpg);"></span>
            </a>
            <a href="/template_vuejs/index.html">
                <span style="background-image: url(/assets/img/version/vuejs.jpg);"></span>
            </a>
        </div>
        <div class="theme-version">
            <a href="/template_reactjs/index.html">
                <span style="background-image: url(/assets/img/version/reactjs.jpg);"></span>
            </a>
            <a href="javascript:alert('.NET Core 3.1 MVC Version only available in downloaded version.');">
                <span style="background-image: url(/assets/img/version/dotnet.jpg);"></span>
            </a>
        </div>
        <div class="divider"></div>
        <h5>Frontend Design (4)</h5>
        <div class="theme-version">
            <a href="/frontend/template/template_one_page_parallax/index.html">
                <span style="background-image: url(/assets/img/theme/one-page-parallax.jpg);"></span>
            </a>
            <a href="/frontend/template/template_e_commerce/index.html">
                <span style="background-image: url(/assets/img/theme/e-commerce.jpg);"></span>
            </a>
        </div>
        <div class="theme-version">
            <a href="/frontend/template/template_blog/index.html">
                <span style="background-image: url(/assets/img/theme/blog.jpg);"></span>
            </a>
            <a href="/frontend/template/template_forum/index.html">
                <span style="background-image: url(/assets/img/theme/forum.jpg);"></span>
            </a>
        </div>
        <div class="divider"></div>
        <div class="row m-t-10">
            <div class="col-md-12">
                <a href="https://seantheme.com/color-admin/documentation/"
                    class="btn btn-inverse btn-block btn-rounded" target="_blank"><b>Documentation</b></a>
                <a href="javascript:;" class="btn btn-default btn-block btn-rounded"
                    data-click="reset-local-storage"><b>Reset Local Storage</b></a>
            </div>
        </div>
    </div>
</div>
<!-- end theme-panel -->
`;
        echo $html;
        return "";
    }
}

<?php

use Imx\html2 as html;

html::head("proyectos");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent([
    ['text' => "Catálogos"],
    ['text' => "proyectos", "link" => "/catalogos/proyectos"]
]);


$latte = new Latte\Engine;

// $latte->setTempDirectory('templates/temp');

$params = [
    'name' => "proyectosTable",
    'rows' => [
        ['text' => 'ID'],
        ['text' => 'Nombre'],
        ['text' => 'Acciones', 'attrs' => 'data-orderable="false"']
    ]

];

$tabla = $latte->renderToString('../app/templates/pager.latte', $params);
$tabla = $tabla . " <button class=\"btn btn-primary\" onclick=\"window.location.href='/catalogos/proyectos/nuevo'\"><i class='fa fa-plus'></i>&nbsp;Agregar </button>";


echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "proyectos", "body" => $tabla]);


?>

<script>
var handleDataTableDefault = function() {

    "use strict";

    if ($('#proyectosTable').length !== 0) {
        $('#proyectosTable').DataTable({
            "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.4/i18n/es_es.json"
        },
            responsive: true,
            processing: true,
            serverSide: true,
            "order": [
                [1, "desc"]
            ],

            ajax: '/api/catalogos/proyectos',
            "columnDefs": [{
                "targets": -1,
                "data": null,
                "render": function(data, type, full, meta) {
                    return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a href="/catalogos/proyectos/` + data[0] + `" class="dropdown-item">Editar</a>
            								</div>
            							</div>
                            `
                }
            }],
        });
    }
};

var TableManageDefault = function() {

    "use strict";
    return {
        //main function
        init: function() {
            handleDataTableDefault();
        }
    };
}();
</script>
<?php
$scripts = "
<script src=\"/assets2/plugins/datatables.net/js/jquery.dataTables.min.js\"></script>
<script src=\"/assets2/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js\"></script>
<script src=\"/assets2/plugins/datatables.net-responsive/js/dataTables.responsive.min.js\"></script>
<script src=\"/assets2/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js\"></script>

<script>

$(document).ready(function() {
	TableManageDefault.init();
});
</script>";
html::endContent();
html::containerEnd();
html::scripts(false, $scripts);
html::bodyEnd();
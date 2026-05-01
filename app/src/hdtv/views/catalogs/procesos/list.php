<?php

use Imx\html;

html::head("Procesos");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent([
    ['text' => "Catálogos"],
    ['text' => "procesos", "link" => "/catalogos/procesos"]
]);


$latte = new Latte\Engine;

// $latte->setTempDirectory('templates/temp');

$params = [
    'name' => "procesosTable",
    'rows' => [
        ['text' => 'ID'],
        ['text' => 'Nombre'],
        ['text' => 'Acciones', 'attrs' => 'data-orderable="false"']
    ]

];

$tabla = $latte->renderToString('../app/templates/pager.latte', $params);
$tabla = $tabla . " <button class=\"btn btn-primary\" onclick=\"window.location.href='/catalogos/procesos/nuevo'\">Nuevo Proceso</button>";


echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "procesos", "body" => $tabla]);


?>

<script>
var handleDataTableDefault = function() {

    "use strict";

    if ($('#procesosTable').length !== 0) {
        $('#procesosTable').DataTable({
            "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.4/i18n/es_es.json"
        },
            responsive: true,
            processing: true,
            serverSide: true,
            "order": [
                [1, "desc"]
            ],

            ajax: '/api/catalogos/procesos',
            "columnDefs": [{
                "targets": -1,
                "data": null,
                "render": function(data, type, full, meta) {
                    return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a href="/catalogos/procesos/` + data[0] + `" class="dropdown-item">Editar</a>
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
<script src=\"/assets/plugins/datatables.net/js/jquery.dataTables.min.js\"></script>
<script src=\"/assets/plugins/datatables.net-bs4/js/dataTables.bootstrap4.min.js\"></script>
<script src=\"/assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js\"></script>
<script src=\"/assets/plugins/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js\"></script>

<script>

$(document).ready(function() {
	TableManageDefault.init();
});
</script>";
html::endContent();
html::containerEnd();
html::scripts(false, $scripts);
html::bodyEnd();
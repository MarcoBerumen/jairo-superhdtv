<?php

use Imx\html2 as html;

html::head("Warranties");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Warranties", "link" => "/catalogs/warranties"]
]);


$latte = new Latte\Engine;

// $latte->setTempDirectory('templates/temp');

$params = [
    'name' => "warrantiesTable",
    'rows' => [
        ['text' => 'ID'],
        ['text' => 'Name'],
        ['text' => 'Days'],
        ['text' => 'Price'],
        ['text' => 'Under Price'],

        ['text' => 'Actions', 'attrs' => 'data-orderable="false"']
    ]

];

$tabla = $latte->renderToString('../app/templates/pager.latte', $params);
$tabla = $tabla . " <button class=\"btn btn-primary\" onclick=\"window.location.href='/catalogs/warranties/new'\"><i class='fa fa-plus'></i>&nbsp;Add </button>";

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Warranties", "body" => $tabla]);


?>

<script>
    var handleDataTableDefault = function() {

        "use strict";

        if ($('#warrantiesTable').length !== 0) {
            $('#warrantiesTable').DataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                "order": [
                    [1, "desc"]
                ],

                ajax: '/api/catalogs/warranties',
                "columnDefs": [{
                    "targets": -1,
                    "data": null,
                    "render": function(data, type, full, meta) {
                        return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a href="/catalogs/warranties/` + data[0] + `" class="dropdown-item">Edit</a>
            									<a href="/catalogs/warranties/` + data[0] + `/delete" class="dropdown-item">Delete</a>
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

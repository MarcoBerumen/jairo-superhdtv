<?php

use Imx\html2 as html;

html::head("Claims");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Claims", "link" => "/back-office/claims"]
]);


$latte = new Latte\Engine;

// $latte->setTempDirectory('templates/temp');

$params = [
    'name' => "claims",
    'rows' => [
        ['text' => 'ID'],
        ['text' => 'Date'],
        ['text' => 'Store'],
        ['text' => 'Product'],
        ['text' => 'Item'],
        ['text' => 'Customer'],
        ['text' => 'Status'],
        ['text' => 'Actions', 'attrs' => 'data-orderable="false"']
    ]
];

$tabla = $latte->renderToString('../app/templates/pager.latte', $params);
$tabla = $tabla . " <button class=\"btn btn-primary\" onclick=\"window.location.href='/back-office/claims/new'\"><i class='fa fa-plus'></i>&nbsp;Add </button>";

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Claims", "body" => $tabla]);
?>

<script>
    var handleDataTableDefault = function() {

        "use strict";

        if ($('#claims').length !== 0) {
            $('#claims').DataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                "order": [
                    [1, "desc"]
                ],

                ajax: '/api/back-office/claims',
                "columnDefs": [{
                    "targets": -1,
                    "data": null,
                    "render": function(data, type, full, meta) {
                        switch(data[6]){
                            case "Received":
                                return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a target='_blank' href="/back-office/claims/` + data[0] + `/print" class="dropdown-item">Print</a>
            									<a href="/back-office/claims/` + data[0] + `" class="dropdown-item">Edit</a>
            									<a href="/back-office/claims/` + data[0] + `/status/sent" class="dropdown-item">Sent</a>
            									<a href="/back-office/claims/` + data[0] + `/status/Return to Inventory" class="dropdown-item">Return to inventory</a>
            								</div>
            							</div>
                            `
                                break;
                            case "Sent":
                                return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a target='_blank' href="/back-office/claims/` + data[0] + `/print" class="dropdown-item">Print</a>
            									<a href="/back-office/claims/` + data[0] + `/status/Credit" class="dropdown-item">Credit</a>
            									<a href="/back-office/claims/` + data[0] + `/status/Reject" class="dropdown-item">Reject</a>
            								</div>
            							</div>
                            `
                                break;
                            default:
                                return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a target='_blank' href="/back-office/claims/` + data[0] + `/print" class="dropdown-item">Print</a>
            								</div>
            							</div>
                            `
                        }

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

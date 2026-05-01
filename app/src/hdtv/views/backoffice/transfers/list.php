<?php

use Imx\html2 as html;

html::head("Transfers");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent([
    ['text' => "Back Office"],
    ['text' => "Transfers", "link" => "/back-office/transfers"]
]);


$latte = new Latte\Engine;

// $latte->setTempDirectory('templates/temp');

$params = [
    'name' => "transfers",
    'rows' => [
        ['text' => 'ID'],
        ['text' => 'Origin'],
        ['text' => 'Destination'],
        ['text' => 'Value'],
        ['text' => 'User'],
        ['text' => 'Status'],
        ['text' => 'Actions', 'attrs' => 'data-orderable="false"']
    ]

];

$tabla = $latte->renderToString('../app/templates/pager.latte', $params);
$tabla = $tabla . " <button class=\"btn btn-primary\" onclick=\"window.location.href='/back-office/transfers/new'\"><i class='fa fa-plus'></i>&nbsp;Add </button>";

echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Inventory Transfers", "body" => $tabla]);


?>

<script>
    var handleDataTableDefault = function() {

        "use strict";

        if ($('#transfers').length !== 0) {
            $('#transfers').DataTable({

                responsive: true,
                processing: true,
                serverSide: true,
                "order": [
                    [1, "desc"]
                ],

                ajax: '/api/back-office/transfers',
                "columnDefs": [{
                    "targets": -1,
                    "data": null,
                    "render": function(data, type, full, meta) {
                        if(data[5] =='Pending')
                        {
                            return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a href="/back-office/transfers/` + data[0] + `" class="dropdown-item">Edit</a>
            									<a href="/back-office/transfers/` + data[0] + `/status/sent" class="dropdown-item" title="Mark items as transfer and change store to the destination">Send Transfer</a>
            								</div>
            							</div>
                            `
                        }
                        else if(data[5] === 'Sent')
                        {
                            return `
                            <div class="btn-group m-r-5 m-b-5">

            								<a href="#" data-bs-toggle="dropdown" class="btn btn-default dropdown-toggle"><b class="caret"></b></a>
            								<div class="dropdown-menu dropdown-menu-right">
            									<a href="/back-office/transfers/` + data[0] + `/status/received" class="dropdown-item" title="Mark items as available on destination store">Received</a>
            								</div>
            							</div>
                            `
                        }
                        else
                        {
                            return `

                            `                        }

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

<?php

use Imx\db;
use Imx\html;

html::head("Procesos -- procesos Ver");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $data = [];
    $form = "Nuevo";
} else {

    $data = Imx\db::dataQuery("select * from procesos where id ='$id'");
    $form = "Editar";
}

html::beginContent([
    ['text' => "Catálogos"],
    ['text' => "procesos", "link" => "/catalogos/procesos"],
    ['text' => $form, "link" => "/catalogos/procesos/$id"],
]);


$latte = new Latte\Engine;


$options = [
    [
        "text" => "No",
        "value" => 0
    ],
    [
        "text" => "Si",
        "value" => 1
    ]

];
if ($id == "new") {
}
$tipo = $data['tipo'];



$params = [
    'title' => "Generales",
    'name' => "proveedor_generales",
    'cols' => "6",
    'fields' => [
        ['name' => 'nombre', 'value' => $data['nombre'] ?? "", 'label' => 'Nombre',  'requiredx' => 'true'],

    ]
];
// }

$form =  $latte->renderToString('../app/templates/form.latte', $params);

// }


$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href="/catalogos/procesos" value="Regresar">
<input type="button" class="btn btn-success" onclick="window.location.href=\'/catalogos/procesos/' . $id . '/export\'" value="Exportar">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogos/procesos/' . $id . '\')" value="Guardar">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "Procesos", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogos/procesos';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();

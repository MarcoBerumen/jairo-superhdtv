<?php

use Imx\db;
use Imx\html;

html::head("proyectos -- proyectos Ver");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $data = [];
    $form = "Nuevo";
} else {

    $data = Imx\db::dataQuery("select * from proyectos where id ='$id'");
    $form = "Editar";
}

html::beginContent([
    ['text' => "Catálogos"],
    ['text' => "proyectos", "link" => "/catalogos/proyectos"],
    ['text' => $form, "link" => "/catalogos/proyectos/$id"],
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


$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href="/catalogos/proyectos" value="Regresar">
<input type="button" class="btn btn-primary" onclick="Imx.validaForm(\'/api/catalogos/proyectos/' . $id . '\')" value="Guardar">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "proyectos", "body" => $form]);
?>

<script>
    function callbackForm(result) {
        window.location.href = '/catalogos/proyectos';
    }
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false);
html::bodyEnd();

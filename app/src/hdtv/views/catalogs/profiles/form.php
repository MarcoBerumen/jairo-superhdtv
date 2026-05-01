<?php

use Imx\db;
use Imx\html;

html::head("Profiles ");
html::bodyInit();
html::header("");
html::sidebar();
if ($id == "new") {
    $formdata = [];
    $formTitle = "New";
} else {

    $formdata = db::dataQuery("select * from profiles where profile_id ='$id'");
    $formdata['password'] = Imx\utils::decrypt($formdata['password']);
    $formTitle = "Edit";
}
html::beginContent([
    ['text' => "Catalogs"],
    ['text' => "Profiles", "link" => "/catalogs/profiles"],
    ['text' => $formTitle, "link" => "/catalogs/profiles/$id"],
]);


$latte = new Latte\Engine;

$options = [
        [
                'text'=>"No",
                'value' =>"0"
        ],        [
                'text'=>"Yes",
                'value' =>"1"
        ], [
                'text'=>"Limited",
                'value' =>"2"
        ],
];
$options2 = [
     [
                'text'=>"User",
                'value' =>"1"
        ],
        [
                'text'=>"Admin",
                'value' =>"3"
        ],
];

$params = [
    'title' => "profile",
    'name' => "verprofile",
    'cols' => "6",
    'fields' => [
        ['name' => 'name', 'value' => $formdata['name'] ?? "", 'label' => 'Name', 'required' => 'true'],
        ['name' => 'bo','cols' =>6,'value' => $formdata['bo'] ?? "", 'label' => 'Back Office Role', 'required' => 'true','type' =>'select','data'=> $options2],
//        ['name' => 'sales','cols' =>3,'value' => $formdata['sales'] ?? "", 'label' => 'Sales', 'required' => 'true','type' =>'select','data'=> $options],
//        ['name' => 'catalogs','cols' =>3,'value' => $formdata['catalogs'] ?? "", 'label' => 'Catalogs', 'required' => 'true','type' =>'select','data'=> $options],
//        ['name' => 'reports','cols' =>3,'value' => $formdata['reports'] ?? "", 'label' => 'Reports', 'required' => 'true','type' =>'select','data'=> $options],
    ]
];

$form =  $latte->renderToString('../app/templates/form.latte', $params);

$form = $form . '<input type="button" class="btn btn-danger" onclick="window.location.href=\'/catalogs/profiles\'" value="Back">
<input type="button" class="btn btn-primary" onclick="validateform();" value="Save">';
echo  $latte->renderToString('../app/templates/panel.latte', ["title" => "$formTitle Profile", "body" => $form]);
$permissions = json_decode($formdata['permissions'],true);
?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <div id="jstree">
        <!-- in this example the tree is populated from inline HTML -->
        <ul>
            <?php

            foreach($_SESSION['menu'] as $m){
                $sub = "";
                if($m['sub']){
                    $sub = "<ul>";
                    foreach($m['items'] as $s) {
                        $selected="false";
                        if(isset($permissions[$m['name']])){
                            $wp = $permissions[$m['name']];
                        if(isset($wp[$s['name']]))
                            $selected = "true";
                        }

                        $sub.= "<li data-jstree='{ \"selected\":{$selected} ,\"icon\" : \"fa fa-chart\"}'>{$s['name']}</li>";
                    }
                    $sub.= "</ul>";
                }
                $selected="false";
                if(isset($permissions[$m['name']]) && $sub == "")
                    $selected = "true";
                echo "<li data-jstree='{\"selected\":{$selected} }'>{$m['name']} $sub</li>";
            }
            ?>

        </ul>
    </div>

<script>
    function validateform(){
        var permissions = t.jstree(true).get_json();
        Imx.validaForm('/api/catalogs/profiles/<?php echo  $id;?>',true,[],permissions);
    }
    function callbackForm(result) {
        window.location.href = '/catalogs/profiles';
    }
    var t;
    document.addEventListener("DOMContentLoaded", function() {

        $(document).ready(function() {
        $(function () {
            // 6 create an instance when the DOM is ready
            window.t = $('#jstree').jstree({'plugins':["wholerow","checkbox"]});
            // 7 bind to events triggered on the tree
            $('#jstree').on("changed.jstree", function (e, data) {
                console.log(data.selected);
            });
        });
    });
    });
</script>
<?php

html::endContent();
html::containerEnd();
html::scripts(false,"<script src=\"https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js\"></script>");
html::bodyEnd();

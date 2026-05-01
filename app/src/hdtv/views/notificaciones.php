<?php
use Imx\html;
html::head("Dashboard");
html::bodyInit();
html::header("");
html::sidebar();
html::beginContent();

$latte = new Latte\Engine;
// $latte->setTempDirectory('templates/temp');
$usuario = $_SESSION['user']['id'];
$notificaciones = Imx\db::dataQueryMultiple("select * from notificaciones
where usuario = '$usuario' and estatus = 1");
$notificaciones = 
[
    'notificaciones' => count($notificaciones),
    'data'=>$notificaciones
];
$todo = "";
$i = 0;
foreach($notificaciones['data']as $t){
    $i++;
    $f = <<< EOT
    <div class="widget-todolist-body">

<div class="widget-todolist-item">
<div class="widget-todolist-input">
<div class="form-check">
<input class="form-check-input" type="checkbox" value='{$t['id']}' id="widget_todolist_$i" />
</div>
</div>
<div class="widget-todolist-content">
<label for="widget_todolist_$i">
<h6 class="mb-2px">
{$t['titulo']}

</h6>
<div class="text-gray-500 fw-bold fs-11px">Fecha : {$t['fecha']}</div>
</label>
</div>
<div class="widget-todolist-icon">
<a href="#"><i class="fa fa-flag"></i></a>
</div>
</div>
EOT;
$todo.=$f;
}
$html = <<< EOT
<div class="col-xl-12 col-lg-12">

<div class="mb-10px mt-10px fs-10px">
<b class="text-inverse"><span>{$notificaciones['notificaciones']}</span> NOTIFICACIONES</b>
</div>
<div class="widget-todolist rounded mb-4" data-id="widget">




$todo





<div class="widget-todolist-item">

</div>

</div>

</div>

</div>
<button class="btn btn-success" onclick='validarn()'>
Confirmar notificaciones Seleccionadas
</button>
<script>
function validarn(){
    var notificaciones = $.map($(':checkbox:checked'), function(n, i){
        return n.value;
  }).join(',');
  if(notificaciones)
  {
      if(confirm('Deseas confirmar las notificaciones seleccionadas?'))
      {
          window.location.href='/api/notifications/clear?id='+notificaciones;
      }
  }
  else
  {
      alert('Selecciona por lo menos una notificacion a confirmar');
  }

}
</script>
EOT;
$data =
['title'=>"Notificaciones",
"body"=>$html];
echo 
$latte->renderToString('../app/templates/panel.latte', $data);
html::endContent();
html::containerEnd();
html::scripts();
html::bodyEnd();
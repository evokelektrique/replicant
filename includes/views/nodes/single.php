<?php 
$item = \Replicant\Tables\Nodes\Functions::get( $id ); 
?>

<div class="wrap">
   <ul>
      <li>ID: <b><?= $item->id ?></b></li>
      <li>NAME: <b><?= $item->name ?></b></li>
      <li>HOST NAME: <b><?= $item->host ?></b></li>
      <li>PORT: <b><?= $item->port ?></b></li>
   </ul>
</div>
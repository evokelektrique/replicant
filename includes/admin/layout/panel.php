<div class="wrap">
   <h3>Settings</h3>
   <hr>

<?php

$default = new Replicant\Database\Defaults();
var_dump($default::authorization());

?>

   <form id="panel_settings">
      <input type="text" name="replicant_key" id="replicant_key" placeholder="<?= __( "Authorization Key", "replicant" );?>" />
      <br>
      <br>
      <button class="button-primary"><?= __("Submit", "replicant") ?></button>
   </form>
</div>
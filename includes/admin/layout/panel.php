<?php global $replicant; ?>

<div class="wrap">
   <h3>Settings</h3>
   <hr>
   
   <span replicant-tab="one"class="replicant-nav-tab active">
      one
   </span>

   <span replicant-tab="two"class="replicant-nav-tab">
      two
   </span>

   <span replicant-tab="three"class="replicant-nav-tab">
      three
   </span>


   <div id="one" class="replicant-tab active">
     tab one content
   </div>

   <div id="two" class="replicant-tab">
     tab two content
   </div>

   <div id="three" class="replicant-tab">
     tab three content
   </div>



   <form id="panel_settings">
      <label>Authorization Key:</label>

      <input type="text" 
         name="replicant_key" 
         id="replicant_key"
         value="<?= $replicant::$default::authorization()->value ?>"
         placeholder="<?= __( "Authorization Key", "replicant" );?>" 
      />

      <br>
      <br>
      <button class="button-primary"><?= __("Submit", "replicant") ?></button>
   </form>
</div>
<?php global $replicant; ?>

<div class="wrap">
   <h3><?= __( "Settings", "replicant" ); ?></h3>
   <hr>
   
   <span replicant-tab="one"class="replicant-nav-tab active">
      <?= __( "Authorization", "replicant" ); ?>
   </span>

   <form id="panel_settings">
      <div class="left_side">
         <div id="one" class="replicant-tab active">
            <label for="replicant_authoriazation">
               <?= __( "Authorization Key:", "replicant" ); ?>
            </label>
            <input type="text" 
               class="replicant_input"
               name="replicant_authoriazation" 
               id="replicant_authoriazation"
               value="<?= $replicant::$default::authorization()->value ?>"
               placeholder="<?= __( "Authorization Key", "replicant" );?>" 
               readonly=""
            />
         </div>
      </div>
      <div class="right_side">
         <button class="button-primary"><?= __("Submit", "replicant") ?></button>
      </div>
   </form>
</div>
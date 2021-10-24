<?php global $replicant; ?>

<div class="wrap">
   <h3><?= __( "Settings", "replicant" ); ?></h3>
   <hr>

   <span replicant-tab="settings" class="replicant-nav-tab active">
      <?= __( "Settings", "replicant" ); ?>
   </span>

   <form id="panel_settings">
      <div class="left_side">
         <div id="settings" class="replicant-tab active">
            <label for="replicant_authoriazation">
               <?= __( "Authorization Key:", "replicant" ); ?>
            </label>
            <input
               type="text"
               class="replicant_input"
               name="replicant_authoriazation"
               id="replicant_authoriazation"
               value="<?= $replicant::$default_db::authorization()->value ?>"
               placeholder="<?= __( "Authorization Key", "replicant" );?>"
               readonly=""
            />

            <br>
            <br>

            <label for="replicant_acting_as">
               <?= __( "Acting as:", "replicant" ); ?>
            </label>
            <input
               type="text"
               class="replicant_input"
               name="replicant_acting_as"
               id="replicant_acting_as"
               value="<?= $replicant::$default_db::acting_as()->value ?>"
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

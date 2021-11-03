<?php global $replicant; ?>

<div class="wrap">
   <h3><?php esc_html_e( "Settings", "replicant" ); ?></h3>
   <hr>

   <span replicant-tab="settings" class="replicant-nav-tab active">
      <?php esc_html_e( "General", "replicant" ); ?>
   </span>

   <form id="panel_settings">
      <div class="left_side">
         <div id="settings" class="replicant-tab active">
            <div class="replicant-form-row">
               <label for="replicant_authoriazation">
                  <?php esc_html_e( "Authorization Key:", "replicant" ); ?>
               </label>
               <input
                  type="text"
                  class="replicant_input"
                  name="replicant_authoriazation"
                  id="replicant_authoriazation"
                  value="<?php echo esc_html($replicant::$default_db::authorization()->value) ?>"
                  placeholder="<?php esc_html_e( "Authorization Key", "replicant" );?>"
                  readonly=""
               />
            </div>

            <div class="replicant-form-row">
               <label for="replicant_acting_as">
                  <?php esc_html_e( "Acting as:", "replicant" ); ?>
               </label>
               <input
                  type="text"
                  class="replicant_input"
                  name="replicant_acting_as"
                  id="replicant_acting_as"
                  value="<?php echo esc_html($replicant::$default_db::acting_as()->value) ?>"
                  placeholder="<?php esc_html_e( "Authorization Key", "replicant" );?>"
                  readonly=""
               />
            </div>
         </div>
      </div>
      <div class="right_side">
         <button class="button-primary"><?php esc_html_e("Submit", "replicant") ?></button>
      </div>
   </form>
</div>

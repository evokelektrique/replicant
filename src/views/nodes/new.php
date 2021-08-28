<div class="wrap">
    <h1><?php _e( 'Add new node', 'replicant' ); ?></h1>

    <form action="" method="post">

        <table class="form-table">
            <tbody>
<!--                 <tr class="row-name">
                    <th scope="row">
                        <label for="name"><?php _e( 'Node Name', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="name" id="name" class="regular-text" placeholder="<?php echo esc_attr( '', 'replicant' ); ?>" value="" required="required" />
                        <span class="description"><?php _e('i.e: Shopping Website', 'replicant' ); ?></span>
                    </td>
                </tr>
 -->                <tr class="row-host">
                    <th scope="row">
                        <label for="host"><?php _e( 'Address', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="host" id="host" class="regular-text" placeholder="<?php echo esc_attr( '', 'replicant' ); ?>" value="" required="required" />
                        <span class="description"><?php _e('i.e: wordpress.org', 'replicant' ); ?></span>
                    </td>
                </tr>
                <tr class="row-ssl">
                    <th scope="row">
                        <label for="ssl"><?php _e( 'SSL', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="ssl" id="ssl" />
                    </td>
                </tr>
                <tr class="row-port">
                    <th scope="row">
                        <label for="port"><?php _e( 'Port', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="port" id="port" class="regular-text" placeholder="<?php echo esc_attr( '', 'replicant' ); ?>" value="80" required="required" />
                        <span class="description"><?php _e('i.e: 80', 'replicant' ); ?></span>
                    </td>
                </tr>
             </tbody>
        </table>

        <input type="hidden" name="field_id" value="0">

        <?php wp_nonce_field( '' ); ?>
        <?php submit_button( __( 'Submit new node', 'replicant' ), 'primary', 'submit_node' ); ?>

    </form>
</div>
<div class="wrap">
    <h1><?php _e( 'Edit node', 'replicant' ); ?></h1>

    <?php $item = \Replicant\Tables\Nodes\Functions::get( $id ); ?>

    <form action="" method="post">

        <table class="form-table">
            <tbody>
                <tr class="row-name">
                    <th scope="row">
                        <label for="name"><?php _e( 'Node Name', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="name" id="name" class="regular-text" placeholder="<?php echo esc_attr( '', 'replicant' ); ?>" value="<?php echo esc_attr( $item->name ); ?>" required="required" />
                        <span class="description"><?php _e('i.e: Shopping Website', 'replicant' ); ?></span>
                    </td>
                </tr>
                <tr class="row-host">
                    <th scope="row">
                        <label for="host"><?php _e( 'Address', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="text" name="host" id="host" class="regular-text" placeholder="<?php echo esc_attr( '', 'replicant' ); ?>" value="<?php echo esc_attr( $item->host ); ?>" required="required" />
                        <span class="description"><?php _e('i.e: wordpress.org', 'replicant' ); ?></span>
                    </td>
                </tr>
                <tr class="row-ssl">
                    <th scope="row">
                        <label for="ssl"><?php _e( 'SSL', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="checkbox" name="ssl" id="ssl" <?php echo $item->ssl ? "checked" : "" ?> />
                    </td>
                </tr>
                <tr class="row-port">
                    <th scope="row">
                        <label for="port"><?php _e( 'Port', 'replicant' ); ?></label>
                    </th>
                    <td>
                        <input type="number" name="port" id="port" class="regular-text" placeholder="<?php echo esc_attr( '', 'replicant' ); ?>" value="<?php echo esc_attr( intval($item->port) ) ?>" required="required"  />
                        <span class="description"><?php _e('i.e: 80', 'replicant' ); ?></span>
                    </td>
                </tr>
             </tbody>
        </table>

        <input type="hidden" name="field_id" value="<?php echo $item->id; ?>">

        <?php wp_nonce_field( '' ); ?>
        <?php submit_button( __( 'Update node', 'replicant' ), 'primary', 'submit_node' ); ?>

    </form>
</div>

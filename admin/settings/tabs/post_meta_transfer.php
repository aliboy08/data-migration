<?php
$action = isset( $_GET['action'] ) ? $_GET['action'] : false;
$action_complete = isset( $_GET['action_complete'] ) ? $_GET['action_complete'] : false;

function pmt_update(){
    if( !isset($_GET['type'] ) ) return;
    if( $_GET['type'] == 'transfer_to_another_post' ) {
        pmt_update_transfer();
    } else {
        pmt_update_change();
    }
}

function pmt_update_change(){
    if( !isset( $_POST['meta_key_from'] ) || ! $_POST['meta_key_from'] ) return;
    if( !isset( $_POST['meta_key_to'] ) || ! $_POST['meta_key_to'] ) return;
    if( ! $_POST['select_post_type'] && ! $_POST['specific_ids'] ) return;

    $post_type = isset($_POST['select_post_type']) ? $_POST['select_post_type'] : false;
    $post_ids = isset($_POST['specific_ids']) && $_POST['specific_ids'] ? explode( ',', sanitize_text_field($_POST['specific_ids']) ) : false;

    $method = $_POST['method'];
    
    if( $post_ids ) {
        $q = $post_ids;
    } else {
        $args = [
            'post_type' => $post_type,
            'showposts' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
        ];
        $q = get_posts( $args );
    }

    $meta_key_from = sanitize_text_field($_POST['meta_key_from']);
    $meta_key_to = sanitize_text_field($_POST['meta_key_to']);
    
    foreach( $q as $post_id ) {
        $meta_from = get_post_meta( $post_id, $meta_key_from, true );
        $meta_to = get_post_meta( $post_id, $meta_key_to, true );
        if( $meta_from != $meta_to ) {
            update_post_meta( $post_id, $meta_key_to, $meta_from );
            pre_debug( 'UPDATED - '. get_the_title( $post_id ) . ' | '. $post_id );
            if( $method == 'transfer' ) {
                delete_post_meta( $post_id, $meta_key_from );
            }
        }
    }
}

function pmt_update_transfer(){
    if( !isset( $_POST['post_id_from'] ) || ! $_POST['post_id_from'] ) return;
    if( !isset( $_POST['post_id_to'] ) || ! $_POST['post_id_to'] ) return;
    if( !isset( $_POST['meta_key_from'] ) || ! $_POST['meta_key_from'] ) return;
    if( !isset( $_POST['meta_key_to'] ) || ! $_POST['meta_key_to'] ) return;

    $post_id_from = sanitize_text_field($_POST['post_id_from']);
    $post_id_to = sanitize_text_field($_POST['post_id_to']);
    $meta_key_from = sanitize_text_field($_POST['meta_key_from']);
    $meta_key_to = sanitize_text_field($_POST['meta_key_to']);

    $meta_from = get_post_meta( $post_id_from, $meta_key_from, true );
    $meta_to = get_post_meta( $post_id_to, $meta_key_to, true );
    if( $meta_from != $meta_to ) {
        update_post_meta( $post_id_to, $meta_key_to, $meta_from );
        pre_debug( 'COPIED DATA TO - '. get_the_title( $post_id_to ) . ' | '. $post_id_to );
    }
}

function pmt_form(){
    if( !isset( $_GET['type'] ) ) return;
?>
<form action="" method="POST">
    <table class="form-table">
        <tbody>

            <?php if( $_GET['type'] == 'transfer_to_another_post' ) : ?>
            
                <tr>
                    <th>Post ID - FROM</th>
                    <td>
                        <?php $val = ( isset( $_POST['post_id_from'] ) ) ? sanitize_text_field($_POST['post_id_from']) : ''; ?>
                        <input type="text" name="post_id_from" value="<?php echo $val; ?>">
                    </td>
                </tr>

                <tr>
                    <th>Post ID - TO</th>
                    <td>
                        <?php $val = ( isset( $_POST['post_id_to'] ) ) ? sanitize_text_field($_POST['post_id_to']) : ''; ?>
                        <input type="text" name="post_id_to" value="<?php echo $val; ?>">
                    </td>
                </tr>

            <?php endif; // change_meta_key  ?>

            <?php if( $_GET['type'] == 'change_meta_key' ) : ?>

                <tr>
                    <th>Method</th>
                    <td>
                        <select name="method">
                            <option value="copy">Copy</option>
                            <option value="transfer">Transfer (Old post meta deleted after transfer)</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th>Select Post Type</th>
                    <td>
                        <select name="select_post_type">
                            <option value="">---</option>
                            <?php
                            $post_types = get_post_types();
                            foreach( $post_types as $post_type ) {
                                echo '<option value="'. $post_type .'">'. $post_type .'</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th>Enter specific IDs (comma separated for multiple)</th>
                    <td>
                        <?php $val = ( isset( $_POST['specific_ids'] ) ) ? sanitize_text_field($_POST['specific_ids']) : ''; ?>
                        <input type="text" name="specific_ids" value="<?php echo $val; ?>">
                    </td>
                </tr>
            <?php endif; // transfer_to_another_post ?>
            
            <tr>
                <th>Meta key - FROM</th>
                <td>
                    <?php $val = ( isset( $_POST['meta_key_from'] ) ) ? sanitize_text_field($_POST['meta_key_from']) : ''; ?>
                    <input type="text" name="meta_key_from" value="<?php echo $val; ?>">
                </td>
            </tr>

            <tr>
                <th>Meta key - TO</th>
                <td>
                    <?php $val = ( isset( $_POST['meta_key_to'] ) ) ? sanitize_text_field($_POST['meta_key_to']) : ''; ?>
                    <input type="text" name="meta_key_to" value="<?php echo $val; ?>">
                </td>
            </tr>
            
        </tbody>
    </table>
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Submit"></p>
</form>
<?php
}
function pmt_select_type(){
    if( isset( $_GET['type'] ) ) return;
    echo '<a href="'. $_SERVER['REQUEST_URI'] .'&type=change_meta_key" class="button button-primary" style="margin-right:20px;">Change post meta key</a>';
    echo '<a href="'. $_SERVER['REQUEST_URI'] .'&type=transfer_to_another_post" class="button button-primary">Transfer post meta to another post</a>';
}

pmt_select_type();
pmt_update();
pmt_form();
?>
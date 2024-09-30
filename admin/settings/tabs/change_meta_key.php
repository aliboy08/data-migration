<?php
namespace FF_Plugin\Data_Migration\Change_Meta;

submit();

function submit(){
    if( !isset($_POST['meta_key_to']) ) return;

    $meta_key_from = sanitize_text_field($_POST['meta_key_from']);
    $meta_key_to = sanitize_text_field($_POST['meta_key_to']);

    if( $meta_key_from == $meta_key_to ) return;

    $post_type = $_POST['select_post_type'] ?? null;
    $post_ids = $_POST['specific_ids'] ?? null;
    if( $post_ids ) {
        $post_ids = explode( ',', sanitize_text_field($post_ids) );
    }
    
    if( !$post_ids ) {
        $q = new WP_Query([
            'post_type' => $post_type,
            'showposts' => -1,
            'fields' => 'ids',
            'no_found_rows' => true,
        ]);
        $post_ids = $q->posts;
    }

    $method = $_POST['method'];

    foreach( $post_ids as $post_id ) {
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
?>
<form action="" method="POST">

    <p>Change post meta key to a different meta key</p>

    <table class="form-table">
        <tbody>
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
            <tr>
                <th>Meta key - FROM</th>
                <td>
                    <?php $val = ( isset( $_POST['meta_key_from'] ) ) ? sanitize_text_field($_POST['meta_key_from']) : ''; ?>
                    <input type="text" name="meta_key_from" value="<?php echo $val; ?>" required>
                </td>
            </tr>
            <tr>
                <th>Meta key - TO</th>
                <td>
                    <?php $val = ( isset( $_POST['meta_key_to'] ) ) ? sanitize_text_field($_POST['meta_key_to']) : ''; ?>
                    <input type="text" name="meta_key_to" value="<?php echo $val; ?>" required>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit"><input type="submit" id="submit" class="button button-primary" value="Submit"></p>
</form>
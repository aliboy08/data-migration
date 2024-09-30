<?php
namespace FF_Plugin\Data_Migration\Transfer_Meta;

submit();

function submit(){
    if( !isset($_POST['meta_key_from']) ) return;

    $meta_key_from = sanitize_text_field($_POST['meta_key_from']);
    $meta_key_to = sanitize_text_field($_POST['meta_key_to']);

    $post_id_from = sanitize_text_field($_POST['post_id_from']); 
    $post_id_to = sanitize_text_field($_POST['post_id_to']); 

    $meta_from = get_post_meta( $post_id_from, $meta_key_from, true );
    $meta_to = get_post_meta( $post_id_to, $meta_key_to, true );
    if( $meta_from != $meta_to ) {
        update_post_meta( $post_id_to, $meta_key_to, $meta_from );
        pre_debug( 'COPIED DATA TO - '. get_the_title( $post_id_to ) . ' | '. $post_id_to );
    }
}
?>
<form action="" method="POST">

    <p>Transfer post meta to a different post</p>

    <table class="form-table">
        <tbody>
            <tr>
                <th>Post ID - FROM</th>
                <td>
                    <?php $val = ( isset( $_POST['post_id_from'] ) ) ? sanitize_text_field($_POST['post_id_from']) : ''; ?>
                    <input type="text" name="post_id_from" value="<?php echo $val; ?>" required>
                </td>
            </tr>
            <tr>
                <th>Post ID - TO</th>
                <td>
                    <?php $val = ( isset( $_POST['post_id_to'] ) ) ? sanitize_text_field($_POST['post_id_to']) : ''; ?>
                    <input type="text" name="post_id_to" value="<?php echo $val; ?>" required>
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
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Submit"></p>
</form>
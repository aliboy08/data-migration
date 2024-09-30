<?php
namespace FF_Plugin\Data_Migration\Change_Post_Type;

submit();

function submit(){
    if( !isset($_POST['post_type_to']) ) return;

    $change_from = $_POST['post_type_from'];
    $change_to = $_POST['post_type_to'];

    if( !$change_from || !$change_to || $change_from == $change_to ) return;

    $q = new \WP_Query([
        'post_type' => $change_from,
        'showposts' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    if( !$q->posts ) return;

    foreach( $q->posts as $id ) {
        wp_update_post([
            'ID' => $id,
            'post_type' => $change_to,
        ]);
    }

    pre_debug('post type changed from '. $change_from . ' to '. $change_to);
}

$post_types = get_post_types();
?>
<form action="" method="POST">
    <table class="form-table">
        <tbody>
            <tr>
                <th>Select Post Type to change</th>
                <td>
                    <select name="post_type_from" required>
                        <option value="">---</option>
                        <?php
                        foreach( $post_types as $post_type ) {
                            echo '<option value="'. $post_type .'">'. $post_type .'</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Change to</th>
                <td>
                    <select name="post_type_to" required>
                        <option value="">---</option>
                        <?php
                        foreach( $post_types as $post_type ) {
                            echo '<option value="'. $post_type .'">'. $post_type .'</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </tbody>
    </table>
    <p><input type="submit" id="submit" class="button button-primary" value="Submit"></p>
</form>

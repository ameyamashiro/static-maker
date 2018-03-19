<?php
namespace Static_Maker;

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://developer.wordpress.org/
 * @since      1.0.0
 *
 * @package    Static_Maker
 * @subpackage Static_Maker/admin/partials
 */

if ( $_GET[ 'paged' ]) {
    $current_page_num = intval( $_GET[ 'paged' ] );
} else {
    $current_page_num = 1;
}
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <hr class="wp-header-end">

    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <td>Post ID</td>
                <td>url</td>
                <td>created</td>
                <td>type</td>
                <td>status</td>
            </tr>
        </thead>
        <tbody>
        <?php foreach( Queue::get_queues( array( 'desc' => true, 'output' => 'original' ) ) as $queue ): ?>
            <tr>
                <th><?php echo $queue->post_id ?></th>
                <td>
                    <a href="<?php echo $queue->url ?>" target="_blank"><?php echo rawurldecode( $queue->url ) ?></a>
                </td>
                <td><?php echo $queue->created ?></td>
                <td><?php echo $queue->type ?></td>
                <td><?php echo $queue->status ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <div class="bottom-actions">
        <button class="process-all button button-primary">Process All Queues</button>
    </div>

</div>

<?php
    add_action( 'admin_footer', 'Static_Maker\static_maker_javascript' );

    function static_maker_javascript() { ?>
    <script>
        jQuery('.enqueue-all-pages').on('click', function(e) {
            e.preventDefault();

            var url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'enqueue_all_pages') ?>';

            jQuery.ajax({
                type: 'post',
                url: url,
                data: {
                    action: 'static-maker-enqueue_all_pages'
                },
                success: function(res) {
                    console.log(res);
                }
            })
        });
    </script>
<?php }

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

    <div class="tablenav top">
        <div class="tablenav-pages">
            <?php $each = 25; ?>
            <?php $count = Queue::get_queue_count() ?>
            <span class="displaying-num"><?php echo $count ?> items</span>
            <span class="pagination-links">
                <?php if ( $current_page_num > 1 ): ?>
                    <a class="prev-page" href="<?php echo strtok( $_SERVER['REQUEST_URI'], '?' ) . '?' . http_build_query( array( 'page' => $_GET['page'], 'paged' => $current_page_num - 1 )) ?>">
                        <span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span>
                    </a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <?php endif ?>

                <span class="paging-input">
                    <label for="current-page-selector" class="screen-reader-text">
                        Current Page
                    </label>
                    <?php echo $current_page_num ?><span class="tablenav-paging-text"> of <span class="total-pages"><?php echo ceil( $count / $each ) ?></span></span>
                </span>

                <?php if ( $current_page_num === intval(ceil( $count / $each ))): ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <?php else: ?>
                    <a class="next-page" href="<?php echo strtok( $_SERVER['REQUEST_URI'], '?' ) . '?' . http_build_query( array( 'page' => $_GET['page'], 'paged' => $current_page_num + 1 )) ?>">
                        <span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span>
                    </a>
                <?php endif ?>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <td><?php _e( 'Post ID', PLUGIN_NAME ) ?></td>
                <td><?php _e( 'URL', PLUGIN_NAME ) ?></td>
                <td><?php _e( 'Created', PLUGIN_NAME ) ?></td>
                <td><?php _e( 'Type', PLUGIN_NAME ) ?></td>
                <td><?php _e( 'Status', PLUGIN_NAME ) ?></td>
            </tr>
        </thead>
        <tbody>
        <?php foreach( Queue::get_queues( array( 'desc' => true, 'output' => 'original', 'paged' => $current_page_num ) ) as $queue ): ?>
            <tr>
                <th><?php echo $queue->post_id ?></th>
                <td>
                    <a href="<?php echo $queue->url ?>" target="_blank"><?php echo rawurldecode( $queue->url ) ?></a>
                </td>
                <td><?php echo $queue->created ?></td>
                <td><?php echo $queue->type ?></td>
                <td><?php switch( $queue->status ) {
                        case 'waiting':
                            _e( 'waiting', PLUGIN_NAME );
                            break;
                        case 'processing':
                            _e( 'processing', PLUGIN_NAME );
                            break;
                        case 'failed':
                            _e( 'failed', PLUGIN_NAME );
                            break;
                        case 'skipped (unknown)':
                            _e( 'skipped (unknown)', PLUGIN_NAME );
                            break;
                        case 'completed':
                            _e( 'completed', PLUGIN_NAME );
                            break;
                        default:
                            _e( 'N/A', PLUGIN_NAME );
                            break;
                    } ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <div class="bottom-actions">
        <button class="process-all button button-primary"><?php _e( 'Process queues', PLUGIN_NAME ) ?></button>
    </div>

</div>

<?php
    add_action( 'admin_footer', 'Static_Maker\static_maker_javascript' );

    function static_maker_javascript() { ?>
    <script>
        jQuery('.process-all').on('click', function(e) {
            e.preventDefault();

            var url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'process_queue_all') ?>';

            jQuery.ajax({
                type: 'post',
                url: url,
                data: {
                    action: 'static-maker-process_queue_all'
                },
                success: function(res) {
                    alert('<?php echo _e('Process Completed', PLUGIN_NAME) ?>');
                }
            });
        });
    </script>
<?php }

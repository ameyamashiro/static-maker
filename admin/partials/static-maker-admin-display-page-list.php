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

    <div class="tablenav top">
        <div class="tablenav-pages">
            <?php $each = 25; ?>
            <?php $count = Page::get_page_count() ?>
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
            <td>Post ID</td>
            <td>Permalink</td>
            <td>Status</td>
        </thead>
        <tbody>
        <?php foreach( Page::get_pages( $current_page_num ) as $page ): ?>
            <tr>
                <th><?php echo $page->post_id ?></th>
                <td>
                    <a href="<?php echo $page->permalink ?>" target="_blank"><?php echo rawurldecode( $page->permalink ) ?></a>
                    <div class="row-actions">
                        <span class="export-individual">
                            <a
                                href=""
                                class="trigger-add-individual"
                                <?php if ($page->post_id): ?>
                                data-post-id="<?php echo $page->post_id ?>"
                                <?php else: ?>
                                data-id="<?php echo $page->id ?>"
                                <?php endif; ?>
                            >
                                Export Request
                            </a>
                            |
                        </span>
                        <span class="trash">
                            <a
                                href=""
                                class="trigger-remove-individual"
                                <?php if ($page->post_id): ?>
                                data-post-id="<?php echo $page->post_id ?>"
                                <?php else: ?>
                                data-id="<?php echo $page->id ?>"
                                <?php endif; ?>
                            >
                                Delete Request
                            </a>
                            |
                        </span>
                        <span class="trash">
                            <a
                                href=""
                                class="trigger-remove-from-list"
                                data-id="<?php echo $page->id ?>"
                            >
                                Delete from List
                            </a>
                        </span>
                    </div>
                </td>
                <td>
                    <?php if ( $page->active === '1' ): ?>
                    Active
                    <div class="row-actions">
                        <span class="trash">
                            <a
                                href=""
                                class="trigger-change-page-status"
                                data-action="disable"
                                data-id="<?php echo $page->id ?>"
                            >
                                Disable
                            </a>
                        </span>
                    </div>
                    <?php else: ?>
                    Disabled
                    <div class="row-actions">
                        <span>
                            <a
                                href=""
                                class="trigger-change-page-status"
                                data-action="activate"
                                data-id="<?php echo $page->id ?>"
                            >
                                Activate
                            </a>
                        </span>
                    </div>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <div class="bottom-actions">
        <button class="enqueue-all-pages button button-primary">Process all pages</button>
    </div>

</div>

<?php
    add_action( 'admin_footer', 'Static_Maker\static_maker_javascript' );

    function static_maker_javascript() { ?>
    <script>
        jQuery('.trigger-add-individual').on('click', function(e) {
            e.preventDefault();
            var url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'enqueue_single_by_id') ?>';
            var $target = jQuery(e.target);
            var postId = $target.data('post-id');

            var data = {
                action: 'static-maker-enqueue_single_by_id'
            };

            if ( postId ) {
                data.post_id = postId;
            } else {
                data.id = $target.data('id');
            }

            jQuery.post(url, data, function(res) {
                console.log(res);
            });
        });

        jQuery('.trigger-remove-individual').on('click', function(e) {
            e.preventDefault();
            var url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'enqueue_single_by_id') ?>';
            var $target = jQuery(e.target);
            var postId = $target.data('post-id');

            var data = {
                action: 'static-maker-enqueue_single_by_id'
            };

            if ( postId ) {
                data.post_id = postId;
            } else {
                data.id = $target.data('id');
            }

            data[ 'action-type' ] = 'remove';

            jQuery.post(url, data, function(res) {
                console.log(res);
            });
        });

        jQuery('.trigger-remove-from-list').on('click', function(e) {
            e.preventDefault();
            var url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'remove_page_from_list') ?>';
            var $target = jQuery(e.target);

            var data = {
                action: 'static-maker-remove_page_from_list'
            };

            data.id = $target.data('id');

            jQuery.post(url, data, function() {
                location.reload();
            });
        });

        jQuery('.trigger-change-page-status').on('click', function(e) {
            e.preventDefault();
            var url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'change_page_status') ?>';
            var $target = jQuery(e.target);

            var data = {
                action: 'static-maker-change-page-status'
            };

            data[ 'action-type' ] = $target.data('action');
            data.id = $target.data('id');

            jQuery.post(url, data, function() {
                location.reload();
            });
        });

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

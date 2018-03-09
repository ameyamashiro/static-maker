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
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

    <h3>ファイルリスト</h3>

    <!-- TODO: 絞り込みとか -->
<!--    <div class="tablenav top">-->
<!--        <div class="alignleft actions bulkactions">-->
<!--            <select class="bulk-action-selector-top" name="" id="">-->
<!--                <option value="-1">投稿タイプを選択</option>-->
<!---->
<!--                --><?php //foreach( PostHelper::get_post_types() as $type => $label ): ?>
<!--                    <option value="--><?php //echo $type ?><!--">--><?php //echo $label ?><!--</option>-->
<!--                --><?php //endforeach; ?>
<!--            </select>-->
<!--            <button class="button button-primary">一括追加</button>-->
<!--        </div>-->
<!--    </div>-->

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <td>Post ID</td>
            <td>Permalink</td>
            <td>状態</td>
        </thead>
        <tbody>
        <?php foreach( Page::get_pages() as $page ): ?>
            <tr>
                <th><?php echo $page->post_id ?></th>
                <td>
                    <a href="<?php echo $page->permalink ?>" target="_blank"><?php echo $page->permalink ?></a>
                    <div class="row-actions">
                        <span class="export-individual">
                            <a
                                href=""
                                class="trigger-individual"
                                <?php if ($page->post_id): ?>
                                data-post-id="<?php echo $page->post_id ?>"
                                <?php else: ?>
                                data-id="<?php echo $page->id ?>"
                                <?php endif; ?>
                            >
                                書き出し
                            </a>
                        </span>
                    </div>
                </td>
                <td><?php echo $page->active === '1' ? '有効' : '無効' ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <div class="bottom-actions">
        <button class="enqueue-all-pages button button-primary">Process all pages</button>
    </div>


    <h3>Queues</h3>

    <table class="wp-list-table widefat fixed striped">
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
                    <?php echo $queue->url ?>
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
        jQuery('.trigger-individual').on('click', function(e) {
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
                    console.log(res);
                }
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

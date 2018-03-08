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
                </td>
                <td><?php echo $page->active === '1' ? '有効' : '無効' ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <pre style="display: block;">
    </pre>

    <h3>Queues</h3>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <td>Post ID</td>
                <td>url</td>
                <td>time</td>
                <td>type</td>
                <td>status</td>
            </tr>
        </thead>
        <tbody>
        <?php foreach( Queue::get_queues() as $queue ): ?>
            <tr>
                <th><?php echo $queue->post_id ?></th>
                <td>
                    <?php $url = get_the_permalink( $queue->post_id ) ?>
                    <?php echo $url ?>
                    <div class="row-actions">
                        <span class="export-individual">
                            <a href="" class="trigger-individual" data-url="<?php echo $queue->url ?>">再書き出し</a></span>
                    </div>
                </td>
                <td><?php echo $queue->time ?></td>
                <td><?php echo $queue->type ?></td>
                <td><?php echo $queue->status ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

</div>

<?php
    add_action( 'admin_footer', 'Static_Maker\static_maker_javascript' );

    function static_maker_javascript() { ?>
    <script>
        jQuery('.trigger-individual').on('click', function(e) {
            e.preventDefault();

            var url = e.target.dataset.url;

            jQuery.post('<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'single_file_get_content') // or ajaxurl in js ?>', {
                action: 'single_file_get_content',
                url: url
            }, function(res) {
                console.log(res);
            })
        });
    </script>
<?php }

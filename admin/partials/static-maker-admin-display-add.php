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

    <div class="metabox-holder">
        <div class="">
            <h3>一括追加</h3>
            <div class="inside">
                <p>投稿タイプ別で管理対象のページを追加できます。</p>
                <form class="add-pages-by-post-type" action="">
                    <select name="post-type-select" id="">
                        <option value="">投稿タイプを選択</option>

                        <?php foreach( PostUtil::get_post_types() as $type => $label ): ?>
                            <option value="<?php echo $type ?>"><?php echo $label ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="submit">
                        <button class="button button-primary">一括追加</button>
                    </div>
                </form>
                <p class="post-type-message"></p>
            </div>
        </div>
    </div>

    <div class="metabox-holder">
        <div class="">
            <h3>個別追加</h3>
            <div class="inside">
                <p>URL を指定して手動で管理対象のページを追加できます。</p>
                <form class="add-page-by-url">
                    <input type="text" name="url" class="regular-text">
                    <div class="submit">
                        <button class="button button-primary">追加</button>
                    </div>
                </form>
                <p class="url-based-message"></p>
            </div>
        </div>
    </div>

    <p class="error"></p>
</div>

<?php
    add_action( 'admin_footer', 'Static_Maker\static_maker_javascript' );

    function static_maker_javascript() { ?>
    <script>
        jQuery('.add-pages-by-post-type').on('submit', function(e) {
            e.preventDefault();
            var postType = jQuery('[name=post-type-select]').val();
            var url = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'add_pages_by_post_type') ?>';

            if (postType.length) {
                jQuery.post(url, {
                    action: 'static-maker-add_pages_by_post_type',
                    post_type: postType
                }, function(res, status) {
                    var $postType = jQuery('.post-type-message');
                    console.log(res);

                    if (status === 'success') {
                        $postType.empty().html('登録に成功しました。');
                    } else {
                        $postType.empty().html('登録に失敗しました。');

                        var $error = jQuery('.error');
                        $error.empty();
                        $error.html(res);
                    }
                });
            }
        });

        jQuery('.add-page-by-url').on('submit', function(e) {
            e.preventDefault();
            var $msg = jQuery('.url-based-message');
            var actionUrl = '<?php echo wp_nonce_url(admin_url('admin-ajax.php'), 'add_page_by_url') ?>';
            var urlValue = jQuery('[name=url]').val();

            if (urlValue.length) {
                jQuery.ajax({
                    type: 'post',
                    url: actionUrl,
                    data: {
                        action: 'static-maker-add_page_by_url',
                        url: urlValue
                    },
                    success: function(res) {
                        console.log(res);

                        $msg.empty().html('登録に成功しました。');
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $msg.empty().html('登録に失敗しました。<br>' + jqXHR.responseText);
                    }
                });
            }
        })
    </script>
<?php }

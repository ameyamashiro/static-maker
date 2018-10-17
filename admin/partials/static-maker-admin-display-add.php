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

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <div class="metabox-holder">
        <div class="">
            <h3><?php _e('Add pages by post type', PLUGIN_NAME)?></h3>
            <div class="inside">
                <div>
                    <p><?php _e('Currently Managed Post Types', PLUGIN_NAME)?>:
                        <span style="font-weight: bold;"><?php echo OptionsUtil::get_accepted_post_types('string') ?></span>
                        <br>(<?php _e('these values can be removed from settings', PLUGIN_NAME)?>)
                    </p>
                </div>

                <form class="add-pages-by-post-type" action="">
                    <select name="post-type-select" id="">
                        <option value=""><?php _e('Select Post types', PLUGIN_NAME)?></option>

                        <?php foreach (PostUtil::get_post_types() as $type => $label): ?>
                            <option value="<?php echo $type ?>"><?php echo $label ?></option>
                        <?php endforeach;?>
                    </select>
                    <div class="submit">
                        <button class="button button-primary"><?php _e('Add', PLUGIN_NAME)?></button>
                    </div>
                </form>
                <p class="post-type-message"></p>
            </div>
        </div>
    </div>

    <div class="metabox-holder">
        <div class="">
            <h3><?php _e('Add a page by url', PLUGIN_NAME)?></h3>
            <div class="inside">
                <p><?php _e('Manually added pages are not tracked changes', PLUGIN_NAME)?></p>
                <form class="add-page-by-url">
                    <input type="text" name="url" class="regular-text">
                    <div class="submit">
                        <button class="button button-primary"><?php _e('Add', PLUGIN_NAME)?></button>
                    </div>
                </form>
                <p class="url-based-message"></p>
            </div>
        </div>
    </div>

    <p class="error"></p>
</div>

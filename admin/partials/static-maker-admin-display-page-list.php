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

$current_page_num = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>

    <div class="tablenav top">
        <div class="tablenav-pages">
            <?php $each = 25;?>
            <?php $count = Page::get_page_count()?>
            <span class="displaying-num"><?php echo $count ?> items</span>
            <span class="pagination-links">
                <?php if ($current_page_num > 1): ?>
                    <a class="prev-page" href="<?php echo strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query(array('page' => $_GET['page'], 'paged' => $current_page_num - 1)) ?>">
                        <span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span>
                    </a>
                <?php else: ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">‹</span>
                <?php endif?>

                <span class="paging-input">
                    <label for="current-page-selector" class="screen-reader-text">
                        Current Page
                    </label>
                    <?php echo $current_page_num ?><span class="tablenav-paging-text"> of <span class="total-pages"><?php echo ceil($count / $each) ?></span></span>
                </span>

                <?php if ($current_page_num === intval(ceil($count / $each))): ?>
                    <span class="tablenav-pages-navspan" aria-hidden="true">›</span>
                <?php else: ?>
                    <a class="next-page" href="<?php echo strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query(array('page' => $_GET['page'], 'paged' => $current_page_num + 1)) ?>">
                        <span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span>
                    </a>
                <?php endif?>
            </span>
        </div>
    </div>

    <table class="wp-list-table widefat striped">
        <thead>
            <td><?php _e('Post ID', PLUGIN_NAME)?></td>
            <td><?php _e('Permalink', PLUGIN_NAME)?></td>
            <td><?php _e('Status', PLUGIN_NAME)?></td>
        </thead>
        <tbody>
        <?php foreach (Page::get_pages(array('paged' => $current_page_num, 'published' => false)) as $page): ?>
            <tr>
                <th><?php echo $page->post_id ?></th>
                <td>
                    <a href="<?php echo $page->permalink ?>" target="_blank"><?php echo rawurldecode($page->permalink) ?></a>
                    <div class="row-actions">
                        <span class="export-individual">
                            <a
                                href=""
                                class="trigger-add-individual"
                                <?php if ($page->post_id): ?>
                                data-post-id="<?php echo $page->post_id ?>"
                                <?php else: ?>
                                data-id="<?php echo $page->id ?>"
                                <?php endif;?>
                            >
                                <?php _e('Export Request', PLUGIN_NAME)?>
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
                                <?php endif;?>
                            >
                                <?php _e('Delete Request', PLUGIN_NAME)?>
                            </a>
                            |
                        </span>
                        <span class="trash">
                            <a
                                href=""
                                class="trigger-remove-from-list"
                                data-id="<?php echo $page->id ?>"
                            >
                                <?php _e('Delete from List', PLUGIN_NAME)?>
                            </a>
                        </span>
                    </div>
                </td>
                <td>
                    <?php if ($page->active === '1'): ?>
                    <?php _e('Active', PLUGIN_NAME)?>
                    <div class="row-actions">
                        <span class="trash">
                            <a
                                href=""
                                class="trigger-change-page-status"
                                data-action="disable"
                                data-id="<?php echo $page->id ?>"
                            >
                                <?php _e('Disable', PLUGIN_NAME)?>
                            </a>
                        </span>
                    </div>
                    <?php else: ?>
                    <?php _e('Disabled', PLUGIN_NAME)?>
                    <div class="row-actions">
                        <span>
                            <a
                                href=""
                                class="trigger-change-page-status"
                                data-action="activate"
                                data-id="<?php echo $page->id ?>"
                            >
                                <?php _e('Activate', PLUGIN_NAME)?>
                            </a>
                        </span>
                    </div>
                    <?php endif?>
                </td>
            </tr>
        <?php endforeach?>
        </tbody>
    </table>

    <div class="bottom-actions">
        <button class="enqueue-all-pages button button-primary"><?php _e('Process all pages', PLUGIN_NAME)?></button>
    </div>

</div>

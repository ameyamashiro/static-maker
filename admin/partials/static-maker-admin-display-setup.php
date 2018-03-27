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

    <?php
    settings_errors();

    $options = get_option( $this->plugin_name );

    $host = isset( $options[ 'host' ] ) ? $options[ 'host' ] : '';
    $basic_enable = isset( $options[ 'basic_enable' ] ) && !empty( $options[ 'basic_enable' ] ) ? 1 : 0;
    $basic_user = isset( $options[ 'basic_auth_user' ] ) ? $options[ 'basic_auth_user' ] : '';
    $basic_pass = isset( $options[ 'basic_auth_pass' ] ) ? $options[ 'basic_auth_pass' ] : '';
    $output = isset( $options[ 'output_path' ] ) ? $options[ 'output_path' ] : '';
    $queue_limit =  isset( $options[ 'queue_limit' ] ) ? $options[ 'queue_limit' ] : '';
    $accepted_post_types =  isset( $options[ 'accepted_post_types' ] ) ? $options[ 'accepted_post_types' ] : '';

    $rsync_initial = array(
        array(
            'host' => '',
            'user' => '',
            'ssh_key' => '',
            'dir' => '',
            'rsync_options' => '',
            'before_command' => ''
        )
    );
    global $rsync_options;
    $rsync_options = isset( $options[ 'rsync' ] ) ? $options[ 'rsync' ] : $rsync_initial;

    $replaces = isset( $options['replaces'] ) ? $options[ 'replaces' ] : array( array( 'from' => '', 'to' => '' ) );


    ?>

    <form method="post" name="static-maker-options" action="options.php">

        <?php
        settings_fields( $this->plugin_name );
        do_settings_sections( $this->plugin_name );
        ?>

        <table class="form-table">
            <tbody>
                <tr style="display: none;">
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-host">Replace Host</label></th>
                    <td>
                        <input type="text" id="<?php echo $this->plugin_name ?>-host" name="<?php echo $this->plugin_name ?>[host]" class="regular-text" placeholder="<?php echo home_url() ?>" value="<?php echo $host ?>">
                        <p class="description">Replace "<?php echo home_url() ?>" with "<?echo $host ? $host : 'this field' ?>" when fetching</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-basic-auth"><?php _e( 'Basic Auth', PLUGIN_NAME ) ?></label></th>
                    <td>
                        <fieldet>
                            <legend class="screen-reader-text"><?php _e( 'Basic Auth', PLUGIN_NAME ) ?></legend>
                        </fieldet>
                        <ul>
                            <li>
                                <label for="<?php echo $this->plugin_name ?>-basic-enable">
                                    <input type="checkbox" id="<?php echo $this->plugin_name ?>-basic-enable" name="<?php echo $this->plugin_name ?>[basic_enable]" value="1" <?php checked($basic_enable, 1) ?>>
                                    <?php _e( 'Enable', PLUGIN_NAME ) ?>
                                </label>
                            </li>
                            <li>
                                <label for="<?php echo $this->plugin_name ?>-basic-auth-user">
                                    <?php _e( 'User', PLUGIN_NAME ) ?>:
                                    <input type="text" id="<?php echo $this->plugin_name ?>-basic-auth-user" name="<?php echo $this->plugin_name ?>[basic_auth_user]" class="regular-text" value="<?php echo $basic_user ?>">
                                </label>
                            </li>
                            <li>
                                <label for="<?php echo $this->plugin_name ?>-basic-auth-pass">
                                    <?php _e( 'Pass', PLUGIN_NAME ) ?>:
                                    <input type="text" id="<?php echo $this->plugin_name ?>-basic-auth-pass" name="<?php echo $this->plugin_name ?>[basic_auth_pass]" class="regular-text" value="<?php echo $basic_pass ?>">
                                </label>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-replaces"><?php _e( 'Replaces', PLUGIN_NAME ) ?></label></th>
                    <td>
                        <div class="replaces-opt">
                            <input type="text" id="<?php echo $this->plugin_name ?>-replaces" name="<?php echo $this->plugin_name ?>[replaces][0][from]" class="regular-text replaces-input" value="<?php echo $replaces[0]['from'] ?>" placeholder="from">
                            <input type="text" id="<?php echo $this->plugin_name ?>-replaces" name="<?php echo $this->plugin_name ?>[replaces][0][to]" class="regular-text replaces-input" value="<?php echo $replaces[0]['to'] ?>" placeholder="to">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-output-path"><?php _e( 'Output Directory', PLUGIN_NAME ) ?></label></th>
                    <td>
                        /<input type="text" id="<?php echo $this->plugin_name ?>-output-path" name="<?php echo $this->plugin_name ?>[output_path]" class="regular-text" placeholder="<?php echo str_replace( get_home_path(), '', wp_upload_dir()[ 'basedir' ] ) ?>/static-maker" value="<?php echo $output ?>">
                        <p class="description"><?php _e( 'Default Value', PLUGIN_NAME ) ?>: /<?php echo str_replace( get_home_path(), '', wp_upload_dir()[ 'basedir' ] ) ?>/static-maker</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-queue-limit"><?php _e( 'Queue Limit', PLUGIN_NAME ) ?></label></th>
                    <td>
                        <input type="text" id="<?php echo $this->plugin_name ?>-queue-limit" name="<?php echo $this->plugin_name ?>[queue_limit]" class="regular-text" value="<?php echo $queue_limit ?>" placeholder="10">
                        <p class="description"><?php _e( 'The number of queues to dequeue at once', PLUGIN_NAME ) ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-accept-post-types"><?php _e( 'Accepted post types', PLUGIN_NAME ) ?></label></th>
                    <td>
                        <input type="text" id="<?php echo $this->plugin_name ?>-accept-post-types" name="<?php echo $this->plugin_name ?>[accepted_post_types]" class="regular-text" value="<?php echo $accepted_post_types ?>" placeholder="post,custom_post_type,and_others">
                        <p class="description"><?php _e( 'Comma separated list. A new page of these post types is automatically added to the page list', PLUGIN_NAME ) ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row" data-rsync-count="<?php echo count($rsync_options) ?>">rsync</th>
                    <td class="rsync-list">
                        <!-- rsync list is inserted here -->

                        <button type="button" class="add-target button" data-sm-target="rsync"><?php _e( 'Add rsync target', PLUGIN_NAME ) ?></button>
                        <button type="button" class="remove-target button" data-sm-target="rsync"><?php _e( 'Remove', PLUGIN_NAME ) ?></button>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(__('Save', $this->plugin_name), 'primary','submit', TRUE); ?>
    </form>

    <script type="text/html" id="rsync-template">
        <ul data-sm-source="rsync" class="rsync-item">
            <li>
                <label for="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][host]">
                    <?php _e( 'Host',  PLUGIN_NAME ) ?>:
                    <input type="text" id="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][host]" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][host]" value="{{HOST}}" class="regular-text" placeholder="IP or Host Domain Name">
                </label>
            </li>
            <li>
                <label for="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][user]">
                    <?php _e( 'User', PLUGIN_NAME ) ?>:
                    <input type="text" id="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][user]" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][user]" value="{{USER}}" class="regular-text" placeholder="ec2-user etc..">
                </label>
            </li>
            <li>
                <label>
                    <input type="radio" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][auth_method]" value="ssh" {{SSH_AUTH}}><?php _e( 'SSH Private Key',  PLUGIN_NAME ) ?>
                </label>
                <label>
                    <input type="radio" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][auth_method]" value="pass" {{PASS_AUTH}}><?php _e( 'Password',  PLUGIN_NAME ) ?>
                </label>
            </li>
            <li>
                <label for="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][ssh_key]">
                    <p><?php _e( 'SSH Private Key', PLUGIN_NAME ) ?> or <?php _e( 'Password', PLUGIN_NAME ) ?>:</p>
                    <textarea id="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][ssh_key]" class="large-text code" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][ssh_key]">{{SSH_KEY}}</textarea>
                </label>
            </li>
            <li>
                <label for="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][dir]">
                    <?php _e( 'Target Directory', PLUGIN_NAME ) ?>:
                    <input type="text" id="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][dir]" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][dir]" class="regular-text" placeholder="~/public" value="{{DIR}}">
                </label>
            </li>
            <li>
                <label for="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][rsync_options]">
                    <?php _e( 'Additional rsync options', PLUGIN_NAME ) ?>:
                    <input type="text" id="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][rsync_options]" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][rsync_options]" class="regular-text" placeholder="--exclude &quot;.git&quot;" value="{{RSYNC_OPTIONS}}">
                </label>
            </li>
            <li>
                <label for="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][before_command]">
                    <?php _e( 'Before command', PLUGIN_NAME ) ?>:
                    <input type="text" id="<?php echo $this->plugin_name ?>-rsync-options[{{COUNT}}][before_command]" name="<?php echo $this->plugin_name ?>[rsync][{{COUNT}}][before_command]" class="regular-text" placeholder="cp -r " value="{{BEFORE_COMMAND}}">
                </label>
                <p class="description"><?php _e( 'Command which will be executed before rsync', PLUGIN_NAME ) ?></p>
                <p class="description">
                    {{ROOT}} : <?php _e( 'Document root', PLUGIN_NAME ) ?> (get_home_path())<br>
                    {{WP_ROOT}} : <?php _e( 'WordPress root', PLUGIN_NAME ) ?> (ABSPATH)<br>
                    {{OUTPUT_DIR}} : <?php _e( 'Static Maker output directory', PLUGIN_NAME ) ?>
                </p>
            </li>
        </ul>
    </script>

</div>

<?php
    add_action( 'admin_footer', 'Static_Maker\static_maker_javascript' );

    function static_maker_javascript() { global $rsync_options; ?>
    <script>
        (function($) {
            var replaceVars = function(html, vars) {
                Object.keys(vars).forEach(function(key) {
                    var reg = new RegExp(key, 'g');
                    html = html.replace(reg, vars[key]);
                });
                return html;
            };

            var count = 0;
            var rsyncVars = [
                <?php foreach ( $rsync_options as $i => $rsync ): ?>
                <?php echo $i !== 0 ? ',' : '' ?>
                <?php echo json_encode(array(
                    '{{HOST}}' => $rsync['host'],
                    '{{USER}}' => $rsync['user'],
                    '{{SSH_KEY}}' => CryptoUtil::decrypt($rsync['ssh_key'], true),
                    '{{DIR}}' => $rsync['dir'],
                    '{{RSYNC_OPTIONS}}' => $rsync['rsync_options'],
                    '{{BEFORE_COMMAND}}' => $rsync['before_command'],
                    '{{SSH_AUTH}}' => $rsync[ 'auth_method' ] === 'ssh' ? 'checked' : '',
                    '{{PASS_AUTH}}' => $rsync[ 'auth_method' ] === 'pass' ? 'checked' : ''
                    )
                ); ?>
                <?php endforeach; ?>
            ];

            rsyncVars.forEach(function(rsyncOpts) {
                var html = $('#rsync-template').html();
                html = html.replace(/{{COUNT}}/g, count);
                html = replaceVars(html, rsyncOpts);
                var $html = $(html);
                count++;

                var $source = $('[data-sm-source="rsync"]').last();
                if ($source.length) {
                    $html.insertAfter($source);
                } else {
                    $('.rsync-list').prepend($html);
                }
            });

            $('.add-target').on('click', function(e) {
                var target = $(this).data('sm-target');
                var $source = $('[data-sm-source="' + target + '"]').last();
                var html = $('#rsync-template').html();
                html = html.replace(/{{COUNT}}/g, count);
                html = replaceVars(html, Object.keys(rsyncVars[0]).reduce(function(a, c) { a[c] = ''; return a; }, {}));
                var $html = $(html);
                count++;

                if ($source.length) {
                    $html.insertAfter($source);
                } else {
                    $('.rsync-list').prepend($html);
                }
            });

            $('.remove-target').on('click', function() {
                var target = $(this).data('sm-target');
                count--;
                $('[data-sm-source="' + target + '"]').last().remove();
            });
        }) (jQuery);
    </script>
<?php };

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
    $basic_user = isset( $options[ 'basic_auth_user' ] ) ? $options[ 'basic_auth_user' ] : '';
    $basic_pass = isset( $options[ 'basic_auth_pass' ] ) ? $options[ 'basic_auth_pass' ] : '';
    $output = isset( $options[ 'output_path' ] ) ? $options[ 'output_path' ] : '';
    $queue_limit =  isset( $options[ 'queue_limit' ] ) ? $options[ 'queue_limit' ] : '';

    ?>

    <form method="post" name="static-maker-options" action="options.php">

        <?php
        settings_fields( $this->plugin_name );
        do_settings_sections( $this->plugin_name );
        ?>

        <p>Disabled inputs means that the feature is not implemented</p>

        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-host">Custom Host</label></th>
                    <td>
                        <input type="text" id="<?php echo $this->plugin_name ?>-host" name="<?php echo $this->plugin_name ?>[host]" class="regular-text" placeholder="<?php echo home_url() ?>" value="<?php echo $host ?>" disabled>
                        <p class="description">Set only if you want to change host name to fetch (e.g. docker containers)</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-basic-auth">Basic Auth</label></th>
                    <td>
                        <fieldet>
                            <legend class="screen-reader-text">Basic Auth</legend>
                        </fieldet>
                        <ul>
                            <li>
                                <label for="<?php echo $this->plugin_name ?>-basic-auth-user">
                                    User:
                                    <input type="text" id="<?php echo $this->plugin_name ?>-basic-auth-user" name="<?php echo $this->plugin_name ?>[basic_auth_user]" class="regular-text" value="<?php echo $basic_user ?>" disabled>
                                </label>
                            </li>
                            <li>
                                <label for="<?php echo $this->plugin_name ?>-basic-auth-pass">
                                    Pass:
                                    <input type="text" id="<?php echo $this->plugin_name ?>-basic-auth-pass" name="<?php echo $this->plugin_name ?>[basic_auth_pass]" class="regular-text" value="<?php echo $basic_pass ?>" disabled>
                                </label>
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-output-path">Output Directory</label></th>
                    <td>
                        /<input type="text" id="<?php echo $this->plugin_name ?>-output-path" name="<?php echo $this->plugin_name ?>[output_path]" class="regular-text" placeholder="<?php echo str_replace( get_home_path(), '', wp_upload_dir()[ 'path' ] ) ?>" value="<?php echo $output ?>" disabled>
                        <p class="description">Default Value: /<?php echo str_replace( get_home_path(), '', wp_upload_dir()[ 'path' ] ) ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo $this->plugin_name ?>-queue-limit">Queue Limit</label></th>
                    <td>
                        <input type="text" id="<?php echo $this->plugin_name ?>-queue-limit" name="<?php echo $this->plugin_name ?>[queue_limit]" class="regular-text" value="<?php echo $queue_limit ?>" placeholder="10">
                        <p class="description">The number of queues to dequeue at once</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <?php submit_button(__('Save all changes', $this->plugin_name), 'primary','submit', TRUE); ?>
    </form>

</div>

<?php
    add_action( 'admin_footer', 'Static_Maker\static_maker_javascript' );

    function static_maker_javascript() { ?>
    <script>
    </script>
<?php }

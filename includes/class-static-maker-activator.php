<?php
namespace Static_Maker;

/**
 * Fired during plugin activation
 *
 * @link       https://developer.wordpress.org/
 * @since      1.0.0
 *
 * @package    Static_Maker
 * @subpackage Static_Maker/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Static_Maker
 * @subpackage Static_Maker/includes
 * @author     ameyamashiro <ameyamashiro@example.com>
 */
class Static_Maker_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
	    Queue::create_table();
	    Page::create_table();
	}

}

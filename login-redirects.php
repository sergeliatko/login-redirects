<?php
/**
 * Login Redirects by TechSpokes Inc.
 *
 * @package     TechSpokes\LoginRedirects
 * @author      TechSpokes Inc.
 * @copyright   2021 TechSpokes Inc. https://techspokes.com
 * @license     GPL-3.0+
 *
 * @wordpress-plugin
 * Plugin Name: Login Redirects by TechSpokes Inc.
 * Plugin URI:  https://github.com/TechSpokes/login-redirects.git?utm_source=wordpress&utm_medium=plugin&utm_campaign=login-redirects&utm_content=plugin-link
 * Description: Allows login redirects based on user roles.
 * Version:     0.0.1
 * Author:      TechSpokes Inc.
 * Author URI:  https://techspokes.com?utm_source=wordpress&utm_medium=plugin&utm_campaign=login-redirects&utm_content=author-link
 * Text Domain: login-redirects
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 */

// do not load this file directly
defined( 'ABSPATH' ) or die( sprintf( 'Please do not load %s directly', __FILE__ ) );

// load namespace
require_once( dirname( __FILE__ ) . '/autoload.php' );

// load plugin text domain
add_action( 'plugins_loaded', function () {

	load_plugin_textdomain(
		'login-redirects',
		false,
		basename( dirname( __FILE__ ) ) . '/languages'
	);
}, 10, 0 );

// load the plugin
add_action( 'plugins_loaded', array( 'TechSpokes\LoginRedirects\Plugin', 'getInstance' ), 10, 0 );


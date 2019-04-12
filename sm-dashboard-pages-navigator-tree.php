<?php
/**
 * Dashboard Pages Navigator Tree
 *
 * @wordpress-plugin
 * @package        WordPress
 * @subpackage     Sm_dashboard_pages_navigator_tree
 * @author         Seth Carstens - WordPress Phoenix
 * @license        GNU GPL v2.0+
 * @link           TODO
 *
 * Built with WP PHX WordPress Development Toolkit v3.1.0 on Friday 1st of March 2019 06:36:00 PM
 * @link           https://github.com/WordPress-Phoenix/wordpress-development-toolkit
 *
 * Plugin Name: SM Dashboard Pages Navigator Tree
 * Plugin URI: TODO
 * Description: TODO
 * Version: 2.0.0
 * Author: Seth Carstens  - WordPress Phoenix
 * Text Domain: sm-dashboard-pages-navigator-tree
 * License: GNU GPL v2.0+
 */

defined( 'ABSPATH' ) or die(); // WordPress must exist.

$current_dir = trailingslashit( dirname( __FILE__ ) );

/**
 * 3RD PARTY DEPENDENCIES
 * (manually include_once dependencies installed via composer for safety)
 */
if ( ! class_exists( 'WPAZ_Plugin_Base\\V_2_6\\Abstract_Plugin' ) ) {
	include_once $current_dir . 'lib/wordpress-phoenix/abstract-plugin-base/src/abstract-plugin.php';
}

/**
 * INTERNAL DEPENDENCIES (autoloader defined in main plugin class)
 */
require_once $current_dir . 'app/class-plugin.php';

SM\Pages_Navigator\Plugin::run(__FILE__);

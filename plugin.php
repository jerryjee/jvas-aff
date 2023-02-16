<?php
/**
 * Plugin Name: JVps-Aff
 * Description: 给你的WordPress添加一个VPS监控功能,支持文章简码插入、定时访问。浏览器更新库存方法：列表页面路径?c=1
 * Author: Leo
 * Author URI:https://jiloc.com
 * Version: 2022.11.26
 */
if (!defined('ABSPATH')) {
	exit;
}

define('JVPS_DIR', plugin_dir_path(__FILE__));
define('JVPS_URL', plugins_url('', __FILE__));
defined('JVPS_PREFIX_KEY') or define('JVPS_PREFIX_KEY', '_jvps_');

require_once JVPS_DIR . '/inc/class-jvps-template.php';
new JVpsTemplate();

require_once JVPS_DIR . '/inc/class-jvps-admin.php';
require_once JVPS_DIR . '/inc/class-jvps-taxonomy.php';
new JVpsCustomTaxonomy();

if (is_admin()) {
	new JVps_Admin();
}

require_once JVPS_DIR . '/inc/class-jvps.php';
new JVps();

require_once JVPS_DIR . '/inc/JVPSAff_Command.class.php';

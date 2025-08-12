<?php
/*
Plugin Name: climbing-project
Description: ジム内でファイル課題を共有するサイトを構築するためのプラグイン。
Version: 1.0.1
Author: Akihisa Kojima
*/
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

require_once( 'includes/clpj_global_var.php' );
require_once( 'includes/clpj_install.php' );
require_once( 'includes/clpj_header.php' );
require_once( 'includes/clpj_body.php' );
require_once( 'includes/clpj_body_admin_upload.php' );
require_once( 'includes/clpj_body_make_project.php' );
require_once( 'includes/clpj_body_project_list.php' );
require_once( 'includes/clpj_upload_file.php' );
require_once( 'includes/clpj_scripts.php' );
require_once( 'includes/clpj_db_handler.php' );
require_once( 'includes/clpj_ajax.php' );
require_once( 'includes/clpj_admin_menu.php' );


add_action( 'wp_headers', 'CLpj_request_handler');
add_action( 'wp_head', 'CLpj_header_handler');
add_action( 'wp_enqueue_scripts', 'CLpj_scripts_method');
add_action( 'wp_ajax_login', 'CLpj_receive_login');
add_action( 'wp_ajax_nopriv_login', 'CLpj_receive_login');
add_action( 'wp_ajax_completed', 'CLpj_receive_completed');
add_action( 'wp_ajax_nopriv_completed', 'CLpj_receive_completed');
add_action( 'wp_ajax_evaluate', 'CLpj_receive_evaluate');
add_action( 'wp_ajax_nopriv_evaluate', 'CLpj_receive_evaluate');

add_filter( 'the_content', 'CLpj_body_rendering_filter');

add_action('admin_menu', 'CLpj_admin_menu');

register_activation_hook( __FILE__, 'CLpj_plugin_activate');
register_deactivation_hook( __FILE__, 'CLpj_plugin_deactivate');

// プラグインを有効化した時テーブル、固定ページを作成
function CLpj_plugin_activate() {
    CLpj_do_activate();
}

// プラグインを無効化した時、固定ページを削除
function CLpj_plugin_deactivate() {
    CLpj_do_deactivate();
}

// End of File

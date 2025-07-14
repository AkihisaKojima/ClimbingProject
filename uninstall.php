<?php
/**
 * climbing-project Uninstall
 *
 * Removes all settings climbing-project added to the WP table
 * 
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// If uninstall is not called from WordPress, kill the uninstall.
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die( 'invalid uninstall' );
}
 
// プラグインを削除した時、climbing-projectで作成したテーブルをドロップ
if ( WP_UNINSTALL_PLUGIN ) {
    require_once( 'includes/clpj_global_var.php' );

    global $CLpj_table_names;
    global $wpdb;

    // 作成の逆順でテーブルをドロップ
    for( $i = count($CLpj_table_names); $i > 0; $i--){
        CLpj_drop_table($wpdb->prefix.$CLpj_table_names[$i-1]);        
    }
/*
    $tables = $wpdb->get_col("SHOW TABLES LIKE '".$wpdb->prefix."clpj_%'");
    foreach($tables as $table_name) {
        if( $table_name == 'CLpj_user_log' or $table_name == 'CLpj_picture_project'){
            //外部キーを設定しているテーブルから削除
            CLpj_drop_table($table_name);
        }
    }

    // それ以外のテーブルを削除
    $tables = $wpdb->get_col("SHOW TABLES LIKE '".$wpdb->prefix."clpj_%'");
    foreach($tables as $table_name) {
        CLpj_drop_table($table_name);
    }
*/

    // WordPress optionsをDBから削除
    $options = $wpdb->get_col(
        "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE 'clpj_%'"
    );

    foreach ($options as $option_name) {
        delete_option($option_name);
    }

}


// climbing-projectで作成したテーブルをドロップ
function CLpj_drop_table( $table_name) {
    global $wpdb;

    $sql = "DROP TABLE IF EXISTS ".esc_sql($table_name);
    $wpdb->query($sql);
}

// End of File
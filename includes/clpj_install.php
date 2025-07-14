<?php
/**
 * climbing-project Install
 *
 */



// プラグインを有効化した時、テーブル、固定ページを作成
function CLpj_do_activate() {
    global $CLpj_table_names;
    global $CLpj_wall_list;
    global $CLpj_grade_list;
    global $CLpj_completed_message;
    global $CLpj_completed_btn;
    global $CLpj_evaluate_btn;


    foreach($CLpj_table_names as $table_name) {
        CLpj_create_table($table_name);
    }

    update_option('clpj_wall_list', $CLpj_wall_list);
    update_option('clpj_grade_list', $CLpj_grade_list);
    update_option('clpj_completed_message', $CLpj_completed_message);
    update_option('clpj_completed_btn', $CLpj_completed_btn);
    update_option('clpj_evaluate_btn', $CLpj_evaluate_btn);

    CLpj_create_page();

}


function CLpj_create_page() {
    global $CLpj_pages;

    foreach ( $CLpj_pages as $page) {
        // 同じスラッグのページがすでに存在するか確認
        if ( ! get_page_by_path( $page['post_name'])) {
            wp_insert_post( $page);
        }
    }
}


// プラグインを無効化した時、固定ページを削除
function CLpj_do_deactivate() {
    CLpj_delete_page();
}

function CLpj_delete_page() {
    global $CLpj_pages;

    foreach ( $CLpj_pages as $page) {
        $target_page = get_page_by_path( $page['post_name']);
        wp_delete_post( $target_page->ID, false);
    }
}



// End of File
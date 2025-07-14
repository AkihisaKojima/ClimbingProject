<?php
/**
 * climbing-project enqueue scripts
 *
 */

function CLpj_scripts_method(){
    global $CLpj_plugin_dirURL;

    // ログイン
    wp_enqueue_script( 'CLpj_login', $CLpj_plugin_dirURL.'js/login.js', array(), false, array('in_footer' => true));

    // 課題作成
    if( is_page('clpj_make_project')){
        wp_enqueue_script( 'CLpj_drawCanvas', $CLpj_plugin_dirURL.'js/drawCanvas.js', array(), false, array('in_footer' => true));
    }

    // 課題作成
    if( is_page('clpj_project_list')){
        wp_enqueue_script( 'CLpj_evaluateProjects', $CLpj_plugin_dirURL.'js/evaluateProjects.js', array(), false, array('in_footer' => true));
    }

}


// End of File
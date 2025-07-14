<?php
/**
 * climbing-project html body処理
 *
 */

function CLpj_body_rendering_filter( $content){

    // 壁写真アップロード
    if( is_page('clpj_admin_upload')){
        $content = CLpj_body_admin_upload( $content);
    }

    // 課題作成
    if( is_page('clpj_make_project')){
        $content = CLpj_body_make_project( $content);
    }

    // 課題一覧
    if( is_page('clpj_project_list')){
        $content = CLpj_body_project_list( $content);
    }

    return $content;
}


// End of File
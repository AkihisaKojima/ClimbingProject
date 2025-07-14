<?php
/**
 * climbing-project file upload 処理
 *
 */

function clpj_handle_file_upload(){
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }

    $current_url = $_SERVER['REQUEST_URI'];

    // 壁写真アップロード
    if( is_page('clpj_admin_upload')){
        foreach ($_FILES as $img_file => $file_data) {
            if( !empty($file_data['name'])){
                $today = date('Y-m-d');
                $info = pathinfo($file_data['name']);
                $file_data['name'] = $img_file. '_' . $today . '.' .$info['extension'];
                $upload_overrides = array('test_form' => false);

                $upload_file = wp_handle_upload($file_data, $upload_overrides);

                if( !empty($_POST[$img_file.'_check'])){
                    $result = CLpj_insert_picture_org( $img_file, $today, $upload_file['url'], true);
                }else{
                    $result = CLpj_insert_picture_org( $img_file, $today, $upload_file['url'], false);
                }

            }
        }

        wp_redirect($current_url);
        return;


    // 課題作成
    }elseif( is_page('clpj_make_project')){

        $user_name = $_COOKIE['clpj_user_name'];
        $comment = $_POST['clpj_project_comment'];
        $grade = intval($_POST['clpj_project_grade']) + floatval($_POST['clpj_project_subgrade']);

        $imgFile = $_FILES['clpj_project_image'];

        $now = current_time('timestamp');
        $now = date('Ymd_His', $now);
        $picture_project = $imgFile['name'];
        $imgFile['name'] = $imgFile['name']. '_' . $now . '.jpeg';

        $upload_overrides = array('test_form' => false);
        $upload_file = wp_handle_upload($imgFile, $upload_overrides);

        $result = CLpj_insert_picture_project( $picture_project, $upload_file['url'], $grade, $user_name, $comment);
    }

}


// End of File
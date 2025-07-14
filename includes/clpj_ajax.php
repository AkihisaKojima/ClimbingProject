<?php
/**
 * climbing-project ajax 処理
 *
 */

function CLpj_receive_login(){
//    check_ajax_referer('login');
    $admin_name = get_option('clpj_admin_name', '');
    $admin_pass = get_option('clpj_admin_pass', '');
    $user_name = sanitize_user($_POST['clpj_login_name']);

    if( isset($_POST['clpj_login_name'])){
        $user_name = $_POST['clpj_login_name'];

        if( $user_name == $admin_name and !empty($admin_pass)){
            if( isset($_POST['clpj_pass']) and ( $_POST['clpj_pass'] == $admin_pass)){
                setcookie('clpj_user_name', $user_name, time()+36000, '/');
                $result = CLpj_insert_user( $user_name);
                wp_send_json(['success' => true, 'required' => false]);
            }else{
                wp_send_json(['success' => false, 'required' => true]);                    
            }
        }else{
            setcookie('clpj_user_name', $user_name, time()+36000, '/');
            $result = CLpj_insert_user( $user_name);
            wp_send_json(['success' => true, 'required' => false]);
        }
    }

}


function CLpj_receive_completed(){
    check_ajax_referer('completed');

    $user_name = $_COOKIE['clpj_user_name'];
    $project_name = $_POST['projectName'];
    $now = current_time('mysql');
    $completed_message = get_option('clpj_completed_message', '');

    $result = CLpj_select_user_log( $user_name, 'completed', $project_name);
    if( empty($result)){
        CLpj_insert_user_log( $user_name, $now, 'completed', $project_name);
    }
    wp_send_json_success($completed_message);
}

function CLpj_receive_evaluate(){
    check_ajax_referer('evaluate');

    $user_name = $_COOKIE['clpj_user_name'];
    $project_name = $_POST['projectName'];
    $evaluate_grade = $_POST['evaluateGrade'];
    $now = current_time('mysql');

    $result = CLpj_select_user_log( $user_name, 'eva：'.$project_name, '');
    if( empty($result)){
        CLpj_insert_user_log( $user_name, $now, 'eva：'.$project_name, $evaluate_grade);
    }else{
        CLpj_update_user_log( $user_name, $now, 'eva：'.$project_name, $evaluate_grade);        
    }
    wp_send_json_success('評価ありがとうございます。');
}

// End of File
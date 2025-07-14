<?php
/**
 * climbing-project html header処理
 *
 */

function CLpj_request_handler(){
    $current_url = $_SERVER['REQUEST_URI'];

    // ログアウト処理
    if( ( strpos($current_url, 'logout') !== false )){
        setcookie('clpj_user_name', 'logout', time()-1, '/');
        wp_redirect(home_url());
        exit;
    }

    // ログイン処理（POST送信時）
/*    if( isset($_POST['clpj_login_name'])){
        $user = get_user_by('login', sanitize_user($_POST['clpj_login_name']));
        if( !empty($use_wp_user)){
            if ( $user) {
                wp_set_current_user($user->ID);
                wp_set_auth_cookie($user->ID);
            }
        }else{
            $user_name = $_POST['clpj_login_name'];
            setcookie('clpj_user_name', $user_name, time()+36000, '/');
            $result = CLpj_insert_user( $user_name);
        }
    } */

    // 壁写真アップロードにadmin以外でアクセスしたらリダイレクト
    if( is_page('clpj_admin_upload')){
        if( !isadmin()){
            wp_redirect(home_url());
            exit;
        }
    }
    
    // 課題作成にログアウト状態でアクセスしたらリダイレクト
    if( is_page('clpj_make_project')){
        if( !islogedin()){
            wp_redirect(home_url());
            exit;
        }            
    }

    // アップロード処理（POST送信時）
    if ( !empty($_FILES)) {
        clpj_handle_file_upload();
    }


}


function CLpj_header_handler( ) {
    Clpj_make_login();
    Clpj_make_menu();

}

function CLpj_make_login( ) {
    $page_url = get_permalink( get_page_by_path( 'clpj_top_page'));
    $ajax_url = admin_url('admin-ajax.php');

    //ログイン済み
    if( islogedin()){
        if( isset($_COOKIE['clpj_user_name'])){
            $user_name = $_COOKIE['clpj_user_name'];
        }else{
            $user_name = $_POST['clpj_login_name'];            
        }
?>
<form action="<?php echo home_url() ?>/logout" method="get">
    <label style="padding-left: 30px"><?php echo $user_name; ?></label>
    <button type="submit">ログアウト</button>
</form>
<?php
    }else{
    //未ログイン
?>
    <label style="padding-left: 30px">ユーザ名：<input type="text" name="clpj_login_name" id="clpj_login_name"/></label>
    <button id="clpj_btn_login" data-nonce="<?php echo wp_create_nonce('clpj_login') ?>">ログイン</button>

    <script>
        const ajaxUrl = "<?php echo $ajax_url ?>";
    </script>
<?php
    }

}

function CLpj_make_menu() {
    $page_project_list = get_page_by_path( 'clpj_project_list');
    $page_make_project = get_page_by_path( 'clpj_make_project');
    $page_admin_upload = get_page_by_path( 'clpj_admin_upload');

    ?>
<p style="text-align: right">
<a href="<?php echo get_permalink($page_project_list) ?>" style="padding-right: 10px"><?php echo $page_project_list->post_title ?></a>
<?php

    // ログイン済みの場合、課題作成ページをリストに追加
    if( islogedin() and !isadmin()){
?>
<a href="<?php echo get_permalink($page_make_project) ?>" style="padding-right: 10px"><?php echo $page_make_project->post_title ?></a>
<?php
    }

    // 管理者の場合、アップロードページをリストに追加
    if( islogedin() and isadmin()){
?>
<a href="<?php echo get_permalink($page_admin_upload) ?>" style="padding-right: 10px"><?php echo $page_admin_upload->post_title ?></a>
<?php
    }
?>
</p>
<?php
}


function islogedin(){
    if( isset($_COOKIE['clpj_user_name']) or isset($_POST['clpj_login_name'])){
        return true;
    }else{
        return false;
    }
}

function isadmin(){
    $admin_name = get_option('clpj_admin_name');

    if( isset($_COOKIE['clpj_user_name']) and ($_COOKIE['clpj_user_name'] == $admin_name)){
        return true;
    }elseif( isset($_POST['clpj_login_name']) and ($_POST['clpj_login_name'] == $admin_name)){
        return true;
    }else{
        return false;
    }
}

// End of File
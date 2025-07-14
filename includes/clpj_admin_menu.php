<?php
/**
 * climbing-project Admin Menu
 *
 */


function CLpj_admin_menu() {
    add_menu_page(
        'ClimbingProjects -管理パラメータ設定-',     // ページのタイトル（ブラウザタイトルバーなど）
        'ClimbingProjects',                  // メニューに表示される名前
        'manage_options',              // 権限（管理者のみなら 'manage_options'）
        'clpj_admin_menu',         // メニュースラッグ（URLの末尾）
        'CLpj_admin_menu_page_content', // 表示する関数
        'dashicons-admin-generic',     // アイコン（WordPress Dashicons）
        20                             // メニューの位置
    );
}


function CLpj_admin_menu_page_content() {
    global $CLpj_grade_list;

	CLpj_admin_menu_setting_option();

    $wall_list = explode(',', get_option('clpj_wall_list'));
    $grade_list = explode(',', get_option('clpj_grade_list'));
    $grade_list_min = $grade_list[0];
    $grade_list_max = $grade_list[count($grade_list)-1];

	$completed_message = get_option('clpj_completed_message');
	$completed_btn = get_option('clpj_completed_btn');
	$evaluate_btn = get_option('clpj_evaluate_btn');
    $admin_name = get_option('clpj_admin_name');
    $admin_pass = get_option('clpj_admin_pass');

    ?>
    <div class="wrap">
        <h1>設定</h1>
        <form method="post" action="">
            <?php wp_nonce_field('clpj_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="clpj_num_wall">壁面の数</label></th>
                    <td><select name="clpj_num_wall" id="clpj_num_wall">
                        <?php
                            for( $i = 1; $i <= 10; $i++) {
                                $selected = '';
                                if(count($wall_list) == $i){ $selected = ' selected';}
                                echo '<option value="'.$i.'"'.$selected.'>'.$i.'</option>
                                ';
                            }
                        ?></select>
                    </td>
                </tr>
            </table>
            <?php
                for( $i = 0; $i < 10; $i++) {
                    $hidden = '';
                    if( empty($wall_list[$i])){
                        $wall_list[$i] = '';
                        $hidden = ' hidden';
                    }
                    echo '
                    <table class="form-table" id="clpj_table_wallName_'.$i.'" style="margin: 0; border-collapse: collapse;"'.$hidden.'>
                        <tr style="padding: 0;">
                            <th scope="row" style="padding: 0; text-align: right;"><label for="clpj_wall_name" style="padding-right: 10px;">壁：'.($i+1).'</label></th>
                            <td style="padding: 0;"><input type="text" name="clpj_wall_name_'.$i.'" id="clpj_wall_name_'.$i.'" value="'.$wall_list[$i].'" class="regular-text" /></td>
                        </tr>
                    </table>
                    ';
                }
            ?>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const selectElement = document.getElementById('clpj_num_wall');
                    selectElement.addEventListener('change', function(event){
                        const selectedValue = event.target.value;
                        for (let i = 0; i < 10; i++) {
                            let targetTable = 'clpj_table_wallName_'+i;
                            if( i < selectedValue){
                                document.getElementById(targetTable).hidden = false;
                            }else{
                                document.getElementById(targetTable).hidden = true;
                                document.getElementById('clpj_wall_name_'+i).value = '';
                            }
                        }
                    });
                });
            </script>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="clpj_grade_list_min">グレードレンジ（下限）</label></th>
                    <td><select name="clpj_grade_list_min" id="clpj_grade_list_min">
                        <?php 
                            foreach( explode(',', $CLpj_grade_list) as $grade) {
                                $selected = '';
                                if($grade == $grade_list_min){ $selected = ' selected';}
                                echo '<option value="'.$grade.'"'.$selected.'>'.$grade.'</option>
                                ';
                            }
                        ?></select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="clpj_grade_list_max">グレードレンジ（上限）</label></th>
                    <td><select name="clpj_grade_list_max" id="clpj_grade_list_max">
                        <?php 
                            foreach( explode(',', $CLpj_grade_list) as $grade) {
                                $selected = '';
                                if($grade == $grade_list_max){ $selected = ' selected';}
                                echo '<option value="'.$grade.'"'.$selected.'>'.$grade.'</option>
                                ';
                            }
                        ?></select>
                    </td>
            </table>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="clpj_completed_message">完登ボタンを押した時の応答メッセージ</label></th>
                    <td><input type="text" name="clpj_completed_message" id="clpj_completed_message" value="<?php echo esc_attr($completed_message); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="clpj_completed_btn">完登ボタンに表示する言葉</label></th>
                    <td><input type="text" name="clpj_completed_btn" id="clpj_completed_btn" value="<?php echo esc_attr($completed_btn); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="clpj_evaluate_btn">グレード投票ボタンに表示する言葉</label></th>
                    <td><input type="text" name="clpj_evaluate_btn" id="clpj_evaluate_btn" value="<?php echo esc_attr($evaluate_btn); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <table class="form-table">
                <tr>
                    <th scope="row"><label for="clpj_admin_name">管理者のログイン名</label></th>
                    <td><input type="text" name="clpj_admin_name" id="clpj_admin_name" value="<?php echo esc_attr($admin_name); ?>" class="regular-text" /></td>
                </tr>
            </table>
        <?php
            if( empty($admin_pass)){
                $checked = '';
                $disabled = ' disabled';
            }else{
                $checked = ' checked';
                $disabled = '';
            }
        ?>
            <table class="form-table" for="clpj_admin_pass" style="margin: 0; border-collapse: collapse;">
                <tr style="padding: 0;">
                    <th scope="row" style="padding: 0; text-align: right;"><input type="checkbox" id="clpj_admin_pass_check" name="clpj_admin_pass_check" <?php echo esc_attr($checked)?>/><label for="clpj_admin_pass" style="padding-right: 10px;">パスワード</label></th>
                    <td style="padding: 0;"><input type="text" name="clpj_admin_pass" id="clpj_admin_pass" class="regular-text" <?php echo esc_attr($disabled); ?>/></td>
                </tr>
            </table>
            <script>
                const checkBox = document.getElementById('clpj_admin_pass_check');
                const textAdminPass = document.getElementById('clpj_admin_pass');
                checkBox.addEventListener('change', function(event){
                    if( this.checked){
                        textAdminPass.disabled = false;
                    }else{
                        textAdminPass.disabled = true;
                    }
                });
            </script>


            <?php submit_button('保存', 'primary', 'clpj_setting_submit'); ?>
        </form>
    </div>
    <?php
}

function CLpj_admin_menu_setting_option() {
    if( isset($_POST['clpj_setting_submit'])){

        $i = 0;
        $wall_list = '';
        while( isset($_POST['clpj_wall_name_'.$i]) and !empty($_POST['clpj_wall_name_'.$i])){
            $wall_list = $wall_list . $_POST['clpj_wall_name_'.$i] . ',';
            error_log($wall_list);
            $i++;
        }
        if(strlen($wall_list) > 0){$wall_list = substr($wall_list, 0, -1);}
        update_option('clpj_wall_list', sanitize_text_field($wall_list));

        if( !empty($_POST['clpj_grade_list_min']) and !empty($_POST['clpj_grade_list_max'])){
            global $CLpj_grade_list;
            $grade_list = explode(',', $CLpj_grade_list);
            $str_list = '';

            while( $grade_list[0] !== $_POST['clpj_grade_list_min']){array_shift($grade_list); }
            while( $grade_list[count($grade_list)-1] !== $_POST['clpj_grade_list_max']){array_pop($grade_list); }
            while( !empty($grade_list)){ $str_list = $str_list.array_shift($grade_list).',';}
            if(strlen($str_list) > 0){$str_list = substr($str_list, 0, -1);}
            update_option('clpj_grade_list', sanitize_text_field($str_list));
        }

        if( isset($_POST['clpj_completed_message'])){
            update_option('clpj_completed_message', sanitize_text_field($_POST['clpj_completed_message']));
        }

        if( !empty($_POST['clpj_completed_btn'])){
            update_option('clpj_completed_btn', sanitize_text_field($_POST['clpj_completed_btn']));
        }

        if( !empty($_POST['clpj_evaluate_btn'])){
            update_option('clpj_evaluate_btn', sanitize_text_field($_POST['clpj_evaluate_btn']));
        }

        if( !empty($_POST['clpj_admin_name'])){
            update_option('clpj_admin_name', sanitize_text_field($_POST['clpj_admin_name']));

            if( !empty($_POST['clpj_admin_pass_check'])){
                if( !empty($_POST['clpj_admin_pass'])){
                    update_option('clpj_admin_pass', sanitize_text_field($_POST['clpj_admin_pass']));
                }
            }else{
                update_option('clpj_admin_pass', '');            
            }
        }


        echo '<div class="updated"><p>設定を保存しました。</p></div>';
   	}
}


// End of File

<?php
/**
 * climbing-project html body処理
 *
 */

// 課題一覧
function CLpj_body_project_list( $content) {
    if( !empty($_COOKIE['clpj_user_name'])){
        $user_name = $_COOKIE['clpj_user_name'];
    }else{
        $user_name = '';
    }

    $page_url = get_permalink( get_page_by_path( 'clpj_project_list'));
    $ajax_url = admin_url('admin-ajax.php');
    $completed_btn = get_option('clpj_completed_btn', '');
    $evaluate_btn = get_option('clpj_evaluate_btn', '');
    $wall_list = explode(',', get_option('clpj_wall_list'));
    $grade_list = explode(',', get_option('clpj_grade_list'));
    $use_wp_user = get_option('clpj_use_wp_user');

    $content = '
<br>
<script>
    const ajaxUrl = "'. $ajax_url .'";
</script>
<form action="'. $page_url .'" method="POST">
<div>
    壁選択：<select name="clpj_wallSelect">
                   <option value="0">ーー　</option>';

    for( $i = 0; $i < count($wall_list); $i++) {
        $content = $content .'
                   <option value="'. $i+1 .'">'. $wall_list[$i] .'</option>';
    }

    $content = $content .'
           </select>
　セッター：<input type="text" name="clpj_setter">　グレード：<select name="clpj_grade_min">
                   <option value="0">ーー　</option>';

    for( $i = 0; $i < count($grade_list); $i++) {
        $grade = $grade_list[$i];
        $content = $content .'
                   <option value="'. $i+1 .'">'. $grade_list[$i] .'</option>';
    }

    $content = $content .'
           </select>～<select name="clpj_grade_max">
                   <option value="0">ーー　</option>';

    for( $i = 0; $i < count($grade_list); $i++) {
        $grade = $grade_list[$i];
        $content = $content .'
                   <option value="'. $i+1 .'">'. $grade_list[$i] .'</option>';
    }

    $content = $content .'
           </select>
</div>
<button type="submit" formmethod="post">検索</button>
</form>
<hr>';

    if( $_SERVER['REQUEST_METHOD'] != 'POST'){ return $content;}

    $picture_project = '';
    if( isset($_POST['clpj_wallSelect'])){
        if( $_POST['clpj_wallSelect'] == '1'){
            $picture_project = 'clpj_Wall_0';
        }elseif( $_POST['clpj_wallSelect'] == '2'){
            $picture_project = 'clpj_Wall_1';
        }elseif( $_POST['clpj_wallSelect'] == '3'){
            $picture_project = 'clpj_Wall_2';
        }elseif( $_POST['clpj_wallSelect'] == '4'){
            $picture_project = 'clpj_Wall_3';
        }elseif( $_POST['clpj_wallSelect'] == '5'){
            $picture_project = 'clpj_Wall_4';
        }elseif( $_POST['clpj_wallSelect'] == '6'){
            $picture_project = 'clpj_Wall_5';
        }elseif( $_POST['clpj_wallSelect'] == '7'){
            $picture_project = 'clpj_Wall_6';
        }elseif( $_POST['clpj_wallSelect'] == '8'){
            $picture_project = 'clpj_Wall_7';
        }elseif( $_POST['clpj_wallSelect'] == '9'){
            $picture_project = 'clpj_Wall_8';
        }elseif( $_POST['clpj_wallSelect'] == '10'){
            $picture_project = 'clpj_Wall_9';
        }
    }

    $setter = '';
    if( isset($_POST['clpj_setter'])){
        $setter = $_POST['clpj_setter'];
    }

    $grade_max = '';
    if( isset($_POST['clpj_grade_max'])){
        $grade_max = $_POST['clpj_grade_max'];
    }

    $grade_min = '';
    if( isset($_POST['clpj_grade_min'])){
        $grade_min = $_POST['clpj_grade_min'];
    }

    if( !empty($grade_max) and !empty($grade_min)){
        if( $grade_min > $grade_max){
            $tmp = $grade_min;
            $grade_min = $grade_max;
            $grade_max = $tmp;
        }
    }

    if( !empty($picture_project)){
        $projectList = CLpj_select_picture_project( $picture_project, $setter, $grade_max, $grade_min);
    }else{
        $projectList = [];
        for( $i = 0; $i < count($wall_list); $i++){
            $result = CLpj_select_picture_project( 'clpj_Wall_'.$i, $setter, $grade_max, $grade_min);
            $projectList = array_merge($projectList, $result);
        }
    }
    $content = $content .'<table>';
    foreach( $projectList as $project) {
        $grade_org = CLpj_plotSlider($project['grade_org']);
        $project_name = basename($project['url']);

        $results = CLpj_select_user_log( '', 'eva：'.$project_name, '');
        $grade_usr;
        if( !empty($results)){
            $sum_grade = 0;
            $n = 0;
            foreach( $results as $result){
                $sum_grade = $sum_grade + $result['value'];
                $n = $n+1;
            }
            $grade_usr = CLpj_plotSlider($sum_grade/$n);
        }else{
            $grade_usr = CLpj_plotSlider('');
        }

        $completed = '';
        if( !empty($user_name)){
            $result = CLpj_select_user_log( $user_name, 'completed', $project_name);
            if( !empty($result)){
                $completed = 'disabled';
            }
        }

        $num_completed = 0;
        $result = CLpj_select_user_log( '', 'completed', $project_name);
        $num_completed = count($result);


        $content = $content . '
<tr><td style="vertical-align: top; padding: 0px 0px 0px 0px;"><a href="'. $project['url'] .'"><img src="'. $project['url'] .'" width="100%"></a></td>
<td width="300px" style="vertical-align: top; padding: 0px 0px 30px 10px;">
<p><span style="white-space: nowrap;">セッター：</span><span style="white-space: nowrap; font-weight:bold;">'. $project['setter'] .'</span></p>
<p></p>
<span style="white-space: nowrap;">グレード（セッター評価）：</span><br>
<span style="white-space: nowrap; font-weight:bold;">'. $grade_org .'</span>
<p></p>
<span style="white-space: nowrap;">グレード（ユーザー評価）：</span><br>
<span style="white-space: nowrap; font-weight:bold;">'. $grade_usr .'</span>
<p></p>
<span style="white-space: nowrap;">完登者数：</span>
<span style="white-space: nowrap; font-weight:bold;">'. $num_completed .'</span>
<p></p>
<span>セッターより（コメント）：</span><br>
<textarea disabled style="resize: none;">'. $project['comment'] .'</textarea>
<hr>';
        $flag = false;
        if( isset($_COOKIE['clpj_user_name']) and !isadmin()){
            $content = $content . '
<span data-project="'. $project_name .'">
<p style="vertical-align: top; padding: 0px 0px 15px 0px;"><button class="clpj_btn_completed" data-nonce="'. wp_create_nonce('completed') .'" '. $completed .'>'. $completed_btn .'</button></p>
<button class="clpj_btn_evaluate" data-nonce="'. wp_create_nonce('evaluate') .'" disabled>'. $evaluate_btn .'</button><br>
    <select class="clpj_evaluate_grade">
           <option value="0">ーー　</option>';

    for( $i = 0; $i < count($grade_list); $i++) {
        $grade = $grade_list[$i];
        $content = $content .'
                   <option value="'. $i+1 .'">'. $grade_list[$i] .'</option>';
    }

    $content = $content .'
   </select>
   <span style="white-space: nowrap; font-size:smaller;">甘い<input class="clpj_evaluate_subgrade" type="range" min="0" max="0.8" value="0" step="0.2">辛い</span>
</span>';}
        $content = $content . '
</td></tr>';
    }
    $content = $content . '
</table>';

    return $content;
}

function CLpj_plotSlider( $grade){
    if( $grade != '' and $grade >= 0){
        $grade = $grade-1;
        $grade_list = explode(',', get_option('clpj_grade_list'));
        $plot = '';

        $grade_min = intval($grade);
        $grade_max = intval($grade+1);
        $minGrade = $grade_list[$grade_min];
        $maxGrade = $grade_list[$grade_max];

        $plot = $plot . $minGrade .'<span style="font-size:smaller; font-weight:normal;">　甘い<input type="range" min="'. $grade_min .'" max="'. $grade_max .'" value="'. intval($grade*10)/10 .'" step="0.1" disabled>辛い</span>';
    }else{
        $plot = 'なし';                
    }

    return $plot;
}


// End of File
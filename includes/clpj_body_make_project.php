<?php
/**
 * climbing-project html body処理
 *
 */

// 課題作成
function CLpj_body_make_project( $content) {
    $wall_list = explode(',', get_option('clpj_wall_list'));
    $grade_list = explode(',', get_option('clpj_grade_list'));

    $content = '
<div>
    壁選択：<select id="clpj_select_wall">
                   <option value="0">ーー　</option>';

    for( $i = 0; $i < count($wall_list); $i++) {
        $content = $content .'
                   <option value="'. $i+1 .'">'. $wall_list[$i] .'</option>';
    }

    $content = $content .'
           </select>
</div>
<div>
    太さ：<input id="clpj_slider" type="range" min="1" max="100" value="10"><span id="clpj_brushSize">10</span>
         <input type="button" id="clpj_btn_undo" value="Undo" disabled="true"><input type="button" id="clpj_btn_redo" value="Redo" disabled="true">
</div>

<div id="clpj_canvas-area" width="100%" style="height: 80vh; position: relative; overflow: auto;">
    <!-- 画像Canvas -->
    <canvas id="clpj_imageCanvas" style="top: 0px; left: 0; position: absolute;"></canvas>
    <!-- 描画Canvas -->
    <canvas id="clpj_drawCanvas" style="top: 0px; left: 0; position: absolute;"></canvas>
    <!-- ポインタCanvas -->
    <canvas id="clpj_pointerCanvas" style="top: 0px; left: 0; position: absolute;"></canvas>
</div>

<div>
<table>
    <tr><td style="vertical-align: top;">
    グレード：<select id="clpj_select_grade">';

    for( $i = 0; $i < count($grade_list); $i++) {
        $grade = $grade_list[$i];
        $content = $content .'
                   <option value="'. $i+1 .'">'. $grade_list[$i] .'</option>';
    }

    $content = $content .'
           </select>
           甘い<input id="clpj_subgrade_slider" type="range" min="0" max="1" value="0" step="0.2">辛い
    </td><td style="vertical-align: top; padding: 0px 0px 0px 10px;">
        コメント：
<textarea id="clpj_txt_comment"></textarea>
    </td></tr>
</table>
<input type="button" id="clpj_btn_postFile" value="投稿" disabled="true">
</div>

<div hidden>';

    for( $i = 0; $i < count($wall_list); $i++) {
        $wall_id = 'clpj_Wall_'.$i;
        $picture_org = CLpj_select_picture_org( $wall_id, true);
        if( !isset($picture_org)){$picture_org['url']='__';}
        $content = $content .'
    <span id="clpj_tmpImg_'. $i .'">'. $picture_org['url'] .'</span>';
    }

    $content = $content .'
    <span id="clpj_dispX">0</span>
    <span id="clpj_dispY">0</span>
    <span id="clpj_url_make_project">'. get_permalink( get_page_by_path( 'clpj_make_project')) .'</span>
</div>';


    return $content;
}


// End of File
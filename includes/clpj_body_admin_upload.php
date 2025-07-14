<?php
/**
 * climbing-project html body処理
 *
 */


// 壁写真アップロード
function CLpj_body_admin_upload( $content) {
    $wall_list = explode(',', get_option('clpj_wall_list'));

    $content = '
<form action="'. get_permalink( get_page_by_path( 'admin_upload')) .'" method="POST" enctype="multipart/form-data">
    <input type="submit" value="アップロード">
    <p></p>
    <table>
';

    for( $i = 0; $i < count($wall_list); $i++) {
        $wall_id = 'clpj_Wall_'.$i;
        $picture_org = CLpj_select_picture_org( $wall_id, true);
        if( !isset($picture_org)){$picture_org['url']='__';}
        $content = $content .'
        <tr><td><div style="text-align: right">'. $wall_list[$i] .'：</div></td><td><input type="file" name="'. $wall_id .'"><input type="checkbox" id="'. $wall_id .'_check" name="'. $wall_id .'_check" /><label for="'. $wall_id .'_check" style="padding-right: 10px;">追加セット</label></td></tr>
        <tr><td></td><td style="padding: 5px 0px 30px 0px;"><img src="'. $picture_org['url'] .'" width="200" height="200"></td></tr>
        ';
    }

    $content = $content .'
    </table>
    <p></p>
    <input type="submit" value="アップロード">
</form>
';

    return $content;
}


// End of File
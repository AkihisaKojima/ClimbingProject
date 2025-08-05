<?php
/**
 * climbing-project gloval var
 *
 */


global $CLpj_plugin_dirURL;
$CLpj_plugin_dirURL = plugin_dir_url(__DIR__);


global $CLpj_table_names;
$CLpj_table_names = ['CLpj_user','CLpj_user_log','CLpj_picture_org','CLpj_picture_project'];

global $CLpj_wall_list;
$CLpj_wall_list = 'スラブ壁,90°壁,100°壁,110°壁,120°壁,135°壁';
global $CLpj_grade_list;
$CLpj_grade_list = '１０級,９級,８級,７級,６級,５級,４級,３級,２級,１級,初段,二段,三段,四段,五段';
global $CLpj_completed_message;
$CLpj_completed_message = 'ナイッス～';
global $CLpj_completed_btn;
$CLpj_completed_btn = '完登したよ ^^';
global $CLpj_evaluate_btn;
$CLpj_evaluate_btn = 'グレード評価を送信';


global $CLpj_pages;
$CLpj_pages = [
    [
        'post_title'   => '課題一覧',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'clpj_project_list',
        'post_content' => ''
    ],[
        'post_title'   => '課題作成',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'clpj_make_project',
        'post_content' => ''
    ],
    [
        'post_title'   => '壁写真アップロード',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_name'    => 'clpj_admin_upload',
        'post_content' => ''
    ]
];




// End of File

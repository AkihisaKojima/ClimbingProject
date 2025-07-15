<?php
/**
 * climbing-project DB handler
 *
 */


// climbing-projectで使用するテーブルを作成
function CLpj_create_table( $table_name) {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    global $wpdb;
    $target_table_name = $wpdb->prefix . $table_name;

    if($wpdb->get_var("SHOW TABLES LIKE '".$target_table_name."'") != $target_table_name) {

        if($table_name == 'CLpj_user'){
            $sql = "CREATE TABLE ".$target_table_name." (
                `userName` varchar(45) NOT NULL,
                PRIMARY KEY (userName)
            ) ".$wpdb->get_charset_collate().";";

        } elseif($table_name == 'CLpj_user_log'){
            $sql = "CREATE TABLE ".$target_table_name." (
                `userName` varchar(45) NOT NULL,
                `datetime` datetime NOT NULL,
                `type` varchar(1000),
                `value` varchar(1000),
                INDEX (userName),
                FOREIGN KEY (userName) REFERENCES ".$wpdb->prefix ."CLpj_user (userName) ON DELETE RESTRICT ON UPDATE CASCADE,
                PRIMARY KEY (userName, datetime)
            ) ".$wpdb->get_charset_collate().";";

        } elseif($table_name == 'CLpj_picture_org'){
            $sql = "CREATE TABLE ".$target_table_name." (
                `picture_org` varchar(100) NOT NULL,
                `date` date NOT NULL,
                `url` varchar(375) NOT NULL,
                `additional` boolean NOT NULL DEFAULT false,
                PRIMARY KEY (url)
            ) ".$wpdb->get_charset_collate().";";

        } elseif($table_name == 'CLpj_picture_project'){
            $sql = "CREATE TABLE ".$target_table_name." (
                `picture_project` varchar(100) NOT NULL,
                `createtime` datetime NOT NULL,
                `url` varchar(375) NOT NULL,
                `grade_org` float,
                `setter` varchar(100) NOT NULL,
                `comment` varchar(1000),
                INDEX (setter),
                FOREIGN KEY (setter) REFERENCES ".$wpdb->prefix ."CLpj_user (userName) ON DELETE RESTRICT ON UPDATE CASCADE,
                PRIMARY KEY (url)
            ) ".$wpdb->get_charset_collate().";";

        }
        
        dbDelta( $sql );
    }

}


// ユーザーテーブル
function CLpj_insert_user( $user_name){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_user';

    $sql = $wpdb->prepare("
        INSERT INTO ".$target_table_name." (userName)
        SELECT * FROM (SELECT %s) AS tmp
        WHERE NOT EXISTS (
            SELECT userName 
            FROM ".$target_table_name."
            WHERE userName = %s
        )", $user_name, $user_name);
    $wpdb->query($sql);

}


// 壁写真テーブル
function CLpj_insert_picture_org( $picture_org, $date, $url, $additional){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_picture_org';

    if( $additional){
        $sql = $wpdb->prepare("
            INSERT INTO ".$target_table_name." ( picture_org, date, url, additional)
            SELECT * FROM (SELECT %s AS 'picture_org', %s AS 'date', %s AS 'url', %s AS 'additional') AS tmp
            ", $picture_org, $date, $url, $additional);
    }else{
        $sql = $wpdb->prepare("
            INSERT INTO ".$target_table_name." ( picture_org, date, url, additional)
            SELECT * FROM (SELECT %s AS 'picture_org', %s AS 'date', %s AS 'url', %s AS 'additional') AS tmp
            WHERE NOT EXISTS (
                SELECT * FROM ".$target_table_name."
                WHERE picture_org = %s and date = %s
            )", $picture_org, $date, $url, $additional, $picture_org, $date);
    }
    $wpdb->query($sql);

}


function CLpj_select_picture_org( $picture_org, bool $additional){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_picture_org';

    $sqlFragment_additional = '';
    if( !$additional){ $sqlFragment_additional = ' and additional = false';}

    $sql = $wpdb->prepare("
        SELECT * FROM ".$target_table_name."
        WHERE picture_org = %s".$sqlFragment_additional."
        ORDER by date DESC
        ", $picture_org);

    $result = $wpdb->get_row( $sql, ARRAY_A, 0);
    return $result;

}
    

// 課題テーブル
function CLpj_insert_picture_project( $picture_project, $url, $grade, $setter, $comment){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_picture_project';
    $now = current_time('mysql');

    $sql = $wpdb->prepare("
        INSERT INTO ".$target_table_name." ( picture_project, createtime, url, grade_org, setter, comment)
        SELECT * FROM (SELECT %s AS 'picture_project',
                        %s AS 'createtime',
                        %s AS 'url',
                        %s AS 'grade_org',
                        %s AS 'setter',
                        %s AS 'comment') AS tmp
        WHERE NOT EXISTS (
            SELECT * FROM ".$target_table_name."
            WHERE url = %s
        )", $picture_project, $now, $url, $grade, $setter, $comment, $url);
    $wpdb->query($sql);

}

function CLpj_select_picture_project( $picture_project, $setter, $grade_max, $grade_min){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_picture_project';

    if( !empty($picture_project)){
        $result = CLpj_select_picture_org( $picture_project, false);
    }else{
        $result = CLpj_select_picture_org( 'CLpj_Wall_0', false);
    }
    $setDay = $result['date'];

    $sqlFragment_where = 'WHERE createtime > '. $setDay;
    if( !empty($picture_project) or !empty($setter) or !empty($grade_max) or !empty($grade_min) ){
        if( !empty($picture_project)){
            $sqlFragment_where = $sqlFragment_where .' and picture_project = "'.$picture_project.'"';
        }
        if( !empty($setter)){
            $sqlFragment_where = $sqlFragment_where .' and setter = "'.$setter.'"';
        }
        if( !empty($grade_max)){
            $sqlFragment_where = $sqlFragment_where .' and grade_org <= '.$grade_max;
        }
        if( !empty($grade_min)){
            $sqlFragment_where = $sqlFragment_where .' and grade_org >= '.$grade_min;
        }
    }

    $sql = $wpdb->prepare('
        SELECT * FROM '.$target_table_name.'
        '.$sqlFragment_where.'
        ORDER by %s ASC
        ', 'createtime');

    $result = $wpdb->get_results( $sql, ARRAY_A);
    return $result;
}



// ユーザログテーブル
function CLpj_insert_user_log( $userName, $datetime, $type, $value){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_user_log';

    $sql = $wpdb->prepare("
        INSERT INTO ".$target_table_name." ( userName, datetime, type, value)
        SELECT * FROM (SELECT %s AS 'userName', %s AS 'datetime', %s AS 'type', %s AS 'value') AS tmp
        WHERE NOT EXISTS (
            SELECT * FROM ".$target_table_name."
            WHERE userName = %s and datetime = %s
        )", $userName, $datetime, $type, $value, $userName, $datetime);
    $wpdb->query($sql);

}

function CLpj_select_user_log( $user_name, $type, $value){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_user_log';

    if( empty($user_name) and empty($value)){
        $sql = $wpdb->prepare('
            SELECT * FROM '.$target_table_name.'
            WHERE type = %s
            ', $type);
    }elseif( empty($user_name)){
        $sql = $wpdb->prepare('
            SELECT * FROM '.$target_table_name.'
            WHERE type = %s AND value = %s
            ', $type, $value);
    }elseif( empty($value)){
        $sql = $wpdb->prepare('
            SELECT * FROM '.$target_table_name.'
            WHERE userName = %s AND type = %s
            ', $user_name, $type);
    }else{
        $sql = $wpdb->prepare('
            SELECT * FROM '.$target_table_name.'
            WHERE userName = %s AND type = %s AND value = %s
            ', $user_name, $type, $value);
    }

    $result = $wpdb->get_results( $sql, ARRAY_A);
    return $result;

}

function CLpj_update_user_log( $userName, $datetime, $type, $value){
    global $wpdb;
    $target_table_name = $wpdb->prefix .'CLpj_user_log';

    $sql = $wpdb->prepare('
        DELETE FROM '.$target_table_name.'
        WHERE userName = %s AND type = %s
        ', $userName, $type);
    $wpdb->query($sql);

    CLpj_insert_user_log( $userName, $datetime, $type, $value);

}

// End of File
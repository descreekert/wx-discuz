
<?php

define('APPTYPEID', 0);
define('CURSCRIPT', 'search');
require './source/class/class_core.php';

$discuz = C::app();

$discuz->init();

// 在此修改默认NO image available的图片地址
define('NO_PIC_URL', 'https://7jita.com/data/attachment/common/logo_small.png');

$keyword = $_GET['keyword'];
echo getNews($keyword);

function compare_picurl($a, $b) {
    // 图片不存在的排在后面
    return ($a['picurl']===NO_PIC_URL) - ($b['picurl']===NO_PIC_URL);
}

function getNews($keyword) {
    $tsql = "SELECT t.tid, min(t.subject) subject, count(a.aid) cnta
        FROM ".DB::table('forum_thread')." t
        LEFT JOIN ".DB::table('forum_attachment')." a
          ON t.tid = a.tid
        WHERE t.subject LIKE '%$keyword%'
        GROUP BY t.tid
        ORDER BY t.views DESC, count(a.aid) DESC
        LIMIT 10";

    $news = array();
    foreach(DB::fetch_all($tsql) as $thread) {
        $tid = $thread['tid'];
        $tableid=substr($tid,-1,1);
        $asql = "SELECT a.tid,a.attachment,a.remote
            FROM ".DB::table("forum_attachment_{$tableid}")." a
            WHERE a.tid ='$tid'
            AND a.isimage <>0";
        $attach=DB::fetch_first($asql);

        $picurl = NO_PIC_URL;
        if($attach){
            //在此处修改你的网站附件地址
            $picurl = 'https://7jita.com/data/attachment/forum/'.$attach['attachment'];
        }
        
        $newsItem = array('title'=>$thread['subject'],'desc'=>$thread['subject'] ,'picurl'=>$picurl,
            'url'=>'http://yourdomain.com/forum.php?mod=viewthread&tid='.$tid);
        $news[] = $newsItem;
    }


    // sort alphabetically by name
    usort($news, 'compare_picurl');

    if(count($news) > 5) {
        $news = array_slice($news, 0, 5);
    }
    return json_encode($news);
}
?>

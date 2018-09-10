
<?php

define('APPTYPEID', 0);
define('CURSCRIPT', 'search');
require './source/class/class_core.php';

$discuz = C::app();

$discuz->init();

$keyword = $_GET['keyword'];
echo getNews($keyword);

function compare_picurl($a, $b) {
    return strnatcmp($a['picurl'], $b['picurl']);
}

function getNews($keyword) {
    $tsql = "SELECT t.tid, min(t.subject) subject, count(a.aid) cnta
        FROM ".DB::table('forum_thread')." t
        LEFT JOIN ".DB::table('forum_attachment')." a
          ON t.tid = a.tid
        WHERE t.subject LIKE '%$keyword%'
          //AND t.fid in (65,121) --modify here to search in specific blocks
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

        $picurl;
        if($attach){
            $picurl = 'http://yourdomain.come/data/attachment/forum/'.$attach['attachment']; //在此处修改你的网站附件地址
        } else {
            $picurl = 'https://thingsgounsaid1.files.wordpress.com/2011/04/no-pic.jpg';  //在此修改no image available 的图片地址
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

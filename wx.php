<?php
/**
 * wechat php test
 */
//define your token
define("TOKEN", "yourtoken");

$wechatObj = new WXGZH();
// $wechatObj->validToken();
$wechatObj->responseMsg();

class WXGZH
{
    private $_msg_template = array(
        'text' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[%s]]></Content></xml>',//文本回复XML模板
        'image' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[image]]></MsgType><Image><MediaId><![CDATA[%s]]></MediaId></Image></xml>',//图片回复XML模板
        'music' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[music]]></MsgType><Music><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><MusicUrl><![CDATA[%s]]></MusicUrl><HQMusicUrl><![CDATA[%s]]></HQMusicUrl><ThumbMediaId><![CDATA[%s]]></ThumbMediaId></Music></xml>',//音乐模板
        'news' => '<xml><ToUserName><![CDATA[%s]]></ToUserName><FromUserName><![CDATA[%s]]></FromUserName><CreateTime>%s</CreateTime><MsgType><![CDATA[news]]></MsgType><ArticleCount>%s</ArticleCount><Articles>%s</Articles></xml>',// 新闻主体
        'news_item' => '<item><Title><![CDATA[%s]]></Title><Description><![CDATA[%s]]></Description><PicUrl><![CDATA[%s]]></PicUrl><Url><![CDATA[%s]]></Url></item>',//某个新闻模板
    );

    public function validToken()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }
    private function _msgText($to, $from, $content) {
        $response = sprintf($this->_msg_template['text'], $to, $from, time(), $content);
        die($response);
    }

    private function _msgNews($to,$from,$item_list=array()){
        //拼凑文章部分
        $item_str = '';
        foreach ($item_list as $item) {
            $item_str .= sprintf($this->_msg_template['news_item'],$item['title'],$item['desc'],$item['picurl'],$item['url']);
        }
        //拼凑主体部分
        $response = sprintf($this->_msg_template['news'], $to, $from, time(), count($item_list), $item_str);
        return $response;
    }

    public function responseMsg($postStr)
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //extract post data
        if (!empty($postStr)){

            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $keyword = trim($postObj->Content);

            if(!empty( $keyword ))
            {
                //$item_list = getNews($keyword);
                $item_list = json_decode(file_get_contents("http://yourdomain.com/wxsearch.php?keyword=".$keyword), true);
                $resultStr;
                if (empty($item_list)) {
                    $resultStr = $this->_msgText($fromUsername,$toUsername,"抱歉，没有找到您搜索的内容:".$keyword);
                } else {
                    $resultStr = $this->_msgNews($fromUsername,$toUsername,$item_list);
                }
                echo $resultStr;

            }else{
                echo "Input something...";
            }

        }else {
            echo "";
            exit;
        }
    }

    private function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}

?>

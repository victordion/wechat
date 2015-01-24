<?php
/**
  * wechat php test
  */
function random_pic($dir = '/var/www/html/weixin/pic')
{
    $files = glob($dir . '/*.*');
    $file = array_rand($files);
    return $files[$file];
   // return $file;
}

$dataPOST = trim(file_get_contents('php://input'));
$xmlData = simplexml_load_string($dataPOST);
$me = $xmlData->ToUserName;
$user = $xmlData->FromUserName;
$content = "你刚才说的是". $xmlData->Content . "对吗？";
$msgtype = $xmlData->MsgType;
$event = $xmlData->Event;


if($msgtype == "event" && $event == "subscribe"){
	$reply = "
		<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>12345678</CreateTime>
		<MsgType><![CDATA[text]]></MsgType>
		<Content><![CDATA[%s]]></Content>
		</xml>";
	$content = "欢迎关注我！回复问号'?'即可获得一张内涵图！";
	echo sprintf($reply, $user, $me, $content);
	return;
}

if($xmlData->Content == "0201"){
	$reply = "<xml>
               	<ToUserName><![CDATA[%s]]></ToUserName>
               	<FromUserName><![CDATA[%s]]></FromUserName>
               	<CreateTime>12345678</CreateTime>
               	<MsgType><![CDATA[news]]></MsgType>
               	<ArticleCount>1</ArticleCount>
               	<Articles>
               	<item>
               	<Title><![CDATA[0201]]></Title>
               	<Description><![CDATA[回忆]]></Description>
               	<PicUrl><![CDATA[%s]]></PicUrl>
               	<Url><![CDATA[%s]]></Url>
               	</item>
               	</Articles>
               	</xml>";
	$picfilename = basename(random_pic('/var/www/html/weixin/0201'));
	$picurl = "http://jcui.info/weixin/0201/" . $picfilename;
	echo sprintf($reply, $user, $me, $picurl, $picurl);
	return;
}


if($xmlData->Content == "neihan"){
	$reply = "<xml>
               	<ToUserName><![CDATA[%s]]></ToUserName>
               	<FromUserName><![CDATA[%s]]></FromUserName>
               	<CreateTime>12345678</CreateTime>
               	<MsgType><![CDATA[news]]></MsgType>
               	<ArticleCount>1</ArticleCount>
               	<Articles>
               	<item>
               	<Title><![CDATA[]]></Title>
               	<Description><![CDATA[]]></Description>
               	<PicUrl><![CDATA[%s]]></PicUrl>
               	<Url><![CDATA[%s]]></Url>
               	</item>
               	</Articles>
               	</xml>";
	$picfilename = basename(random_pic('/var/www/html/weixin/neihan'));
	$picurl = "http://jcui.info/weixin/neihan/" . $picfilename;
	echo sprintf($reply, $user, $me, $picurl, $picurl);
	return;
}






if($xmlData->Content == "?" || $xmlData->Content == "？"){
	$reply = "<xml>
               	<ToUserName><![CDATA[%s]]></ToUserName>
               	<FromUserName><![CDATA[%s]]></FromUserName>
               	<CreateTime>12345678</CreateTime>
               	<MsgType><![CDATA[news]]></MsgType>
               	<ArticleCount>1</ArticleCount>
               	<Articles>
               	<item>
               	<Title><![CDATA[内涵图]]></Title>
               	<Description><![CDATA[你懂的]]></Description>
               	<PicUrl><![CDATA[%s]]></PicUrl>
               	<Url><![CDATA[%s]]></Url>
               	</item>
               	</Articles>
               	</xml>";
	$picfilename = basename(random_pic());
	$picurl = "http://jcui.info/weixin/pic/" . $picfilename;
	echo sprintf($reply, $user, $me, $picurl, $picurl);
	return;
}


$file = '/var/www/html/weixin/wx_record.txt';
file_put_contents($file, $me."\n", FILE_APPEND );
file_put_contents($file, $user."\n", FILE_APPEND );
file_put_contents($file, $content."\n", FILE_APPEND);

$reply = "
<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>12345678</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>
";
echo sprintf($reply, $user, $me, $content);

//define your token
define("TOKEN", "victordion");
$wechatObj = new wechatCallbackapiTest();
$wechatObj->valid();




class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
                   the best way is to check the validity of xml by yourself */
                libxml_disable_entity_loader(true);
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
		if(!empty( $keyword ))
                {
              		$msgType = "text";
                	$contentStr = "Welcome to wechat world!";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
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
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
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

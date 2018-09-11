# wx-discuz
## 微信公众号对接discuz论坛，php版本，提供如下简单功能：
微信公众号输入搜索关键字，搜索论坛并将包含关键字的帖子（最多5条）以图文消息返回公众号，以帖子里的第一张附件图片作为缩略图（如果没有图片附件，则显示一张[no image available](https://thingsgounsaid1.files.wordpress.com/2011/04/no-pic.jpg)的图片），帖子标题作为描述，根据帖子的访问量排序，如果搜索不到对应帖子，返回一条文本信息，提示搜索结果不存在。

## 效果
![alt text](./images/wx-discuz.png)

## 使用
1. 下载 wx.php 和 wxsearch.php，放在discuz网站根目录
2. 修改 wx.php 和 wxsearch.php, 将其中的 yourdomain.com 全部替换成你自己的域名。
3. 修改 wx.php, 将其中的 yourtoken 替换成微信公众号后台设置token （开发-基本配置-服务器配置-令牌Token里设置）
4. 微信公众号后台，开发-基本配置-服务器配置-服务器地址(URL) 设置为：http://<yourdomain.com>/wx.php (yourdomain.com即为你自己discuz网站的域名）

## 补充说明
1. 关于微信公众号后台具体配置，请参考微信官方文档[接入指南](https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421135319)
2. wx.php里提供了微信公众号接入网站的首次Token校验功能（如果已校验成功，可以忽略），使用方法为，将wx.php中的首次验证代码打开，临时关闭搜索功能。
```php
$wechatObj = new WXGZH();
$wechatObj->validToken();
//$wechatObj->responseMsg();
```
等微信公众号Token校验成功之后，将代码还原即可
```php
$wechatObj = new WXGZH();
//$wechatObj->validToken();
$wechatObj->responseMsg();
```

3. wxsearch.php里的论坛附件地址是在DiscuzX3.2下测试的，默认为：http://yourdomain.com/data/attachment/forum/, 如果你的discuz网站附件地址进行过修改，可以进行相应的修改。另外，no image available 的图片地址也可以在此修改。
```php
if($attach){
    $picurl = 'http://yourdomain.come/data/attachment/forum/'.$attach['attachment']; //在此处修改你的网站附件地址
} else {
    $picurl = 'https://thingsgounsaid1.files.wordpress.com/2011/04/no-pic.jpg';  //在此修改no image available 的图片地址
}
```
## 感谢
wx.php部分代码参考了[Ivanlovening/wechat](https://github.com/Ivanlovening/wechat),特此表示感谢

## 版权
Apache license 2.0

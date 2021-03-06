<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"
          name="viewport">
    <link href="/Template/mobile/wuyang/Static/css/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/web.css"/>
    <link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/iconfont.css"/>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/jquery.min.js"></script>
    <script type="text/javascript" src="/Public/js/html2canvas.min.js"></script>
    <script type="text/javascript" src="/Public/js/html2image.js"></script>
    <script src="/Template/mobile/wuyang/Static/js/swiper.jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/web.js"></script>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/layer/layer.js"></script>
    <title>
        分享好友
    </title>
    <style type="text/css">
		.main-container{ min-height:inherit;}
        #savehb {
            z-index: 9;
            width: 100%;
            height: 100%;
            opacity: 0;
            position: absolute;
            left: 0;
            top: 0;
        }
        .text{
            text-align: center;
            font-size: 15px;
			 font-weight:bold;
            color: #fff;
			margin-top:10px;
            width: 100%;
        }
        .text p{
            line-height: 1.8em;
            width: 100%;
            float: left;
            color: #000;
        }
	</style>
</head>

<body>
<div class="main-container">
    <div class="header_list">
        <a href="javascript:window.history.go(-1);" class="go_back">
            <i class="iconfont">&#xe892;</i>
        </a>
        <h3>
            分享好友
        </h3>
    </div>
    <!--<div class="tuiguang_top">-->
    <!--<p>推广链接:</p>-->
    <!--<Div class="lianjie_con clearfixd">-->
    <!--<input type="text"  class="fr_l" id="foo" value="<?php echo ($url); ?>"  />-->
    <!--<button class="btn fr_r" data-clipboard-action="copy" data-clipboard-target="#foo">复制</button>-->
    <!--</Div>-->
    <!--</div>-->

    <div class="tuiguang_con" id="saveHaibao" style="margin-top:45px;">
        <img src="<?php echo ($config['shop_info_share_img']); ?>" id="shb0"/>
        <div class="con">
            <img src="/<?php echo ($fileName); ?>" alt="" id="shb1" style="border: none;"/>
            <div class="text">
                <p>昵称:<?php echo (getSubstr($user["nickname"],0,4)); ?></p>
                <p>邀请码：<?php echo ($user["user_code"]); ?></p>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="/Template/mobile/wuyang/Static/js/clipboard.js"></script>
</div>
<script>

    var clipboard = new Clipboard('.btn');

    clipboard.on('success', function (e) {
//            alert(111); return false;
        layer.msg("复制成功", {icon: 6}, function (index) {

            layer.close(index);

        });

    });
    clipboard.on('error', function (e) {
        console.log(e);
    });
</script>
<script type="text/javascript">
    $(function() {
//        layer.load(1, {
//            icon: 0,
//            shade: [0.5, 'black']
//        });
        $("#shb0,#shb1").attr("crossOrigin", 'anonymous');
        html2canvas($("#saveHaibao")[0], {
            useCORS: true,
            allowTaint: true,
            width: $("#saveHaibao").innerWidth(),
            height: $("#saveHaibao").innerHeight()
        }).then(function(canvas) {
            var canvasimg = Canvas2Image.convertToPNG(canvas, canvas.width, canvas.height);
            var imggg = new Image();
            imggg.id = "savehb";
            imggg.setAttribute("crossOrigin", 'anonymous');
            imggg.src = canvasimg.src;
            $("#saveHaibao").append(imggg);

            $("#savehb").load(function() {
                layer.closeAll('loading');
                $.getScript('/Public/js/mui.min.js', function() {
                    try {
                        mui.init({
                            swipeBack: false,
                            gestureConfig: {
                                longtap: true, //默认为false
                                release: false //默认为false，不监听
                            }
                        });
                        mui.plusReady(function() {
                            document.getElementById("savehb").addEventListener('longtap', function(e) {
                                var buttonTit = [{
                                    title: "保存二维码到相册"
                                }];
                                plus.nativeUI.actionSheet({
                                    cancel: "取消",
                                    buttons: buttonTit
                                }, function(b) {
                                    switch (b.index) {
                                        case 0:
                                            break;
                                        case 1:
                                            getImage();
                                            break;
                                        default:
                                            break;
                                    }
                                });
                            });
                            function getImage() {
                                var bitmap = new plus.nativeObj.Bitmap();
                                var base64 = document.getElementById("savehb").src;
                                bitmap.loadBase64Data(base64, function() {
                                    bitmap.save("_doc/我的专属二维码.jpg", {
                                        overwrite: true,
                                        quality: 100
                                    }, function(i) {
                                        plus.gallery.save(i.target, function() { //保存到相册
                                            plus.io.resolveLocalFileSystemURL(i.target, function(enpty) {
                                                mui.toast('保存成功');
                                            });
                                        });
                                        console.log('保存图片成功：'+JSON.stringify(i));
                                    }, function(e) {
                                        mui.toast('保存失败');
                                        console.log('保存图片失败：' + JSON.stringify(e));
                                    });
                                }, function(e) {
                                    mui.toast('加载图片失败');
                                    //console.log('加载图片失败：' + JSON.stringify(e));
                                });
                            }
                        });
                    } catch (e) {

                    }
                });
            });
        });
    });
</script>
</body>
</html>
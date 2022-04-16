<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta id="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport">
<link href="/Template/mobile/wuyang/Static/css/swiper.min.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/web.css"/>
<link rel="stylesheet" type="text/css" href="/Template/mobile/wuyang/Static/css/iconfont.css"/>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/js/jquery.min.js"></script>
<script src="/Template/mobile/wuyang/Static/js/swiper.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/Template/mobile/wuyang/Static/js/web.js"></script>
<title><?php echo ($detail['title']); ?></title>
</head>

<body>
<div class="main-container">
	<div class="header_list">
    	<a href="javascript:history.back(-1)" class="go_back">
        	<i class="iconfont">&#xe892;</i>
        </a>
        <h3><?php echo ($detail['title']); ?></h3>
    </div>
    <div style="height:45px;"></div>
    <div class="newsxiang_con">
    	<div class="xiangqing_con">
            <!--<div class="title">-->
                <!--<h3><?php echo ($detail['title']); ?></h3>-->
                <!--<div class="left">-->
                    <!--发布时间：<?php echo ($detail['publish_time']); ?>-->
                <!--</div>-->
            <!--</div>-->
            <div class="con">
                <?php echo ($detail['content']); ?>
            </div>
        </div>
    </div>


</body>
</html>
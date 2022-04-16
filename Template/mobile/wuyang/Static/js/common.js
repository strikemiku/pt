var $maskRule = $("#mask-rule"),//规则遮罩层
    $mask = $("#mask"),//红包遮罩层
    $winning = $(".winning"),//红包
    $card = $("#card"),
    $close = $("#close");
    //link = false;//判断是否在链接跳转中

//规则
$(".a1").click(function () {
    $maskRule.show();
});
$("#close-rule").click(function () {
    $maskRule.hide();
});

/*中奖信息提示*/
function win(mark1) {
    //遮罩层显示
    $mask.show();
    var mark2=mark1+1;
	switch(mark2)
   {
    case 1:
       	$("#text1").html("<h3>恭喜您获得</h3>"+
              "<p>20<span>元</span></p>"+
              "<b>话费一张</b>");
        break;
    case 2:
        $("#text1").html("<h3>恭喜您获得</h3>"+
              "<p>300<span>M</span></p>"+
              "<b>流量一张</b>");
        break;
	case 3:
        $("#text1").html("<h3>恭喜您获得</h3>"+
              "<p>500<span>M</span></p>"+
              "<b>流量一张</b>");
        break;
	case 4:
        $("#text1").html("<h3>恭喜您获得</h3>"+
              "<p>50<span>元</span></p>"+
              "<b>话费一张</b>");
        break;
    case 6:
        $("#text1").html("<h3>恭喜您获得</h3>"+
              "<p>100<span>元</span></p>"+
              "<b>话费一张</b>");
        break;
    case 7:
        $("#text1").html("<h3>恭喜您获得</h3>"+
              "<p>1<span>G</span></p>"+
              "<b>流量一张</b>");
        break;
    default:
	  $("#text1").html('<div class="div-h4"><img src="/Template/mobile/wuyang/Static/image/box0-1.png"/></div>'+
              '<b>谢谢参与</b>');
  }
    //关闭弹出层
    $("#close,.win,.btn").click(function () {
    //$close.click(function () {
        $mask.hide();
	    $("#mask2").hide();
        $winning.removeClass("reback");
        $card.removeClass("pull");
    });
    /*$(".win,.btn").click(function () {
        link = true;
    });*/
}

//此处可以在commonjs中合并
function queryString(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.search);
    if(results === null) {
        return "";
    }
    else {
        return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}




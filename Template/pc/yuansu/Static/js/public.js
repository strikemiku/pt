
jQuery(function() {
    //选项卡滑动切换通用
    jQuery(function() { jQuery(".hoverTag .chgBtn").hover(function() { jQuery(this).parent().find(".chgBtn").removeClass("chgCutBtn"); jQuery(this).addClass("chgCutBtn"); var cutNum = jQuery(this).parent().find(".chgBtn").index(this); jQuery(this).parents(".hoverTag").find(".chgCon").hide(); jQuery(this).parents(".hoverTag").find(".chgCon").eq(cutNum).show(); }) })

    //选项卡点击切换通用
    jQuery(function() { jQuery(".clickTag .chgBtn").click(function() { jQuery(this).parent().find(".chgBtn").removeClass("chgCutBtn"); jQuery(this).addClass("chgCutBtn"); var cutNum = jQuery(this).parent().find(".chgBtn").index(this); jQuery(this).parents(".clickTag").find(".chgCon").hide(); jQuery(this).parents(".clickTag").find(".chgCon").eq(cutNum).show(); }) })
    $(".m2c_coder").click(function(e) {
        $("#codeImg").attr("src", $("#codeImg").attr("src") + "&rnd=" + Math.floor(Math.random() * 9999));
    });
    $("#checkAll").click(function(e) { setAllCheckbox($(this), "g1"); });
    $(".fl").click(function(e) {
        $("#codeImg").attr("src", $("#codeImg").attr("src") + "&rnd=" + Math.floor(Math.random() * 9999));
    });
    $(".top_itm li:last").css("border", "none");
    $(".top_wx").hover(function() { $(this).find(".top_wxImg").fadeIn(200) }, function() { $(this).find(".top_wxImg").fadeOut(200) });
    $(".head_a1:first").css("padding-top", 5);
    $(".head_proList li:last").css("border", "none");
    $(".head_itm1").hover(function() { $(this).find(".head_layer1").fadeIn(200) }, function() { $(this).find(".head_layer1").fadeOut(200) });
    $(".head_itm2").hover(function() { $(this).addClass("head_itm2a"); $(".headLine").show(); $(this).find(".head_layer2").fadeIn(200) }, function() { $(this).removeClass("head_itm2a"); $(this).find(".head_layer2").fadeOut(200); $(".headLine").fadeOut(200); });
    $(".subMneu li:even").css("background-color", "#F7F7F7");
    $(".navMenu").hover(function() { $(".subMneu2").slideDown(200) }, function() { $(".subMneu2").slideUp(200) });
    $(".subMneu li").hover(function() {
        $(this).addClass("subCutLi"); $(this).find(".subMenu2").show();
    }, function() {
        $(this).removeClass("subCutLi"); $(this).find(".subMenu2").hide();
    });
    $(".m2c_coder").click(function(e) {
        $("#codeImg").attr("src", $("#codeImg").attr("src") + "&rnd=" + Math.floor(Math.random() * 9999));
    });

    $(".f1").click(function(e) {
        $("#codeImg").attr("src", $("#codeImg").attr("src") + "&rnd=" + Math.floor(Math.random() * 9999));
    });


    $(".m2btn_a2").click(function() { $(this).hide().parents("td").find(".m2btn_a1").show().parents("tr").next("tr").show(); });
    $(".m2btn_a1").click(function() { $(this).hide().parents("td").find(".m2btn_a2").show().parents("tr").next("tr").hide(); });
    $(".m2add1").click(function() {
        $(".table" + $(this).attr("val")).find(".m2pInt").each(function() {
            //    alert($(this).attr("val"));
            ajax_addCarts($(this).attr("val"), $(this).val());
        });

        tag = confirm("产品已加入购物车，是否立即查看购物车？");
        if (tag != "0") window.location.href = "/cart.aspx";
    });

    //	2015-1-20 ma update
    $(".subMenu2 dl dd:first-child").css("background", "none");
    $(".subMneu li").eq(1).find(".subMenu2").css("top", -40);
    $(".subMneu li").eq(2).find(".subMenu2").css("top", -80);
    $(".subMneu li").eq(3).find(".subMenu2").css("top", -120);
    $(".subMneu li").eq(4).find(".subMenu2").css("top", -160);
    $(".subMneu li").eq(5).find(".subMenu2").css("top", -200);
    $(".subMneu li").eq(6).find(".subMenu2").css("top", -240);
    $(".subMneu li").eq(7).find(".subMenu2").css("top", -280);

    $(".m2btn_a2").click(function() { $(this).hide().parents("td").find(".m2btn_a1").show().parents("tr").next("tr").show(); });
    $(".m2btn_a1").click(function() { $(this).hide().parents("td").find(".m2btn_a2").show().parents("tr").next("tr").hide(); });
    //产品图片
    $(".m2a_bigImg").html($(".m2a_smImg li:first").find("span").html());
    $(".m2a_smImg li").click(function() { $(".m2a_bigImg").html($(this).find("span").html()); $(this).addClass("m2a_cutImg").siblings().removeClass("m2a_cutImg"); });

    $(".m2a_tr1:odd").find("td").css("background", "#DCEEFF");
    var lh = $(".m3L").height();
    var rh = $(".m3R").height();
    if (rh > lh) {
        $(".m3L").height(rh);
    }

    $(".m3l_menu:last").css("border", "none");

    $(".m2l_dl dt").click(function() {
        if ($(this).next("dd").css("display") == "block") {
            $(".m2l_dl").removeClass("m2l_cut");
            $(".m2l_dl").removeClass("m2l_cut2");
            $(".m2l_dl dd").slideUp(200);
        } else {
            $(".m2l_dl").removeClass("m2l_cut2"); $(this).parents("dl").addClass("m2l_cut2");
            $(".m2l_dl dd").slideUp(200); $(this).next("dd").slideDown(200);
        }
    });

    $(".m2pInt").change(function() {
        if (isNaN($(this).val())) {
            $(this).val("1");
        }
    });
    $(".m2p_sbtn2").click(function() {
        if (parseInt($(this).parent(".m2pNum").find(".m2pInt").val()) > 0) {
            $(this).parent(".m2pNum").find(".m2pInt").val(parseInt($(this).parent(".m2pNum").find(".m2pInt").val()) - 1);
        }
    });

    $(".m2p_sbtn1").click(function() {

        $(this).parent(".m2pNum").find(".m2pInt").val(parseInt($(this).parent(".m2pNum").find(".m2pInt").val()) + 1);
    });
    $(".ser_int").focus(function() {
        if ($(this).val() == "输入产品编码，CAS变码或关键词") {
            $(this).val("");
            $(this).css("color", "#555");
        }
    });
    $(".ser_int").blur(function() {
        if ($(this).val() == "") {
            $(this).val("输入产品编码，CAS变码或关键词");
            $(this).css("color", "#b3b3b3");
        }
    });

    $("#repw").click(function() {


        var username = $("#username").val();
        var code = $("#code").val();

        $.get("/ajax/password.ashx", {

            username: username,
            code: code
        }, function(data, textStatus) {
            if (textStatus == "success") {

                alert(data);
            }
        });

    });
    $("#order1").click(function() {



        $.get("/ajax/isadmin.ashx", {

    }, function(data, textStatus) {
        if (textStatus == "success") {
            if (data == "1") {
                window.location = "/order.html";
            }
            else {
                window.location = "/login.html";
            }
         
        }
    });

});


$(".m2c_int1").focus(function() {
    if ($(this).val() == "") {
        $(this).addClass("m2c_int1a");
    }
});
$(".m2c_int1").blur(function() {
    if ($(this).val() == "") {
        $(this).removeClass("m2c_int1a");
    }
});
$(".m2c_int2").focus(function() {
    if ($(this).val() == "") {
        $(this).addClass("m2c_int2a");
    }
});
$(".m2c_int2").blur(function() {
    if ($(this).val() == "") {
        $(this).removeClass("m2c_int2a");
    }
});
$(".m2c_int3").focus(function() {
    if ($(this).val() == "验证码") {
        $(this).val("");
        $(this).css("color", "#333");
    }
});
$(".m2c_int3").blur(function() {
    if ($(this).val() == "") {
        $(this).val("验证码");
        $(this).css("color", "#ccc");
    }
});

jQuery(".slideBox").slide({ mainCell: ".bd ul", effect: "leftLoop", autoPlay: true });
jQuery(".mc1box").slide({ titCell: "", mainCell: ".mc1_ul ul", autoPage: true, effect: "left", vis: 1 });
$(".mkf_colseBtn").click(function() { $(".mkf").fadeOut(300) });

$(".mc4_li").hover(function() {
    $(".mc4_li div", this).stop();
    $(this).find("div").animate({ "width": "50px", "height": "50px", "left": "-2px", "top": "-2px" }, 300);
}, function() {
    $(".mc4_li div", this).stop();
    $(this).find("div").animate({ "width": "45px", "height": "45px", "left": "0", "top": "0" }, 300);
});

$(window).scroll(function() {
    if ($(document).scrollTop() > 10) {
        $(".mbackTop").fadeIn(200);
    } else {
        $(".mbackTop").fadeOut(200);
    }
});
$(".mbackTop").click(function() { $("body,html").animate({ "scrollTop": 0 }, 300) });

})
//屏蔽页面错误
jQuery(window).error(function(){
  return true;
});
jQuery("img").error(function(){
  $(this).hide();
});
function check(v)
{
if(v==1)
{
document.getElementById("zenzhi").style.visibility="hidden";
}
else
{
document.getElementById("zenzhi").style.visibility="visible";
}
}
 function loadXMLDoc() {
        
            var code= document.getElementById("code").value;                 
            var name=document.getElementById("username").value;
            var tel=document.getElementById("tel").value;
            var email=document.getElementById("email").value;
            var password=document.getElementById("password").value;  
             var password1=document.getElementById("password1").value;   
             var QQ=document.getElementById("QQ").value;      
            if(!/^[\u4e00-\u9fa50-9A-Za-z]{6,10}$/i.test(name))
            {
           
            document.getElementById("errname").style.visibility="visible";
         
            }           
           else
           {
            document.getElementById("errname").style.visibility="hidden";
           }
         if(!/^[\d]{11}$/i.test(tel))
            {

              document.getElementById("errtel").style.visibility="visible";
             
            }
         else
         {
         document.getElementById("errtel").style.visibility="hidden";
         }
             if(!/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i.test(email))
            {
               document.getElementById("erremail").style.visibility="visible";
             
            }
else
{
document.getElementById("erremail").style.visibility="hidden";
}
        
             if(!/^[\u4e00-\u9fa50-9A-Za-z]{6,16}$/i.test(password))
            {
                document.getElementById("errpassword").style.visibility="visible";
          
            }
            else
            {
              document.getElementById("errpassword").style.visibility="hidden";
            }
if(password!=password1)
{
 document.getElementById("errpassword1").style.visibility="visible";
          
            }
            else
            {
              document.getElementById("errpassword1").style.visibility="hidden";
            }
            if(/^[\u4e00-\u9fa50-9A-Za-z]{6,10}$/i.test(name)&
           /^[\d]{11}$/i.test(tel)&
            /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/i.test(email)&
            /^[\u4e00-\u9fa50-9A-Za-z]{6,16}$/i.test(password)&
            password==password1)
           {  self.location = "/message.ashx?username="+name+"&email="+email+"&tel="+tel+"&password="+password+"&QQ="+QQ+"&code="+code;}
     
           
        }
         function password() {
        
            var password=document.getElementById("password").value;
            var password1=document.getElementById("password1").value;  
             var password2=document.getElementById("password2").value;   
 
        
             if(!/^[\u4e00-\u9fa50-9A-Za-z]{6,16}$/i.test(password1))
            {
               alert("密码6位以上，最好由英文数字组成");
          
            }
            else
            {
              document.getElementById("errpassword").style.visibility="hidden";
            }
if(password1!=password2)
{
alert("两次密码不相同！");
          
            }
            else
            {
              document.getElementById("errpassword1").style.visibility="hidden";
            }
            if( /^[\u4e00-\u9fa50-9A-Za-z]{6,16}$/i.test(password)&
   
            /^[\u4e00-\u9fa50-9A-Za-z]{6,16}$/i.test(password1)&
            password1==password2)
           {  self.location = "/ajax/member.ashx?password="+password1+"&Pwd="+password;}
     
           
        }
 //添加到购物车
function ajax_addCart(pkid, num) {
    if (num.length < 1 ) {
//        alert("请输入要购买的数量");
        return;
    }

    $.get("/ajax/cart.ashx", {
        rnd: getRnd(),
        handle: "add",
        pkid: pkid,
        num: num
    }, function(data, textStatus) {
        if (textStatus == "success") {

        }
    });
}
   //查找质量检测书
function ajax_check(pkid, num) {
    if (num.length < 1 ) {
       alert("请输入型号");
        return;
    }

    $.get("/ajax/check.ashx", {
        rnd: getRnd(),
        pkid: pkid,
        num: num
    }, function(data, textStatus) {
        if (textStatus == "success") {
 $("#xiazai").html(data);
        }
    });
}
        
// JavaScript Document

//网站空间大小
function ajax_getFilesSize(objId) {
    $.get("/ajax/getFilesSize.ashx", {
        rnd: getRnd()
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#" + objId).html(data);
        }
    });
}

//菜单类型下拉列表
function ajax_setDataTypeSelect(objId, fatherId) {
    $.get("/ajax/setDataTypeSelect.ashx", {
        rnd: getRnd(),
        fid: fatherId
    }, function(data, textStatus) {
        if (textStatus == "success") {
            bindSelect(objId, data, null, true);
        }
    });
}

//删除图片
function ajax_delImage(objId, category, pkid) {
    tag = confirm("确实要删除此图片吗？");
    if (tag == "0") return;

    $.get("/ajax/delImage.ashx", {
        rnd: getRnd(),
        category: category,
        pkid: pkid
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#" + objId).attr("src", "/sys/images/noImage.gif");
            alert(data);
        }
    });
}
//生成报价单
function ajax_baojia() {
if($("#spec").val()==""||$("#packing").val()==""||$("#num").val()==0||$("#time").val()==""){alert("请填全信息");return;}
    
    $.get("/ajax/quote.ashx", {
     rnd: Math.floor(Math.random() * 9999),
        spec:$("#spec").val(),
        packing: $("#packing").val(),
        num: $("#num").val(),
        title: $("#title").val(),
          proid: $("#proid").val(),
        time:$("#time").val()
        
       
        
    }, function(data, textStatus) {
        if (textStatus == "success") {
        if(data=="11"){alert("请先登录！");return;}
        if(data=="输入错误"){alert("输入错误！");return;}
             tag = confirm("请求已发出，是否立即查看？");
            if (tag != "0") window.location.href = "/user/quotation.html";

        }
        else
        {
        alert("请先登录！");
        
        }
    });
}
//抢购计时(后台)
function ajax_getBackProTimer(objId, pkid, overMsg) {
    $.get("/ajax/getBackProTimer.ashx", {
        rnd: getRnd(),
        pkid: pkid
    }, function(data, textStatus) {
        if (textStatus == "success") {
            if (data == "0") {
                $("#" + objId).html(overMsg);9
                clearTimeout(timer);
            }
            else
                $("#" + objId).html(data);
        }
    });
}

//顶部登录信息
function ajax_topLogin(objId) {
    $.get("/ajax/topLogin.ashx", {
        rnd: getRnd()
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#" + objId).html(data);
        }
    });
}

//发表留言
function ajax_addMessage(objId, groupName, fkid, realname, tel, email, content, code,cClass,mess) {
    if (realname.length < 1) { alert("请填写您的姓名！"); return; }
    if (realname.length > 10) { alert("请填写正确的姓名！"); return; }
    if (tel.length < 1) { alert("请填写您的电话！"); return; }
    if (email.length < 1) { alert("请填写您的邮箱！"); return; }
    if (content.length < 1) { alert("请填写留言内容！"); return; }
    if (content.length > 400) { alert("留言内容在400字以下！"); return; }
    if (code.length < 1) { alert("请填写图片中显示的验证码！"); return; }
    
     if(!(/^1[3|4|5|8][0-9]\d{4,8}$/.test(tel))){ 
       alert("请填写正确电话！"); return;
        }

    var re = /^([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\-|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
   
    if( re.test(email)==false){ alert("请填写正确邮箱！"); return;}
    
    $.get("/ajax/message.ashx", {
        rnd: getRnd(),
        handle: "add",
        groupName: groupName,
        fkid: fkid,
        realname: escape(realname),
        tel: escape(tel),
        email: escape(email),
        code: code,
        cClass: cClass,
        mess: mess,
        content: escape(content)
    }, function(data, textStatus) {
        if (textStatus == "success") {
            alert("感谢您的留言！");
            ajax_getMessageList(objId, groupName, fkid, 1,cClass,mess);
            $('html,body').animate({ scrollTop: $('#qz_message').offset().top }, 1000);
        }
    });
}

//获取留言列表
function ajax_getMessageList(objId, groupName, fkid, page,cClass,mess) {
    $.get("/ajax/message.aspx", {
        rnd: getRnd(),
        objId: objId,
        groupName: groupName,
        fkid: fkid,
        cClass: cClass,
        mess: mess,
        page: page
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#" + objId).html(data);
        }
    });
}

//发表评论
function ajax_addComment(objId, groupName, fkid, content, point) {
    if (content.length < 1) {
        alert("请填写评价内容");
        return;
    }
    $.get("/ajax/comment.ashx", {
        rnd: getRnd(),
        handle: "add",
        groupName: groupName,
        fkid: fkid,
        content: escape(content),
        point: point
    }, function(data, textStatus) {
        if (textStatus == "success") {
            alert(data);
            ajax_getCommentList(objId, groupName, fkid, 1);
            $('html,body').animate({ scrollTop: $('#qz_comment').offset().top }, 1000);
        }
    });
}

//支持/反对评论
function ajax_apComment(handle, objId, groupName, fkid, pkid, page) {
    $.get("/ajax/comment.ashx", {
        rnd: getRnd(),
        handle: handle,
        pkid: pkid
    }, function(data, textStatus) {
        if (textStatus == "success") {
            alert(data);
            ajax_getCommentList(objId, groupName, fkid, page);
        }
    });
}

//获取评论列表
function ajax_getCommentList(objId, groupName, fkid, page) {
    $.get("/ajax/comment.aspx", {
        rnd: getRnd(),
        objId: objId,
        groupName: groupName,
        fkid: fkid,
        page: page
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#" + objId).html(data);
        }
    });
}

//添加到购物车
function ajax_addCart(pkid, num) {
    if (num.length < 1) {
        alert("请输入要购买的数量");
        return;
    }

    $.get("/ajax/cart.ashx", {
        rnd: getRnd(),
        handle: "add",
        pkid: pkid,
        num: num
    }, function(data, textStatus) {
        if (textStatus == "success") {
            tag = confirm("产品已加入购物车，是否立即查看购物车？");
            if (tag != "0") window.location.href = "/cart.aspx";

            ajax_getCartTotalNum();
            ajax_getTopCartList();
        }
    });
}
//添加到购物车
function ajax_addCarts(pkid, num) {
    if (num.length < 1) {
        alert("请输入要购买的数量");
        return;
    }

    $.get("/ajax/cart.ashx", {
        rnd: getRnd(),
        handle: "add",
        pkid: pkid,
        num: num
    }, function(data, textStatus) {
        if (textStatus == "success") {
         

            ajax_getCartTotalNum();
            ajax_getTopCartList();
        }
    });
}
//更改购物车数量
function ajax_updateCart(inputId, index, num) {
    var inputVal = $("#" + inputId).val();
    if (inputVal.length < 1 ||isNaN(inputVal)) {
        alert("商品数量只能使用数字输入！");
        ajax_getCartList(objId);
        return;
    }

    if (num.length < 1 || isNaN(inputVal)) return;
    num = parseInt(inputVal) + parseInt(num);
    if (num <= 0) return;

    $.get("/ajax/cart.ashx", {
         rnd: getRnd(),
        handle: "update",
        index: index,
        num: num
    }, function(data, textStatus) {
        if (textStatus == "success") {
            ajax_getCartTotalNum();
            ajax_getTopCartList();
            ajax_getCartList();
          
        }
    });
}

//删除购物车中的商品
function ajax_delCart(index) {
 tag = confirm("确实要删除此产品吗？");
    if (tag == "0") return;
    $.get("/ajax/cart.ashx", {
        rnd: getRnd(),
        handle: "del",
        index: index
    }, function(data, textStatus) {
        if (textStatus == "success") {
            ajax_getCartTotalNum();
            ajax_getTopCartList();
            ajax_getCartList();
        }
    });
}

//清空购物车
function ajax_clearCart() {
 tag = confirm("确实要清空购物车吗？");
    if (tag == "0") return;
    $.get("/ajax/cart.ashx", {
        rnd: getRnd(),
        handle: "clear"
    }, function(data, textStatus) {
        if (textStatus == "success") {
            ajax_getCartTotalNum();
            ajax_getTopCartList();
            ajax_getCartList();
        }
    });
}

//获取购物车列表
function ajax_getCartList() {
 
    $.get("/ajax/cart.aspx", {
        rnd: getRnd()
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#cartList").html(data);
           
        }
    });
}

//获取顶部购物车列表
function ajax_getTopCartList() {
    if ($("#topCartList") == null) return;
    $.get("/ajax/cartTop.aspx", {
        rnd: getRnd()
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#topCartList").html(data);
             ajax_getCartTotalNum();
        }
    });
}

//获取购物车总物品数
function ajax_getCartTotalNum() {

    $.get("/ajax/cart.ashx", {
        rnd: getRnd(),
        handle: "count"
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#cartTotalNum").html(data);
        }
    });
}

//加入收藏
function ajax_addFavorite(pkid,obj) {
    $.get("/ajax/favorite.ashx", {
        rnd: getRnd(),
        pkid: pkid
    }, function(data, textStatus) {
        if (textStatus == "success") {

obj.html("<img src='/images/sc_ico1.png' width='15' height='13' />");
        }
        else
        {
        alert("请先登录");
        }
    });
}

//多级联动(无刷新)下拉列表
//参数 mark: 组标识; level: 要填充的<select>的级别; vals: 各级被选中值(可为空，多级值用“,”分隔); headVisible: 是否显示标头;
//select标签的id规则: mark + level. 例如 area1, area2, area3
function ajax_setMultilSelect(mark, level, vals, headVisible) {
    var mainObj = $("#" + mark + (level - 1).toString());
    var subObj = $("#" + mark + level.toString());
    if (subObj.length < 1) return;

    var mainVal = -1;
    if (mainObj.length > 0) mainVal = mainObj.val();

    $.get("/ajax/setMultilSelect.ashx", {
        rnd: getRnd(),
        mark: mark,
        fatherId: mainVal,
        myLevel: level
    }, function(data, textStatus) {
        if (textStatus == "success") {
            var arrVals = new Array();
            if (vals != null) arrVals = vals.split(",");

            bindSelect(subObj.attr("id"), data, arrVals[0], headVisible);

            vals = "";
            for (var i = 1; i < arrVals.length; i++)
                if (arrVals[i].length > 0) vals += arrVals[i] + ",";
            if (vals.length > 0) ajax_setMultilSelect(mark, level + 1, vals, headVisible);
        }
    });
}


//会员注册
function ajax_AddReg(userName, password, password2, rename, email, mTel, code) {

    if (userName.length < 1) { alert("请填写您的用户名！"); return; }
    if (password.length < 1) { alert("请填写您的密码！"); return; }
    if (password2.length < 1) { alert("请填写您的确认密码！"); return; }
    if (rename.length < 1) { alert("请填写您的姓名！"); return; }
    if (email.length < 1) { alert("请填写您的电话！"); return; }
    if (mTel.length < 1) { alert("请填写您的邮箱！"); return; }
    if (password != password2) { alert("两次密码不相同！"); return; }
    if (code.length < 1) { alert("请填写图片中显示的验证码！"); return; }
    if (userName.length < 6) { alert("请填写正确的用户名！"); return; }

    $.get("/ajax/userManage.ashx", {
        rnd: getRnd(),
        handle: "reg",
        userName: escape(userName),
        password: escape(password),
        rename: escape(rename),
        mTel: escape(mTel),
        email: escape(email),
        code: code
    }, function(data, textStatus) {
        if (textStatus == "success") {
            alert(data);
            window.location.href = "index.html";

        }
    });
}

//获取注册页
function ajax_getReg(objId) {
    $.get("/ajax/reg.aspx", {
        rnd: getRnd()
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#" + objId).html(data);
        }
    });
}
//获取注册页
function ajax_getLog(objId) {
    $.get("/ajax/login.aspx", {
        rnd: getRnd()
    }, function(data, textStatus) {
        if (textStatus == "success") {
            $("#" + objId).html(data);
        }
    });
}


//会员注册
function ajax_AddLogin(userName, password, code) {
    if (userName.length < 1) { alert("请填写您的用户名！"); return; }
    if (password.length < 1) { alert("请填写您的密码！"); return; }
    $.get("/ajax/userManage.ashx", {
        rnd: getRnd(),
        handle: "log",
        userName: escape(userName),
        password: escape(password),
        code: code
    }, function(data, textStatus) {
        if (textStatus == "success") {
            alert(data);
            if (data == "登录成功")
                window.location.href = "index.html";
        }
    });
}


//获取常用菜单按钮
function ajax_Code() {
  
            $("#codeImg").attr("src","/admin/ajax/checkCode.ashx?w=50&h=22&f=13");      

      
}
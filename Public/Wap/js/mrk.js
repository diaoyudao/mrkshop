/**
 * 普通ajax表单提交
 * @param {Object} form
 * @param {Object} callback
 * @param {String} confirmMsg 提示确认信息
 */
function validateCallback(form, callback) {
    var $form = $(form);
    if (!$form.valid()) {
        return false;
    }
    var _submitFn = function() {
        $.ajax({
            type: form.method || 'POST',
            url: $form.attr("action"),
            data: $form.serializeArray(),
            dataType: "json",
            cache: false,
            success: callback,
            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                layer.msg("表单错误，" + XMLHttpRequest + thrownError);
            }
        });
    }
    _submitFn();
    return false;
}
function dialogAjaxDone(json) {
    if (json["status"] == 0) {
        if (json["info"]) {
            layer.msg(json["info"]);
        }
    } else if (json["status"] == 301) {
        layer.msg("亲,请登录后操作哦");
    } else if (json["status"] == 1) {
        if (json["url"]) {
            if (json["url"]) {
                layer.msg(json.info);
                window.location = json["url"];
            }
        } else {
            if (json["info"]) {
                layer.msg(json["info"]);
            }
        }
    }
}



//加入收藏夹
function addfavortable(obj, goods_id) {
    var gid = goods_id;
    var url = _FAVORTABLE_URL;//地址

    var uexist = "{:get_username()}";
    if (uexist) {
        var favorid = goods_id;
        $.ajax({
            type: 'post', //传送的方式,get/post
            url: url, //发送数据的地址
            data: {id: gid, types: 2},
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    layer.msg(data.msg);
                } else if (data.status == -1) {
                    layer.msg("<a style='color:red;' href='" + LOGIN_URL + "'>" + data.msg + "</a>");
                } else if (data.status == 0) {
                    layer.msg(data.msg);
                }

//                $("#message").html(data.msg).show();
//                $(obj).after("<i class='hadfavor'></i>" + " <span>" + data.msg + "</span>");
//                $(obj).remove();
//                setTimeout(function() {
//                    $("#message").fadeOut();
//                }, 1000);
//                $(obj).clearQueue();
            }
            ,
            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                alert(XMLHttpRequest + thrownError);
            }
        });
    } else {
        showBg();
    }

}


/***
 * 加入购物车
 * @param {type} goods_id 商品ID
 * @param {type} goods_num　商品数量
 * @param {type} goods_price　商品价格
 * @param {type} parameters　其他参数 
 * @param {type} type　是否组合商品 0 单品
 * @param {type} proid　促销ID 0 没有促销
 * @param {type} store 门店ID 0 平台购买  
 * @returns {undefined}
 */
function addcart(goods_id, goods_num, goods_price, parameters, type,proid,store) {
    var gid = goods_id;
    var url = _CART_URL;//地址
    var gnum = goods_num;//数量
    var gprice = goods_price;//价格 
    var parameters = parameters;//参数 
    var t = type ? type : 0;
    var pro = proid ? proid : 0;
    var store_id = store ? store : 0;
    $.ajax({
        type: 'post', //传送的方式,get/post
        url: url, //发送数据的地址
        data: {
            id: gid, num: gnum, price: gprice, i: parameters, t: t,pro:pro,store_id:store_id
        },
        dataType: "json",
        success: function(data)
        {
            $(".cart_sum").html(data.goodsNum);
            layer.msg(data.msg);
            return false;
        },
        error: function(XMLHttpRequest, ajaxOptions, thrownError) {
            alert(XMLHttpRequest + thrownError);
        }
    });
}

/*立即购买
 * @param {type} goods_id 商品ID
 * @param {type} goods_num　商品数量
 * @param {type} goods_price　商品价格
 * @param {type} parameters　其他参数 
 * @param {type} type　是否组合商品 0 单品
 * @param {type} proid　促销ID 0 没有促销
 * @param {type} store 门店ID 0 平台购买  
 * @returns {undefined}
 */
function now_buy(goods_id, goods_num, goods_price, parameters, type,proid,store,go_url) {
//function now_buy(goods_id, goods_num, goods_price, parameters,proid,type,store,go_url) {
    var gid = goods_id;
    var url = _CART_URL;//地址
    var gnum = goods_num;//数量
    var gprice = goods_price;//价格 
    var parameters = parameters;//参数 
    var t = type ? type : 0;
    var pro = proid ? proid : 0;
    var store_id = store ? store : 0;

    $.ajax({
        type: 'post', //传送的方式,get/post
        url: url, //发送数据的地址
        data: {
            id: gid, num: gnum, price: gprice, i: parameters, t: t, pro:pro,store_id:store_id
        },
        dataType: "json",
        success: function(data)
        {
            window.location.href = go_url;
        },
        error: function(XMLHttpRequest, ajaxOptions, thrownError) {
            alert(XMLHttpRequest + thrownError);
        }
    });
}
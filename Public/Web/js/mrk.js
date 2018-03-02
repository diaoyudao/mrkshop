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



//添加和取消收藏
$(".addfavortable").click(function(){
	//console.log($(this).data('id'));
	var goods_id = $(this).data('id');
	var obj = $(this);
    var url = _FAVORTABLE_URL;//地址
    var uexist = "{:get_username()}";
    if (goods_id == null) {
        layer.msg("选择商品无效!");
        return false;
    }
    if (uexist) {
    	if($(this).hasClass('collected')){
    		 layer.confirm('确定要删除该收藏商品吗?', {icon: 3, title: '提示'}, function(index) {
		        if (index) {
		            $.ajax({
		                type: 'post', //传送的方式,get/post
		                url: delcollect_url, //发送数据的地址
		                data: {id: goods_id},
		                dataType: "json",
		                success: function(data)
		                {
		                	if (data.status == 1) {
		                		layer.msg(data.msg);
		                		$(obj).removeClass('collected');
		                		$(obj).attr('title','收藏商品');
		                    	$(obj).find('span').text('收藏商品('+data.cnum+')');
		                	}else{
		                		layer.msg(data.msg);
		                	}
		                },
		                error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
		                    alert(XMLHttpRequest + thrownError);
		                }
		            });
		        }
		        layer.close(index);
		    });
    	}else{
    		$.ajax({
                type: 'post', //传送的方式,get/post
                url: url, //发送数据的地址
                data: {id: goods_id, types: 2},
                dataType: "json",
                success: function(data) {
                    if (data.status == 1) {
                    	$(obj).addClass('collected');
                    	$(obj).attr('title','取消收藏');
                    	$(obj).find('span').text('已收藏('+data.cnum+')');
                        layer.msg(data.msg);
                    } else if (data.status == -1) {
                        layer.msg("<a style='color:#fff;' href='" + LOGIN_URL + "'>" + data.msg + "</a>");
                    } else if (data.status == 0) {
                        layer.msg(data.msg);
                    }
                },
                error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                    alert(XMLHttpRequest + thrownError);
                }
            });
    	}
    } else {
    	alert('请先登录！');return false;
    }
});

function addfavortable(obj, goods_id) {
    var url = _FAVORTABLE_URL;//地址
    var uexist = "{:get_username()}";
    console.log($(obj).addClass('test'));
    return false;
    if (uexist) {
        $.ajax({
            type: 'post', //传送的方式,get/post
            url: url, //发送数据的地址
            data: {id: goods_id, types: 2},
            dataType: "json",
            success: function(data) {
                if (data.status) {
                    layer.msg(data.msg);
                    window.location.reload();
                } else if (data.status == -1) {
                    layer.msg("<a style='color:#fff;' href='" + LOGIN_URL + "'>" + data.msg + "</a>");
                } else if (data.status == 0) {
                    layer.msg(data.msg);
                }
            },
            error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                alert(XMLHttpRequest + thrownError);
            }
        });
    } else {
    	alert('请先登录！');return false;
    }
}

//取消收藏
function delcollect(aid) {
    layer.confirm('确定要删除该收藏商品吗?', {icon: 3, title: '提示'}, function(index) {
        if (aid == null) {
           layer.msg("选择商品无效!");
           return false;
        }
        if (index) {
            $.ajax({
                type: 'post', //传送的方式,get/post
                url: delcollect_url, //发送数据的地址
                data: {id: aid},
                dataType: "json",
                success: function(data)
                {
                    layer.msg(data.msg);
                    window.location.reload();
                },
                error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                    alert(XMLHttpRequest + thrownError);
                }
            });
        }
        layer.close(index);
    });
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
 * @param {type} store 购买方式 0 平台购买  
 * @returns {undefined}
 */
function addcart(goods_id, goods_num, goods_price, parameters, type,proid,store,is_system) {
    var gid = goods_id;
    var url = _CART_URL;//地址
    var gnum = goods_num;//数量
    var gprice = goods_price;//价格 
    var parameters = parameters;//参数 
    var t = type ? type : 0;
    var pro = proid ? proid : 0;
    var store_id = store ? store : 0;
    var system = is_system ? is_system :0;
    $.ajax({
        type: 'post', //传送的方式,get/post
        url: url, //发送数据的地址
        data: {
            id: gid, num: gnum, price: gprice, i: parameters, t: t,pro:pro,store_id:store_id,is_system:system
        },
        dataType: "json",
        success: function(data)
        {
//            $(".cart_sum").html(data.goodsNum);
            $(".cart_sum").html(data.goodsTotalNum);
           layer.open({
            title: '提示信息',
            type: 1,
          //  shift: 3,
            shadeClose: true, //开启遮罩关闭
            skin: 'layui-layer-mrk', //加上边框
            area: ['440px', '180px'], //宽高
            content: $("#cart_message_box").html()
          });
//            layer.msg(data.msg);
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
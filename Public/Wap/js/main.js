;(function ($) {

})(jQuery)

//修改商品数量
function changeBuyAmountg(countvalue)
{
	var numberui = $('#number').val();
	var number = parseInt(numberui);
	number = number+countvalue;
	if (number <= 0)
	{
		number = 1;
	}
	$('#number').val(number);
	$(".number_div_l").text(number);
}


/* *
 * 添加商品到购物车
 */
function addToCart(goodsId,opendiv, parentId)
{
  var goods        = new Object();
  var spec_arr     = new Array();
  var fittings_arr = new Array();
  var number       = 1;
  var quick		   = 0;
  // 检查是否有商品规格
  if ($("#CM_FORMBUY").length>0)
  {
    spec_arr = getSelectedAttributes();

    if ($('#number').length>0)
    {
      number = $('#number').val();
    }

	quick = 1;
  }
  goods.quick    = quick;
  goods.spec     = spec_arr;
  goods.goods_id = goodsId;
  goods.number   = number;
  goods.parent   = (typeof(parentId) == "undefined") ? 0 : parseInt(parentId);

  $.post('flow.php?step=add_to_cart&opendiv='+opendiv,{goods: $.toJSON(goods)},function(result){
	   if (result.error > 0)
		  {
			// 如果需要缺货登记，跳转
			if (result.error == 2)
			{
			  alert(result.message);
			}
			// 没选规格，弹出属性选择框
			else if (result.error == 6)
			{
			  openSpeDiv(result.message, result.goods_id, result.parent);
			}
			else
			{
			  alert(result.message);
			}
		  }
		  else
		  {
			var cartInfo = document.getElementById('CM_CARTINFO');
			var cart_url = 'flow.php?step=cart';
			if (cartInfo)
			{
			  cartInfo.innerHTML = result.content;
			  $("#CM_CARTINFO").slideDown();
			}
			if (result.one_step_buy == '1')
			{
			  location.href = cart_url;
			}
			else
			{
			  switch(result.confirm_type)
			  {
				case '1' :
				  if (confirm(result.message)) location.href = cart_url;
				  break;
				case '2' :
				  if (!confirm(result.message)) location.href = cart_url;
				  break;
				case '3' :
				  location.href = cart_url;
				  break;
				default :
				  break;
			  }
			}
		  }
	  },'json');
  //Ajax.call('flow.php?step=add_to_cart&opendiv='+opendiv, 'goods=' + $.toJSON(goods), addToCartResponse, 'POST', 'JSON');
}


/**
 * 获得选定的商品属性
 */
function getSelectedAttributes(formBuy)
{
  var spec_arr = new Array();
  var j = 0;
  var input_arr=$("#CM_FORMBUY input,#CM_FORMBUY select")
  for (i = 0; i < input_arr.length; i ++ )
  {
    var prefix = input_arr.eq(i).attr('name').substr(0, 5);

    if (prefix == 'spec_' && (
      ((input_arr.eq(i).attr('type') == 'radio' || input_arr.eq(i).attr('type')  == 'checkbox') && input_arr.eq(i).attr('checked') ) ||
      input_arr.eq(i).tagName == 'SELECT' || input_arr.eq(i).attr('type')== 'hidden' ))
    {

      spec_arr[j] = input_arr.eq(i).val();
      j++ ;
    }
  }

  return spec_arr;
}

/* *
 * 添加商品到收藏夹
 */
function collect(goodsId)
{
  $.getJSON('user.php?act=collect',{id:goodsId},function(rs){
	  alert(rs.message);
  });
}


//生成属性选择层
function openSpeDiv(message, goods_id, parent)
{
  var _id = "speDiv";
  var m = "mask";
  if (docEle(_id)) document.removeChild(docEle(_id));
  if (docEle(m)) document.removeChild(docEle(m));
  //计算上卷元素值
  var scrollPos;
  if (typeof window.pageYOffset != 'undefined')
  {
    scrollPos = window.pageYOffset;
  }
  else if (typeof document.compatMode != 'undefined' && document.compatMode != 'BackCompat')
  {
    scrollPos = document.documentElement.scrollTop;
  }
  else if (typeof document.body != 'undefined')
  {
    scrollPos = document.body.scrollTop;
  }

  var i = 0;
  var sel_obj = document.getElementsByTagName('select');
  while (sel_obj[i])
  {
    sel_obj[i].style.visibility = "hidden";
    i++;
  }

  // 新激活图层
  var newDiv = document.createElement("div");
  newDiv.id = _id;
  newDiv.style.position = "absolute";
  newDiv.style.zIndex = "10000";
  newDiv.style.width = "300px";
  newDiv.style.height = "260px";
  newDiv.style.top = (parseInt(scrollPos + 200)) + "px";
  newDiv.style.left = (parseInt(document.body.offsetWidth) - 200) / 2 + "px"; // 屏幕居中
  newDiv.style.overflow = "auto";
  newDiv.style.background = "#FFF";
  newDiv.style.border = "3px solid #59B0FF";
  newDiv.style.padding = "5px";

  //生成层内内容
  newDiv.innerHTML = '<h4 style="font-size:14; margin:15 0 0 15;">' + select_spe + "</h4>";

  for (var spec = 0; spec < message.length; spec++)
  {
      newDiv.innerHTML += '<hr style="color: #EBEBED; height:1px;"><h6 style="text-align:left; background:#ffffff; margin-left:15px;">' +  message[spec]['name'] + '</h6>';

      if (message[spec]['attr_type'] == 1)
      {
        for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++)
        {
          if (val_arr == 0)
          {
            newDiv.innerHTML += "<input style='margin-left:15px;' type='radio' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' checked /><font color=#555555>" + message[spec]['values'][val_arr]['label'] + '</font> [' + message[spec]['values'][val_arr]['format_price'] + ']</font><br />';
          }
          else
          {
            newDiv.innerHTML += "<input style='margin-left:15px;' type='radio' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' /><font color=#555555>" + message[spec]['values'][val_arr]['label'] + '</font> [' + message[spec]['values'][val_arr]['format_price'] + ']</font><br />';
          }
        }
        newDiv.innerHTML += "<input type='hidden' name='spec_list' value='" + val_arr + "' />";
      }
      else
      {
        for (var val_arr = 0; val_arr < message[spec]['values'].length; val_arr++)
        {
          newDiv.innerHTML += "<input style='margin-left:15px;' type='checkbox' name='spec_" + message[spec]['attr_id'] + "' value='" + message[spec]['values'][val_arr]['id'] + "' id='spec_value_" + message[spec]['values'][val_arr]['id'] + "' /><font color=#555555>" + message[spec]['values'][val_arr]['label'] + ' [' + message[spec]['values'][val_arr]['format_price'] + ']</font><br />';
        }
        newDiv.innerHTML += "<input type='hidden' name='spec_list' value='" + val_arr + "' />";
      }
  }
  newDiv.innerHTML += "<br /><center>[<a href='javascript:submit_div(" + goods_id + "," + parent + ")' class='f6' >" + btn_buy + "</a>]&nbsp;&nbsp;[<a href='javascript:cancel_div()' class='f6' >" + is_cancel + "</a>]</center>";
  document.body.appendChild(newDiv);


  // mask图层
  var newMask = document.createElement("div");
  newMask.id = m;
  newMask.style.position = "absolute";
  newMask.style.zIndex = "9999";
  newMask.style.width = document.body.scrollWidth + "px";
  newMask.style.height = document.body.scrollHeight + "px";
  newMask.style.top = "0px";
  newMask.style.left = "0px";
  newMask.style.background = "#FFF";
  newMask.style.filter = "alpha(opacity=30)";
  newMask.style.opacity = "0.40";
  document.body.appendChild(newMask);
}

function getE(a)
{
	return document.getElementById(a);
}


function clear_s_history()
{
	$.get('./search.php?act=clear',function(rs){
	    	$(".inner").html('');
	});
}

/* *
 * 修改会员信息
 */
function userEdit()
{
  var email = $("form[name=formEdit] input[name=email]").val();
  var msg = '';
  var reg = null;
  var passwd_answer = $("form[name=formEdit] input[name=passwd_answer]").length>0 ?$("form[name=formEdit] input[name=passwd_answer]").val() : '';
  var sel_question =  $("form[name=formEdit] input[name=sel_question]").length>0 ?$("form[name=formEdit] input[name=sel_question]").val() : '';
  if (email.length == 0)
  {
    msg += email_empty + '\n';
  }
  else
  {
    if ( ! (isEmail(email)))
    {
      msg += email_error + '\n';
    }
  }

  if (passwd_answer.length > 0 && sel_question == 0 || $("form[name=formEdit] input[name=passwd_answer]").length>0 && passwd_answer.length == 0)
  {
    msg += no_select_question + '\n';
  }
  var finput=$("form[name=formEdit] input");
  for (i = 7; i <finput.length - 2; i++)	// 从第七项开始循环检查是否为必填项
  {
	needinput = $('#'+finput.eq(i).name + 'i');

	if ($('#'+finput.eq(i).name + 'i').length>0 && ('#'+finput.eq(i).name ).val().length == 0)
	{
	  msg += '- ' + needinput.text() + msg_blank + '\n';
	}
  }

  if (msg.length > 0)
  {
    alert(msg);
    return false;
  }
  else
  {
    return true;
  }
}
isEmail = function( email )
{
  var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;

  return reg1.test( email );
}


/* *
 * 检查收货地址信息表单中填写的内容
 */
function checkConsignee(frm)
{
  var msg = new Array();
  var err = false;

  if (frm.elements['country'] && frm.elements['country'].value == 0)
  {
    msg.push(country_not_null);
    err = true;
  }

  if (frm.elements['province'] && frm.elements['province'].value == 0 && frm.elements['province'].length > 1)
  {
    err = true;
    msg.push(province_not_null);
  }

  if (frm.elements['city'] && frm.elements['city'].value == 0 && frm.elements['city'].length > 1)
  {
    err = true;
    msg.push(city_not_null);
  }

  if (frm.elements['district'] && frm.elements['district'].length > 1)
  {
    if (frm.elements['district'].value == 0)
    {
      err = true;
      msg.push(district_not_null);
    }
  }

  if ($.trim((frm.elements['consignee'].value)).length==0)
  {
    err = true;
    msg.push(consignee_not_null);
  }

  if ( frm.elements['email'] && frm.elements['email'].value.length > 0 && !/^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/.test(frm.elements['email'].value))
  {
    err = true;
    msg.push(invalid_email);
  }

  if (frm.elements['address'] && $.trim((frm.elements['address'].value)).length==0)
  {
    err = true;
    msg.push(address_not_null);
  }

  if (frm.elements['zipcode'] && frm.elements['zipcode'].value.length > 0 && !/^[\d|\.|,]+$/.test(frm.elements['zipcode'].value))
  {
    err = true;
    msg.push(zip_not_num);
  }

  if ($.trim((frm.elements['mobile'].value)).length==0)
  {
    err = true;
    msg.push(mobile_not_null);
  }
  else if (frm.elements['mobile'] &&  (!/^1[0-9]{10}$/.test(frm.elements['mobile'].value)))
  {
    err = true;
    msg.push(mobile_invaild);
  }

  if (err)
  {
    message = msg.join("\n");
    alert(message);
  }
  return ! err;
}

function getScrollTop(){
　　var scrollTop = 0, bodyScrollTop = 0, documentScrollTop = 0;
　　if(document.body){
　　　　bodyScrollTop = document.body.scrollTop;
　　}
　　if(document.documentElement){
　　　　documentScrollTop = document.documentElement.scrollTop;
　　}
　　scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
　　return scrollTop;
}
//文档的总高度
function getScrollHeight(){
　　var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
　　if(document.body){
　　　　bodyScrollHeight = document.body.scrollHeight;
　　}
　　if(document.documentElement){
　　　　documentScrollHeight = document.documentElement.scrollHeight;
　　}
　　scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
　　return scrollHeight;
}
//浏览器视口的高度
function getWindowHeight(){
　　var windowHeight = 0;
　　if(document.compatMode == "CSS1Compat"){
　　　　windowHeight = document.documentElement.clientHeight;
　　}else{
　　　　windowHeight = document.body.clientHeight;
　　}
　　return windowHeight;
}

function can_ajax_load(height){
　　if(getScrollTop() + getWindowHeight()+height > getScrollHeight()){
　　　　return true;
　　}
	return false;
};

function ajax_load_page(url,param,page,conten_id,alert_id){
	$(alert_id).html("加载中····");
	if(page<=param.pages){
		$.get(url,param,function(rs){
			$(conten_id).append(rs);
			if(page>=param.pages)
			{
			$(alert_id).html("已全部加载");
			}else{
				$(alert_id).html("向上滑动继续加载");
			}
		});
	}else{
	$(alert_id).html("已全部加载");
	}
}

ajax_load_page = function (options) {
	var that = this;
	// Default options
	that.options = {
		url: location.href.lastIndexOf("?") == -1 ? location.href.substring((location.href.lastIndexOf("/")) + 1) : location.href.substring((location.href.lastIndexOf("/")) + 1, location.href.lastIndexOf("?")),
		param: {},
		pages:1,
		conten_id: '#conten_id',
		alert_id: '#loding_ajax',
	};
	for (i in options){ that.options[i] = options[i] ;}
	that.conten=$(that.options.conten_id),
	that.alert_c=$(that.options.alert_id);

	that.ajax_load=function(){
			that.alert_c.html("加载中····");
			if(that.options.param.page<that.options.pages){
				that.alert_c.show();
				that.options.param.page++;
				$.get(that.options.url,that.options.param,function(rs){
					that.conten.append(rs);
					if(that.options.param.page>=that.pages)
					{
					that.alert_c.html("已全部加载");
					}else{
					that.alert_c.html("向上滑动继续加载");
					}
				},'html');
			}else{
			that.alert_c.html("已全部加载");
			}
	}

}

function c(obj){
	console.log(obj);
}

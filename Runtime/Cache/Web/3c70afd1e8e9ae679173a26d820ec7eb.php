<?php if (!defined('THINK_PATH')) exit();?><style>
    .add-address{
        margin: 0;
        left: 0;
        top: 0;
    }
    .add-table{width: 620px;}
</style>
<div class="add-address" style="display: block;">
    <div class="add-table">
        <form method="post" name="form" action="<?php echo U('Checkout/save');?>" class="validate" id="addaddress" onsubmit="validateCallback(this);
                return false;">
            <dl><dt><span class="colo6">*</span>收货人：</dt><dd><input type="text" name="realname" id="realname" value="<?php echo ($addressinfo["realname"]); ?>" class="tex2" placeholder="请输入您的名字"></dd></dl>
            <dl><dt><span class="colo6">*</span>所在地址：</dt>
                <dd id="selectaddress">
                    <select name="province" class="prov text2"></select>
                    <select name="city" class="city text2"></select>
                    <select name="area" class="dist text2"></select>
                </dd>
            </dl>
            <dl><dt><span class="colo6">*</span>详细地址：</dt>
                <dd><input type="text" name="address" id="address" value="<?php echo ($addressinfo['address']); ?>" class="tex2" placeholder="街道名称或小区名称" style="width:340px"></dd></dl>
            <dl><dt><span class="colo6">*</span>身份证：</dt><dd><input type="text" name="card_no" value="<?php echo ($addressinfo['card_no']); ?>" class="tex2" placeholder="请输入身份证号码"></dd></dl>
            <dl><dt><span class="colo6">*</span>手机号码：</dt><dd><input type="text" name='cellphone' value="<?php echo ($addressinfo['cellphone']); ?>" class="tex2" placeholder="请输入手机号码"></dd></dl>
            <dl><dt><span class="colo6">*</span>邮政编码：</dt><dd><input type="text" name="youbian" value="<?php echo ($addressinfo['youbian']); ?>" class="tex2" placeholder="请输入邮政编码"></dd></dl>
            <dl class="set"><dd>
                <?php if($addressinfo['isdefault'] == 1): ?><label><input name="isdefault" type="checkbox" checked="checked" class="txt2"><span>设为默认地址</span></label>
                    <?php else: ?>
                    <label> <input  name="isdefault" type="checkbox" class="txt2"><span>设为默认地址</span></label><?php endif; ?>
                </dd></dl>
            <dl class="submi"><dt></dt><dd>
                    <input type="hidden" value="<?php echo ($addressinfo['id']); ?>" name="id" class="text">
                    <input class="save a-btn" type="submit" name="submit-add" id="submit-add" value="确认收货人信息" />
                </dd></dl>
    </div>
</form>
<!--<div class="colse  bg"></div>-->
</div>

    <script src="/Public/Web/js/jquery.validate.js" type="text/javascript"></script>
    <script src="/Public/Web/js/jquery.cityselect.js" type="text/javascript"></script>
    <script>

            function validateCallback(form) {
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
                        success: function(res) {
                            if (res.status) {
                                layer.close(address_layer);
                                layer.msg(res.info);
                            } else {
                                layer.msg(res.info);
                            }
                            myaddresslist();
                            shippinglist(0);
                        },
                        error: function(event, XMLHttpRequest, ajaxOptions, thrownError) {
                            alert("表单错误，" + XMLHttpRequest + thrownError);
                        }
                    });
                }
                _submitFn();
                return false;
            }


            $(function() {
                var prov = "<?php echo ($addressinfo['province']); ?>";
                if (prov) {
                    $("#selectaddress").citySelect({
                        prov: "<?php echo ($addressinfo['province']); ?>",
                        city: "<?php echo ($addressinfo['city']); ?>",
                        nodata: "<?php echo ($addressinfo['area']); ?>"
                    });
                } else {
                    $("#selectaddress").citySelect({
                        prov: "北京",
                        city: "东城区",
                        nodata: "none"
                    });
                }



                $('#addaddress').validate({
                    rules: {
                        realname: {
                            required: true
                        },
                        card_no: {
                            required: true,
                            rangelength: [18, 18]
                        },
                        cellphone: {
                            required: true,
//                            ismobile: true
                            rangelength: [11, 11]
                        },
                        address: {
                            required: true
                        },
                        youbian: {
                            rangelength: [6, 6]
                        }
                    },
                    messages: {
                        realname: {
                            required: '收货人不能为空'
                        },
                        card_no: {
                            required: '身份证不能为空',
                            rangelength: "身份证格式不正确"
                        },
                        cellphone: {
                            required: '手机号码不能为空',
                            rangelength: '手机格式错误'
                        },
                        address: {
                            required: '收货详细地址不能为空'
                        },
                        youbian: {
                            rangelength: '邮编长度为6位数字'
                        }
                    },
                    errorElement: 'i',
                    errorClass: 'ts2',
                    highlight: function(e) {
                        //   $(e).addClass('ts2');
                    },
                    success: function(e) {
                        $(e).removeClass('ts2');
                    }
                });
                jQuery.validator.addMethod('ismobile', function(value, element) {
                    var length = value.length;
                    var mobile = /^(13[0-9]|14[5|7]|15[0|1|2|3|5|6|7|8|9]|18[0-9]|170|177)d{8}$/;
                    return this.optional(element) || (length == 11 && mobile.test(value));
                }, '请正确填写手机号码');
            });

    </script>
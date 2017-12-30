<?php

namespace Wizhi\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * 颜色选择
 */
class SmsInput extends TextInput {

	private $settings = [];

	/**
	 * @param  string|object Html      标签
	 * @param  array         $settings TinyMce 设置
	 */
	public function __construct( $label = null, $settings = [] ) {
		parent::__construct( $label );
		$this->settings = $settings;
	}


	/**
	 * 生成控件 HTML 内容
	 *
	 * @return string
	 */
	public function getControl() {

		$el = parent::getControl();

		$action_id = $this->getHtmlId() . '-action';
		$data_url  = $this->control->getAttribute( 'data-url' );

		$input_group   = Html::el( 'div class=input-group' );
		$action_button = Html::el( 'span class=input-group-btn' )
		                     ->addHtml( Html::el( 'input type=button class="btn btn-primary" id="' . $action_id . '" value="获取验证码"' ) );

		$input_group->addHtml( $el );
		$input_group->addHtml( $action_button );

		$script = "<script>
            jQuery(document).ready(function ($) {
                //timer处理函数
			    var InterValObj; //timer变量，控制时间
			    var count = 60; //间隔函数，1秒执行
			    var curCount;//当前剩余秒数
                
                var action_id= $('#" . $action_id . "');
                
                // 设置倒计时
                function set_count_dwon() {
			        if (curCount === 0) {
			            window.clearInterval(InterValObj);//停止计时器
			            action_id.removeAttr('disabled');//启用按钮
			            action_id.val('重新发送');
			        }
			        else {
			            curCount--;
			            action_id.val(curCount + '后重新获取');
			        }
			    }
                
                action_id.click(function () {
                    $.ajax({
                        type      : 'POST',
                        dataType  : 'json',
                        url       : '" . $data_url . "',
                        data      : {
                            'mobile': $('input[name=mobile]').val()
                        },
                        beforeSend: function () {
                            $(this).addClass('loading');
                        },
                        success   : function (data) {
		                    alert(data.message);
                            if (data.sucees === 1) {
                                // 验证码发送成功后，启动计时器
			                    curCount = count;
			
			                    // 设置button效果，开始计时
			                    action_id.prop('disabled', true);
			                    action_id.val(curCount + '后重新获取');
			                    
	                            InterValObj = window.setInterval(set_count_dwon, 1000); //启动计时器，1秒执行一次
                                $(this).removeClass('loading');
                            }
                        },
                        error     : function (data) {
                            $(this).removeClass('loading');
                            alert('发送失败');
                        }
                    });
                })
            });
        </script>";

		return $input_group . $script;
	}
}

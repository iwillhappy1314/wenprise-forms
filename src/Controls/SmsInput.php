<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * 颜色选择
 */
class SmsInput extends TextInput
{

    private $settings = [];

    public $url;

    /**
     * @param null       $label
     * @param array|null $settings TinyMce 设置
     */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;

        $this->setOption('type', 'sms');
    }


    /**
     * 生成控件 HTML 内容
     *
     * @return \Nette\Utils\Html
     */
    public function getControl(): Html
    {

        $el = parent::getControl();

        $name      = $this->getName();
        $action_id = $this->getHtmlId() . '-action';

        $input_group   = Html::el('div class=rs-input-group');
        $action_button = Html::el('span class=rs-input-group-append')
                             ->addHtml(
                                 Html::el('button type=button')
                                     ->setAttribute('id', $action_id)
                                     ->setAttribute('class', 'rs-btn rs-btn-default')
                                     ->addText(__('Get Code', 'wprs'))
                             );

        $input_group->addHtml($el);
        $input_group->addHtml($action_button);

        $script = "<script>
            jQuery(document).ready(function ($) {
                //timer处理函数
                var InterValObj; //timer 变量，控制时间
                var count = 60; //间隔函数，1秒执行
                var current_count;//当前剩余秒数
                
                var action_id= $('#$action_id');
                
                // 设置倒计时
                function set_count_down() {
                    if (current_count === 0) {
                        window.clearInterval(InterValObj);//停止计时器
                        action_id.removeAttr('disabled');//启用按钮
                        action_id.val('" . __('Get Again', 'wprs') . "');
                    }
                    else {
                        current_count--;
                        action_id.val(current_count + '" . __('Get Again', 'wprs') . "');
                    }
                }
                
                action_id.click(function () {
                    $.ajax({
                        type      : 'POST',
                        dataType  : 'json',
                        url       : '$this->url',
                        data      : {
                            '$name': $('input[name=$name]').val()
                        },
                        beforeSend: function () {
                            $(this).addClass('loading');
                        },
                        success   : function (data) {
                            if (parseInt(data.success) === 1) {
                                // 验证码发送成功后，启动计时器
                                current_count = count;
            
                                // 设置button效果，开始计时
                                action_id.prop('disabled', true);
                                action_id.val(current_count + '" . __('minutes Later Get Again', 'wprs') . "');
                                
                                InterValObj = window.setInterval(set_count_down, 1000); //启动计时器，1秒执行一次
                                $(this).removeClass('loading');
                            }
                        },
                        error     : function (data) {
                            $(this).removeClass('loading');
                            alert('" . __('Send failed', 'wprs') . "');
                        }
                    });
                })
            });
        </script>";

        return $input_group->addHtml($script);
    }


    /**
     * 设置后端 URL
     *
     * @param $url
     *
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }
}

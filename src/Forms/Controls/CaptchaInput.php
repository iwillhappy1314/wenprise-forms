<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * 图形验证码
 */
class CaptchaInput extends TextInput
{

    private $settings = [];

    /**
     * @param  string|object Html      标签
     * @param  array $settings TinyMce 设置
     */
    public function __construct($label = null, $settings = [])
    {
        parent::__construct($label);
        $this->settings = $settings;

        $this->setOption('type', 'captcha');
    }


    /**
     * 生成控件 HTML 内容
     *
     * @return string
     */
    public function getControl()
    {

        $el = parent::getControl();

        $id        = $this->getHtmlId();
        $action_id = $id . '-action';
        $data_url  = $this->control->getAttribute('data-url');

        $script = "<script>
            // 刷新验证码
		    function refresh_code(obj) {
		        obj.src = obj.src + '?code=' + Math.random();
		    }</script>";

        $input_group   = Html::el('div class=rs-input-group');
        $action_button = Html::el('span class=rs-input-group-btn')
                             ->addHtml(
                                 Html::el('img')
                                     ->data('toggle', 'tooltip')
                                     ->setAttribute('id', $action_id)
                                     ->setAttribute('title', __('Click to refresh', 'wprs'))
                                     ->setAttribute('onclick', 'refresh_code(this)')
                                     ->setAttribute('alt', 'Captcha')
                                     ->setAttribute('src', $data_url)
                             );

        $input_group->addHtml($el->setAttribute('class', 'rs-form-control'));
        $input_group->addHtml($action_button);

        return $script . $input_group;
    }
}

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

    public $url = '';

    /**
     * @param string|object Html      标签
     * @param array $settings TinyMce 设置
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
     * @return \Nette\Utils\Html
     */
    public function getControl(): Html
    {

        $el = parent::getControl();

        $id        = $this->getHtmlId();
        $action_id = $id . '-action';

        $script = "<script>
            // 刷新验证码
		    function wprs_refresh_code(obj) {
		        var href = new URL(obj.src);
                href.searchParams.set('code', Math.random());
		        obj.src = href;
		    }</script>";

        $input_group   = Html::el('div class=rs-input-group');
        $action_button = Html::el('span class=rs-input-group-btn')
                             ->addHtml(
                                 Html::el('img')
                                     ->data('toggle', 'tooltip')
                                     ->setAttribute('id', $action_id)
                                     ->setAttribute('class', 'rs-captcha__img')
                                     ->setAttribute('title', __('Click to refresh', 'wprs'))
                                     ->setAttribute('onclick', 'wprs_refresh_code(this)')
                                     ->setAttribute('alt', 'Captcha')
                                     ->setAttribute('src', $this->url)
                             );

        $input_group->addHtml($el->setAttribute('class', 'rs-form-control'));
        $input_group->addHtml($action_button);

        return $input_group->addHtml($script);
    }


    /**
     * Set backend data url
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

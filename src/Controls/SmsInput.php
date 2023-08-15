<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;
use Wenprise\Forms\Helpers;

/**
 * 颜色选择
 */
class SmsInput extends TextInput
{

    private array $settings = [];

    public string $url;

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

        $el       = parent::getControl();
        $settings = $this->settings;

        if ( ! $url = Helpers::data_get($settings, 'url')) {
            $url = $this->url;
        }

        $action_id = $this->getHtmlId() . '-action';

        $settings = [
            'url'            => $url,
            'count'          => 60,
            'get_again_text' => __('Get Again', 'wprs'),
            'countdown_text' => __(' Minutes Later Get Again', 'wprs'),
        ];

        $input_group   = Html::el('div class=rs-input-group');
        $action_button = Html::el('span class=rs-input-group-append')
                             ->addHtml(
                                 Html::el('input type=button')
                                     ->setAttribute('id', $action_id)
                                     ->setAttribute('Value', __('Get Code', 'wprs'))
                                     ->setAttribute('class', 'rs-btn rs-btn-default')
                                     ->addText(__('Get Code', 'wprs'))
                                     ->data('settings', $settings)
                             );


        $input_group->addHtml($el);
        $input_group->addHtml($action_button);

        return $input_group;
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

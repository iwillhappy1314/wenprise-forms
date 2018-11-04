<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;

/**
 * 链式选择输入
 */
class ChainedInput extends BaseControl
{

    private $settings = [];
    private $fields = [];

    /**
     * @param  string|object $label    Html 标签
     * @param  array         $settings TinyMce 设置
     * @param  array         $fields   TinyMce 设置
     */
    public function __construct($label = null, $settings = [], $fields = [])
    {
        parent::__construct($label);
        $this->settings = $settings;
        $this->fields   = $fields;

        $this->setOption('type', 'chained');
    }


    /**
     *  生成 html
     *
     * @return string
     */
    public function getControl()
    {

        $id            = $this->getHtmlId();
        $name          = $this->getHtmlName();
        $settings      = $this->settings;
        $fields        = $this->fields;
        $default_value = $this->value ? $this->value : [];

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('frm-chained');
        }

        $settings_default = [
            'selects'    => $fields,
            'emptyStyle' => 'none',
        ];

        $settings = array_merge($settings_default, $settings);

        $html = '<div id="' . $id . '" class="input-group frm-chained">';

        $i = 0;
        foreach ($fields as $field) {
            $html .= '<select name="' . $name . '" class="form-control ' . $field . '" data-value="' . $default_value[ $i ] . '"></select>';
            $i++;
        }

        $html .= '</div>';

        $html .= "<script>
		    jQuery(document).ready(function($){
		        $('#$id').cxSelect(" . json_encode($settings) . ");
		    });
		</script>";

        return $html;
    }


    /**
     * 获取 HTML 名称
     *
     * @return mixed
     */
    public function getHtmlName()
    {
        return parent::getHtmlName() . '[]';
    }

}
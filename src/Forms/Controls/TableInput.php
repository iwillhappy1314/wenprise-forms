<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;

/**
 * 链式选择输入
 */
class TableInput extends BaseControl
{

    private $settings = [];
    private $fields = [];

    /**
     * @param string|object $label    Html 标签
     * @param array         $settings TinyMce 设置
     * @param array         $fields   TinyMce 设置
     */
    public function __construct($label = null, array $settings = null, array $fields = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;
        $this->fields   = (array)$fields;

        $this->setOption('type', 'table');
    }


    /**
     * Loads HTTP data.
     */
    public function loadHttpData()
    {
        $values = isset($_POST[ $this->getName() ]) ? $_POST[ $this->getName() ] : false;
        $values = array_map(function ($value)
        {
            return array_map('esc_attr', $value);
        }, $values);

        $this->setValue($values);
    }


    /**
     *  生成 html
     *
     * @return string
     */
    public function getControl()
    {

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('wprs-table-input');
        }

        $name          = $this->getName();
        $settings      = $this->settings;
        $fields        = $this->fields;
        $default_value = $this->getValue() ? $this->getValue() : [];

        $default = [
            'caption'          => '',
            'initRows'         => '',
            'rowDragging'      => '',
            'hideRowNumColumn' => '',
            'hideButtons'      => [
                'insert'   => true,
                'moveUp'   => true,
                'moveDown' => true,
            ],
            'buttonClasses'    => [
                'append'     => 'rs-btn rs-btn--sm rs-btn--primary',
                'removeLast' => 'rs-btn rs-btn--sm rs-btn--danger',
            ],
            'columns'          => $fields,
            'initData'         => $default_value,
        ];

        $settings = wp_parse_args($settings, $default);

        $html = Html::el('table id=' . $name);
        $html->setAttribute('class', 'rs-table rs-table-bordered rs-table-input');

        $html .= "<script>
			jQuery(document).ready(function ($) {
			    $('#$name').appendGrid(" . json_encode($settings) . ");
			});
			</script>";

        return $html;
    }

}
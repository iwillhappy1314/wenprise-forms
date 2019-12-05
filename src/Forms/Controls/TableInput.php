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
        $name        = $this->getName();
        $fields      = $this->fields;
        $values      = [];
        $row_numbers = isset($_POST[ $name . '_rowOrder' ]) ? $_POST[ $name . '_rowOrder' ] : false;

        if ( ! empty($row_numbers)) {
            $row_numbers = explode(',', $row_numbers);

            foreach ($row_numbers as $row_number) {
                $vs = [];
                foreach ($fields as $field) {
                    $vs[ $field[ 'name' ] ] = esc_attr($_POST[ $name . '_' . $field[ 'name' ] . '_' . $row_number ]);
                }

                $values[] = $vs;
            }
        }

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

        $name     = $this->getName();
        $settings = $this->settings;
        $fields   = $this->fields;
        $value    = $this->getValue();

        $default = [
            'element'          => $name,
            'caption'          => '',
            'uiFramework'      => 'bootstrap4',
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
            'sectionClasses'   => [
                'table'       => "rs-table",
                'buttonGroup' => "rs-btn-group",
                'control'     => "rs-form-control",
                'append'      => "rs-btn rs-btn-primary",
                'remove'      => "rs-btn rs-btn-default",
                'removeLast'  => "rs-btn rs-btn-default",
            ],
            'columns'          => $fields,
            'initData'         => $value,
        ];

        $settings = wp_parse_args($settings, $default);

        $html = Html::el('table id=' . $name);
        $html->setAttribute('class', 'rs-table rs-table-bordered rs-table-input');

        $html .= "<script>
			document.addEventListener('DOMContentLoaded', function () {
			    window.AppendGrid = new AppendGrid(" . json_encode($settings) . ");
			});
			</script>";

        return $html;
    }

}
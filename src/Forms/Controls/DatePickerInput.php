<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;

/**
 * Multiline text input control.
 */
class DatePickerInput extends TextInput
{

    private $settings = [];

    /**
     * @param  string|object $label    Html 标签
     * @param  array         $settings TinyMce 设置
     */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;

        $this->setOption('type', 'datepicker');
    }


    /**
     * Generates control's HTML element.
     *
     * @return string
     */
    public function getControl()
    {

        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'dateFormat' => 'yy-mm-dd',
        ];

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_style('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-datepicker');
        }

        $settings = array_merge($settings_default, $settings);

        $script = "<script>
		        jQuery(document).ready(function($){
		        	$( '#$id' ).datepicker('" . json_encode($settings) . "');
		        });
		    </script>";

        return $el . $script;
    }
}

<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * DatePicker input control.
 */
class DatePickerInput extends TextInput
{

    private array $settings = [];

    /**
     * @param null       $label    Html 标签
     * @param array|null $settings TinyMce 设置
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
     * @return \Nette\Utils\Html
     */
    public function getControl(): Html
    {
        wp_enqueue_style('wprs-daterangepicker');
        wp_enqueue_script('wprs-daterangepicker');

        $el = parent::getControl();
        $el->setAttribute('autocomplete', 'off');

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            "parentEl"            => 'rs-form--daterangepicker',
            "buttonClasses"       => 'rs-btn rs-btn-sm',
            "applyButtonClasses"  => 'rs-btn-primary',
            "cancelButtonClasses" => 'rs-btn-default',
            "singleDatePicker"    => true,
            "showDropdowns"       => true,
            "minYear"             => 1970,
            "maxYear"             => (int)date("Y", strtotime("+10 year")),
            "locale"              => [
                "format" => "YYYY-MM-DD",
            ],
        ];

        $settings = array_merge($settings_default, $settings);

        $script = "<script>
		        jQuery(document).ready(function($){
		            var defaults = {},
		            options = " . json_encode($settings) . ",
		            settings = $.extend({}, defaults, options);
		            
		        	$( '#$id' ).daterangepicker(settings);
		        });
		    </script>";

	    return Html::fromHtml( $el . $script);
    }
}

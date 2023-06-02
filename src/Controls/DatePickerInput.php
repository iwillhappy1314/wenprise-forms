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
        $el = parent::getControl();
        $el->setAttribute('autocomplete', 'off');

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

        $el->data('settings', json_encode($settings));

	    return $el;
    }
}

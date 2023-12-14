<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * DatePicker input control.
 */
class DateRangePickerInput extends TextInput
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

        $this->setOption('type', 'daterangepicker');
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

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            "parentEl"            => 'rs-form--daterangepicker',
            "buttonClasses"       => 'rs-btn rs-btn-sm',
            "applyButtonClasses"  => 'rs-btn-primary',
            "cancelButtonClasses" => 'rs-btn-default',
            "locale"              => [
                "format"           => "YYYY-MM-DD",
                "applyLabel"       => __('Apply', 'wprs'),
                "cancelLabel"      => __('Cancel', 'wprs'),
                "fromLabel"        => __('From', 'wprs'),
                "toLabel"          => __('To', 'wprs'),
                "customRangeLabel" => __('Custom', 'wprs'),
                "weekLabel"        => __('W', 'wprs'),
                "daysOfWeek"        => [
                    __('Su', 'wprs'),
                    __('Mo', 'wprs'),
                    __('Tu', 'wprs'),
                    __('We', 'wprs'),
                    __('Th', 'wprs'),
                    __('Fr', 'wprs'),
                    __('Sa', 'wprs')
                ],
                "monthNames" => [
                    __('January', 'wprs'),
                    __('February', 'wprs'),
                    __('March', 'wprs'),
                    __('April', 'wprs'),
                    __('May', 'wprs'),
                    __('June', 'wprs'),
                    __('July', 'wprs'),
                    __('August', 'wprs'),
                    __('September', 'wprs'),
                    __('October', 'wprs'),
                    __('November', 'wprs'),
                    __('December', 'wprs'),
                ]
            ],
        ];

        $settings = array_merge($settings_default, $settings);

        $el->data('settings', json_encode($settings));

        return $el;
    }
}

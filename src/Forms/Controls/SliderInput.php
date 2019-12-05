<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextBase;
use Nette\Forms\Form;


/**
 * Slider 输入
 */
class SliderInput extends TextBase
{

    private $settings = [];

    /**
     * Slider Input constructor.
     *
     * @param string|null $label
     * @param array       $settings
     */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->control->type = 'hidden';
        $this->settings      = (array)$settings;

        $this->setOption('type', 'slider');
        $this->addCondition(Form::BLANK);
    }


    /**
     * 生成 HTML 元素
     *
     * @return string
     */
    public function getControl()
    {

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('wprs-ion-rangeslider');
            wp_enqueue_style('wprs-ion-rangeslider');
        }

        $el       = parent::getControl();
        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'type' => 'single',
            'min'  => '0',
            'max'  => '100',
            'grid' => 'false',
        ];

        $settings = array_merge($settings_default, $settings);

        $script = "<script>
	        jQuery(document).ready(function($) {
	            $('#$id').ionRangeSlider('" . json_encode($settings) . "');
	        });
	    </script>";

        return $el . $script;

    }

}

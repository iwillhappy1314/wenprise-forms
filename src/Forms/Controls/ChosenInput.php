<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\SelectBox;

/**
 * Chosen 增加选择
 */
class ChosenInput extends SelectBox
{

    private $settings = [];

    /**
     * @param  string|object $label    标签
     * @param  array         $items    选择项
     * @param  array         $settings Chosen 设置
     */
    public function __construct($label = null, array $items = null, array $settings = null)
    {
        parent::__construct($label, $items);
        $this->settings = (array)$settings;
    }

    public function getControl()
    {

        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'disable_search' => false,
        ];

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_style('wprs-chosen');
            wp_enqueue_script('wprs-chosen');
        }

        $settings = array_merge($settings_default, $settings);

        $script = "<script>
		        jQuery(document).ready(function($){
		        	$( '#$id' ).chosen(" . json_encode($settings) . ");
		        });
		    </script>";

        return $el . $script;

    }

}

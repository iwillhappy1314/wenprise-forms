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
     * @param string|object $label    标签
     * @param array         $items    选择项
     * @param array         $settings Chosen 设置
     */
    public function __construct($label = null, array $items = null, array $settings = null)
    {
        parent::__construct($label, $items);
        $this->settings = (array)$settings;

        $this->setOption('type', 'chosen');
    }

    public function getControl()
    {

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_style('wprs-chosen');
            wp_enqueue_script('wprs-chosen');
        }

        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'disable_search'            => false,
            'placeholder_text_multiple' => __('Select Some Options', 'wprs'),
            'placeholder_text_single'   => __('Select an Option', 'wprs'),
            'no_results_text'           => __('No results match', 'wprs'),
        ];

        $settings = array_merge($settings_default, $settings);

        $script = "<script>
		        jQuery(document).ready(function($){
		        	$( '#$id' ).chosen(" . json_encode($settings) . ");
		        });
		    </script>";

        return $el . $script;

    }

}

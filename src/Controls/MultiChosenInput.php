<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\MultiSelectBox;

/**
 * Chosen 增加选择
 */
class MultiChosenInput extends MultiSelectBox
{

    private $settings = [];

    /**
     * @param null       $label    标签
     * @param array|null $items    选择项
     * @param array|null $settings Chosen 设置
     */
    public function __construct($label = null, array $items = null, array $settings = null)
    {
        parent::__construct($label, $items);
        $this->settings = (array)$settings;

        $this->setOption('type', 'multi-chosen');
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
            'disable_search' => false,
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

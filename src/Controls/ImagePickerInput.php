<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\SelectBox;

/**
 * Chosen 增加选择
 */
class ImagePickerInput extends SelectBox
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

        $this->setOption('type', 'image-picker');
    }

    public function getControl()
    {

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('wprs-image-picker');
        }

        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'hide_select'             => true,
            'show_label'              => false,
        ];

        $settings = array_merge($settings_default, $settings);

        $script = "<script>
		        jQuery(document).ready(function($){
		        	$( '#$id' ).imagepicker(" . json_encode($settings) . ");
		        });
		    </script>";

        return $el . $script;

    }

}

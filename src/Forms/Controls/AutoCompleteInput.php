<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;

/**
 * AutoComplete Input
 */
class AutoCompleteInput extends TextInput
{

    private $settings = [];

    public $source = '';

    /**
     * @param  string|object Html      标签
     * @param  array $settings TinyMce 设置
     */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;

        $this->setOption('type', 'birthday');
    }
    
    /**
     * 生成控件 HTML 内容
     *
     * @return string
     *
     * @todo: 解决和内置 autocomplete 的冲突
     */
    public function getControl()
    {
        if (function_exists('wp_enqueue_script')) {
	        wp_dequeue_script('jquery-ui-autocomplete');
            wp_enqueue_script('wprs-autocomplete');
        }

        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

	    if(is_array($this->source)){
		    $settings_default['lookup'] = $this->source;
	    } else {
		    $settings_default['serviceUrl'] = $this->source;
	    }

        $settings = array_merge($settings_default, $settings);
        $settings = json_encode($settings);

        $script = "<script>
			jQuery(document).ready(function($) {
				$('#$id').devbridgeAutocomplete($settings);
			});
		</script>";

        return $el . $script;
    }


    /**
     * 设置输入提示源
     */
    public function setSource($source){
		$this->source = $source;

		return $this;
    }
}

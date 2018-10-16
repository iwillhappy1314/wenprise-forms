<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;

/**
 * 颜色选择
 */
class BirthdayPickerInput extends TextInput
{

    private $settings = [];

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
     */
    public function getControl()
    {

        $el = parent::getControl();

        $id       = $this->getHtmlId();
        $settings = $this->settings;

        $settings_default = [
            'format'   => 'YYYY-MM-DD',
            'template' => 'YYYY-MM-DD',
            'maxYear'  => date("Y"),
        ];

        $settings = array_merge($settings_default, $settings);

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('wprs-combodate');
        }

        $script = "<script>
			jQuery(document).ready(function($) {
				$('#$id').combodate(" . json_encode($settings) . ");
			});
		</script>";

        return $el . $script;
    }
}

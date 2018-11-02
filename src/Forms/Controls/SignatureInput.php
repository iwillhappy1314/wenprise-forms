<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * Multiline text input control.
 */
class SignatureInput extends TextInput
{

    private $settings = [];

    /**
     * @param  string|object $label    Html 标签
     * @param  array         $settings TinyMce 设置
     */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;

        $this->setOption('type', 'signature');
    }


    /**
     * Generates control's HTML element.
     *
     * @return string
     */
    public function getControl()
    {

        $el = parent::getControl();

        $id       = $this->getHtmlId()->addClass('u-hide');
        $settings = $this->settings;

        $holder = Html::el('div id=' . 'js-' . $id);

        $settings_default = [
            'width'      => '500',
            'height'     => '250',
            'border'     => '#999',
            'background' => '#f3f3f3',
        ];

        if (function_exists('wp_enqueue_script')) {
            wp_enqueue_script('js-signature');
        }

        $settings = array_merge($settings_default, $settings);

        $script = "<script>
		        jQuery(document).ready(function($){
		            var el = $('#$id'),
		                pad = $('#js-$id');
		        	pad.jqSignature(" . json_encode($settings) . ");
		        	pad.on('jq.signature.changed', function() {
		        	  el.val(pad.jqSignature('getDataURL'));
                    });
		        });
		    </script>";

        return $el . $holder . $script;
    }
}

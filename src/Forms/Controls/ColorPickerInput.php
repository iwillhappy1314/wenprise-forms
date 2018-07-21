<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;

/**
 * 颜色选择
 */
class ColorPickerInput extends TextInput
{

	private $settings = [];

	/**
	 * @param  string|object Html      标签
	 * @param  array $settings TinyMce 设置
	 */
	public function __construct( $label = null, $settings = [] )
	{
		parent::__construct( $label );
		$this->settings = $settings;
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
			'palettes' => [ '#125', '#459', '#78b', '#ab0', '#de3', '#f0f' ],
		];

		$settings = array_merge( $settings_default, $settings );

		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'wp-color-picker' );
		}

		$script = "<script>
			jQuery(document).ready(function($) {
				var picker = $('#" . $id . "');
				picker.iris(" . json_encode( $settings ) . ");
				picker.blur(function() {
					setTimeout(function() {
					  if (!$(document.activeElement).closest('.iris-picker').length){
					  	  picker.iris('hide');
					  }else{
					      picker.focus();
					  }
					}, 0);
				});
				picker.focus(function() {
					picker.iris('show');
				});
			});
		</script>";

		return $el . $script;
	}
}

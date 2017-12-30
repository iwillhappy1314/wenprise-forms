<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;

/**
 * Multiline text input control.
 */
class DatePickerInput extends TextInput {

	private $settings = [];

	/**
	 * @param  string|object $label    Html 标签
	 * @param  array         $settings TinyMce 设置
	 */
	public function __construct( $label = null, $settings = [] ) {
		parent::__construct( $label );
		$this->settings = $settings;
	}


	/**
	 * Generates control's HTML element.
	 *
	 * @return string
	 */
	public function getControl() {

		$el = parent::getControl();

		$id       = $this->getHtmlId();
		$settings = $this->settings;

		$settings_default = [
			'dateFormat' => 'yy-mm-dd',
		];

		wp_enqueue_script( 'jquery-ui-datepicker' );

		$settings = wp_parse_args( $settings_default, $settings );

		$script = ' <script>
		        jQuery(document).ready(function($){
		        	$( "#' . $id . '" ).datepicker({
		        		"dateFormat" : "yy-mm-dd"
		        	});
		        });
		    </script>';

		return $el . $script;
	}
}

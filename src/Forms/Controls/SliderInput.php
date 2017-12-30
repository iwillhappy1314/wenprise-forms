<?php

namespace Wizhi\Forms\Controls;

use Nette\Forms\Controls\TextBase;
use Nette\Forms\Form;


/**
 * 克隆输入
 *
 * todo: 优化实现方法
 */
class SliderInput extends TextBase {

	private $args = [];

	/**
	 * Slider Input constructor.
	 *
	 * @param string|null $label
	 * @param array       $args
	 */
	public function __construct( $label = null, array $args ) {
		parent::__construct( $label );
		$this->control->type = 'hidden';
		$this->args          = $args;
		$this->addCondition( Form::BLANK );
	}


	/**
	 * 生成 HTML 元素
	 *
	 * @return string
	 */
	public function getControl() {

		wp_enqueue_script( 'frm-slider' );

		$el   = parent::getControl();
		$id   = $this->getHtmlId();
		$args = $this->args;

		$script = '<script>
	        jQuery(document).ready(function($) {
	            $("#' . $id . '").ionRangeSlider({
				    type: "' . $args[ "type" ] . '",
				    min: ' . $args[ "min" ] . ',
				    max: ' . $args[ "max" ] . ',
				    grid: ' . $args[ "grid" ] . '
				});
	        });
	    </script>';

		return $el . $script;

	}

}

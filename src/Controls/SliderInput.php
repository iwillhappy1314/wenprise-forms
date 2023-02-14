<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextBase;
use Nette\Forms\Form;
use Nette\Utils\Html;


/**
 * Slider 输入
 */
class SliderInput extends TextBase {

	private array $settings = [];

	/**
	 * Slider Input constructor.
	 *
	 * @param null       $label
	 * @param array|null $settings
	 */
	public function __construct( $label = null, array $settings = null ) {
		parent::__construct( $label );
		$this->control->type = 'hidden';
		$this->settings      = (array) $settings;

		$this->setOption( 'type', 'slider' );
		$this->addCondition( Form::BLANK );
	}


	/**
	 * 生成 HTML 元素
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl(): Html {

		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_script( 'wprs-ion-rangeslider' );
			wp_enqueue_style( 'wprs-ion-rangeslider' );
		}

		$el       = parent::getControl();
		$id       = $this->getHtmlId();
		$settings = $this->settings;
		$value    = $this->getValue();
		$values   = explode( ';', $value );

		$settings_default = [
			'type' => 'single',
			'min'  => '0',
			'max'  => '100',
			'grid' => 'false',
		];

		$settings = array_merge( $settings_default, $settings );

		$settings[ 'from' ] = $values[ 0 ];

		if ( $settings[ 'type' ] == 'double' ) {
			$settings[ 'to' ] = $values[ 1 ];
		}

		$script = "<script>
	        jQuery(document).ready(function($) {
	            $('#$id').ionRangeSlider(" . json_encode( $settings ) . ");
	        });
	    </script>";

		return Html::fromHtml( $el . $script );

	}

}

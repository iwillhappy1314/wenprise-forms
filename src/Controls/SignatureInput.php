<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * Multiline text input control.
 */
class SignatureInput extends TextInput {

	private array $settings = [];

	/**
	 * @param null       $label    Html 标签
	 * @param array|null $settings TinyMce 设置
	 */
	public function __construct( $label = null, array $settings = null ) {
		parent::__construct( $label );
		$this->settings = (array) $settings;

		$this->setOption( 'type', 'signature' );
	}


	/**
	 * Generates control's HTML element.
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl(): Html {

		$el = parent::getControl();
		$el->appendAttribute( 'class', 'rs-hide' );

		$id       = $this->getHtmlId();
		$settings = $this->settings;

		$holder = Html::el( 'div class="rs-signature--control"' )
		              ->setAttribute( 'id', "js-$id" )
		              ->addHtml( Html::el( 'span' )
		                             ->setAttribute( 'class', 'rs-btn rs-clear-signature' )
		                             ->setText( __( 'Clear', 'wprs' ) )
		              );

		$settings_default = [
			'width'      => '500',
			'height'     => '250',
			'border'     => '#999',
			'background' => '#f3f3f3',
		];

		$settings = array_merge( $settings_default, $settings );

        $el->data('id', $id);
        $el->data('settings', $settings);

		return Html::fromHtml( $el . $holder );
	}
}

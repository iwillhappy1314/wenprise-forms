<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\MultiSelectBox;
use Nette\Utils\Html;

/**
 * Chosen 增加选择
 */
class MultiChosenInput extends MultiSelectBox {

	private array $settings = [];

	/**
	 * @param null       $label    标签
	 * @param array|null $items    选择项
	 * @param array|null $settings Chosen 设置
	 */
	public function __construct( $label = null, array $items = null, array $settings = null ) {
		parent::__construct( $label, $items );
		$this->settings = (array) $settings;

		$this->setOption( 'type', 'multi-chosen' );
	}

	public function getControl(): Html {

		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_style( 'wprs-chosen' );
			wp_enqueue_script( 'wprs-chosen' );
		}

		$el = parent::getControl();
		$settings = $this->settings;

		$settings_default = [
			'disable_search' => false,
		];

		$settings = array_merge( $settings_default, $settings );

        $el->data('settings', json_encode($settings));

		return Html::fromHtml( $el );

	}

}

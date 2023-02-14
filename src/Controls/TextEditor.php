<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextArea;
use Nette\Utils\Html;


/**
 * WordPress TinyMce 可视化编辑器
 */
class TextEditor extends TextArea {

	private array $settings = [];

	/**
	 * @param null       $label
	 * @param array|null $settings TinyMce 设置
	 */
	public function __construct( $label = null, array $settings = null ) {
		parent::__construct( $label );
		$this->settings = (array) $settings;

		$this->setOption( 'type', 'editor' );
	}


	/**
	 * 生成控件 HTML 内容
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl(): Html {

		$id       = $this->getHtmlId();
		$name     = $this->getHtmlName();
		$settings = $this->settings;

		$default_value = $this->getValue() ? $this->getValue() : '';

		$settings_default = [
			'textarea_name' => $name,
			'teeny'         => true,
			'media_buttons' => false,
		];

		$settings = array_merge( $settings_default, $settings );

		ob_start();
		if ( function_exists( 'wp_editor' ) ) {
			wp_editor( $default_value, $id, $settings );
		}
		$html = ob_get_contents();
		ob_end_clean();

		return Html::fromHtml( $html );
	}
}

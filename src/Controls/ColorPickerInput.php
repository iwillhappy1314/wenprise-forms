<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * 颜色选择
 */
class ColorPickerInput extends TextInput
{

	private array $settings = [];

	/**
	 * @param null       $label
	 * @param array|null $settings TinyMce 设置
	 */
	public function __construct( $label = null, array $settings = null )
	{
		parent::__construct( $label );
		$this->settings = (array) $settings;

        $this->setOption('type', 'color-picker');
	}


    /**
     * 生成控件 HTML 内容
     *
     * @return \Nette\Utils\Html
     */
	public function getControl(): Html
	{
		$el = parent::getControl();
        $el->setAttribute('autocomplete', 'off');

		$id       = $this->getHtmlId();
		$settings = $this->settings;

		$settings_default = [
			'palettes' => [ '#125', '#459', '#78b', '#ab0', '#de3', '#f0f' ],
		];

		$settings = array_merge( $settings_default, $settings );

        $el->data('id', $id);
        $el->data('settings', json_encode($settings));

		return $el;
	}
}

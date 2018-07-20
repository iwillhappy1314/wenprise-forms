<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;
use Wenprise\Forms\Form;

/**
 * 链式选择输入
 */
class TableInput extends BaseControl
{

	private $settings = [];
	private $fields = [];

	/**
	 * @param  string|object $label    Html 标签
	 * @param  array         $settings TinyMce 设置
	 * @param  array         $fields   TinyMce 设置
	 */
	public function __construct( $label = null, $settings = [], $fields = [] )
	{
		parent::__construct( $label );
		$this->settings = $settings;
		$this->fields   = $fields;
	}


	/**
	 * Loads HTTP data.
	 *
	 * @return void
	 */
	public function loadHttpData()
	{
		$this->setValue( $this->getHttpData( Form::DATA_LINE ) );
	}


	/**
	 * Sets control's value.
	 *
	 * @return static
	 * @internal
	 */
	public function setValue( $value )
	{
		$this->value = $value;

		return $this;
	}


	/**
	 *  生成 html
	 *
	 * @return string
	 */
	public function getControl()
	{

		$id            = $this->getHtmlId();
		$name          = str_replace( 'frm-', '', $id );
		$settings      = $this->settings;
		$fields        = $this->fields;
		$default_value = $this->value ? $this->value : [];

		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_script( 'frm-table-input' );
		}

		$default = [
			'caption'          => '',
			'initRows'         => '',
			'rowDragging'      => '',
			'hideRowNumColumn' => '',
			'hideButtons'      => [
				'insert'   => true,
				'moveUp'   => true,
				'moveDown' => true,
			],
			'buttonClasses'    => [
				'append'     => 'btn btn-sm btn-primary',
				'removeLast' => 'btn btn-sm btn-danger',
			],
			'columns'          => $fields,
			'initData'         => $default_value,
		];

		$settings = wp_parse_args( $settings, $default );

		$html          = Html::el( 'table id=' . $name );
		$html->class[] = 'table table-bordered c-table-input';

		$html .= "<script>
			jQuery(document).ready(function ($) {
			    $('#$name').appendGrid(" . json_encode( $settings ) . ");
			});
			</script>";

		return $html;
	}


	/**
	 * 获取 HTML 名称
	 *
	 * @return mixed
	 */
	public function getHtmlName()
	{
		return parent::getHtmlName() . '[]';
	}

}
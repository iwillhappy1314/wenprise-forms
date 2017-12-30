<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;

/**
 * 链式选择输入
 */
class ChainedInput extends BaseControl {

	private $settings = [];
	private $fields = [];

	/**
	 * @param  string|object $label    Html 标签
	 * @param  array         $settings TinyMce 设置
	 * @param  array         $fields   TinyMce 设置
	 */
	public function __construct( $label = null, $settings = [], $fields = [] ) {
		parent::__construct( $label );
		$this->settings = $settings;
		$this->fields   = $fields;
	}


	/**
	 *  生成 html
	 *
	 * @return string
	 */
	public function getControl() {

		$id            = $this->getHtmlId();
		$name          = $this->getHtmlName();
		$settings      = $this->settings;
		$fields        = $this->fields;
		$default_value = $this->value ? $this->value : [];

		wp_enqueue_script( 'frm-chained' );

		$js_fields = '["' . implode( '","', $fields ) . '"]';

		$html = '<div id="' . $id . '" class="input-group frm-chained">';

		$i = 0;
		foreach ( $fields as $field ) {
			$html     .= '<select name="' . $name . '" class="form-control ' . $field . '" data-value="'. $default_value[ $i ]  .'"></select>';
			$i ++;
		}

		$html .= '</div>';

		$html .= "<script>
		    jQuery(document).ready(function($){
		        $('#" . $id . "').cxSelect({
				  url: '" . $settings[ 'url' ] . "',
				  selects: $js_fields,
				  emptyStyle: 'none'
				});
		    });
		</script>";

		return $html;
	}


	/**
	 * 获取 HTML 名称
	 *
	 * @return mixed
	 */
	public function getHtmlName() {
		return parent::getHtmlName() . '[]';
	}

}
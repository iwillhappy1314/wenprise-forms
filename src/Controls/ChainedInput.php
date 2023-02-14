<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;

/**
 * 链式选择输入
 */
class ChainedInput extends BaseControl {

	private array $settings = [];

	private array $fields = [];

	/**
	 * @param null       $label    Html 标签
	 * @param array|null $settings TinyMce 设置
	 * @param array|null $fields   TinyMce 设置
	 */
	public function __construct( $label = null, ?array $settings = [], ?array $fields = [] ) {
		parent::__construct( $label );
		$this->settings = $settings;
		$this->fields   = $fields;

		$this->setOption( 'type', 'chained' );
	}


	/**
	 *  生成 html
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl(): Html {

		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_script( 'frm-chained' );
		}

		$id            = $this->getHtmlId();
		$name          = $this->getHtmlName();
		$settings      = $this->settings;
		$fields        = $this->fields;
		$default_value = $this->getValue() ? $this->getValue() : [];

		$settings_default = [
			'selects'    => $fields,
			'emptyStyle' => 'none',
		];

		$settings = array_merge( $settings_default, $settings );

		$html = Html::el( 'div' )
		            ->setAttribute( 'id', $id )
		            ->setAttribute( 'class', 'input-group frm-chained' );

		$i = 0;
		foreach ( $fields as $field ) {

			$html->addHtml(
				Html::el( 'select class=rs-form-control' )
				    ->appendAttribute( 'class', $field )
				    ->setAttribute( 'name', $name )
				    ->data( 'value', $default_value[ $i ] )
			);

			$i ++;
		}

		$script = "<script>
		    jQuery(document).ready(function($){
		        $('#$id').cxSelect(" . json_encode( $settings ) . ");
		    });
		</script>";

		return Html::fromHtml( $html . $script );
	}


	public function addFields() {

	}


	/**
	 * 获取 HTML 名称
	 *
	 * @return string
	 */
	public function getHtmlName(): string {
		return parent::getHtmlName() . '[]';
	}

}
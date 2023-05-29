<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;

/**
 * AutoComplete Input
 */
class AutoCompleteInput extends TextInput {

	private array $settings = [];

	public string|array $source = '';

	/**
	 * @param null       $label
	 * @param array|null $settings TinyMce 设置
	 */
	public function __construct( $label = null, array $settings = null ) {
		parent::__construct( $label );
		$this->settings = (array) $settings;

		$this->setOption( 'type', 'autocomplete' );
	}

	/**
	 * 生成控件 HTML 内容
	 *
	 * @return \Nette\Utils\Html
	 *
	 * @todo: 解决和内置 autocomplete 的冲突
	 */
	public function getControl(): Html {
		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_dequeue_script( 'jquery-ui-autocomplete' );
		}

		$el = parent::getControl();
		$el->setAttribute( 'class', 'rs-form-control' );

		$id       = $this->getHtmlId();
		$settings = $this->settings;

		if ( is_array( $this->source ) ) {
			$settings_default[ 'lookup' ] = $this->source;
		} else {
			$settings_default[ 'serviceUrl' ] = $this->source;
		}

		$settings = array_merge( $settings_default, $settings );
		$settings = json_encode( $settings );

        $el->data('settings', json_encode($settings));

		$script = "<script>
			jQuery(document).ready(function($) {
				$('#$id').devbridgeAutocomplete($settings);
			});
		</script>";

		return Html::fromHtml( $el . $script );
	}

	/**
	 * 设置输入提示源
	 */
	public function setSource( $source ): static {
		$this->source = $source;

		return $this;
	}
}

<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;
use Nette\Utils\Html;
use Wenprise\Forms\Helpers;

/**
 * 图形验证码
 */
class CaptchaInput extends TextInput {

	private array $settings = [];

	public string $url = '';

	/**
	 * @param string|object $label    Html 标签
	 * @param array         $settings TinyMce 设置
	 */
	public function __construct( $label = null, $settings = [] ) {
		parent::__construct( $label );
		$this->settings = $settings;

		$this->setOption( 'type', 'captcha' );
	}


	/**
	 * 生成控件 HTML 内容
	 *
	 * @return \Nette\Utils\Html
	 */
	public function getControl(): Html {

		$el = parent::getControl();

		$id        = $this->getHtmlId();
		$settings  = $this->settings;
		$action_id = $id . '-action';

		if ( ! $url = Helpers::data_get( $settings, 'url' ) ) {
			$url = $this->url;
		}

		$input_group   = Html::el( 'div class=rs-input-group' );
		$action_button = Html::el( 'span class=rs-input-group-btn' )
		                     ->addHtml(
			                     Html::el( 'img' )
			                         ->data( 'toggle', 'tooltip' )
			                         ->setAttribute( 'id', $action_id )
			                         ->setAttribute( 'class', 'rs-captcha__img' )
			                         ->setAttribute( 'title', __( 'Click to refresh', 'wprs' ) )
			                         ->setAttribute( 'alt', 'Captcha' )
			                         ->setAttribute( 'src', $url )
		                     );

		$input_group->addHtml( $el->setAttribute( 'class', 'rs-form-control' ) );
		$input_group->addHtml( $action_button );

		return $input_group;
	}


	/**
	 * Set backend data url
	 *
	 * @param $url
	 *
	 * @return $this
	 */
	public function setUrl( $url ): static {
		$this->url = $url;

		return $this;
	}
}

<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;

/**
 * Push button control with no default behavior.
 */
class HtmlContent extends BaseControl {

	/**
	 * @param  string|object $caption
     */
	public function __construct( $caption = null ) {
		parent::__construct( $caption );
		$this->control->type = 'html';

		$this->setOption( 'type', 'html' );
	}

	/**
	 * 跳过 label
	 *
	 * @param null $caption
	 *
	 * @return void
	 */
	public function getLabel( $caption = null ): void {
	}
	
	/**
	 * 输出 HTML 内容
	 *
	 * @param object|string|null $caption
     *
	 * @return string
	 */
	public function getControl( $caption = null ): string {
		$this->setOption( 'rendered', true );

		return $this->getCaption();
	}

}
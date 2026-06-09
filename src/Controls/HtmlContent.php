<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Stringable;

/**
 * Push button control with no default behavior.
 */
class HtmlContent extends BaseControl {

	/**
	 * @param  string|object $caption
     */
	public function __construct( ?string $caption = null ) {
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
	public function getLabel(string|Stringable|null $caption = null)
    {
        return null;
    }
	
	/**
	 * 输出 HTML 内容
	 * 
	 * @return string
	 */
	public function getControl(): string {
		$this->setOption( 'rendered', true );

		return (string) $this->getCaption();
	}

}

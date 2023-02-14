<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;


/**
 * Single line text input control.
 */
class GroupInput extends TextInput {

	/**
	 * @var string|null
	 */
	public string|null $prefix;

	/**
	 * @var string|null
	 */
	public string|null $suffix;


	/**
	 * GroupInput constructor.
	 *
	 * @param null        $label     string|object
	 * @param null        $maxLength int
	 * @param string|null $prefix    string|object
	 * @param string|null $suffix    string|object
	 */
	public function __construct( $label = null, $maxLength = null, string $prefix = null, string $suffix = null ) {
		parent::__construct( $label, $maxLength );

		$this->prefix = $prefix;
		$this->suffix = $suffix;

		$this->setOption( 'type', 'group' );
	}


	/**
	 * 设置前缀
	 *
	 * @param string $prefix
	 *
	 * @return $this
	 */
	public function setPrefix( string $prefix ): static {
		$this->prefix = $prefix;

		return $this;
	}


	/**
	 * 设置后缀
	 *
	 * @param string $suffix
	 *
	 * @return $this
	 */
	public function setSuffix( string $suffix ): static {
		$this->suffix = $suffix;

		return $this;
	}


	/**
	 * 获取前缀
	 *
	 * @return string|null
	 */
	public function getPrefix(): ?string {
		return $this->prefix;
	}


	/**
	 * 获取后缀
	 *
	 * @return string|null
	 */
	public function getSuffix(): ?string {
		return $this->suffix;
	}


}

<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\TextInput;


/**
 * Single line text input control.
 */
class GroupInput extends TextInput
{

	/**
	 * @var string
	 */
	public $prefix;

	/**
	 * @var string
	 */
	public $suffix;


	/**
	 * GroupInput constructor.
	 *
	 * @param null   $label     string|object
	 * @param null   $maxLength int
	 * @param string $prefix    string|object
	 * @param string $suffix    string|object
	 */
	public function __construct( $label = null, $maxLength = null, $prefix = null, $suffix = null )
	{
		parent::__construct( $label, $maxLength );

		$this->prefix = $prefix;
		$this->suffix = $suffix;

        $this->setOption('type', 'group');
	}


	/**
	 * 设置前缀
	 *
	 * @param string $prefix
	 *
	 * @return $this
	 */
	public function setPrefix( $prefix )
	{
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
	public function setSuffix( $suffix )
	{
		$this->suffix = $suffix;

		return $this;
	}


}

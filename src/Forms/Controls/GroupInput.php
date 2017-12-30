<?php

namespace Wizhi\Forms\Controls;

use Nette;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Forms\Validator;
use Nette\Forms\Controls\BaseControl;


/**
 * 群组输入，在一行中显示关联的输入组
 *
 * todo: 实现方法需要优化，以便更灵活的实现多种输入控件
 */
class GroupInput extends BaseControl {

	/** validation rule */
	const VALID = ':selectBoxValid';

	/** @var array of option / optgroup */
	private $options = [];

	/** @var mixed */
	private $prompt = false;

	/** @var array */
	private $optionAttributes = [];


	/**
	 * DropdownInput constructor.
	 *
	 * @param string|null $label
	 * @param array|null  $items
	 */
	public function __construct( $label = null, array $items = null ) {
		parent::__construct( $label, $items );
		$this->setOption( 'type', 'text' );
		$this->addCondition( Form::BLANK )
		     ->addRule( [ $this, 'isOk' ], Validator::$messages[ self::VALID ] );
	}


	/**
	 * 加载 HTTP 数据
	 *
	 * @return void
	 */
	public function loadHttpData() {
		$this->setValue( $this->getHttpData( Form::DATA_LINE ) );
	}


	/**
	 * Sets selected item (by key).
	 *
	 * @param  string|int
	 *
	 * @return static
	 * @internal
	 */
	public function setValue( $value ) {
		$this->value = $value;

		return $this;
	}


	/**
	 * 修改控制类型属性
	 *
	 * @param  string
	 *
	 * @return static
	 */
	public function setHtmlType( $type ) {
		return $this->setType( $type );
	}


	/**
	 * setHtmlType 的别名
	 *
	 * Alias for setHtmlType()
	 *
	 * @param  string
	 *
	 * @return static
	 */
	public function setType( $type ) {
		$this->control->type = $type;

		return $this;
	}


	/**
	 * 生成 HTML 元素
	 *
	 * @return string
	 */
	public function getControl() {

		$name        = $this->getHtmlName();
		$id          = $this->getHtmlId();
		$placeholder = $this->control->getAttribute( 'placeholder' );
		$required    = $this->isRequired ? 'required' : '';
		$rules       = json_encode( Helpers::exportRules( $this->rules ) ? : null );

		// 模拟下拉选择默认值
		$default_value = $this->value ? $this->value : [];

		$items = $this->prompt === false ? [] : [ '' => $this->translate( $this->prompt ) ];

		foreach ( $this->options as $key => $value ) {
			$items[ is_array( $value ) ? $this->translate( $key ) : $key ] = $this->translate( $value );
		}

		$html = '<div id="' . $id . '" class="frm-group-input">';
		$html .= '<div class="input-group">
				  <input name="' . $name . '" value="' . $default_value[ 0 ] . '" class="form-control">
			      <span class="input-group-addon">
			      	  <select name="' . $name . '">';
						foreach ( $items[ 'select' ][ 'options' ] as $k => $v ) {
							$selected = ( $default_value[ 1 ] == $k ) ? 'selected' : '';
							$html     .= '<option ' . $selected . ' value="' . $k . '">' . $v . '</option>';
						};
						$html .= '</select>
			      </span>
			    </div>';
		$html .= '</div>';

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


	/**
	 * 设置下拉选项群组
	 *
	 * @return static
	 */
	public function setItems( array $items, $useKeys = true ) {
		if ( ! $useKeys ) {
			$res = [];
			foreach ( $items as $key => $value ) {
				unset( $items[ $key ] );
				if ( is_array( $value ) ) {
					foreach ( $value as $val ) {
						$res[ $key ][ (string) $val ] = $val;
					}
				} else {
					$res[ (string) $value ] = $value;
				}
			}
			$items = $res;
		}
		$this->options = $items;

		return parent::setItems( Nette\Utils\Arrays::flatten( $items, true ) );
	}

	/**
	 * 添加验证规则
	 *
	 * @return static
	 */
	public function addRule( $validator, $errorMessage = null, $arg = null ) {
		if ( $this->control->type === null && in_array( $validator, [ Form::EMAIL, Form::URL, Form::INTEGER ], true ) ) {
			static $types = [ Form::EMAIL => 'email', Form::URL => 'url', Form::INTEGER => 'number' ];
			$this->control->type = $types[ $validator ];

		} elseif ( in_array( $validator, [ Form::MIN, Form::MAX, Form::RANGE ], true )
		           && in_array( $this->control->type, [ 'number', 'range', 'datetime-local', 'datetime', 'date', 'month', 'week', 'time' ], true )
		) {
			if ( $validator === Form::MIN ) {
				$range = [ $arg, null ];
			} elseif ( $validator === Form::MAX ) {
				$range = [ null, $arg ];
			} else {
				$range = $arg;
			}
			if ( isset( $range[ 0 ] ) && is_scalar( $range[ 0 ] ) ) {
				$this->control->min = isset( $this->control->min ) ? max( $this->control->min, $range[ 0 ] ) : $range[ 0 ];
			}
			if ( isset( $range[ 1 ] ) && is_scalar( $range[ 1 ] ) ) {
				$this->control->max = isset( $this->control->max ) ? min( $this->control->max, $range[ 1 ] ) : $range[ 1 ];
			}

		} elseif ( $validator === Form::PATTERN && is_scalar( $arg )
		           && in_array( $this->control->type, [ null, 'text', 'search', 'tel', 'url', 'email', 'password' ], true )
		) {
			$this->control->pattern = $arg;
		}

		return parent::addRule( $validator, $errorMessage, $arg );
	}

	/**
	 * 只要输入不为空，即为验证通过
	 *
	 * @return bool
	 */
	public function isOk() {

		return $this->isDisabled()
		       || $this->getValue() == 0
		       || $this->getValue() !== null;
	}

}

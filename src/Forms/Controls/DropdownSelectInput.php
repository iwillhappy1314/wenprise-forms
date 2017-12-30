<?php

namespace Wizhi\Forms\Controls;

use Nette;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Forms\Validator;
use Nette\Forms\Controls\ChoiceControl;


/**
 * 多级下拉选择输入，以 BootStrap 层级下拉的方式显示 WordPress 分类条目作为虚拟选择框，选择后，设置分类项目 ID 到隐藏的 input 中
 *
 * todo: 添加验证规则，验证规则其实就是 text input 的验证规则
 * todo: 考虑在已选择项目上添加高亮的可能性
 */
class DropdownSelectInput extends ChoiceControl {

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
		$default_value = $this->value ? $this->value : 0;
		$term          = get_term( $default_value );
		if ( $term && ! is_wp_error( $term ) ) {
			$placeholder = $term->name;
		}

		$items = $this->prompt === false ? [] : [ '' => $this->translate( $this->prompt ) ];

		foreach ( $this->options as $key => $value ) {
			$items[ is_array( $value ) ? $this->translate( $key ) : $key ] = $this->translate( $value );
		}

		$html = '<div id="' . $id . '" class="frm-dropdow-input">';
		$html .= '<input class="input-real" type="hidden" ' . $required . ' name="' . $name . '" value="' . $default_value . '" data-nette-rules=\'' . $rules . '\'>';
		$html .= '<div class="input-shadow dropdown">';
		$html .= '<a id="dLabel" data-toggle="dropdown" class="btn btn-default"  data-value="0" href="#"><span class="value-holder">' . $placeholder . ' </span> <span class="caret"></span></a>';
		$html .= '<ul class="dropdown-menu multi-level" role="menu" aria-labelledby="dropdownMenu">';

		unset( $items[ 0 ] );

		foreach ( $items as $k => $v ) {
			if ( is_string( $v ) ) {
				$html .= '<li><a data-value="' . $k . '" href="#">' . $v . '</a></li>';
			} else {
				$parent_kv = explode( '_', $k );
				$html      .= '<li class="dropdown-submenu"><a data-value="' . $parent_kv[ 1 ] . '" href="#">' . $parent_kv[ 0 ] . '</a>';
				$html      .= '<ul class="dropdown-menu">';
				foreach ( $v as $kk => $vv ) {
					$html .= '<li><a data-value="' . $kk . '" href="#">' . $vv . '</a></li>';
				}
				$html .= '</ul>';
				$html .= '</li>';
			}
		}

		$html .= '</ul>';
		$html .= '</div>';
		$html .= '</div>';

		$html .= '<script>
	        jQuery(document).ready(function($){
				$(".input-shadow li a").click(function(){
					var value = $(this).data("value"),
						shadow = $(".input-shadow");
					
					$(".input-real").val(value);
					$(".value-holder").text($(this).text());
					shadow.removeClass("open");
					return false;
				})
	        });
	    </script>';

		return $html;

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

<?php

namespace Wizhi\Forms\Renders;

use Nette;
use Nette\Utils\Html;
use Nette\Utils\IHtmlString;


/**
 * 转到表单到 HTML 输出
 */
class FormRender implements Nette\Forms\IFormRenderer {
	use Nette\SmartObject;

	/**
	 *  /--- form.container
	 *
	 *    /--- error.container
	 *      .... error.item [.class]
	 *    \---
	 *
	 *    /--- hidden.container
	 *      .... HIDDEN CONTROLS
	 *    \---
	 *
	 *    /--- group.container
	 *      .... group.label
	 *      .... group.description
	 *
	 *      /--- controls.container
	 *
	 *        /--- pair.container [.required .optional .odd]
	 *
	 *          /--- label.container
	 *            .... LABEL
	 *            .... label.suffix
	 *            .... label.requiredsuffix
	 *          \---
	 *
	 *          /--- control.container [.odd]
	 *            .... CONTROL [.required .text .password .file .submit .button]
	 *            .... control.requiredsuffix
	 *            .... control.description
	 *            .... control.errorcontainer + control.erroritem
	 *          \---
	 *        \---
	 *      \---
	 *    \---
	 *  \--
	 *
	 * @var array of HTML tags
	 */
	public $wrappers = [
		'form' => [
			'container' => null,
		],

		'error' => [
			'container' => 'ul class=error',
			'item'      => 'li',
		],

		'group' => [
			'container'   => 'fieldset',
			'label'       => 'legend',
			'description' => 'p',
		],

		'controls' => [
			'container' => 'table',
		],

		'pair' => [
			'container' => 'tr',
			'.required' => 'required',
			'.optional' => null,
			'.odd'      => null,
			'.error'    => null,
		],

		'control' => [
			'container' => 'td',
			'.odd'      => null,

			'description'    => 'small',
			'requiredsuffix' => '',
			'errorcontainer' => 'span class=error',
			'erroritem'      => '',

			'.required' => 'required',
			'.text'     => 'text',
			'.password' => 'text',
			'.file'     => 'text',
			'.email'    => 'text',
			'.number'   => 'text',
			'.submit'   => 'button',
			'.image'    => 'imagebutton',
			'.button'   => 'button',
		],

		'label' => [
			'container'      => 'th',
			'suffix'         => null,
			'requiredsuffix' => '',
		],

		'hidden' => [
			'container' => null,
		],
	];

	/** @var Nette\Forms\Form */
	protected $form;

	/** @var int */
	protected $counter;


	/**
	 * Provides complete form rendering.
	 *
	 * @param  $form Nette\Forms\Form
	 * @param  string 'begin', 'errors', 'ownerrors', 'body', 'end' or empty to render all
	 *
	 * @return string
	 */
	public function render( Nette\Forms\Form $form, $mode = null ) {
		if ( $this->form !== $form ) {
			$this->form = $form;
		}

		$s = '';
		if ( ! $mode || $mode === 'begin' ) {
			$s .= $this->renderBegin();
		}
		if ( ! $mode || strtolower( $mode ) === 'ownerrors' ) {
			$s .= $this->renderErrors();

		} elseif ( $mode === 'errors' ) {
			$s .= $this->renderErrors( null, false );
		}
		if ( ! $mode || $mode === 'body' ) {
			$s .= $this->renderBody();
		}
		if ( ! $mode || $mode === 'end' ) {
			$s .= $this->renderEnd();
		}

		return $s;
	}


	/**
	 * 开始渲染表单
	 *
	 * @return string
	 */
	public function renderBegin() {
		$this->counter = 0;

		foreach ( $this->form->getControls() as $control ) {
			$control->setOption( 'rendered', false );
		}

		if ( $this->form->isMethod( 'get' ) ) {
			$el         = clone $this->form->getElementPrototype();
			$query      = parse_url( $el->action, PHP_URL_QUERY );
			$el->action = str_replace( "?$query", '', $el->action );
			$s          = '';
			foreach ( preg_split( '#[;&]#', $query, - 1, PREG_SPLIT_NO_EMPTY ) as $param ) {
				$parts = explode( '=', $param, 2 );
				$name  = urldecode( $parts[ 0 ] );
				if ( ! isset( $this->form[ $name ] ) ) {
					$s .= Html::el( 'input', [ 'type' => 'hidden', 'name' => $name, 'value' => urldecode( $parts[ 1 ] ) ] );
				}
			}

			return $el->startTag() . ( $s ? "\n\t" . $this->getWrapper( 'hidden container' )->setHtml( $s ) : '' );

		} else {
			return $this->form->getElementPrototype()->startTag();
		}
	}


	/**
	 * 结束渲染表单
	 *
	 * @return string
	 */
	public function renderEnd() {
		$s = '';
		foreach ( $this->form->getControls() as $control ) {
			if ( $control->getOption( 'type' ) === 'hidden' && ! $control->getOption( 'rendered' ) ) {
				$s .= $control->getControl();
			}
		}
		if ( iterator_count( $this->form->getComponents( true, Nette\Forms\Controls\TextInput::class ) ) < 2 ) {
			$s .= '<!--[if IE]><input type=IEbug disabled style="display:none"><![endif]-->';
		}
		if ( $s ) {
			$s = $this->getWrapper( 'hidden container' )->setHtml( $s ) . "\n";
		}

		return $s . $this->form->getElementPrototype()->endTag() . "\n";
	}


	/**
	 * 渲染验证错误，每个表单或每个控件
	 *
	 * @return string
	 */
	public function renderErrors( Nette\Forms\IControl $control = null, $own = true ) {
		$translator = $control
			? ( $control instanceof \Nette\Forms\Controls\BaseControl ? $control->getTranslator() : null )
			: $this->form->getTranslator();

		$errors = $control
			? $control->getErrors()
			: ( $own ? $this->form->getOwnErrors() : $this->form->getErrors() );
		if ( ! $errors ) {
			return '';
		}
		$container = $this->getWrapper( $control ? 'control errorcontainer' : 'error container' );
		$item      = $this->getWrapper( $control ? 'control erroritem' : 'error item' );

		foreach ( $errors as $error ) {
			$item = clone $item;
			if ( $error instanceof IHtmlString ) {
				$item->addHtml( $error );
			} else {
				$item->setText( $translator ? $translator->translate( $error ) : $error );
			}
			$container->addHtml( $item );
		}

		return "\n" . $container->render( $control ? 1 : 0 );
	}


	/**
	 * 渲染表单主体
	 *
	 * @return string
	 */
	public function renderBody() {
		$s = $remains = '';

		$defaultContainer = $this->getWrapper( 'group container' );
		$translator       = $this->form->getTranslator();

		foreach ( $this->form->getGroups() as $group ) {
			if ( ! $group->getControls() || ! $group->getOption( 'visual' ) ) {
				continue;
			}

			$container = $group->getOption( 'container', $defaultContainer );
			$container = $container instanceof Html ? clone $container : Html::el( $container );

			$id = $group->getOption( 'id' );
			if ( $id ) {
				$container->id = $id;
			}

			$s .= "\n" . $container->startTag();

			$text = $group->getOption( 'label' );
			if ( $text instanceof IHtmlString ) {
				$s .= $this->getWrapper( 'group label' )->addHtml( $text );

			} elseif ( $text != null ) { // intentionally ==
				if ( $translator !== null ) {
					$text = $translator->translate( $text );
				}
				$s .= "\n" . $this->getWrapper( 'group label' )->setText( $text ) . "\n";
			}

			$text = $group->getOption( 'description' );
			if ( $text instanceof IHtmlString ) {
				$s .= $text;

			} elseif ( $text != null ) { // intentionally ==
				if ( $translator !== null ) {
					$text = $translator->translate( $text );
				}
				$s .= $this->getWrapper( 'group description' )->setText( $text ) . "\n";
			}

			$s .= $this->renderControls( $group );

			$remains = $container->endTag() . "\n" . $remains;
			if ( ! $group->getOption( 'embedNext' ) ) {
				$s       .= $remains;
				$remains = '';
			}
		}

		$s .= $remains . $this->renderControls( $this->form );

		$container = $this->getWrapper( 'form container' );
		$container->setHtml( $s );

		return $container->render( 0 );
	}


	/**
	 * 渲染控件组
	 *
	 * @param  Nette\Forms\Container|Nette\Forms\ControlGroup
	 *
	 * @return string
	 */
	public function renderControls( $parent ) {
		if ( ! ( $parent instanceof Nette\Forms\Container || $parent instanceof Nette\Forms\ControlGroup ) ) {
			throw new Nette\InvalidArgumentException( 'Argument must be Nette\Forms\Container or Nette\Forms\ControlGroup instance.' );
		}

		$container = $this->getWrapper( 'controls container' );

		$buttons = null;
		foreach ( $parent->getControls() as $control ) {
			if ( $control->getOption( 'rendered' ) || $control->getOption( 'type' ) === 'hidden' || $control->getForm( false ) !== $this->form ) {
				// skip

			} elseif ( $control->getOption( 'type' ) === 'button' || $control->getOption( 'type' ) === 'html' ) {
				$buttons[] = $control;

			} else {
				if ( $buttons ) {
					$container->addHtml( $this->renderPairMulti( $buttons ) );
					$buttons = null;
				}
				$container->addHtml( $this->renderPair( $control ) );
			}
		}

		if ( $buttons ) {
			$container->addHtml( $this->renderPairMulti( $buttons ) );
		}

		$s = '';
		if ( count( $container ) ) {
			$s .= "\n" . $container . "\n";
		}

		return $s;
	}


	/**
	 * 渲染一行
	 *
	 * @return string
	 */
	public function renderPair( Nette\Forms\IControl $control ) {
		$pair = $this->getWrapper( 'pair container' );
		$pair->addHtml( $this->renderLabel( $control ) );
		$pair->addHtml( $this->renderControl( $control ) );
		$pair->class( $this->getValue( $control->isRequired() ? 'pair .required' : 'pair .optional' ), true );
		$pair->class( $control->hasErrors() ? $this->getValue( 'pair .error' ) : null, true );
		$pair->class( $control->getOption( 'class' ), true );
		if ( ++ $this->counter % 2 ) {
			$pair->class( $this->getValue( 'pair .odd' ), true );
		}
		$pair->id = $control->getOption( 'id' );

		return $pair->render( 0 );
	}


	/**
	 * 在一行中渲染多个控件
	 *
	 * @param  Nette\Forms\IControl[]
	 *
	 * @return string
	 */
	public function renderPairMulti( array $controls ) {
		$s = [];
		foreach ( $controls as $control ) {
			if ( ! $control instanceof Nette\Forms\IControl ) {
				throw new Nette\InvalidArgumentException( 'Argument must be array of Nette\Forms\IControl instances.' );
			}
			$description = $control->getOption( 'description' );
			if ( $description instanceof IHtmlString ) {
				$description = ' ' . $description;

			} elseif ( $description != null ) { // intentionally ==
				if ( $control instanceof Nette\Forms\Controls\BaseControl ) {
					$description = $control->translate( $description );
				}
				$description = ' ' . $this->getWrapper( 'control description' )->setText( $description );

			} else {
				$description = '';
			}

			$control->setOption( 'rendered', true );
			$el = $control->getControl();
			if ( $el instanceof Html && $el->getName() === 'input' ) {
				$el->class( $this->getValue( "control .$el->type" ), true );
			}
			$s[] = $el . $description;
		}
		$pair = $this->getWrapper( 'pair container' );
		$pair->addHtml( $this->renderLabel( $control ) );
		$pair->class( $control->getOption( 'class' ), true );
		$pair->addHtml( $this->getWrapper( 'control container' )->setHtml( implode( ' ', $s ) ) );

		return $pair->render( 0 );
	}


	/**
	 * 渲染控件的 Label 标签
	 *
	 * @return Html
	 */
	public function renderLabel( Nette\Forms\IControl $control ) {
		$suffix = $this->getValue( 'label suffix' ) . ( $control->isRequired() ? $this->getValue( 'label requiredsuffix' ) : '' );
		$label  = $control->getLabel();
		if ( $label instanceof Html ) {
			$label->addHtml( $suffix );
			if ( $control->isRequired() ) {
				$label->class( $this->getValue( 'control .required' ), true );
			}
		} elseif ( $label != null ) { // @intentionally ==
			$label .= $suffix;
		}

		return $this->getWrapper( 'label container' )->setHtml( $label );
	}


	/**
	 * 渲染控件的空间部分
	 *
	 * @return Html
	 */
	public function renderControl( Nette\Forms\IControl $control ) {
		$body = $this->getWrapper( 'control container' );
		if ( $this->counter % 2 ) {
			$body->class( $this->getValue( 'control .odd' ), true );
		}

		$description = $control->getOption( 'description' );
		if ( $description instanceof IHtmlString ) {
			$description = ' ' . $description;

		} elseif ( $description != null ) { // intentionally ==
			if ( $control instanceof Nette\Forms\Controls\BaseControl ) {
				$description = $control->translate( $description );
			}
			$description = ' ' . $this->getWrapper( 'control description' )->setText( $description );

		} else {
			$description = '';
		}

		if ( $control->isRequired() ) {
			$description = $this->getValue( 'control requiredsuffix' ) . $description;
		}

		$control->setOption( 'rendered', true );
		$el = $control->getControl();
		if ( $el instanceof Html && $el->getName() === 'input' ) {
			$el->class( $this->getValue( "control .$el->type" ), true );
		}

		return $body->setHtml( $el . $description . $this->renderErrors( $control ) );
	}


	/**
	 * 获取外层包含
	 *
	 * @param  string
	 *
	 * @return Html
	 */
	protected function getWrapper( $name ) {
		$data = $this->getValue( $name );

		return $data instanceof Html ? clone $data : Html::el( $data );
	}


	/**
	 * 获取值
	 *
	 * @param  string
	 *
	 * @return mixed
	 */
	protected function getValue( $name ) {
		$name = explode( ' ', $name );
		$data = &$this->wrappers[ $name[ 0 ] ][ $name[ 1 ] ];

		return $data;
	}
}

<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\Utils\Html;
use Nette\Utils\IHtmlString;

/**
 * 转到表单到 HTML 输出
 */
class FormRender extends Nette\Forms\Rendering\DefaultFormRenderer
{
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
            '.addon'    => 'rs-input-group',
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
            '.submit'   => 'rs-btn rs-btn-default',
            '.image'    => 'rs-btn--image',
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


    /**
     * 渲染控件组
     *
     * @param Nette\Forms\Container|Nette\Forms\ControlGroup
     *
     * @return string
     */
    public function renderControls($parent)
    {
        if ( ! ($parent instanceof Nette\Forms\Container || $parent instanceof Nette\Forms\ControlGroup)) {
            throw new Nette\InvalidArgumentException('Argument must be Nette\Forms\Container or Nette\Forms\ControlGroup instance.');
        }

        $container = $this->getWrapper('controls container');

        $buttons = null;
        foreach ($parent->getControls() as $control) {
            if ($control->getOption('rendered') || $control->getOption('type') === 'hidden' || $control->getForm(false) !== $this->form) {
                // skip

                // add Html type
            } elseif ($control->getOption('type') === 'button' || $control->getOption('type') === 'html') {
                $buttons[] = $control;

            } else {
                if ($buttons) {
                    $container->addHtml($this->renderPairMulti($buttons));
                    $buttons = null;
                }
                $container->addHtml($this->renderPair($control));
            }
        }

        if ($buttons) {
            $container->addHtml($this->renderPairMulti($buttons));
        }

        $s = '';
        if (count($container)) {
            $s .= "\n" . $container . "\n";
        }

        return $s;
    }


    /**
     * 渲染一行
     *
     * @param \Nette\Forms\IControl $control
     *
     * @return string
     */
    public function renderPair(Nette\Forms\IControl $control)
    {
        $pair = $this->getWrapper('pair container');
        $pair->addHtml($this->renderLabel($control));

        if (++$this->counter % 2) {
            $pair->class($this->getValue('pair .odd'), true);
        }

        $pair->id = $control->getOption('id');

        // Add prefix and suffix
        if (isset($control->prefix) || isset($control->suffix)) {

            $prefix = $control->prefix;
            $suffix = $control->suffix;

            // 群组 wrap
            $group_parent = $this->getWrapper('control container');

            // 群组 HTML
            $group = Html::el('div')
                         ->setAttribute('class', [$this->getValue('pair .addon')]);

            // 前缀
            if (isset($prefix)) {

                if (is_object($prefix)) {
                    $prefix_html = $prefix->getControl();
                } else {
                    $prefix_html = Html::el('span class=rs-input-group-text')
                                       ->addHtml($prefix);
                }

                $group->addHtml(
                    Html::el('div class=rs-input-group-prepend')
                        ->addHtml($prefix_html)
                );
            }

            // 中间
            $group->addHtml($this->renderControl($control->setAttribute('class', 'rs-form-control'))
                                 ->getChildren()[ 0 ]);

            // 后缀
            if (isset($suffix)) {

                if (is_object($suffix)) {
                    $suffix_html = $suffix->getControl();
                } else {
                    $suffix_html = Html::el('span class=rs-input-group-text')
                                       ->addHtml($suffix);
                }

                $group->addHtml(
                    Html::el('div class=rs-input-group-append')
                        ->addHtml($suffix_html)
                );
            }

            // 前缀后缀组
            $pair->addHtml($group_parent->addHtml($group));

        } else {

            $pair->addHtml($this->renderControl($control));

        }

        $pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), true);
        $pair->class($control->hasErrors() ? $this->getValue('pair .error') : null, true);
        $pair->class($control->getOption('class'), true);

        return $pair->render(0);
    }


    /**
     * 在一行中渲染多个控件
     *
     * @param Nette\Forms\IControl[]
     *
     * @return string
     */
    public function renderPairMulti(array $controls)
    {
        $s = [];
        foreach ($controls as $control) {
            if ( ! $control instanceof Nette\Forms\IControl) {
                throw new Nette\InvalidArgumentException('Argument must be array of Nette\Forms\IControl instances.');
            }
            $description = $control->getOption('description');
            if ($description instanceof IHtmlString) {
                $description = ' ' . $description;

            } elseif ($description != null) { // intentionally ==
                if ($control instanceof Nette\Forms\Controls\BaseControl) {
                    $description = $control->translate($description);
                }
                $description = ' ' . $this->getWrapper('control description')
                                          ->setText($description);

            } else {
                $description = '';
            }

            $control->setOption('rendered', true);
            $el = $control->getControl();
            if ($el instanceof Html && $el->getName() === 'input') {
                $el->class($this->getValue("control .$el->type"), true);
            }
            $s[] = $el . $description;
        }

        $pair = $this->getWrapper('pair container');
        $pair->addHtml($this->renderLabel($control));

        // wrapper class
        $pair->class($control->getOption('class'), true);
        $pair->addHtml($this->getWrapper('control container')
                            ->setHtml(implode(' ', $s)));

        return $pair->render(0);
    }


}

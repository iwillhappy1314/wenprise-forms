<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\Utils\Html;
use Nette\HtmlStringable;

/**
 * 转到表单到 HTML 输出
 */
class BaseFormRender extends Nette\Forms\Rendering\DefaultFormRenderer
{
    use Nette\SmartObject;

    var $layout = 'horizontal';

    public function __construct($type = 'horizontal')
    {
        $this->layout = $type;

        $this->wrappers[ 'form' ][ 'container' ] = "div class=rs-form--$this->layout";

        $this->wrappers[ 'control' ][ '.submit' ]        = 'rs-btn rs-btn-primary';
        $this->wrappers[ 'control' ][ '.image' ]         = 'rs-btn--image';
        $this->wrappers[ 'control' ][ '.required' ]      = 'rs-required';
        $this->wrappers[ 'control' ][ 'description' ]    = 'span class=rs-help-block';
        $this->wrappers[ 'control' ][ 'errorcontainer' ] = 'span class=rs-has-error';

        $this->wrappers[ 'pair' ][ '.required' ] = 'rs-form--required';
        $this->wrappers[ 'pair' ][ '.error' ]    = 'rs-has-error';
        $this->wrappers[ 'pair' ][ '.addon' ]    = 'rs-input-group';

        $this->wrappers[ 'error' ][ '.container' ] = 'ul class=rs-alert--danger';

        add_action('wp_enqueue_scripts', function ()
        {
            wp_enqueue_style('wprs-forms-main');
            wp_enqueue_script('wprs-forms-main');
        });
    }

    /**
     * 渲染控件组，主要添加 Html render
     *
     * @param Nette\Forms\Container|Nette\Forms\ControlGroup
     *
     * @return string
     */
    public function renderControls($parent): string
    {
        if ( ! ($parent instanceof Nette\Forms\Container || $parent instanceof Nette\Forms\ControlGroup)) {
            throw new \Nette\InvalidArgumentException('Argument must be Nette\Forms\Container or Nette\Forms\ControlGroup instance.');
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
     * 在一行中渲染多个控件，主要添加 wrapper class
     *
     * @param Nette\Forms\IControl[]
     *
     * @return string
     */
    public function renderPairMulti(array $controls): string
    {
        $s = [];
        foreach ($controls as $control) {
            if ( ! $control instanceof Nette\Forms\Control) {
                throw new Nette\InvalidArgumentException('Argument must be array of Nette\Forms\IControl instances.');
            }

            $description = $control->getOption('description');

            if ($description instanceof HtmlStringable) {

                $description = ' ' . $description;

            } elseif ($description != null) {

                // intentionally ==
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

        if ( ! empty($control->getOption('class'))) {
            $group_class[] = $control->getOption('class');
        } else {
            $group_class[] = 'rs-col-md-12';

            if ($this->layout === 'horizontal') {
                $group_class[] = 'rs-form-group rs-row';
            }
        }

        // 允许添加Class到Wrapper上
        $pair->class($group_class, true);

        $pair->addHtml($this->getWrapper('control container')->setHtml(implode(' ', $s)));

        return $pair->render(0);
    }


    /**
     * 渲染标签组
     *
     * @param $control
     *
     * @return \Nette\Utils\Html|string
     */
    public function renderControlGroup($control): Html|string
    {
        $html = '';

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

            $html = $group_parent->addHtml($group);

        }

        return $html;
    }
}

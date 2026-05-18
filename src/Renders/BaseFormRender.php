<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;
use Nette\HtmlStringable;
use Nette\Localization\Translator;

/**
 * 转到表单到 HTML 输出
 */
class BaseFormRender extends Nette\Forms\Rendering\DefaultFormRenderer
{
    use Nette\SmartObject;

    var $layout = 'horizontal';

    public function __construct($layout = 'horizontal')
    {
        $this->layout = $layout;

        $this->wrappers[ 'form' ][ 'container' ] = "div class='rs-form rs-form--$this->layout'";

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
     * @param Nette\Forms\Container|Nette\Forms\ControlGroup $parent
     *
     * @return string
     */
    public function renderControls($parent): string
    {
        if ( ! ($parent instanceof Nette\Forms\Container || $parent instanceof Nette\Forms\ControlGroup)) {
            throw new InvalidArgumentException('Argument must be Nette\Forms\Container or Nette\Forms\ControlGroup instance.');
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
     * 渲染表单主体，支持按 Tab 渲染分组。
     *
     * @return string
     */
    public function renderBody(): string
    {
        $s = $remains = '';

        $default_container = $this->getWrapper('group container');
        $translator = $this->form->getTranslator();
        $tab_groups = [];

        foreach ($this->form->getGroups() as $group) {
            if (!$group->getControls() || !$group->getOption('visual')) {
                continue;
            }

            if ($group->getOption('wprs_tab_name')) {
                $tab_groups[] = $group;
                continue;
            }

            $container = $group->getOption('container') ?? $default_container;
            $container = $container instanceof Html ? clone $container : Html::el($container);

            $id = $group->getOption('id');
            if ($id) {
                $container->id = $id;
            }

            $s .= "\n" . $container->startTag();

            $group_label = $group->getOption('label');
            if ($group_label instanceof HtmlStringable) {
                $s .= $this->getWrapper('group label')->addHtml($group_label);
            } elseif ($group_label != null) {
                $s .= "\n" . $this->getWrapper('group label')->setText($this->translate_text($group_label, $translator)) . "\n";
            }

            $group_description = $group->getOption('description');
            if ($group_description instanceof HtmlStringable) {
                $s .= $group_description;
            } elseif ($group_description != null) {
                $s .= $this->getWrapper('group description')->setText($this->translate_text($group_description, $translator)) . "\n";
            }

            $s .= $this->renderControls($group);

            $remains = $container->endTag() . "\n" . $remains;
            if (!$group->getOption('embedNext')) {
                $s .= $remains;
                $remains = '';
            }
        }

        if (!empty($tab_groups)) {
            $s .= $this->render_tab_groups($tab_groups, $translator);
        }

        $s .= $remains . $this->renderControls($this->form);

        $container = $this->getWrapper('form container');
        $container->setHtml($s);

        return $container->render(0);
    }


    /**
     * 渲染 Tab 导航与内容。
     *
     * @param array<\Nette\Forms\ControlGroup> $tab_groups
     * @param \Nette\Localization\Translator|null $translator
     *
     * @return string
     */
    protected function render_tab_groups(array $tab_groups, ?Translator $translator = null): string
    {
        $tab_nav = Html::el('ul')
            ->class('rs-tabs-nav')
            ->setAttribute('role', 'tablist');
        $tab_content = Html::el('div')->class('rs-tabs-content');

        $has_active = false;
        foreach ($tab_groups as $group) {
            if ($group->getOption('wprs_tab_active')) {
                $has_active = true;
                break;
            }
        }

        foreach ($tab_groups as $index => $group) {
            $tab_name = (string) $group->getOption('wprs_tab_name');
            $tab_slug = (string) $group->getOption('wprs_tab_slug');
            $tab_label = $group->getOption('wprs_tab_label') ?? $group->getOption('label') ?? $tab_name;
            $tab_label = $this->translate_text($tab_label, $translator);

            $is_active = (bool) $group->getOption('wprs_tab_active');
            if (!$has_active && $index === 0) {
                $is_active = true;
            }

            $tab_link = Html::el('a')
                ->setAttribute('href', '#wprs-tab-' . $tab_slug)
                ->setAttribute('data-wprs-tab-target', 'wprs-tab-' . $tab_slug)
                ->setAttribute('role', 'tab')
                ->setAttribute('aria-selected', $is_active ? 'true' : 'false')
                ->class($is_active ? 'rs-is-active' : null)
                ->setText((string) $tab_label);

            $tab_nav->addHtml(
                Html::el('li')
                    ->class('rs-tabs-nav-item')
                    ->addHtml($tab_link)
            );

            $tab_pane = Html::el('div')
                ->setAttribute('id', 'wprs-tab-' . $tab_slug)
                ->setAttribute('role', 'tabpanel')
                ->class('rs-tab-pane' . ($is_active ? ' rs-is-active' : ''))
                ->addHtml($this->render_tab_controls($group));

            $tab_content->addHtml($tab_pane);
        }

        return Html::el('div')
            ->class('rs-tabs')
            ->addHtml($tab_nav)
            ->addHtml($tab_content)
            ->render();
    }


    /**
     * 渲染 Tab 面板中的控件，按钮控件留在 Tab 外层统一渲染。
     *
     * @param \Nette\Forms\ControlGroup $group
     *
     * @return string
     */
    protected function render_tab_controls(Nette\Forms\ControlGroup $group): string
    {
        $container = $this->getWrapper('controls container');

        foreach ($group->getControls() as $control) {
            if ($control->getOption('rendered') || $control->getOption('type') === 'hidden' || $control->getForm(false) !== $this->form) {
                continue;
            }

            // Keep submit/button controls outside the tab panes.
            if ($control->getOption('type') === 'button') {
                continue;
            }

            $container->addHtml($this->renderPair($control));
        }

        $s = '';
        if (count($container)) {
            $s .= "\n" . $container . "\n";
        }

        return $s;
    }


    /**
     * 翻译文本标签。
     *
     * @param mixed $text
     * @param \Nette\Localization\Translator|null $translator
     *
     * @return mixed
     */
    protected function translate_text($text, ?Translator $translator = null)
    {
        if ($translator !== null && is_string($text)) {
            return $translator->translate($text);
        }

        return $text;
    }


    /**
     * 在一行中渲染多个控件，主要添加 wrapper class
     *
     * @param Nette\Forms\IControl[] $controls
     *
     * @return string
     */
    public function renderPairMulti(array $controls): string
    {
        $s = [];
        foreach ($controls as $control) {
            if ( ! $control instanceof Nette\Forms\IControl) {
                throw new InvalidArgumentException('Argument must be array of Nette\Forms\IControl instances.');
            }

            $description = $control->getOption('description');

            if ($description instanceof HtmlStringable) {

                $description = ' ' . $description;

            } elseif ($description != null) {

                // intentionally ==
                if ($control instanceof Nette\Forms\Controls\BaseControl || $control instanceof Nette\Forms\IControl) {
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
    public function renderControlGroup($control)
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

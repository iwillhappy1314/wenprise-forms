<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\InvalidArgumentException;
use Nette\Utils\Html;
use Nette\HtmlStringable;
use Nette\Localization\Translator;
use Wenprise\Forms\Containers\Repeater;

/**
 * 转到表单到 HTML 输出
 */
class BaseFormRender extends Nette\Forms\Rendering\DefaultFormRenderer
{
    use Nette\SmartObject;
    protected bool $has_rendered_stepper_submit_controls = false;

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
        $items = $parent instanceof Nette\Forms\ControlGroup ? $parent->getControls() : iterator_to_array($parent->getComponents());

        foreach ($items as $item) {
            if ($item instanceof Nette\Forms\IControl) {
                $repeater = $this->find_parent_repeater($item);
                if ($repeater instanceof Repeater && ! $repeater->getOption('rendering')) {
                    if ($repeater->getOption('rendered')) {
                        continue;
                    }

                    if ($buttons) {
                        $container->addHtml($this->renderPairMulti($buttons));
                        $buttons = null;
                    }

                    $container->addHtml($this->render_repeater($repeater));
                    $this->mark_repeater_rendered($repeater);
                    continue;
                }

                if ($item->getOption('rendered') || $item->getOption('type') === 'hidden' || $item->getForm(false) !== $this->form) {
                    continue;
                }

                if ($item->getOption('type') === 'button' || $item->getOption('type') === 'html') {
                    $buttons[] = $item;
                    continue;
                }

                if ($buttons) {
                    $container->addHtml($this->renderPairMulti($buttons));
                    $buttons = null;
                }

                $container->addHtml($this->renderPair($item));
                continue;
            }

            if ($item instanceof Repeater && ! $item->getOption('rendered')) {
                if ($buttons) {
                    $container->addHtml($this->renderPairMulti($buttons));
                    $buttons = null;
                }

                $container->addHtml($this->render_repeater($item));
                $this->mark_repeater_rendered($item);
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
        $group_sets = [
            'tab' => [],
            'step' => [],
        ];

        foreach ($this->form->getGroups() as $group) {
            if (!$group->getControls() || !$group->getOption('visual')) {
                continue;
            }

            $group_name = $group->getOption('wprs_group_name') ?? $group->getOption('wprs_tab_name');
            if ($group_name) {
                $group_type = (string) ($group->getOption('wprs_group_type') ?? 'tab');
                if (!isset($group_sets[$group_type])) {
                    $group_type = 'tab';
                }
                $group_sets[$group_type][] = $group;
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

        if (!empty($group_sets['tab'])) {
            $s .= $this->render_tab_groups($group_sets['tab'], $translator);
        }

        if (!empty($group_sets['step'])) {
            $s .= $this->render_step_groups($group_sets['step'], $translator);
        }

        $form_controls = $this->renderControls($this->form);
        if ($this->has_rendered_stepper_submit_controls) {
            $form_controls = '';
        }

        $s .= $remains . $form_controls;

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
     * 渲染 Stepper 导航与内容。
     *
     * @param array<\Nette\Forms\ControlGroup> $step_groups
     * @param \Nette\Localization\Translator|null $translator
     *
     * @return string
     */
    protected function render_step_groups(array $step_groups, ?Translator $translator = null): string
    {
        $step_nav = Html::el('ol')
            ->class('rs-stepper-nav')
            ->setAttribute('role', 'tablist');
        $step_content = Html::el('div')->class('rs-stepper-content');

        $active_index = 0;
        foreach ($step_groups as $index => $group) {
            if ($group->getOption('wprs_group_active') || $group->getOption('wprs_tab_active')) {
                $active_index = $index;
                break;
            }
        }

        $last_index = count($step_groups) - 1;
        foreach ($step_groups as $index => $group) {
            $group_name = (string) ($group->getOption('wprs_group_name') ?? $group->getOption('wprs_tab_name'));
            $group_slug = (string) ($group->getOption('wprs_group_slug') ?? $group->getOption('wprs_tab_slug'));
            $group_label = $group->getOption('wprs_group_label') ?? $group->getOption('wprs_tab_label') ?? $group->getOption('label') ?? $group_name;
            $group_label = $this->translate_text($group_label, $translator);
            $target_id = 'wprs-step-' . $group_slug;

            $is_active = $index === $active_index;
            $is_completed = $index < $active_index;

            $step_link = Html::el('a')
                ->setAttribute('href', '#' . $target_id)
                ->setAttribute('data-wprs-step-target', $target_id)
                ->setAttribute('data-wprs-step-index', (string) $index)
                ->setAttribute('role', 'tab')
                ->setAttribute('aria-selected', $is_active ? 'true' : 'false')
                ->class('rs-stepper-link' . ($is_active ? ' rs-is-active' : '') . ($is_completed ? ' rs-is-completed' : ''));

            $step_badge = Html::el('span')
                ->class('rs-stepper-badge')
                ->setText($is_completed ? '✓' : (string) ($index + 1));
            $step_label = Html::el('span')
                ->class('rs-stepper-title')
                ->setText((string) $group_label);

            $step_link->addHtml($step_badge)->addHtml($step_label);

            $step_item_class = 'rs-stepper-nav-item';
            if ($is_active) {
                $step_item_class .= ' rs-is-active';
            } elseif ($is_completed) {
                $step_item_class .= ' rs-is-completed';
            }
            if ($index === $last_index) {
                $step_item_class .= ' rs-is-last';
            }

            $step_nav->addHtml(
                Html::el('li')
                    ->class($step_item_class)
                    ->addHtml($step_link)
            );

            $step_controls = Html::el('div')
                ->class('rs-stepper-controls')
                ->addHtml($this->render_tab_controls($group));

            $step_actions = Html::el('div')->class('rs-stepper-actions');
            if ($index > 0) {
                $step_actions->addHtml(
                    Html::el('button')
                        ->setAttribute('type', 'button')
                        ->setAttribute('data-wprs-step-prev', (string) $index)
                        ->class('rs-btn rs-btn-secondary rs-stepper-prev')
                        ->setText('Previous')
                );
            }
            if ($index < $last_index) {
                $step_actions->addHtml(
                    Html::el('button')
                        ->setAttribute('type', 'button')
                        ->setAttribute('data-wprs-step-next', (string) $index)
                        ->class('rs-btn rs-btn-primary rs-stepper-next')
                        ->setText('Next')
                );
            } else {
                $stepper_submit_controls = $this->render_stepper_submit_controls();
                if ($stepper_submit_controls !== '') {
                    $step_actions->addHtml(
                        Html::el('div')
                            ->class('rs-stepper-submit')
                            ->setHtml($stepper_submit_controls)
                    );
                }
            }

            $step_pane = Html::el('div')
                ->setAttribute('id', $target_id)
                ->setAttribute('role', 'tabpanel')
                ->class('rs-step-pane' . ($is_active ? ' rs-is-active' : ''))
                ->addHtml($step_controls)
                ->addHtml($step_actions);

            $step_content->addHtml($step_pane);
        }

        return Html::el('div')
            ->class('rs-stepper')
            ->addHtml($step_nav)
            ->addHtml($step_content)
            ->render();
    }


    /**
     * 渲染 Stepper 最后一步的提交按钮，并标记为已渲染避免重复输出。
     *
     * @return string
     */
    protected function render_stepper_submit_controls(): string
    {
        $submit_container = Html::el('div');

        foreach ($this->form->getControls() as $control) {
            if ($control->getOption('rendered') || $control->getForm(false) !== $this->form) {
                continue;
            }

            if ($control->getOption('type') !== 'button') {
                continue;
            }

            $control->setOption('rendered', true);
            $submit_container->addHtml($control->getControl());
            $this->has_rendered_stepper_submit_controls = true;
        }

        return (string) $submit_container;
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
            $repeater = $this->find_parent_repeater($control);
            if ($repeater instanceof Repeater && ! $repeater->getOption('rendering')) {
                if (!$repeater->getOption('rendered')) {
                    $container->addHtml($this->render_repeater($repeater));
                    $this->mark_repeater_rendered($repeater);
                }

                continue;
            }

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
     * 查找控件所属的父级 repeater。
     *
     * @param Nette\ComponentModel\IComponent $component 当前组件。
     *
     * @return Repeater|null
     */
    protected function find_parent_repeater(Nette\ComponentModel\IComponent $component): ?Repeater
    {
        $parent = $component->getParent();

        while ($parent !== null) {
            if ($parent instanceof Repeater) {
                return $parent;
            }

            if (!method_exists($parent, 'getParent')) {
                break;
            }

            $parent = $parent->getParent();
        }

        return null;
    }


    /**
     * 标记 repeater 与其子控件已经完成渲染。
     *
     * @param Repeater $repeater repeater 容器。
     *
     * @return void
     */
    protected function mark_repeater_rendered(Repeater $repeater): void
    {
        $repeater->setOption('rendered', true);

        foreach ($repeater->getControls() as $control) {
            $control->setOption('rendered', true);
        }
    }


    /**
     * 渲染独立 repeater 容器。
     *
     * @param Repeater $repeater repeater 容器。
     *
     * @return string
     */
    protected function render_repeater(Repeater $repeater): string
    {
        $repeater->setOption('rendering', true);

        $pair = $this->getWrapper('pair container');
        $pair->class('rs-form rs-form--repeater', true);

        if ($this->layout === 'horizontal') {
            $pair->class('rs-row rs-col-md-12', true);
        }

        $label_container = $this->getWrapper('label container');
        if ($label_container !== null && $repeater->getLabel() !== null) {
            $label_container = clone $label_container;
            $label_container->setHtml(
                Html::el('label')
                    ->class('rs-control-label')
                    ->setText($repeater->getLabel())
                    ->render()
            );
            $pair->addHtml($label_container);
        }

        $control_container = $this->getWrapper('control container');
        $control_container = $control_container !== null ? clone $control_container : Html::el('div');
        $control_container->addClass('rs-repeater-container');

        $repeater_html = Html::el('div')
            ->class('rs-repeater')
            ->setAttribute('data-rs-repeater-prefix', $repeater->getHtmlNamePrefix())
            ->setAttribute('data-rs-repeater-max', $repeater->getMaxCopies() ?? '')
            ->setAttribute('data-rs-repeater-next-index', (string) count($repeater->getRows()));

        foreach ($repeater->getRows() as $row_name => $row) {
            $row_prefix = Nette\Forms\Helpers::generateHtmlName($row->lookupPath(Nette\Forms\Form::class));
            $row_id_prefix = $row->lookupPath(Nette\Forms\Form::class);
            $row_html = Html::el('div')
                ->class('rs-repeater__row')
                ->setAttribute('data-rs-repeater-row-name', (string) $row_name)
                ->setAttribute('data-rs-repeater-row-prefix', $row_prefix)
                ->setAttribute('data-rs-repeater-row-id-prefix', $row_id_prefix);

            $row_controls = Html::el('div')->class('rs-repeater__row-controls');
            $row_controls->setHtml($this->renderControls($row));

            $row_actions = Html::el('div')->class('rs-repeater__row-actions');
            $row_actions->addHtml(
                Html::el('button')
                    ->setAttribute('type', 'button')
                    ->class('rs-btn rs-btn-default rs-btn--sm rs-repeater__duplicate')
                    ->setText(__('Duplicate', 'wprs'))
            );
            $row_actions->addHtml(
                Html::el('button')
                    ->setAttribute('type', 'button')
                    ->class('rs-btn rs-btn-default rs-btn--sm rs-repeater__remove')
                    ->setText(__('Remove', 'wprs'))
            );

            $row_html
                ->addHtml(
                    Html::el('div')
                        ->class('rs-repeater__row-body')
                        ->addHtml($row_controls)
                        ->addHtml($row_actions)
                );
            $repeater_html->addHtml($row_html);
        }

        $repeater_html->addHtml(
            Html::el('div')
                ->class('rs-repeater__footer')
                ->addHtml(
                    Html::el('button')
                        ->setAttribute('type', 'button')
                        ->class('rs-btn rs-btn-primary rs-btn--sm rs-repeater__add')
                        ->setText(__('Add Contact Group', 'wprs'))
                )
        );

        $control_container->addHtml($repeater_html);
        $pair->addHtml($control_container);

        $repeater->setOption('rendering', false);

        return $pair->render(0);
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

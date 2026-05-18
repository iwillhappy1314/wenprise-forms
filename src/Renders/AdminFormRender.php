<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\Utils\Html;
use Nette\Localization\Translator;

/**
 * 转到表单到 HTML 输出
 */
class AdminFormRender extends BaseFormRender
{
    use Nette\SmartObject;

    var $layout = 'post_meta';

    public function __construct($layout = 'post_meta')
    {
        $this->layout = $layout;
        $screen       = function_exists('get_current_screen') ? get_current_screen() : null;

        $this->wrappers['form']['container'] = "div class='rs-form rs-admin-form rs-form--$layout'";

        switch ($layout) {
            case 'term_meta':
            case 'term':
                if ($screen && $screen->base == 'term') {
                    $this->wrappers['controls']['container'] = 'table class="form-table rs-form-group"';
                    $this->wrappers['pair']['container']     = 'tr class=rs-form-field';
                } else {
                    $this->wrappers['controls']['container'] = '';
                    $this->wrappers['pair']['container']     = 'tr class=rs-form-field"';
                }
                break;
            default:
                $this->wrappers['controls']['container'] = 'table class="form-table rs-form-group"';
                $this->wrappers['pair']['container']     = 'tr class=rs-form-field';
        }

        $this->wrappers['label']['container']   = 'th class=row scope=row';
        $this->wrappers[ 'control' ][ 'description' ] = 'p class=description';

        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_style('wprs-forms-main');
            wp_enqueue_script('wprs-forms-main');
        });
    }

    /**
     * 渲染一行
     *
     * @param \Nette\Forms\Control $control
     *
     * @return string
     */
    public function renderPair(Nette\Forms\IControl $control): string
    {
        $pair = $this->getWrapper('pair container');
        $pair->addHtml($this->renderLabel($control));

        if (++$this->counter % 2) {
            $pair->class($this->getValue('pair .odd'), true);
        }

        $text_control_type = ['text', 'textarea', 'select', 'sms', 'datepicker', 'color-picker'];

        $type = $control->getOption('type');

        if (! $control->getOption('class')) {
            $control->setOption('class', 'rs-form rs-form--' . $type);
        }

        $control->setOption('id', 'grp-' . $control->getName());

        if ($type === 'button') {

            $control->getControlPrototype()
                ->addClass(empty($usedPrimary) ? 'button button-primary' : 'button');
        } elseif (in_array($type, $text_control_type, true)) {

            $control->getControlPrototype()
                ->addClass('regular-text');
        } elseif (in_array($type, ['checkbox', 'radio'], true)) {

            $control->getSeparatorPrototype()
                ->setName('fieldset')
                ->addClass($type . ' ' . $type . '-inline');
        }

        $pair->id = $control->getOption('id');

        if (! empty($this->renderControlGroup($control))) {
            $pair->addHtml($this->renderControlGroup($control));
            $control->setOption('rendered', true);
        } else {
            $pair->addHtml($this->renderControl($control));
        }

        $pair->class($this->getValue($control->isRequired() ? 'pair .rs-required' : 'pair .optional'), true);
        $pair->class($control->hasErrors() ? $this->getValue('pair .error') : null, true);
        $pair->class($control->getOption('class'), true);

        return $pair->render(0);
    }


    /**
     * 渲染 WordPress 后台风格 Tab 导航与内容。
     *
     * @param array<\Nette\Forms\ControlGroup> $tab_groups
     * @param \Nette\Localization\Translator|null $translator
     *
     * @return string
     */
    protected function render_tab_groups(array $tab_groups, ?Translator $translator = null): string
    {
        $tab_nav = Html::el('h2')
            ->class('nav-tab-wrapper rs-tabs-nav')
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
                ->class('nav-tab' . ($is_active ? ' nav-tab-active rs-is-active' : ''))
                ->setText((string) $tab_label);

            $tab_nav->addHtml($tab_link);

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
}

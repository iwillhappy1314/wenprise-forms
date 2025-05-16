<?php

namespace Wenprise\Forms\Renders;

use Nette;

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
    public function renderPair(Nette\Forms\Control $control): string
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
}

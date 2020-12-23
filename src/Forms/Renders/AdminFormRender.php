<?php

namespace Wenprise\Forms\Renders;

use Nette;

/**
 * 转到表单到 HTML 输出
 */
class AdminFormRender extends BaseFormRender
{
    use Nette\SmartObject;

    var $type = 'post_meta';

    public function __construct($type = 'post_meta')
    {
        $this->type = $type;
        $screen     = get_current_screen();

        switch ($type) {
            case 'term_meta':
                if ($screen->base == 'term') {
                    $this->wrappers[ 'controls' ][ 'container' ] = 'table class=form-table';
                    $this->wrappers[ 'pair' ][ 'container' ]     = 'tr class=rs-form-field';
                } else {
                    $this->wrappers[ 'controls' ][ 'container' ] = '';
                    $this->wrappers[ 'pair' ][ 'container' ]     = 'div class="form-field wprs-form-field"';
                }
                break;
            default:
                $this->wrappers[ 'controls' ][ 'container' ] = 'table class=form-table';
                $this->wrappers[ 'pair' ][ 'container' ]     = 'tr class=rs-form-field';
        }

        $this->wrappers[ 'label' ][ 'container' ]   = 'th class=row scope=row';
        $this->wrappers[ 'control' ][ 'container' ] = 'td';

        parent::__construct();
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

        $text_control_type = ['text', 'textarea', 'select', 'sms', 'datepicker', 'color-picker'];

        $type = $control->getOption('type');

        if ( ! $control->getOption('class')) {
            $control->setOption('class', 'rs-form--' . $type);
        }

        $control->setOption('id', 'grp-' . $control->name);

        if ($type === 'button') {

            $control->getControlPrototype()
                    ->addClass(empty($usedPrimary) ? 'rs-btn rs-btn-primary' : 'rs-rs-btn rs-btn-default');

        } elseif (in_array($type, $text_control_type, true)) {

            $control->getControlPrototype()
                    ->addClass('regular-text');

        } elseif (in_array($type, ['checkbox', 'radio'], true)) {

            $control->getSeparatorPrototype()
                    ->setName('fieldset')
                    ->addClass($type . ' ' . $type . '-inline');

        }

        $pair->id = $control->getOption('id');

        if ( ! empty($this->renderControlGroup($control))) {
            $pair->addHtml($this->renderControlGroup($control));
            $control->setOption('rendered', true);
        } else {
            $pair->addHtml($this->renderControl($control));
        }

        $pair->class($this->getValue($control->isRequired() ? 'pair .required' : 'pair .optional'), true);
        $pair->class($control->hasErrors() ? $this->getValue('pair .error') : null, true);
        $pair->class($control->getOption('class'), true);

        return $pair->render(0);
    }

}

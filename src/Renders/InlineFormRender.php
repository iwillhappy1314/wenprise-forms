<?php

namespace Wenprise\Forms\Renders;

use Nette;

/**
 * 转到表单到 HTML 输出
 */
class InlineFormRender extends BaseFormRender
{
    use Nette\SmartObject;

    var $layout = 'inline';

    public function __construct($type = 'inline')
    {
	    parent::__construct($type);

        $this->layout = $type;

	    $this->wrappers[ 'control' ][ '.submit' ]        = 'button';
	    $this->wrappers[ 'control' ][ '.image' ]         = 'rs-btn--image';
        $this->wrappers[ 'group' ][ 'container' ]    = 'fieldset class=rs-form-row';
        $this->wrappers[ 'group' ][ 'label' ]        = 'legend class="rs-form-legend"';
        $this->wrappers[ 'controls' ][ 'container' ] = null;
        $this->wrappers[ 'pair' ][ 'container' ] = 'div class=rs-form-group';
    }

    /**
     * 渲染一行
     *
     * @param \Nette\Forms\IControl $control
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

        $row_class   = ['rs-form rs-form--' . $type];
        $group_class = [];

        if ( ! empty($control->getOption('class'))) {
            $group_class[] = $control->getOption('class');
        } else {
            $group_class[] = '';
        }

        $control->setOption('class', implode(' ', $row_class) . ' ' . implode(' ', $group_class));

        $control->setOption('id', 'rs-form-' . $control->getName());

        if ($type === 'button') {

            $control->getControlPrototype()
                    ->addClass(empty($usedPrimary) ? 'button button-primary' : 'button');

        } elseif (in_array($type, ['checkbox', 'radio'], true)) {

            $control->getSeparatorPrototype()
                    ->setName('div')
                    ->addClass('rs-' . $type . ' rs-' . $type . '-inline');
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

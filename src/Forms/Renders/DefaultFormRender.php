<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\Utils\Html;

/**
 * 转到表单到 HTML 输出
 */
class DefaultFormRender extends BaseFormRender
{
    use Nette\SmartObject;

    var $layout = 'horizontal';

    public function __construct($type = 'horizontal')
    {
        $this->layout = $type;

        $this->wrappers[ 'group' ][ 'container' ]    = 'fieldset class=rs-form-row';
        $this->wrappers[ 'group' ][ 'label' ]        = 'legend class="rs-form-legend rs-col-md-12"';
        $this->wrappers[ 'controls' ][ 'container' ] = null;
        $this->wrappers[ 'pair' ][ 'container' ]     = 'div class=rs-form-group';
        $this->wrappers[ 'pair' ][ '.required' ]     = 'rs-form--required';
        $this->wrappers[ 'pair' ][ '.error' ]        = 'rs-has-error';
        $this->wrappers[ 'pair' ][ '.addon' ]        = 'rs-input-group';
        $this->wrappers[ 'label' ][ 'container' ]    = ($this->layout === 'horizontal') ? 'div class="rs-col-sm-3 rs-control-label"' : '';

        $this->wrappers[ 'control' ][ 'container' ]      = ($this->layout === 'horizontal') ? 'div class="rs-col-sm-9 rs-control-input"' : '';
        $this->wrappers[ 'control' ][ '.required' ]      = 'rs-required';
        $this->wrappers[ 'control' ][ 'description' ]    = 'span class=rs-help-block';
        $this->wrappers[ 'control' ][ 'errorcontainer' ] = 'span class=rs-help-block';

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

        $row_class   = ['rs-form--' . $type];
        $group_class = [];

        if ($this->layout === 'horizontal') {
            $row_class[]   = 'rs-row';
            $group_class[] = 'rs-col-md-12';
        } else {
            if ( ! empty($control->getOption('class'))) {
                $group_class[] = $control->getOption('class');
            } else {
                $group_class[] = 'rs-col-md-12';
            }
        }

        $control->setOption('class', implode(' ', $row_class) . ' ' . implode(' ', $group_class));

        $control->setOption('id', 'rs-form-' . $control->name);

        if ($type === 'button') {

            $control->getControlPrototype()
                    ->addClass(empty($usedPrimary) ? 'rs-btn rs-btn-primary' : 'rs-btn rs-btn-default');

        } elseif (in_array($type, $text_control_type, true)) {

            $control->getControlPrototype()
                    ->addClass('rs-form-control');

        } elseif (in_array($type, ['checkbox', 'radio'], true)) {

            $control->getSeparatorPrototype()
                    ->setName('div')
                    ->addClass('rs-' . $type . ' rs-' . $type . '-inline');
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

}

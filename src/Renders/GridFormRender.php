<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\HtmlStringable;
use Nette\InvalidArgumentException;
use Nette\Localization\Translator;
use Nette\Utils\Html;
use Wenprise\Forms\Containers\Repeater;

/**
 * 使用 Tailwind Grid 渲染表单布局，避免 Bootstrap gutter 带来的对齐问题。
 */
class GridFormRender extends BaseFormRender
{
    use Nette\SmartObject;

    /**
     * 当前布局模式。
     *
     * @var string
     */
    var $layout = 'vertical';

    /**
     * 初始化 Tailwind Grid 表单渲染器。
     *
     * @param string $layout 布局模式，支持 vertical 与 horizontal。
     */
    public function __construct($layout = 'horizontal')
    {
        $this->layout = $layout;

        $this->wrappers['group']['container'] = 'fieldset class="rs-form-row rs-grid-form__section"';
        $this->wrappers['group']['label'] = 'legend class="rs-form-legend rs-grid-form__legend"';
        $this->wrappers['controls']['container'] = 'div class="rs-grid-form__grid"';
        $this->wrappers['pair']['container'] = 'div class="rs-form-group rs-grid-form__item"';

        $this->wrappers['label']['container'] = ($this->layout === 'horizontal') ? 'div class="rs-grid-form__label"' : '';
        $this->wrappers['control']['container'] = ($this->layout === 'horizontal') ? 'div class="rs-grid-form__control"' : '';

        parent::__construct($layout);

        $this->wrappers['form']['container'] = "div class='rs-form rs-form--$this->layout rs-form--grid rs-grid-form'";
    }


    /**
     * 渲染单个控件行。
     *
     * @param \Nette\Forms\IControl $control 表单控件。
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

        $text_control_type = ['text', 'textarea', 'select', 'sms', 'datepicker', 'color-picker', 'autocomplete'];
        $type = (string) $control->getOption('type');

        $control->setOption('class', implode(' ', $this->build_pair_classes($control, $type)));
        $control->setOption('id', 'rs-form-' . str_replace(Nette\Forms\Form::NameSeparator, '-', $control->lookupPath(Nette\Forms\Form::class)));

        if ($type === 'button') {
            $control->getControlPrototype()->addClass('rs-btn rs-btn-primary');
        } elseif (in_array($type, $text_control_type, true)) {
            $control->getControlPrototype()->addClass('rs-form-control');
        } elseif (in_array($type, ['checkbox', 'radio'], true)) {
            $control->getSeparatorPrototype()
                ->setName('div')
                ->addClass('rs-' . $type . ' rs-' . $type . '-inline');
        }

        $pair->id = $control->getOption('id');

        if (!empty($this->renderControlGroup($control))) {
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


    /**
     * 在一行中渲染多个控件，通常用于按钮组。
     *
     * @param Nette\Forms\IControl[] $controls 控件列表。
     *
     * @return string
     */
    public function renderPairMulti(array $controls): string
    {
        $s = [];
        foreach ($controls as $control) {
            if (!$control instanceof Nette\Forms\IControl) {
                throw new InvalidArgumentException('Argument must be array of Nette\Forms\IControl instances.');
            }

            $description = $control->getOption('description');

            if ($description instanceof HtmlStringable) {
                $description = ' ' . $description;
            } elseif ($description != null) {
                if ($control instanceof Nette\Forms\Controls\BaseControl || $control instanceof Nette\Forms\IControl) {
                    $description = $control->translate($description);
                }

                $description = ' ' . $this->getWrapper('control description')->setText($description);
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
        $pair->class('rs-grid-form__item--full', true);
        $pair->addHtml($this->getWrapper('control container')->setHtml(implode(' ', $s)));

        return $pair->render(0);
    }


    /**
     * 渲染 repeater 容器，使用 grid 作为列布局基础。
     *
     * @param Repeater $repeater repeater 容器。
     *
     * @return string
     */
    protected function render_repeater(Repeater $repeater): string
    {
        $repeater->setOption('rendering', true);

        $pair = $this->getWrapper('pair container');
        $pair->class('rs-form rs-form--repeater rs-grid-form__item rs-grid-form__item--full', true);

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

            $row_html->addHtml(
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
                        ->setText(__('Add More', 'wprs'))
                )
        );

        $control_container->addHtml($repeater_html);
        $pair->addHtml($control_container);

        $repeater->setOption('rendering', false);

        return $pair->render(0);
    }


    /**
     * 生成控件外层容器类名。
     *
     * @param \Nette\Forms\IControl $control 表单控件。
     * @param string                $type    控件类型。
     *
     * @return array<int, string>
     */
    protected function build_pair_classes(Nette\Forms\IControl $control, string $type): array
    {
        $pair_classes = [
            'rs-form',
            'rs-form--' . $type,
            'rs-grid-form__item',
        ];

        if ($this->layout === 'horizontal') {
            $pair_classes[] = 'rs-grid-form__row';

            return array_values(array_unique(array_merge(
                $pair_classes,
                ['rs-grid-form__span-base-12', 'rs-grid-form__span-md-12'],
                $this->filter_custom_classes((string) $control->getOption('class'))
            )));
        }

        return array_values(array_unique(array_merge(
            $pair_classes,
            $this->map_width_option_classes($control->getOption('width')),
            $this->map_grid_classes((string) $control->getOption('class')),
            $this->filter_custom_classes((string) $control->getOption('class'))
        )));
    }


    /**
     * 将 width option 转换为 grid span 类。
     *
     * @param mixed $width_option 宽度配置。
     *
     * @return array<int, string>
     */
    protected function map_width_option_classes(mixed $width_option): array
    {
        if ($width_option === null || $width_option === []) {
            return [];
        }

        if (is_numeric($width_option)) {
            $width_option = ['md' => (int) $width_option];
        }

        if (!is_array($width_option)) {
            return [];
        }

        $mapped_classes = [];
        foreach ($width_option as $breakpoint => $width) {
            $width = (int) $width;
            if ($width < 1 || $width > 12) {
                continue;
            }

            if ($breakpoint === 'base') {
                $mapped_classes[] = 'rs-grid-form__span-base-' . $width;
                continue;
            }

            if (in_array($breakpoint, ['sm', 'md', 'lg', 'xl'], true)) {
                $mapped_classes[] = 'rs-grid-form__span-' . $breakpoint . '-' . $width;
            }
        }

        return $mapped_classes;
    }


    /**
     * 将 Bootstrap 风格列类映射为 grid span 类。
     *
     * @param string $class_name 传入的类名字符串。
     *
     * @return array<int, string>
     */
    protected function map_grid_classes(string $class_name): array
    {
        $mapped_classes = [];
        $class_names = preg_split('/\s+/', trim($class_name)) ?: [];

        foreach ($class_names as $single_class_name) {
            if (preg_match('/^(?:rs-)?col-(sm|md|lg|xl)-([1-9]|1[0-2])$/', $single_class_name, $matches) === 1) {
                $mapped_classes[] = 'rs-grid-form__span-' . $matches[1] . '-' . $matches[2];
                continue;
            }

            if (preg_match('/^(?:rs-)?col-([1-9]|1[0-2])$/', $single_class_name, $matches) === 1) {
                $mapped_classes[] = 'rs-grid-form__span-base-' . $matches[1];
            }
        }

        return $mapped_classes;
    }


    /**
     * 过滤掉 Bootstrap 风格网格类，仅保留业务自定义类。
     *
     * @param string $class_name 传入的类名字符串。
     *
     * @return array<int, string>
     */
    protected function filter_custom_classes(string $class_name): array
    {
        $custom_classes = [];
        $class_names = preg_split('/\s+/', trim($class_name)) ?: [];

        foreach ($class_names as $single_class_name) {
            if ($single_class_name === '') {
                continue;
            }

            if (preg_match('/^(?:rs-)?col(?:-(?:sm|md|lg|xl))?-(?:[1-9]|1[0-2])$/', $single_class_name) === 1) {
                continue;
            }

            if (in_array($single_class_name, ['row', 'rs-row'], true)) {
                continue;
            }

            $custom_classes[] = $single_class_name;
        }

        return $custom_classes;
    }
}

<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Utils\Html;
use Wenprise\Forms\Helpers;

/**
 * 链式选择输入
 */
class ChainedInput extends BaseControl
{

    private array $settings = [];

    private array $fields = [];

    /**
     * @param null       $label    Html 标签
     * @param array|null $settings TinyMce 设置
     * @param array|null $fields   TinyMce 设置
     */
    public function __construct(?string $label = null, ?array $settings = [], ?array $fields = [])
    {
        parent::__construct($label);
        $this->settings = $settings;
        $this->fields   = $fields;

        $this->setOption('type', 'chained');
    }


    /**
     * Loads HTTP data.
     */
    public function loadHttpData(): void
    {
        $values = (array) Helpers::input_get($this->get_request_key(), []);

        $this->setValue($values);
    }


    /**
     *  生成 html
     *
     * @return \Nette\Utils\Html
     */
    public function getControl(): Html
    {

        $id            = $this->getHtmlId();
        $settings      = $this->settings;
        $fields        = $this->fields;
        $default_value = $this->getValue() ? $this->getValue() : [];

        $settings_default = [
            'selects'    => $fields,
            'emptyStyle' => 'none',
        ];

        $settings = array_merge($settings_default, $settings);
        if (isset($settings['data'])) {
            $settings['data'] = $this->normalize_settings_data($settings['data']);
        }

        $el = Html::el('div')
                  ->setAttribute('id', $id)
                  ->setAttribute('class', 'input-group frm-chained');

        foreach ($fields as $field) {
            $field_name = $this->getHtmlName() . '[' . $field . ']';
            $el->addHtml(
                Html::el('select class=rs-form-control')
                    ->appendAttribute('class', $field)
                    ->setAttribute('name', $field_name)
                    ->data('value', $default_value[ $field ] ?? '')
            );
        }

        $el->data('settings', json_encode($settings));

        return $el;
    }


    /**
     * 获取当前控件在请求数组中的键路径。
     *
     * @return string
     */
    protected function get_request_key(): string
    {
        return str_replace(['][', '[', ']'], ['.', '.', ''], $this->getHtmlName());
    }


    /**
     * 将更直观的关联数组数据转换为 cxSelect 需要的数组结构。
     *
     * @param mixed $data 原始配置数据
     *
     * @return mixed
     */
    protected function normalize_settings_data($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        if ($this->is_level_based_data($data)) {
            return $this->normalize_level_based_data($data, 0, null);
        }

        if ($this->is_sequential_array($data)) {
            return array_map(function ($item) {
                if (is_array($item) && isset($item['s'])) {
                    $item['s'] = $this->normalize_settings_data($item['s']);
                }

                return $item;
            }, $data);
        }

        $normalized = [];
        foreach ($data as $value => $label_or_children) {
            $item = [
                'v' => (string) $value,
                'n' => is_array($label_or_children) ? (string) $value : (string) $label_or_children,
            ];

            if (is_array($label_or_children)) {
                $item['s'] = $this->normalize_settings_data($label_or_children);
            }

            $normalized[] = $item;
        }

        return $normalized;
    }


    /**
     * 将按层级拆分的数据结构转换为 cxSelect 嵌套树。
     *
     * @param array       $data_levels 层级数据
     * @param int         $level_index 当前层级
     * @param string|null $parent_value 父级值
     *
     * @return array
     */
    protected function normalize_level_based_data(array $data_levels, int $level_index, ?string $parent_value): array
    {
        if (!isset($this->fields[$level_index])) {
            return [];
        }

        $field_key  = $this->fields[$level_index];
        $level_data = $data_levels[$field_key] ?? [];

        if ($level_index === 0) {
            $options = $level_data;
        } else {
            $options = is_array($level_data) && $parent_value !== null ? ($level_data[$parent_value] ?? []) : [];
        }

        $normalized = [];
        foreach ($options as $value => $label) {
            $item = [
                'v' => (string) $value,
                'n' => (string) $label,
            ];

            $children = $this->normalize_level_based_data($data_levels, $level_index + 1, (string) $value);
            if (!empty($children)) {
                $item['s'] = $children;
            }

            $normalized[] = $item;
        }

        return $normalized;
    }


    /**
     * 判断数组是否为顺序数组。
     *
     * @param array $data 输入数组
     *
     * @return bool
     */
    protected function is_sequential_array(array $data): bool
    {
        return array_keys($data) === range(0, count($data) - 1);
    }


    /**
     * 判断是否为按层级拆分的链式选择数据。
     *
     * @param array $data 输入数据
     *
     * @return bool
     */
    protected function is_level_based_data(array $data): bool
    {
        foreach ($this->fields as $field) {
            if (!array_key_exists($field, $data)) {
                return false;
            }
        }

        return !$this->is_sequential_array($data);
    }

}

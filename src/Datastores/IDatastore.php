<?php

namespace Wenprise\Forms\Datastores;

use Nette\Utils\ArrayHash;
use Wenprise\Forms\Form;
use Wenprise\Forms\Containers\Repeater;

/**
 * Datastore 抽象基类，负责定义保存接口和可保存字段筛选逻辑。
 */
abstract class IDatastore
{
    /**
     * 当前 datastore 绑定的表单实例。
     */
    public ?Form $form = null;

    /**
     * 初始化 datastore。
     *
     * @param Form|null $form 表单实例。
     */
    public function __construct(?Form $form = null)
    {
        $this->form = $form;
    }

    /**
     * 绑定表单实例，供保存时读取控件和值。
     *
     * @param Form $form 表单实例。
     *
     * @return void
     */
    public function setForm(Form $form): void
    {
        $this->form = $form;
    }


    /**
     * 持久化保存表单数据。
     *
     * @return mixed
     */
    abstract public function save();


    /**
     * 获取允许参与持久化的字段列表，排除 submit 控件。
     *
     * @return array<string, mixed>
     */
    public function getFields(): array
    {
        if ( ! $this->form instanceof Form) {
            throw new \RuntimeException('Datastore form is not configured.');
        }

        $items = [];
        foreach ($this->form->getComponents() as $key => $item) {
            if ($item instanceof Repeater) {
                $items[ $key ] = $item->getLabel() ?? $key;
                continue;
            }

            if ( ! is_string($item->getControl()) && $item->getControl()->type !== 'submit') {
                $items[ $key ] = $item->caption;
            }
        }

        return $items;
    }


    /**
     * 将表单值递归转换为适合 WordPress 持久化的标量或数组。
     *
     * @param mixed $value 原始值。
     *
     * @return mixed
     */
    protected function normalize_value_for_storage(mixed $value): mixed
    {
        if ($value instanceof ArrayHash) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->normalize_value_for_storage($item);
            }
        }

        return $value;
    }
}

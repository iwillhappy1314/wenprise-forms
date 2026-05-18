<?php

namespace Wenprise\Forms\Datastores;

use Wenprise\Forms\Form;

/**
 * Datastore 抽象基类，负责定义保存接口和可保存字段筛选逻辑。
 */
abstract class IDatastore
{
    /**
     * 当前 datastore 绑定的表单实例。
     */
    public Form $form;

    /**
     * 初始化 datastore。
     *
     * @param Form $form 表单实例。
     */
    public function __construct(Form $form)
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
        $items = [];
        foreach ($this->form->getComponents() as $key => $item) {
            if ( ! is_string($item->getControl()) && $item->getControl()->type !== 'submit') {
                $items[ $key ] = $item->caption;
            }
        }

        return $items;
    }
}

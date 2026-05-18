<?php

namespace Wenprise\Forms\Datastores;

/**
 * 将表单值保存到 WordPress options。
 */
class OptionsDatastore extends IDatastore
{
    /**
     * 初始化 options datastore。
     *
     * @param \Wenprise\Forms\Form|null $form 表单实例。
     */
    public function __construct(?\Wenprise\Forms\Form $form = null)
    {
        parent::__construct($form);
    }

    /**
     * 保存表单字段到 options，缺失字段不覆盖已有值。
     *
     * @return void
     */
    public function save(): void
    {
        $values = $this->form->getValues();
        $fields = $this->getFields();
        $value_map = (array) $values;

        foreach ($fields as $name => $label) {
            if ( ! array_key_exists($name, $value_map)) {
                continue;
            }

            update_option($name, $value_map[ $name ]);
        }

    }
}

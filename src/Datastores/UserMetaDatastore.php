<?php

namespace Wenprise\Forms\Datastores;


/**
 * 将表单值保存到指定用户的 user meta。
 */
class UserMetaDatastore extends IDatastore
{
    /**
     * 目标用户 ID。
     */
    public int $user_id = 0;

    /**
     * 初始化用户元数据 datastore。
     *
     * @param mixed                        $user_id 用户 ID。
     * @param \Wenprise\Forms\Form|null    $form    表单实例。
     */
    public function __construct($user_id, ?\Wenprise\Forms\Form $form = null)
    {
        $this->user_id = $user_id;
        parent::__construct($form);
    }

    /**
     * 保存表单字段到 user meta，缺失字段不覆盖已有值。
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

            update_user_meta($this->user_id, $name, $value_map[ $name ]);
        }

    }
}

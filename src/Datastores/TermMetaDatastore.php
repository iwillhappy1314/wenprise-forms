<?php

namespace Wenprise\Forms\Datastores;

/**
 * 将表单值保存到指定术语的 term meta。
 */
class TermMetaDatastore extends IDatastore
{
    /**
     * 目标术语 ID。
     */
    public int $term_id = 0;

    /**
     * 初始化术语元数据 datastore。
     *
     * @param mixed                        $term_id 术语 ID。
     * @param \Wenprise\Forms\Form|null    $form    表单实例。
     */
    public function __construct($term_id, ?\Wenprise\Forms\Form $form = null)
    {
        $this->term_id = $term_id;
        parent::__construct($form);
    }

    /**
     * 保存表单字段到 term meta，缺失字段不覆盖已有值。
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

            update_term_meta($this->term_id, $name, $this->normalize_value_for_storage($value_map[ $name ]));
        }

    }
}

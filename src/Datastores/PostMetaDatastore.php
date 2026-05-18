<?php

namespace Wenprise\Forms\Datastores;


/**
 * 将表单值保存到指定文章的 post meta，并支持标题更新。
 */
class PostMetaDatastore extends IDatastore
{
    /**
     * 目标文章 ID。
     */
    public int $post_id = 0;

    /**
     * 初始化文章元数据 datastore。
     *
     * @param mixed                        $post_id 文章 ID。
     * @param \Wenprise\Forms\Form|null    $form    表单实例。
     */
    public function __construct($post_id, ?\Wenprise\Forms\Form $form = null)
    {
        $this->post_id = $post_id;
        parent::__construct($form);
    }

    /**
     * 保存表单字段到 post meta，并在有标题值时更新文章标题。
     *
     * @return void
     */
    public function save(): void
    {
        wp_enqueue_script('wprs-sweetalert');

        $values = $this->form->getValues();
        $fields = $this->getFields();
        $value_map = (array) $values;
        $post_data = [
            'ID' => $this->post_id,
        ];

        if (array_key_exists('post_title', $value_map)) {
            $post_data['post_title'] = $value_map['post_title'];
        }

        $post_id = wp_update_post($post_data, true);
        if (is_wp_error($post_id) || empty($post_id)) {
            $error_message = is_wp_error($post_id) ? $post_id->get_error_message() : 'Unknown post update error';
            throw new \RuntimeException('Failed to update post data: ' . $error_message);
        }

        unset($fields[ 'post_title' ], $fields[ 'post_type' ], $fields[ 'post_status' ]);

        foreach ($fields as $name => $label) {
            if ( ! array_key_exists($name, $value_map)) {
                continue;
            }

            update_post_meta($post_id, $name, $value_map[ $name ]);
        }

    }
}

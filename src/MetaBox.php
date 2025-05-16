<?php

namespace Wenprise\Forms;

use Wenprise\Forms\Renders\AdminFormRender;

/**
 * Meta Box 类
 * 用于快速添加 post meta box 或 user metabox
 */
class MetaBox
{
    /**
     * 表单实例
     *
     * @var Form
     */
    protected Form $form;

    /**
     * Meta Box 标题
     *
     * @var string
     */
    protected string $title;

    /**
     * Meta Box 类型 (post, user, term, options)
     *
     * @var string
     */
    protected string $type;

    /**
     * Meta Box ID
     *
     * @var string
     */
    protected string $id;

    /**
     * 上下文 (normal, side, advanced)
     *
     * @var string
     */
    protected string $context = 'normal';

    /**
     * 优先级 (default, high, low)
     *
     * @var string
     */
    protected string $priority = 'default';

    /**
     * 条件数组
     *
     * @var array
     */
    protected array $conditions = [];

    /**
     * 构造函数
     *
     * @param string $id    Meta Box ID
     * @param string $title Meta Box 标题
     * @param string $type  Meta Box 类型 (post, user, term, options)
     */
    public function __construct(string $id, string $title, string $type = 'post')
    {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->form = new Form();
        $this->form->setRenderer(new AdminFormRender($type));

        // 注册 Meta Box
        $this->register();
    }

    /**
     * 创建 Post Meta Box
     *
     * @param string $id    Meta Box ID
     * @param string $title Meta Box 标题
     *
     * @return static
     */
    public static function post(string $id, string $title): MetaBox
    {
        return new static($id, $title, 'post');
    }

    /**
     * 创建 User Meta Box
     *
     * @param string $id    Meta Box ID
     * @param string $title Meta Box 标题
     *
     * @return static
     */
    public static function user(string $id, string $title): MetaBox
    {
        return new static($id, $title, 'user');
    }

    /**
     * 创建 Term Meta Box
     *
     * @param string $id    Meta Box ID
     * @param string $title Meta Box 标题
     *
     * @return static
     */
    public static function term(string $id, string $title): MetaBox
    {
        return new static($id, $title, 'term');
    }

    /**
     * 创建 Options Page
     *
     * @param string $id    Meta Box ID
     * @param string $title Meta Box 标题
     *
     * @return static
     */
    public static function options(string $id, string $title): MetaBox
    {
        return new static($id, $title, 'options');
    }

    /**
     * 设置上下文
     *
     * @param string $context 上下文 (normal, side, advanced)
     *
     * @return $this
     */
    public function setContext(string $context): MetaBox
    {
        $this->context = $context;
        return $this;
    }

    /**
     * 设置优先级
     *
     * @param string $priority 优先级 (default, high, low)
     *
     * @return $this
     */
    public function setPriority(string $priority): MetaBox
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * 添加条件
     *
     * @param string $key      条件键
     * @param string $operator 条件操作符
     * @param mixed  $value    条件值
     *
     * @return $this
     */
    public function where(string $key, string $operator, $value): MetaBox
    {
        $this->conditions[] = [
            'key'      => $key,
            'operator' => $operator,
            'value'    => $value,
        ];

        return $this;
    }

    /**
     * 添加文本输入字段
     *
     * @param string      $name     字段名称
     * @param string|null $label    字段标签
     * @param int|null    $maxLength 最大长度
     *
     * @return \Nette\Forms\Controls\TextInput
     */
    public function addText(string $name, string $label = null, int $maxLength = null): \Nette\Forms\Controls\TextInput
    {
        return $this->form->addText($name, $label, $maxLength);
    }

    /**
     * 添加文本域字段
     *
     * @param string      $name  字段名称
     * @param string|null $label 字段标签
     *
     * @return \Nette\Forms\Controls\TextArea
     */
    public function addTextArea(string $name, string $label = null): \Nette\Forms\Controls\TextArea
    {
        return $this->form->addTextArea($name, $label);
    }

    /**
     * 添加选择框字段
     *
     * @param string      $name  字段名称
     * @param string|null $label 字段标签
     * @param array|null  $items 选项数组
     *
     * @return \Nette\Forms\Controls\SelectBox
     */
    public function addSelect(string $name, string $label = null, array $items = null): \Nette\Forms\Controls\SelectBox
    {
        return $this->form->addSelect($name, $label, $items);
    }

    /**
     * 添加多选框字段
     *
     * @param string      $name  字段名称
     * @param string|null $label 字段标签
     * @param array|null  $items 选项数组
     *
     * @return \Nette\Forms\Controls\MultiSelectBox
     */
    public function addMultiSelect(string $name, string $label = null, array $items = null): \Nette\Forms\Controls\MultiSelectBox
    {
        return $this->form->addMultiSelect($name, $label, $items);
    }

    /**
     * 添加单选框字段
     *
     * @param string      $name  字段名称
     * @param string|null $label 字段标签
     * @param array|null  $items 选项数组
     *
     * @return \Nette\Forms\Controls\RadioList
     */
    public function addRadioList(string $name, string $label = null, array $items = null): \Nette\Forms\Controls\RadioList
    {
        return $this->form->addRadioList($name, $label, $items);
    }

    /**
     * 添加复选框字段
     *
     * @param string      $name  字段名称
     * @param string|null $label 字段标签
     *
     * @return \Nette\Forms\Controls\Checkbox
     */
    public function addCheckbox(string $name, string $label = null): \Nette\Forms\Controls\Checkbox
    {
        return $this->form->addCheckbox($name, $label);
    }

    /**
     * 添加复选框列表字段
     *
     * @param string      $name  字段名称
     * @param string|null $label 字段标签
     * @param array|null  $items 选项数组
     *
     * @return \Nette\Forms\Controls\CheckboxList
     */
    public function addCheckboxList(string $name, string $label = null, array $items = null): \Nette\Forms\Controls\CheckboxList
    {
        return $this->form->addCheckboxList($name, $label, $items);
    }

    /**
     * 添加隐藏字段
     *
     * @param string      $name  字段名称
     * @param string|null $value 字段值
     *
     * @return \Nette\Forms\Controls\HiddenField
     */
    public function addHidden(string $name, string $value = null): \Nette\Forms\Controls\HiddenField
    {
        return $this->form->addHidden($name, $value);
    }

    /**
     * 添加上传字段
     *
     * @param string      $name  字段名称
     * @param string|null $label 字段标签
     *
     * @return \Nette\Forms\Controls\UploadControl
     */
    public function addUpload(string $name, string $label = null): \Nette\Forms\Controls\UploadControl
    {
        return $this->form->addUpload($name, $label);
    }

    /**
     * 添加日期选择字段
     *
     * @param string      $name     字段名称
     * @param string|null $label    字段标签
     * @param array|null  $settings 设置
     *
     * @return \Wenprise\Forms\Controls\DatePickerInput
     */
    public function addDatePicker(string $name, string $label = null, array $settings = null): Controls\DatePickerInput
    {
        return $this->form->addDatePicker($name, $label, $settings);
    }

    /**
     * 添加颜色选择字段
     *
     * @param string      $name     字段名称
     * @param string|null $label    字段标签
     * @param array|null  $settings 设置
     *
     * @return \Wenprise\Forms\Controls\ColorpickerInput
     */
    public function addColorPicker(string $name, string $label = null, array $settings = null): Controls\ColorpickerInput
    {
        return $this->form->addColorPicker($name, $label, $settings);
    }

    /**
     * 添加 WordPress 编辑器字段
     *
     * @param string      $name     字段名称
     * @param string|null $label    字段标签
     * @param array|null  $settings 设置
     *
     * @return \Wenprise\Forms\Controls\TextEditor
     */
    public function addEditor(string $name, string $label = null, array $settings = null): Controls\TextEditor
    {
        return $this->form->addEditor($name, $label, $settings);
    }

    /**
     * 注册 Meta Box
     */
    protected function register()
    {
        switch ($this->type) {
            case 'post':
                $this->registerPostMetaBox();
                break;
            case 'user':
                $this->registerUserMetaBox();
                break;
            case 'term':
                $this->registerTermMetaBox();
                break;
            case 'options':
                $this->registerOptionsPage();
                break;
        }
    }

    /**
     * 注册 Post Meta Box
     */
    protected function registerPostMetaBox()
    {
        add_action('add_meta_boxes', function () {
            $post_types = [];

            // 从条件中获取文章类型
            foreach ($this->conditions as $condition) {
                if ($condition['key'] === 'post_type' && $condition['operator'] === '=') {
                    $post_types[] = $condition['value'];
                }
            }

            // 如果没有指定文章类型，则使用所有文章类型
            if (empty($post_types)) {
                $post_types = get_post_types(['public' => true]);
            }

            foreach ($post_types as $post_type) {
                add_meta_box(
                    $this->id,
                    $this->title,
                    [$this, 'renderPostMetaBox'],
                    $post_type,
                    $this->context,
                    $this->priority
                );
            }
        });

        // 保存 Meta Box 数据
        add_action('save_post', [$this, 'savePostMetaBox'], 10, 2);
    }

    /**
     * 渲染 Post Meta Box
     *
     * @param \WP_Post $post 文章对象
     */
    public function renderPostMetaBox($post)
    {
        // 检查条件
        if (!$this->checkConditions($post->ID, 'post')) {
            return;
        }

        // 添加 nonce 字段
        wp_nonce_field($this->id . '_nonce', $this->id . '_nonce');

        echo '<div class="wprs-metabox-container">';

        // 设置字段值
        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'setDefaultValue')) {
                $value = get_post_meta($post->ID, $component->getName(), true);
                if ($value) {
                    $component->setDefaultValue($value);
                }
            }
        }

        // 渲染表单
        echo $this->form->renderBody('body');

        echo '</div>';
    }

    /**
     * 保存 Post Meta Box 数据
     *
     * @param int      $post_id 文章ID
     * @param \WP_Post $post    文章对象
     */
    public function savePostMetaBox(int $post_id, \WP_Post $post)
    {
        // 检查自动保存
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // 检查 nonce
        if (!isset($_POST[$this->id . '_nonce']) || !wp_verify_nonce($_POST[$this->id . '_nonce'], $this->id . '_nonce')) {
            return;
        }

        // 检查权限
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // 检查条件
        if (!$this->checkConditions($post_id, 'post')) {
            return;
        }

        // 保存字段值
        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'getName') && isset($_POST[$component->getName()])) {
                update_post_meta($post_id, $component->getName(), $_POST[$component->getName()]);
            }
        }
    }

    /**
     * 注册 User Meta Box
     */
    protected function registerUserMetaBox()
    {
        add_action('show_user_profile', [$this, 'renderUserMetaBox']);
        add_action('edit_user_profile', [$this, 'renderUserMetaBox']);
        add_action('personal_options_update', [$this, 'saveUserMetaBox']);
        add_action('edit_user_profile_update', [$this, 'saveUserMetaBox']);
    }

    /**
     * 渲染 User Meta Box
     *
     * @param \WP_User $user 用户对象
     */
    public function renderUserMetaBox(\WP_User $user)
    {
        // 检查条件
        if (!$this->checkConditions($user->ID, 'user')) {
            return;
        }

        echo '<h2>' . esc_html($this->title) . '</h2>';
        echo '<table class="form-table wprs-metabox-container">';

        // 设置字段值
        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'setDefaultValue')) {
                $value = get_user_meta($user->ID, $component->getName(), true);
                if ($value) {
                    $component->setDefaultValue($value);
                }
            }
        }

        // 渲染表单
        echo $this->form;

        echo '</table>';
    }

    /**
     * 保存 User Meta Box 数据
     *
     * @param int $user_id 用户ID
     */
    public function saveUserMetaBox(int $user_id)
    {
        // 检查权限
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }

        // 检查条件
        if (!$this->checkConditions($user_id, 'user')) {
            return;
        }

        // 保存字段值
        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'getName') && isset($_POST[$component->getName()])) {
                update_user_meta($user_id, $component->getName(), $_POST[$component->getName()]);
            }
        }
    }

    /**
     * 注册 Term Meta Box
     */
    protected function registerTermMetaBox()
    {
        // 获取分类法
        $taxonomies = [];
        foreach ($this->conditions as $condition) {
            if ($condition['key'] === 'taxonomy' && $condition['operator'] === '=') {
                $taxonomies[] = $condition['value'];
            }
        }

        // 如果没有指定分类法，则使用所有分类法
        if (empty($taxonomies)) {
            $taxonomies = get_taxonomies(['public' => true]);
        }

        foreach ($taxonomies as $taxonomy) {
            // 添加分类表单字段
            add_action($taxonomy . '_add_form_fields', [$this, 'renderTermAddMetaBox']);

            // 编辑分类表单字段
            add_action($taxonomy . '_edit_form_fields', [$this, 'renderTermEditMetaBox']);

            // 保存分类表单字段
            add_action('created_' . $taxonomy, [$this, 'saveTermMetaBox']);
            add_action('edited_' . $taxonomy, [$this, 'saveTermMetaBox']);
        }
    }

    /**
     * 渲染添加分类页面的 Meta Box
     */
    public function renderTermAddMetaBox()
    {
        echo '<div class="wprs-metabox-container">';
        echo '<h2>' . esc_html($this->title) . '</h2>';

        // 渲染表单
        $this->form->render('body');

        echo '</div>';
    }

    /**
     * 渲染编辑分类页面的 Meta Box
     *
     * @param \WP_Term $term 分类对象
     */
    public function renderTermEditMetaBox($term)
    {
        // 检查条件
        if (!$this->checkConditions($term->term_id, 'term')) {
            return;
        }

        echo '<tr class="form-field wprs-metabox-container">';
        echo '<th scope="row"><h2>' . esc_html($this->title) . '</h2></th>';
        echo '<td>';

        // 设置字段值
        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'setDefaultValue')) {
                $value = get_term_meta($term->term_id, $component->getName(), true);
                if ($value) {
                    $component->setDefaultValue($value);
                }
            }
        }

        // 渲染表单
        echo $this->form;

        echo '</td>';
        echo '</tr>';
    }

    /**
     * 保存 Term Meta Box 数据
     *
     * @param int $term_id 分类ID
     */
    public function saveTermMetaBox(int $term_id)
    {
        // 检查条件
        if (!$this->checkConditions($term_id, 'term')) {
            return;
        }

        // 保存字段值
        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'getName') && isset($_POST[$component->getName()])) {
                update_term_meta($term_id, $component->getName(), $_POST[$component->getName()]);
            }
        }
    }

    /**
     * 注册选项页
     */
    protected function registerOptionsPage()
    {
        add_action('admin_menu', function () {
            add_options_page(
                $this->title,
                $this->title,
                'manage_options',
                $this->id,
                [$this, 'renderOptionsPage']
            );
        });

        add_action('admin_init', [$this, 'registerSettings']);
    }

    /**
     * 渲染选项页
     */
    public function renderOptionsPage()
    {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html($this->title) . '</h1>';
        echo '<form method="post" action="options.php">';

        settings_fields($this->id);
        do_settings_sections($this->id);

        submit_button();

        echo '</form>';
        echo '</div>';
    }

    /**
     * 注册设置
     */
    public function registerSettings()
    {
        register_setting($this->id, $this->id, [$this, 'sanitizeOptions']);

        add_settings_section(
            $this->id . '_section',
            '',
            '__return_false',
            $this->id
        );

        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'getName')) {
                add_settings_field(
                    $component->getName(),
                    method_exists($component, 'getCaption') ? $component->getCaption() : $component->getName(),
                    function () use ($component) {
                        // 设置字段值
                        if (method_exists($component, 'setDefaultValue')) {
                            $options = get_option($this->id, []);
                            if (isset($options[$component->getName()])) {
                                $component->setDefaultValue($options[$component->getName()]);
                            }
                        }

                        // 渲染字段
                        if (method_exists($component, '__toString')) {
                            echo $component;
                        } else if (method_exists($component, 'getControl')) {
                            echo $component->getControl();
                        } else if (method_exists($component, 'getHtml')) {
                            echo $component->getHtml();
                        } else {
                            // 如果没有合适的方法，尝试使用默认的表单渲染器
                            $renderer = $this->form->getRenderer();
                            if ($renderer && method_exists($renderer, 'renderControl')) {
                                echo $renderer->renderControl($component);
                            }
                        }
                    },
                    $this->id,
                    $this->id . '_section'
                );
            }
        }
    }

    /**
     * 净化选项值
     *
     * @param array|null $input 输入值
     *
     * @return array
     */
    public function sanitizeOptions($input): array
    {
        $output = [];

        // 如果输入为 null，则将其转换为空数组
        if ($input === null) {
            $input = [];
        }

        // 确保输入是数组
        if (!is_array($input)) {
            return $output;
        }

        foreach ($this->form->getComponents() as $component) {
            if (method_exists($component, 'getName') && isset($input[$component->getName()])) {
                $output[$component->getName()] = $input[$component->getName()];
            }
        }

        return $output;
    }

    /**
     * 检查条件是否满足
     *
     * @param int    $object_id 对象ID
     * @param string $type      对象类型
     *
     * @return bool
     */
    protected function checkConditions(int $object_id, string $type): bool
    {
        if (empty($this->conditions)) {
            return true;
        }

        foreach ($this->conditions as $condition) {
            $key = $condition['key'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            $object_value = null;

            // 根据对象类型获取对象值
            switch ($type) {
                case 'post':
                    if ($key === 'post_type') {
                        $object_value = get_post_type($object_id);
                    } else {
                        $object_value = get_post_meta($object_id, $key, true);
                    }
                    break;
                case 'term':
                    if ($key === 'taxonomy') {
                        $term = get_term($object_id);
                        $object_value = $term ? $term->taxonomy : null;
                    } else {
                        $object_value = get_term_meta($object_id, $key, true);
                    }
                    break;
                case 'user':
                    $object_value = get_user_meta($object_id, $key, true);
                    break;
                case 'options':
                    $object_value = get_option($key);
                    break;
            }

            // 检查条件
            switch ($operator) {
                case '=':
                case '==':
                    if ($object_value != $value) {
                        return false;
                    }
                    break;
                case '!=':
                case '<>':
                    if ($object_value == $value) {
                        return false;
                    }
                    break;
                case '>':
                    if ($object_value <= $value) {
                        return false;
                    }
                    break;
                case '>=':
                    if ($object_value < $value) {
                        return false;
                    }
                    break;
                case '<':
                    if ($object_value >= $value) {
                        return false;
                    }
                    break;
                case '<=':
                    if ($object_value > $value) {
                        return false;
                    }
                    break;
                case 'IN':
                case 'in':
                    if (!in_array($object_value, (array)$value)) {
                        return false;
                    }
                    break;
                case 'NOT IN':
                case 'not in':
                    if (in_array($object_value, (array)$value)) {
                        return false;
                    }
                    break;
            }
        }

        return true;
    }
}

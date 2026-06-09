<?php

namespace Wenprise\Forms;

use Nette\HtmlStringable;
use Nette\Forms\ControlGroup;
use Wenprise\Forms\Containers\Repeater;
use Wenprise\Forms\Datastores\IDatastore;
use Wenprise\Forms\Renders\DefaultFormRender;
use Wenprise\Forms\Translator\DefaultTranslator;

class Form extends \Nette\Forms\Form implements HtmlStringable
{
    public ?IDatastore $datastore = null;
    protected string $save_method = 'default';
    protected array $group_collections = [];
    protected ?string $active_group_name = null;

    /**
     * @param \Wenprise\Forms\Datastores\IDatastore $datastore
     *
     * @return void
     */
    public function setDatastore(IDatastore $datastore): void
    {
        $datastore->setForm($this);
        $this->datastore = $datastore;
    }

    public function save(): void
    {
        if ($this->isSuccess()) {
            $this->datastore->save();
        }
    }


    public function __construct(?string $name = null)
    {
        new Init();

        $this->setRenderer(new DefaultFormRender());
        $this->setTranslator(new DefaultTranslator());

        wp_enqueue_style('wprs-forms-main');

        /**
         * @todo 无法加载？
         */
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('jquery-form');
            wp_enqueue_script('wprs-sweetalert');
        });

        $this->httpRequest = (new \Nette\Http\RequestFactory())->fromGlobals();

        parent::__construct($name);

        if (method_exists($this, 'allowCrossOrigin')) {
            $this->allowCrossOrigin();
        }
    }


    /**
     * 添加 Csrf 跨站保护控件
     *
     * @param string $name
     * @param null   $errorMessage
     *
     * @return \Wenprise\Forms\Controls\CsrfInput
     */
    public function addCsrf(string $name, ?string $errorMessage = null): Controls\CsrfInput
    {
        return $this[$name] = (new Controls\CsrfInput($errorMessage));
    }


    /**
     * 添加 WordPress Tinymce 可视化编辑器控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     *
     * @return Controls\TextEditor
     */
    public function addEditor(string $name, ?string $label = null, ?array $settings = null): Controls\TextEditor
    {
        return $this[$name] = (new Controls\TextEditor($label, $settings));
    }


    /**
     * 添加独立 repeater 容器。
     *
     * @param string      $name       容器名称。
     * @param string|null $label      容器标签。
     * @param callable    $factory    行容器工厂。
     * @param int         $copy_count 默认行数。
     * @param int|null    $max_copies 最大行数。
     *
     * @return Repeater
     */
    public function addRepeater(string $name, ?string $label, callable $factory, int $copy_count = 1, ?int $max_copies = null): Repeater
    {
        return $this[$name] = new Repeater($label, $factory, $copy_count, $max_copies);
    }


    /**
     * Ajax 上传，支持单文件和多文件控件
     *
     * @param string      $name
     * @param string|null $label
     * @param bool        $multiple
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\AjaxUploadInput
     */
    public function addAjaxUpload(string $name, ?string $label = null, bool $multiple = false, ?array $settings = null): Controls\AjaxUploadInput
    {
        return $this[$name] = (new Controls\AjaxUploadInput($label, $multiple, $settings));
    }


    /**
     * WordPress 上传，支持单文件和多文件控件
     *
     * @param string      $name
     * @param string|null $label
     * @param bool        $multiple
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\WpUploaderInput
     */
    public function addWpUploader(string $name, ?string $label = null, bool $multiple = false, ?array $settings = null): Controls\WpUploaderInput
    {
        return $this[$name] = (new Controls\WpUploaderInput($label, $multiple, $settings));
    }


    /**
     * 带前缀或者后缀的文本输入
     *
     * @param string      $name
     * @param string|null $label
     * @param int|null    $maxLength
     * @param string|null $prefix
     * @param string|null $suffix
     *
     * @return \Wenprise\Forms\Controls\GroupInput
     */
    public function addGroupInput(string $name, ?string $label = null, ?int $maxLength = null, ?string $prefix = null, ?string $suffix = null): Controls\GroupInput
    {
        return $this[$name] = (new Controls\GroupInput($label, $maxLength, $prefix, $suffix));
    }


    /**
     * 添加 Slider 滑动输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings Slider 设置
     *
     * @return \Wenprise\Forms\Controls\SliderInput
     */
    public function addSlider(string $name, ?string $label = null, ?array $settings = null): Controls\SliderInput
    {
        return $this[$name] = (new Controls\SliderInput($label, $settings));
    }


    /**
     * 日期选择
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\DatePickerInput
     */
    public function addDatePicker(string $name, ?string $label = null, ?array $settings = null): Controls\DatePickerInput
    {
        return $this[$name] = (new Controls\DatePickerInput($label, $settings));
    }


    /**
     * 日期选择
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 表单设置
     *
     * @return \Wenprise\Forms\Controls\DateRangePickerInput
     */
    public function addDateRangePicker(string $name, ?string $label = null, ?array $settings = null): Controls\DateRangePickerInput
    {
        return $this[$name] = (new Controls\DateRangePickerInput($label, $settings));
    }


    /**
     * 颜色选择
     *
     * @param string      $name     string  表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\BirthdayPickerInput
     */
    public function addBirthdayPicker(string $name, ?string $label = null, ?array $settings = null): Controls\BirthdayPickerInput
    {
        return $this[$name] = (new Controls\BirthdayPickerInput($label, $settings));
    }


    /**
     * 颜色选择
     *
     * @param string      $name     string  表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\ColorpickerInput
     */
    public function addColorPicker(string $name, ?string $label = null, ?array $settings = null): Controls\ColorpickerInput
    {
        return $this[$name] = (new Controls\ColorpickerInput($label, $settings));
    }


    /**
     * 关联选择
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     * @param array|null  $field
     *
     * @return \Wenprise\Forms\Controls\ChainedInput
     */
    public function addChainedSelect(string $name, ?string $label = null, ?array $settings = null, ?array $field = null): Controls\ChainedInput
    {
        return $this[$name] = (new Controls\ChainedInput($label, $settings, $field));
    }


    /**
     * 添加 Html 控件
     *
     * @param string      $name
     * @param string|null $caption
     *
     * @return \Wenprise\Forms\Controls\HtmlContent
     */
    public function addHtml(string $name, ?string $caption = null): Controls\HtmlContent
    {
        return $this[$name] = (new Controls\HtmlContent($caption));
    }


    /**
     * 获取 SMS 验证码
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\SmsInput
     */
    public function AddSmsInput(string $name, ?string $label = null, ?array $settings = null): Controls\SmsInput
    {
        return $this[$name] = (new Controls\SmsInput($label, $settings));
    }


    /**
     * 添加 Captcha 验证码
     *
     * @param string      $name
     * @param string|null $label
     *
     * @return \Wenprise\Forms\Controls\CaptchaInput
     */
    public function AddCaptcha(string $name, ?string $label = null): Controls\CaptchaInput
    {
        return $this[$name] = (new Controls\CaptchaInput($label));
    }


    /**
     * 表格输入
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     * @param array|null  $field
     *
     * @return \Wenprise\Forms\Controls\TableInput
     */
    public function addTableInput(string $name, ?string $label = null, ?array $settings = null, ?array $field = []): Controls\TableInput
    {
        return $this[$name] = (new Controls\TableInput($label, $settings, $field));
    }


    /**
     * Chosen 输入控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $items
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\ChosenInput
     */
    public function addChosen(string $name, ?string $label = null, ?array $items = null, ?array $settings = null): Controls\ChosenInput
    {
        return $this[$name] = (new Controls\ChosenInput($label, $items, $settings));
    }


    /**
     * Chosen 输入控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $items
     *
     * @return \Wenprise\Forms\Controls\SwitchInput
     */
    public function addSwitch(string $name, ?string $label = null, ?array $items = null): Controls\SwitchInput
    {
        return $this[$name] = (new Controls\SwitchInput($label, $items));
    }


    /**
     * Chosen 多选输入控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $items
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\MultiChosenInput
     */
    public function addMultiChosen(string $name, ?string $label = null, ?array $items = null, ?array $settings = null): Controls\MultiChosenInput
    {
        return $this[$name] = (new Controls\MultiChosenInput($label, $items, $settings));
    }


    /**
     * 签字/签名
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 表单设置
     *
     * @return \Wenprise\Forms\Controls\SignatureInput
     */
    public function addSignature(string $name, ?string $label = null, ?array $settings = null): Controls\SignatureInput
    {
        return $this[$name] = (new Controls\SignatureInput($label, $settings));
    }


    /**
     * jQuery Autocomplete 输入提示
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 表单设置
     *
     * @return \Wenprise\Forms\Controls\AutoCompleteInput
     */
    public function addAutocomplete(string $name, ?string $label = null, ?array $settings = null): Controls\AutoCompleteInput
    {
        return $this[$name] = (new Controls\AutoCompleteInput($label, $settings));
    }


    /**
     * Chosen 输入控件
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $items
     * @param array|null  $settings
     *
     * @return \Wenprise\Forms\Controls\ImagePickerInput
     */
    public function addImagePicker(string $name, ?string $label = null, ?array $items = null, ?array $settings = null): Controls\ImagePickerInput
    {
        return $this[$name] = (new Controls\ImagePickerInput($label, $items, $settings));
    }


    /**
     * 星级评分输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\StarRatingInput
     */
    public function addStarRating(string $name, ?string $label = null, ?array $settings = null): Controls\StarRatingInput
    {
        return $this[$name] = (new Controls\StarRatingInput($label, $settings));
    }

    /**
     * 星级评分输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    string  表单标签
     * @param array|null  $settings array   表单设置
     *
     * @return \Wenprise\Forms\Controls\CheckboxTreeInput
     */
    public function addCheckboxTree(string $name, ?string $label = null, ?array $settings = null): Controls\CheckboxTreeInput
    {
        return $this[$name] = (new Controls\CheckboxTreeInput($label, $settings));
    }


    /**
     * 添加分组（Tab/Step 通用底层实现）。
     *
     * @param string      $name       分组唯一名称
     * @param string|null $label      分组标题
     * @param bool        $is_active  是否激活为当前分组
     * @param string      $group_type 分组类型，支持 tab 或 step
     *
     * @return \Nette\Forms\ControlGroup
     */
    protected function add_group(string $name, ?string $label = null, bool $is_active = false, string $group_type = 'tab'): ControlGroup
    {
        $current_group_type = in_array($group_type, ['tab', 'step'], true) ? $group_type : 'tab';
        $group_name = trim($name);
        if ($group_name === '') {
            $group_name = $current_group_type . '_' . (count($this->group_collections) + 1);
        }

        $group_slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $group_name));
        $group_slug = trim($group_slug, '-');
        if ($group_slug === '') {
            $group_slug = $current_group_type . '-' . (count($this->group_collections) + 1);
        }

        $group = $this->addGroup($label ?? $group_name);
        $group->setOption('wprs_group_name', $group_name);
        $group->setOption('wprs_group_slug', $group_slug);
        $group->setOption('wprs_group_label', $label ?? $group_name);
        $group->setOption('wprs_group_type', $current_group_type);
        $group->setOption('wprs_group_active', false);
        // Backward compatibility for existing tab render flow.
        $group->setOption('wprs_tab_name', $group_name);
        $group->setOption('wprs_tab_slug', $group_slug);
        $group->setOption('wprs_tab_label', $label ?? $group_name);
        $group->setOption('wprs_tab_active', false);

        $this->group_collections[$group_name] = $group;

        if ($is_active || $this->active_group_name === null) {
            $this->active_group_name = $group_name;
        }

        foreach ($this->group_collections as $current_group_name => $current_group) {
            $is_current_group_active = $current_group_name === $this->active_group_name;
            $current_group->setOption('wprs_group_active', $is_current_group_active);
            $current_group->setOption('wprs_tab_active', $is_current_group_active);
        }

        $this->setCurrentGroup($group);

        return $group;
    }


    /**
     * 添加 Tab 分组。
     *
     * @param string      $name      Tab 唯一名称
     * @param string|null $label     Tab 标题
     * @param bool        $is_active 是否激活为当前 Tab
     *
     * @return \Nette\Forms\ControlGroup
     */
    public function add_tab(string $name, ?string $label = null, bool $is_active = false): ControlGroup
    {
        return $this->add_group($name, $label, $is_active, 'tab');
    }


    /**
     * 添加 Tab 分组（驼峰别名）。
     *
     * @param string      $name      Tab 唯一名称
     * @param string|null $label     Tab 标题
     * @param bool        $is_active 是否激活为当前 Tab
     *
     * @return \Nette\Forms\ControlGroup
     */
    public function addTab(string $name, ?string $label = null, bool $is_active = false): ControlGroup
    {
        return $this->add_tab($name, $label, $is_active);
    }


    /**
     * 添加 Step 分组。
     *
     * @param string      $name      Step 唯一名称
     * @param string|null $label     Step 标题
     * @param bool        $is_active 是否激活为当前 Step
     *
     * @return \Nette\Forms\ControlGroup
     */
    public function add_step(string $name, ?string $label = null, bool $is_active = false): ControlGroup
    {
        return $this->add_group($name, $label, $is_active, 'step');
    }


    /**
     * 添加 Step 分组（驼峰别名）。
     *
     * @param string      $name      Step 唯一名称
     * @param string|null $label     Step 标题
     * @param bool        $is_active 是否激活为当前 Step
     *
     * @return \Nette\Forms\ControlGroup
     */
    public function addStep(string $name, ?string $label = null, bool $is_active = false): ControlGroup
    {
        return $this->add_step($name, $label, $is_active);
    }


    /**
     * 结束当前 Tab 分组，后续字段不再归入当前 Tab。
     *
     * @return static
     */
    public function end_tab(): static
    {
        $this->setCurrentGroup(null);

        return $this;
    }


    /**
     * 结束当前 Tab 分组（驼峰别名）。
     *
     * @return static
     */
    public function endTab(): static
    {
        return $this->end_tab();
    }


    /**
     * 结束当前 Step 分组，后续字段不再归入当前 Step。
     *
     * @return static
     */
    public function end_step(): static
    {
        return $this->end_tab();
    }


    /**
     * 结束当前 Step 分组（驼峰别名）。
     *
     * @return static
     */
    public function endStep(): static
    {
        return $this->end_step();
    }


}

<?php

namespace Wenprise\Forms;

use Nette\HtmlStringable;
use Nette\Forms\ControlGroup;
use Wenprise\Forms\Datastores\IDatastore;
use Wenprise\Forms\Renders\DefaultFormRender;
use Wenprise\Forms\Translator\DefaultTranslator;

class Form extends \Nette\Forms\Form implements HtmlStringable
{
    /**
     * 已注册的表单实例集合。
     *
     * @var array<string, self>
     */
    protected static array $registered_forms = [];

    public ?IDatastore $datastore = null;
    protected array $tab_groups = [];
    protected ?string $active_tab_name = null;
    protected string $save_method = 'sync';
    protected string $ajax_action = 'wprs_save_form_data';
    protected string $ajax_nonce_action = 'wprs_save_form_data';
    protected string $form_id = '';
    protected string $form_key = '';

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

    /**
     * 注册当前表单实例到运行时注册表。
     *
     * @return void
     */
    protected function register_form_instance(): void
    {
        self::$registered_forms[ $this->form_id ] = $this;
    }

    /**
     * 按 form_id 获取已注册的表单实例。
     *
     * @param string $form_id 表单唯一标识
     *
     * @return self|null
     */
    public static function get_registered_form(string $form_id): ?self
    {
        return self::$registered_forms[ $form_id ] ?? null;
    }

    /**
     * 获取当前表单唯一标识。
     *
     * @return string
     */
    public function getFormId(): string
    {
        return $this->form_id;
    }

    /**
     * 设置表单业务标识，用于跨请求重建表单。
     *
     * @param string $form_key 表单业务标识
     *
     * @return self
     */
    public function setFormKey(string $form_key): self
    {
        $this->form_key = trim($form_key);
        $this->getElementPrototype()->setAttribute('data-wprs-form-key', $this->form_key);

        return $this;
    }

    /**
     * 获取表单业务标识。
     *
     * @return string
     */
    public function getFormKey(): string
    {
        return $this->form_key;
    }

    /**
     * 设置表单保存方式，支持 sync 和 ajax。
     *
     * @param string $save_method 保存方式
     *
     * @return self
     */
    public function setSaveMethod(string $save_method): self
    {
        $save_method = strtolower(trim($save_method));

        if ( ! in_array($save_method, ['sync', 'ajax'], true)) {
            throw new \InvalidArgumentException('Save method must be "sync" or "ajax".');
        }

        $this->save_method = $save_method;

        $this->getElementPrototype()->setAttribute('data-wprs-save-method', $this->save_method);

        if ($this->save_method === 'ajax') {
            $this->getElementPrototype()->setAttribute('data-wprs-ajax-action', $this->ajax_action);
            $this->getElementPrototype()->setAttribute('data-wprs-ajax-nonce', wp_create_nonce($this->ajax_nonce_action));
        }

        return $this;
    }

    public function getSaveMethod(): string
    {
        return $this->save_method;
    }

    /**
     * 兼容下划线命名的保存方式设置方法。
     *
     * @param string $save_method 保存方式
     *
     * @return self
     */
    public function set_save_method(string $save_method): self
    {
        return $this->setSaveMethod($save_method);
    }

    /**
     * 兼容下划线命名的获取保存方式方法。
     *
     * @return string
     */
    public function get_save_method(): string
    {
        return $this->getSaveMethod();
    }

    /**
     * 设置 Ajax 保存 action 名称。
     *
     * @param string $ajax_action Ajax action 名称
     *
     * @return self
     */
    public function setAjaxAction(string $ajax_action): self
    {
        $this->ajax_action = trim($ajax_action);

        if ($this->save_method === 'ajax') {
            $this->getElementPrototype()->setAttribute('data-wprs-ajax-action', $this->ajax_action);
        }

        return $this;
    }

    /**
     * 获取 Ajax 保存 action 名称。
     *
     * @return string
     */
    public function getAjaxAction(): string
    {
        return $this->ajax_action;
    }

    /**
     * 兼容下划线命名的 Ajax action 设置方法。
     *
     * @param string $ajax_action Ajax action 名称
     *
     * @return self
     */
    public function set_ajax_action(string $ajax_action): self
    {
        return $this->setAjaxAction($ajax_action);
    }

    /**
     * 兼容下划线命名的 Ajax action 获取方法。
     *
     * @return string
     */
    public function get_ajax_action(): string
    {
        return $this->getAjaxAction();
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

        $this->form_id = 'wprs_form_' . ($name ?: wp_generate_uuid4());
        $this->form_key = (string) ($name ?: $this->form_id);
        $this->getElementPrototype()->setAttribute('data-wprs-form-id', $this->form_id);
        $this->getElementPrototype()->setAttribute('data-wprs-form-key', $this->form_key);
        $this->register_form_instance();

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
     * 添加文本克隆输入控件
     *
     * @param string      $name     表单名称
     * @param string|null $label    表单标签
     * @param array|null  $settings 设置
     *
     * @return \Wenprise\Forms\Controls\CloneInput
     */
    public function addCloneInput(string $name, ?string $label = null, ?array $settings = null): Controls\CloneInput
    {
        return $this[$name] = (new Controls\CloneInput($label, $settings));
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
     * 询价表格属兔
     *
     * @param string      $name
     * @param string|null $label
     * @param array|null  $settings
     * @param array|null  $fields
     *
     * @return \Wenprise\Forms\Controls\InquiryInput
     */
    public function addInquiryInput(string $name, ?string $label = null, ?array $settings = null, ?array $fields = []): Controls\InquiryInput
    {
        return $this[$name] = (new Controls\InquiryInput($label, $settings, $fields));
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
     * 添加 Tab 分组。
     *
     * @param string      $name     Tab 唯一名称
     * @param string|null $label    Tab 标题
     * @param bool        $is_active 是否激活为当前 Tab
     *
     * @return \Nette\Forms\ControlGroup
     */
    public function add_tab(string $name, ?string $label = null, bool $is_active = false): ControlGroup
    {
        $tab_name = trim($name);
        if ($tab_name === '') {
            $tab_name = 'tab_' . (count($this->tab_groups) + 1);
        }

        $tab_slug = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $tab_name));
        $tab_slug = trim($tab_slug, '-');
        if ($tab_slug === '') {
            $tab_slug = 'tab-' . (count($this->tab_groups) + 1);
        }

        $group = $this->addGroup($label ?? $tab_name);
        $group->setOption('wprs_tab_name', $tab_name);
        $group->setOption('wprs_tab_slug', $tab_slug);
        $group->setOption('wprs_tab_label', $label ?? $tab_name);
        $group->setOption('wprs_tab_active', false);

        $this->tab_groups[$tab_name] = $group;

        if ($is_active || $this->active_tab_name === null) {
            $this->active_tab_name = $tab_name;
        }

        foreach ($this->tab_groups as $current_tab_name => $current_group) {
            $current_group->setOption('wprs_tab_active', $current_tab_name === $this->active_tab_name);
        }

        $this->setCurrentGroup($group);

        return $group;
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


}

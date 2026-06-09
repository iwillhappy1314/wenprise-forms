<?php

namespace Wenprise\Forms;

use Wenprise\Forms\Containers\Repeater;

/**
 * 为嵌套容器提供 Wenprise Forms 扩展控件与 repeater API。
 */
class Container extends \Nette\Forms\Container
{
    /**
     * 添加独立 repeater 容器。
     *
     * @param string        $name       容器名称。
     * @param string|null   $label      容器标签。
     * @param callable      $factory    行容器工厂。
     * @param int           $copy_count 默认行数。
     * @param int|null      $max_copies 最大行数。
     *
     * @return Repeater
     */
    public function addRepeater(string $name, ?string $label, callable $factory, int $copy_count = 1, ?int $max_copies = null): Repeater
    {
        return $this[$name] = new Repeater($label, $factory, $copy_count, $max_copies);
    }

    /**
     * 添加 WordPress Tinymce 可视化编辑器控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 编辑器设置。
     *
     * @return Controls\TextEditor
     */
    public function addEditor(string $name, ?string $label = null, ?array $settings = null): Controls\TextEditor
    {
        return $this[$name] = new Controls\TextEditor($label, $settings);
    }

    /**
     * 添加 Ajax 上传控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param bool        $multiple 是否多文件。
     * @param array|null  $settings 上传设置。
     *
     * @return Controls\AjaxUploadInput
     */
    public function addAjaxUpload(string $name, ?string $label = null, bool $multiple = false, ?array $settings = null): Controls\AjaxUploadInput
    {
        return $this[$name] = new Controls\AjaxUploadInput($label, $multiple, $settings);
    }

    /**
     * 添加 WordPress 媒体上传控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param bool        $multiple 是否多文件。
     * @param array|null  $settings 上传设置。
     *
     * @return Controls\WpUploaderInput
     */
    public function addWpUploader(string $name, ?string $label = null, bool $multiple = false, ?array $settings = null): Controls\WpUploaderInput
    {
        return $this[$name] = new Controls\WpUploaderInput($label, $multiple, $settings);
    }

    /**
     * 添加前后缀文本输入控件。
     *
     * @param string      $name      控件名称。
     * @param string|null $label     控件标签。
     * @param int|null    $maxLength 最大长度。
     * @param string|null $prefix    前缀。
     * @param string|null $suffix    后缀。
     *
     * @return Controls\GroupInput
     */
    public function addGroupInput(string $name, ?string $label = null, ?int $maxLength = null, ?string $prefix = null, ?string $suffix = null): Controls\GroupInput
    {
        return $this[$name] = new Controls\GroupInput($label, $maxLength, $prefix, $suffix);
    }

    /**
     * 添加 Slider 控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\SliderInput
     */
    public function addSlider(string $name, ?string $label = null, ?array $settings = null): Controls\SliderInput
    {
        return $this[$name] = new Controls\SliderInput($label, $settings);
    }

    /**
     * 添加日期选择控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\DatePickerInput
     */
    public function addDatePicker(string $name, ?string $label = null, ?array $settings = null): Controls\DatePickerInput
    {
        return $this[$name] = new Controls\DatePickerInput($label, $settings);
    }

    /**
     * 添加日期范围控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\DateRangePickerInput
     */
    public function addDateRangePicker(string $name, ?string $label = null, ?array $settings = null): Controls\DateRangePickerInput
    {
        return $this[$name] = new Controls\DateRangePickerInput($label, $settings);
    }

    /**
     * 添加生日选择控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\BirthdayPickerInput
     */
    public function addBirthdayPicker(string $name, ?string $label = null, ?array $settings = null): Controls\BirthdayPickerInput
    {
        return $this[$name] = new Controls\BirthdayPickerInput($label, $settings);
    }

    /**
     * 添加颜色选择控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\ColorpickerInput
     */
    public function addColorPicker(string $name, ?string $label = null, ?array $settings = null): Controls\ColorpickerInput
    {
        return $this[$name] = new Controls\ColorpickerInput($label, $settings);
    }

    /**
     * 添加关联选择控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     * @param array|null  $field    字段配置。
     *
     * @return Controls\ChainedInput
     */
    public function addChainedSelect(string $name, ?string $label = null, ?array $settings = null, ?array $field = null): Controls\ChainedInput
    {
        return $this[$name] = new Controls\ChainedInput($label, $settings, $field);
    }

    /**
     * 添加 HTML 内容控件。
     *
     * @param string      $name    控件名称。
     * @param string|null $caption 内容。
     *
     * @return Controls\HtmlContent
     */
    public function addHtml(string $name, ?string $caption = null): Controls\HtmlContent
    {
        return $this[$name] = new Controls\HtmlContent($caption);
    }

    /**
     * 添加 chosen 单选控件、
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $items    选项列表。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\ChosenInput
     */
    public function addChosen(string $name, ?string $label = null, ?array $items = null, ?array $settings = null): Controls\ChosenInput
    {
        return $this[$name] = new Controls\ChosenInput($label, $items, $settings);
    }

    /**
     * 添加 switch 控件。
     *
     * @param string      $name  控件名称。
     * @param string|null $label 控件标签。
     * @param array|null  $items 选项列表。
     *
     * @return Controls\SwitchInput
     */
    public function addSwitch(string $name, ?string $label = null, ?array $items = null): Controls\SwitchInput
    {
        return $this[$name] = new Controls\SwitchInput($label, $items);
    }

    /**
     * 添加 chosen 多选控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $items    选项列表。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\MultiChosenInput
     */
    public function addMultiChosen(string $name, ?string $label = null, ?array $items = null, ?array $settings = null): Controls\MultiChosenInput
    {
        return $this[$name] = new Controls\MultiChosenInput($label, $items, $settings);
    }

    /**
     * 添加签名控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\SignatureInput
     */
    public function addSignature(string $name, ?string $label = null, ?array $settings = null): Controls\SignatureInput
    {
        return $this[$name] = new Controls\SignatureInput($label, $settings);
    }

    /**
     * 添加自动完成输入控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\AutoCompleteInput
     */
    public function addAutocomplete(string $name, ?string $label = null, ?array $settings = null): Controls\AutoCompleteInput
    {
        return $this[$name] = new Controls\AutoCompleteInput($label, $settings);
    }

    /**
     * 添加图片选择控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $items    选项列表。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\ImagePickerInput
     */
    public function addImagePicker(string $name, ?string $label = null, ?array $items = null, ?array $settings = null): Controls\ImagePickerInput
    {
        return $this[$name] = new Controls\ImagePickerInput($label, $items, $settings);
    }

    /**
     * 添加星级评分控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 控件设置。
     *
     * @return Controls\StarRatingInput
     */
    public function addStarRating(string $name, ?string $label = null, ?array $settings = null): Controls\StarRatingInput
    {
        return $this[$name] = new Controls\StarRatingInput($label, $settings);
    }

    /**
     * 添加复选树控件。
     *
     * @param string      $name     控件名称。
     * @param string|null $label    控件标签。
     * @param array|null  $settings 选项树。
     *
     * @return Controls\CheckboxTreeInput
     */
    public function addCheckboxTree(string $name, ?string $label = null, ?array $settings = null): Controls\CheckboxTreeInput
    {
        return $this[$name] = new Controls\CheckboxTreeInput($label, $settings);
    }
}

<?php

namespace Wenprise\Forms\Containers;

use Nette\ComponentModel\IComponent;
use Nette\Forms\Form;
use Nette\Forms\Helpers;
use Nette\Utils\Arrays;
use Wenprise\Forms\Container;

/**
 * 独立的 repeater 数据容器，用于管理重复字段组。
 */
class Repeater extends \Nette\Forms\Container
{
    /**
     * repeater 标签。
     */
    protected ?string $label = null;

    /**
     * 行容器工厂。
     *
     * @var callable
     */
    protected $factory;

    /**
     * 默认行数。
     */
    protected int $copy_count = 1;

    /**
     * 最大行数。
     */
    protected ?int $max_copies = null;

    /**
     * 是否已初始化。
     */
    protected bool $is_initialized = false;

    /**
     * 用户自定义选项。
     *
     * @var array<string, mixed>
     */
    protected array $options = [
        'rendered' => false,
    ];

    /**
     * 初始化 repeater 容器。
     *
     * @param string|null $label      repeater 标签。
     * @param callable    $factory    行容器工厂。
     * @param int         $copy_count 默认行数。
     * @param int|null    $max_copies 最大行数。
     */
    public function __construct(?string $label, callable $factory, int $copy_count = 1, ?int $max_copies = null)
    {
        $this->label = $label;
        $this->factory = $factory;
        $this->copy_count = max(1, $copy_count);
        $this->max_copies = $max_copies;

        $this->monitor(Form::class, function (): void {
            $this->initialize();
        });
    }

    /**
     * 在接入表单后初始化默认行或提交行。
     *
     * @return void
     */
    public function initialize(): void
    {
        if ($this->is_initialized) {
            return;
        }

        $this->is_initialized = true;
        $submitted_rows = $this->getSubmittedRows();

        if (is_array($submitted_rows)) {
            foreach (array_keys($submitted_rows) as $row_name) {
                $this->createRow((string) $row_name);
            }

            return;
        }

        foreach (range(0, $this->copy_count - 1) as $index) {
            $this->createRow((string) $index);
        }
    }

    /**
     * 获取 repeater 标签。
     *
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * 获取默认行数。
     *
     * @return int
     */
    public function getCopyCount(): int
    {
        return $this->copy_count;
    }

    /**
     * 获取最大行数。
     *
     * @return int|null
     */
    public function getMaxCopies(): ?int
    {
        return $this->max_copies;
    }

    /**
     * 设置自定义选项。
     *
     * @param string $key   选项名。
     * @param mixed  $value 选项值。
     *
     * @return static
     */
    public function setOption(string $key, mixed $value): static
    {
        $this->options[$key] = $value;

        return $this;
    }

    /**
     * 获取自定义选项。
     *
     * @param string $key 选项名。
     *
     * @return mixed
     */
    public function getOption(string $key): mixed
    {
        return $this->options[$key] ?? null;
    }

    /**
     * 获取所有行容器。
     *
     * @return array<string, Container>
     */
    public function getRows(): array
    {
        $rows = [];

        foreach ($this->getComponents() as $name => $component) {
            if ($component instanceof Container) {
                $rows[(string) $name] = $component;
            }
        }

        return $rows;
    }

    /**
     * 获取 repeater 的 HTML 名称前缀。
     *
     * @return string
     */
    public function getHtmlNamePrefix(): string
    {
        return Helpers::generateHtmlName($this->lookupPath(Form::class));
    }

    /**
     * 创建一行容器。
     *
     * @param string           $name     行名称。
     * @param array|object|null $defaults 默认值。
     *
     * @return Container
     */
    public function createRow(string $name, array|object|null $defaults = null): Container
    {
        $existing_row = $this->getComponent($name, false);
        if ($existing_row instanceof Container) {
            return $existing_row;
        }

        if ($this->max_copies !== null && count($this->getRows()) >= $this->max_copies) {
            throw new \RuntimeException('Repeater maximum copies exceeded.');
        }

        $row = new Container();
        $row->setCurrentGroup($this->currentGroup);
        $this->addComponent($row, $name);
        ($this->factory)($row);

        if ($defaults !== null) {
            $row->setDefaults($defaults);
        }

        return $row;
    }

    /**
     * 为设置值前动态补齐行容器。
     *
     * @param array|object $values       行值。
     * @param bool         $erase        是否清空旧值。
     * @param bool         $onlyDisabled 是否仅处理禁用控件。
     *
     * @return static
     */
    public function setValues(array|object $values, bool $erase = false, bool $onlyDisabled = false): static
    {
        foreach ($values as $name => $value) {
            if ((is_array($value) || is_object($value)) && !$this->getComponent((string) $name, false)) {
                $this->createRow((string) $name);
            }
        }

        return parent::setValues($values, $erase, $onlyDisabled);
    }

    /**
     * 获取首行容器，用于前端克隆模板。
     *
     * @return Container|null
     */
    public function getFirstRow(): ?Container
    {
        $rows = $this->getRows();

        return $rows === [] ? null : reset($rows);
    }

    /**
     * 根据当前请求提取提交的 repeater 行数据。
     *
     * @return array<string, mixed>|null
     */
    protected function getSubmittedRows(): ?array
    {
        $form = $this->getForm(false);
        if (!$form instanceof Form || !$form->isSubmitted()) {
            return null;
        }

        $path = explode(self::NameSeparator, $this->lookupPath(Form::class));
        $http_data = (array) $form->getHttpData();
        $rows = Arrays::get($http_data, $path, null);

        return is_array($rows) ? $rows : null;
    }
}

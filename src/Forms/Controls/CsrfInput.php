<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Form;
use Nette\Forms\Controls\HiddenField;

/**
 * CSRF 保护字段
 */
class CsrfInput extends HiddenField
{
    const PROTECTION = 'Wenprise\Forms\Controls\CsrfInput::validateCsrf';


    /**
     * @param string|object
     */
    public function __construct($errorMessage)
    {
        parent::__construct();

        $this->setOption('type', 'csrf');
        $this->setOmitted()
             ->setRequired()
             ->addRule(self::PROTECTION, $errorMessage);
    }

    /**
     * @return static
     * @internal
     */
    public function setValue($value)
    {
        return $this;
    }


    /**
     * 加载 HTTP 数据
     *
     * @return void
     */
    public function loadHttpData()
    {
        $this->value = $this->getHttpData(Form::DATA_TEXT);
    }


    /**
     * 生成 Csrf 令牌
     *
     * @return string
     */
    private function generateToken($random = null)
    {
        if ($random === null) {
            $name   = $this->getHtmlName();
            $random = wp_create_nonce($name);
        }

        return $random;
    }


    /**
     * 创建控件 HTML 元素
     *
     * @return String 获取生成的令牌
     */
    public function getControl()
    {
        return parent::getControl()->setAttribute('value', $this->generateToken());
    }


    /**
     * 验证 Csrf 令牌
     *
     * @param \Wenprise\Forms\Controls\CsrfInput $control
     *
     * @return bool
     */
    public static function validateCsrf(CsrfInput $control)
    {
        $name  = $control->getHtmlName();
        $value = $control->getValue();

        return wp_verify_nonce($value, $name);
    }

}

<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\HiddenField;
use Nette\Utils\Html;

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
	 * 生成 Csrf 令牌
	 *
	 * @param null $random
	 *
	 * @return string|null
	 */
    public function generateToken($random = null): ?string {
        if ($random === null) {
            $name   = $this->getHtmlName();
            $random = wp_create_nonce($name);
        }

        return $random;
    }


    /**
     * 创建控件 HTML 元素
     *
     * @return \Nette\Utils\Html 获取生成的令牌
     */
    public function getControl(): Html
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
    public static function validateCsrf(CsrfInput $control): bool {
        $name  = $control->getHtmlName();
        $value = $control->getValue();

        return wp_verify_nonce($value, $name);
    }

}

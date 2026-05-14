<?php

namespace Wenprise\Forms\Translator;

use Nette\Localization\ITranslator;

class DefaultTranslator implements ITranslator
{
    /**
     * 根据前端语言显示对应的字符串
     *
     * @param string|\Stringable $message 需要翻译的字符串
     *
     * @return string|\Stringable 翻译后的字符串
     *
     * @usage: Translator($message);
     */
    public function translate($message, ...$parameters): string
    {
        return __($message, 'wprs');
    }
}
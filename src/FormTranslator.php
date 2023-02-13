<?php

namespace Wenprise\Forms;

use Nette\Localization\Translator;

class FormTranslator implements Translator
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
    public function translate(string|\Stringable $message, mixed ...$parameters): string|\Stringable
    {
        return __($message, 'wprs');
    }
}
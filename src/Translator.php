<?php

namespace Wenprise\Forms;

use Nette\Localization\ITranslator;

class Translator implements ITranslator
{
    /**
     * 根据前端语言显示对应的字符串
     *
     * @param  string $message 需要翻译的字符串
     * @param  null   $count
     *
     * @return string 翻译后的字符串
     *
     * @usage: Translator($message);
     */
    public function translate($message, $count = null)
    {
        return __($message, 'wprs');
    }
}
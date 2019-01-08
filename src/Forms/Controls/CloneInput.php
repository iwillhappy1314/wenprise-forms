<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;


/**
 * 克隆输入
 */
class CloneInput extends BaseControl
{

    /**
     * CloneInput constructor.
     *
     * @param string|null $label
     * @param array|null  $settings
     */
    public function __construct($label = null, array $settings = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;

        $this->setOption('type', 'clone');
        $this->addCondition(Form::BLANK);
    }

    /**
     * 生成 HTML 元素
     *
     * @return string
     */
    public function getControl()
    {


        $el = parent::getControl()->appendAttribute('class', 'form-control');

        $name = $this->getHtmlName();
        $id   = $this->getHtmlId();

        // 模拟下拉选择默认值
        $value = $this->value;

        $input_group = Html::el('div class=input-group');

        $input_control = Html::el('input class=form-control')
                             ->setAttribute('name', $name);

        $input_control_raw = Html::el('input class=form-control')
                                 ->setAttribute('name', $name);

        $action_button = Html::el('span class=input-group-btn')
                             ->addHtml(
                                 Html::el('a class="btn btn-default js-remove-button"')
                                     ->setText('Remove')
                             );

        $clone_group = Html::el('div class=frm-group-input')
                           ->setAttribute('id', $id);

        $add_button = Html::el('button class="btn btn-default btn-sm js-more-button"')
                          ->setText('Add More Fields');

        // 设置默认值
        if (count($value) > 0) {

            // 设置第一个值
            $el->setAttribute('value', $value[ 0 ]);
            unset($value[ 0 ]);

            // 设置其他值
            foreach ($value as $k => $v) {
                if ($k != 0) {
                    $el .= $input_group
                        ->setHtml($input_control->setAttribute('value', $v) . $action_button);
                }
            }

        }

        $html = $clone_group->addHtml($el);

        $scripts = '<script>
                        jQuery(document).ready(function($) {
                            var max_fields      = 10,
                                wrapper         = $("#' . $id . '"),
                                add_button      = $(" .js-more-button"),
                                x = 1;
                                
                            $(add_button).click(function(e){
                                e.preventDefault();
                                
                                if(x < max_fields){
                                    $(wrapper).append(\'' . $input_group->setHtml($input_control_raw . $action_button) . '\');
                                    x++;
                                }
                                
                                return false;
                            });
                
                            $(wrapper).on("click", ".js-remove-button", function(e){
                                e.preventDefault();
                                
                                $(this).closest(".input-group").remove(); 
                                x--;
                                
                                return false;
                            })
                        });
                    </script>';

        return $html . $add_button . $scripts;

    }

    /**
     * 获取 HTML 名称
     *
     * @return mixed
     */
    public function getHtmlName()
    {
        return parent::getHtmlName() . '[]';
    }


    /**
     * 只要输入不为空，即为验证通过
     *
     * @return bool
     */
    public function isOk()
    {

        return $this->isDisabled()
               || $this->getValue() == 0
               || $this->getValue() !== null;
    }

}

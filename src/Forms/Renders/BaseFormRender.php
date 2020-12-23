<?php

namespace Wenprise\Forms\Renders;

use Nette;
use Nette\Utils\Html;
use Nette\Utils\IHtmlString;

/**
 * 转到表单到 HTML 输出
 */
class BaseFormRender extends Nette\Forms\Rendering\DefaultFormRenderer
{
    use Nette\SmartObject;

    /**
     * 渲染控件组
     *
     * @param Nette\Forms\Container|Nette\Forms\ControlGroup
     *
     * @return string
     */
    public function renderControls($parent)
    {
        if ( ! ($parent instanceof Nette\Forms\Container || $parent instanceof Nette\Forms\ControlGroup)) {
            throw new Nette\InvalidArgumentException('Argument must be Nette\Forms\Container or Nette\Forms\ControlGroup instance.');
        }

        $container = $this->getWrapper('controls container');

        $buttons = null;
        foreach ($parent->getControls() as $control) {
            if ($control->getOption('rendered') || $control->getOption('type') === 'hidden' || $control->getForm(false) !== $this->form) {
                // skip

                // add Html type
            } elseif ($control->getOption('type') === 'button' || $control->getOption('type') === 'html') {
                $buttons[] = $control;

            } else {
                if ($buttons) {
                    $container->addHtml($this->renderPairMulti($buttons));
                    $buttons = null;
                }
                $container->addHtml($this->renderPair($control));
            }
        }

        if ($buttons) {
            $container->addHtml($this->renderPairMulti($buttons));
        }

        $s = '';
        if (count($container)) {
            $s .= "\n" . $container . "\n";
        }

        return $s;
    }


    /**
     * 在一行中渲染多个控件
     *
     * @param Nette\Forms\IControl[]
     *
     * @return string
     */
    public function renderPairMulti(array $controls)
    {
        $s = [];
        foreach ($controls as $control) {
            if ( ! $control instanceof Nette\Forms\IControl) {
                throw new Nette\InvalidArgumentException('Argument must be array of Nette\Forms\IControl instances.');
            }

            $description = $control->getOption('description');

            if ($description instanceof IHtmlString) {

                $description = ' ' . $description;

            } elseif ($description != null) {

                // intentionally ==
                if ($control instanceof Nette\Forms\Controls\BaseControl) {
                    $description = $control->translate($description);
                }

                $description = ' ' . $this->getWrapper('control description')
                                          ->setText($description);

            } else {
                $description = '';
            }

            $control->setOption('rendered', true);
            $el = $control->getControl();

            if ($el instanceof Html && $el->getName() === 'input') {
                $el->class($this->getValue("control .$el->type"), true);
            }

            $s[] = $el . $description;
        }

        $pair = $this->getWrapper('pair container');
        $pair->addHtml($this->renderLabel($control));

        // wrapper class
        $pair->class($control->getOption('class'), true);
        $pair->addHtml($this->getWrapper('control container')
                            ->setHtml(implode(' ', $s)));

        return $pair->render(0);
    }

}

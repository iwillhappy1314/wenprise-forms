<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;


/**
 * 克隆输入
 */
class InquiryInput extends BaseControl
{

    private array $settings = [];

    public array $fields = [];

    /**
     * CloneInput constructor.
     *
     * @param string|null $label
     * @param array|null  $settings
     * @param array|null  $fields
     */
    public function __construct($label = null, array $settings = null, array $fields = null)
    {
        parent::__construct($label);
        $this->settings = (array)$settings;
        $this->fields   = (array)$fields;

        $this->setOption('type', 'inquiry');
        $this->addCondition(Form::BLANK);
    }


    /**
     * Loads HTTP data.
     */
    public function loadHttpData(): void
    {
        $fields = $this->fields;
        $names  = wp_list_pluck($fields, 'name');
        $values = [];

        $data_length = count($_POST[ $names[ 0 ] ]);

        for ($i = 0; $i < $data_length; $i++) {
            foreach ($names as $field) {
                $values[ $i ][ $field ] = $_POST[ $field ][ $i ];
            }
        }

        $this->setValue($values);
    }


    /**
     * 生成 HTML 元素
     *
     * @return string
     */
    public function getControl(): string
    {
        wp_enqueue_script('wprs-alpinejs');

        $id    = $this->getHtmlId();
        $js_id = str_replace('-', '_', $id);

        $settings = $this->settings;
        $fields   = $this->fields;
        $value    = $this->getValue();

        $field_names = [];
        $_field_names = [];

        foreach ($fields as $field) {
            $field_names[ $field[ 'name' ] ] = '';
            $_field_names[ '_' . $field[ 'name' ] ] = '';
        }

        ob_start();
        ?>
        <div x-data="<?= $js_id; ?>_handler()">

            <div>
                <table class="table rs-table rs-table-bordered rs-inquiry-input">
                    <thead class="thead-light">
                    <tr>
                        <?php foreach (wp_list_pluck($fields, 'label') as $label): ?>
                            <th><?= $label; ?></th>
                        <?php endforeach; ?>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="(field, index) in fields" :key="index">
                        <tr>
                            <?php foreach (wp_list_pluck($fields, 'name') as $name): ?>
                                <td>
                                    <div x-text="field.<?= $name; ?>">
                                </td>
                            <?php endforeach; ?>
                            <td>
                                <button type="button" class="rs-btn rs-btn-default" @click="removeField(index)">&times;</button>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>

            <table class="table rs-table rs-table-bordered rs-inquiry-input">
                <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <?php foreach (wp_list_pluck($fields, 'label') as $label): ?>
                        <th><?= $label; ?></th>
                    <?php endforeach; ?>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <template x-for="(field, index) in fields" :key="index">
                    <tr>
                        <td x-text="index + 1"></td>
                        <?php foreach (wp_list_pluck($fields, 'name') as $name): ?>
                            <td><input x-model="field.<?= $name; ?>" type="text" name="<?= $name; ?>[]" class="form-control rs-form-control"></td>
                        <?php endforeach; ?>
                        <td>
                            <button type="button" class="rs-btn rs-btn-default" @click="removeField(index)">&times;</button>
                        </td>
                    </tr>
                </template>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="<?= count($field_names) + 2; ?>" class="text-right">
                        <button type="button" class="rs-btn rs-btn-primary" @click="addNewField()"><?= __('+ Add Row', 'wprs'); ?></button>
                    </td>
                </tr>
                </tfoot>
            </table>

            <div>
                <button @click="open = !open" class="rs-btn rs-btn-primary"><?= __('+ Add Row', 'wprs'); ?></button>
                <dialog x-show="open" x-dialog="open = false">
                    <div>
                        <fieldset class="rs-form-row">

                            <?php foreach ($fields as $field): ?>
                                <div class="rs-form-group rs-form--text rs-row rs-col-md-12" id="rs-form-company_name">
                                    <div class="rs-col-sm-3 rs-control-label">
                                        <label for="frm-company_name"><?= $field[ 'label' ]; ?></label>
                                    </div>
                                    <div class="rs-col-sm-9 rs-control-input">
                                        <input x-model="_field._<?= $field[ 'name' ]; ?>" type="text" name="_<?= $field[ 'name' ]; ?>" class="form-control rs-form-control">
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </fieldset>

                        <div>
                            <button type="button" class="rs-btn rs-btn-default" @click="addNewField2()"><?= __('Add', 'wprs'); ?></button>
                        </div>
                    </div>
                </dialog>
            </div>

        </div>

        <script>
          function <?= $js_id; ?>_handler() {
            return {
              open  : false,
              fields: <?= json_encode($value); ?>,
              _field: <?= json_encode($_field_names); ?>,
              addNewField() {
                this.fields.push(<?= json_encode($field_names); ?>);
              },
              addNewField2() {
                let real_field = {};

                for (let key in this._field) {
                  real_field[key.replace('_', '')] = this._field[key];
                }

                this.fields.push(real_field);
                this._field = <?= json_encode($_field_names); ?>;
                this.open = false;

                return false;
              },
              removeField(index) {
                this.fields.splice(index, 1);
              },
            };
          }
        </script>

        <?php
        $html = ob_get_clean();

        $clone_group = Html::el('div class=frm-group-input')
                           ->setAttribute('id', $id);

        return $clone_group->addHtml(Html::el()->setHtml($html));

    }

    /**
     * 获取 HTML 名称
     *
     * @return string
     */
    public function getHtmlName(): string
    {
        return parent::getHtmlName() . '[]';
    }

}

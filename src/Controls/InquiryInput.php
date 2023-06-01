<?php

namespace Wenprise\Forms\Controls;

use Nette\Forms\Controls\BaseControl;
use Nette\Forms\Form;
use Nette\Utils\Html;
use Wenprise\Forms\Helpers;


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
        $id    = $this->getHtmlId();
        $js_id = str_replace('-', '_', $id);

        $settings = $this->settings;
        $fields   = $this->fields;
        $value    = $this->getValue();

        $field_names  = [];
        $_field_names = [];

        foreach ($fields as $field) {
            $field_names[ $field[ 'name' ] ]        = '';
            $_field_names[ '_' . $field[ 'name' ] ] = '';
        }

        $allow_delete = Helpers::data_get($settings, 'allow_delete', 'yes') === 'yes';
        $allow_add = Helpers::data_get($settings, 'allow_add', 'yes') === 'yes';
        $addition_col_number = $allow_delete ? 2 : 1;

        ob_start();
        ?>
        <div x-data="<?= $js_id; ?>_handler()">

            <div class="rsf-block lg:rsf-hidden rs-inquiry-input-mobile">
                <table class="table rs-table rs-table-bordered rs-inquiry-input">
                    <thead class="thead-light">
                    <tr>
                        <?php foreach (wp_list_pluck($fields, 'label') as $label): ?>
                            <th><?= $label; ?></th>
                        <?php endforeach; ?>

                        <?php if ( $allow_delete ) : ?>
                            <th></th>
                        <?php endif; ?>
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

                            <?php if ($allow_delete) : ?>
                                <td>
                                    <button type="button" class="rs-btn rs-btn-default" @click="removeField(index)">&times;</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>

            <div class="rsf-hidden lg:rsf-block">
                <table class="table rs-table rs-table-bordered rs-inquiry-input">
                    <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <?php foreach (wp_list_pluck($fields, 'label') as $label): ?>
                            <th><?= $label; ?></th>
                        <?php endforeach; ?>
                        <?php if ( $allow_delete ) : ?>
                            <th></th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <template x-for="(field, index) in fields" :key="index">
                        <tr>
                            <td x-text="index + 1"></td>
                            <?php foreach ($fields as $field): ?>
                                <?php
                                $field_type = Helpers::data_get($field, 'type', 'text');
                                $field_name= Helpers::data_get($field, 'name', );
                                ?>
                                <td>
                                    <?php if ( $field_type === 'text' ) : ?>
                                        <input x-model="field.<?= $field_name; ?>" type="text" name="<?= $field_name; ?>[]" class="form-control rs-form-control">
                                    <?php elseif($field_type === 'checkbox'): ?>
                                        <input x-model="field.<?= $field_name; ?>" type="checkbox" name="<?= $field_name; ?>[]" class="form-checkbox">
                                    <?php elseif($field_type === 'textarea'): ?>
                                        <textarea x-model="field.<?= $field_name; ?>" name="<?= $field_name; ?>[]" cols="30" rows="10"></textarea>
                                    <?php elseif($field_type === 'select'): ?>
                                        <select x-model="field.<?= $field_name; ?>" name="<?= $field_name; ?>[]">
                                            <?php foreach (Helpers::data_get($field, 'options', ) as $option_key => $option_value): ?>
                                                <option value="<?= $option_key; ?>"><?= $option_value; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>

                            <?php if ($allow_delete) : ?>
                                <td>
                                    <button type="button" class="rs-btn rs-btn-default" @click="removeField(index)">&times;</button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    </template>
                    </tbody>

                    <?php if ($allow_add) : ?>
                        <tfoot>
                        <tr>
                            <td colspan="<?= count($field_names) + $addition_col_number; ?>" class="text-right">
                                <button type="button" class="rs-btn rs-btn-primary" @click="addNewField()"><?= __('+ Add Row', 'wprs'); ?></button>
                            </td>
                        </tr>
                        </tfoot>
                    <?php endif; ?>

                </table>
            </div>

            <div class="rsf-block lg:rsf-hidden rs-inquiry-input-modal">
                <?php if ($allow_delete) : ?>
                    <button @click="open = !open" class="rs-btn rs-btn-primary"><?= __('+ Add Row', 'wprs'); ?></button>
                <?php endif; ?>
                <dialog x-show="open" x-dialog="open = false">
                    <div>
                        <fieldset class="rs-form-row">

                            <?php foreach ($fields as $field): ?>
                                <?php
                                $field_type = Helpers::data_get($field, 'type', 'text');
                                $field_name= Helpers::data_get($field, 'name', );
                                ?>
                                <div class="rs-form-group rs-form--text rs-row rs-col-md-12" id="rs-form-company_name">
                                    <div class="rs-col-sm-3 rs-control-label">
                                        <label for="frm-company_name"><?= $field[ 'label' ]; ?></label>
                                    </div>
                                    <div class="rs-col-sm-9 rs-control-input">
                                        <?php if ( $field_type === 'text' ) : ?>
                                            <input x-model="_field._<?= $field_name; ?>" type="text" name="_<?= $field_name; ?>" class="form-control rs-form-control">
                                        <?php elseif($field_type === 'checkbox'): ?>
                                            <input x-model="_field._<?= $field_name; ?>" type="checkbox" name="_<?= $field_name; ?>" class="form-checkbox">
                                        <?php elseif($field_type === 'textarea'): ?>
                                            <textarea x-model="_field._<?= $field_name; ?>" name="_<?= $field_name; ?>" cols="30" rows="10" class="form-control rs-form-control"></textarea>
                                        <?php elseif($field_type === 'select'): ?>
                                            <select x-model="_field._<?= $field_name; ?>" name="_<?= $field_name; ?>">
                                                <?php foreach (Helpers::data_get($field, 'options', ) as $option_key => $option_value): ?>
                                                    <option value="<?= $option_key; ?>"><?= $option_value; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                        </fieldset>

                        <div>
                            <button type="button" class="rs-btn rs-btn-default" @click="_addNewField()"><?= __('Add', 'wprs'); ?></button>
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
              _addNewField() {
                let real_field = {};

                console.log(this._field);

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

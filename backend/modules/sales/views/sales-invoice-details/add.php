<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\ItemMaster;
use common\models\Tax;
use common\models\Employee;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\EstimatedProforma */

$this->title = 'Sales Invoice';
$this->params['breadcrumbs'][] = ['label' => ' Pre-Funding', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if (isset($estimate)) {
    $estimate_master = common\models\SalesEstimateMaster::find()->where(['id' => $estimate])->one();
    $estimate_details = common\models\SalesEstimateDetails::find()->where(['sales_invoice_master_id' => $estimate])->all();
}
?>
<style>
    form .form-group.has-success .form-control:focus {
        border-color: #ffffff;
    }
    .form-group {
        margin-bottom: 0px;
    }
    #add-invoicee td{
        padding: 0px;
    }
    .stock-dtl-tble{
        max-height: 75px;
        overflow-y: scroll;
    }
    .stock-dtl-tble table{
        width:100%;
        border: 1px solid #cac7c7;
        border-collapse: collapse;
    }
    .stock-dtl-tble th{
        border: 1px solid #cac7c7;
        text-align: center;
        color: #717070;
        font-size: 11px;
    }
    .stock-dtl-tble td{
        border: 1px solid #cac7c7;
        text-align: center;
        color: #717070;
        font-size: 11px;
    }
    .stock-dtl-tble tr {
        border: 1px solid #bfbebe !important;
    }
    .stock-dtl-tble tr td:last-child {
        display: block !important;
    }
</style>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h2  class="appoint-title panel-title"><?= Html::encode($this->title) . '</b>' ?></h2>
                <div class="diplay-amount">S$ <span id="total-order-amount">00.00</span></div>
            </div>
            <?php //Pjax::begin();        ?>
            <div class="panel-body">
                <?= Html::a('<i class="fa-th-list"></i><span> Manage Invoice</span>', ['index'], ['class' => 'btn btn-warning  btn-icon btn-icon-standalone']) ?>
                <div class="modal fade" id="modal-6">
                    <div class="modal-dialog modal-pop-up" id="modal-pop-up">

                    </div>
                </div>
                <div class="panel-body">
                    <input type="hidden" id="salesreturninvoicemaster-amount" class="form-control" name="SalesReturnInvoiceMaster[amount]" readonly="" aria-invalid="false">
                    <div class="row">
                        <div class='col-md-3 col-sm-6 col-xs-12'>
                            <?php
                            if ($model_sales_master->isNewRecord) {
                                $invoice_no = $this->context->generateInvoiceNo();
                                $model_sales_master->sales_invoice_number = $invoice_no;
                            }
                            ?>
                            <?= $form->field($model_sales_master, 'sales_invoice_number')->textInput(['maxlength' => true, 'readonly' => TRUE])->label('Invoice Number') ?>
                        </div>
                        <div class='col-md-3 col-sm-6 col-xs-12'>
                            <?php
                            $customers = ArrayHelper::map(common\models\BusinessPartner::find()->where(['status' => 1, 'type' => 1])->all(), 'id', function($model) {
                                        return $model['name'] . ' ( ' . $model['company_name'] . ' )';
                                    }
                            );
                            ?>
                            <?= $form->field($model_sales_master, 'busines_partner_code')->dropDownList($customers, ['prompt' => '-Choose a Customer-'])->label('Customer') ?>
                        </div>
                        <div class='col-md-3 col-sm-6 col-xs-12'>
                            <?= $form->field($model_sales_master, 'email')->textInput(['maxlength' => true])->label('Email') ?>
                        </div>
                        <div class='col-md-3 col-sm-6 col-xs-12'>
                            <?= $form->field($model_sales_master, 'cc')->textInput(['maxlength' => true])->label('Cc') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-md-4 col-sm-4 col-xs-12'>
                            <?= $form->field($model_sales_master, 'contact_number')->textInput(['maxlength' => true])->label('Contact Number') ?>
                        </div>
                        <div class='col-md-4 col-sm-4 col-xs-12'>
                            <?= $form->field($model_sales_master, 'po_no')->textInput(['maxlength' => true])->label('PO NO.') ?>
                        </div>
                        <div class='col-md-4 col-sm-4 col-xs-12'>
                            <?php
                            $model_sales_master->po_date = date('d-M-Y');
                            ?>
                            <?=
                            $form->field($model_sales_master, 'po_date')->widget(DatePicker::classname(), [
                                'type' => DatePicker::TYPE_INPUT,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-M-yyyy'
                                ]
                            ])->label('PO Date');
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <?= $form->field($model_sales_master, 'ship_to_adress')->textarea(['rows' => '1'])->label('Billing Address') ?>
                        </div>
                        <div class='col-md-6 col-sm-6 col-xs-12'>
                            <?= $form->field($model_sales_master, 'delivery_address')->textarea(['rows' => '1']) ?>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="billing-hr">
            <?php $item_datas = ItemMaster::findAll(['status' => 1]); ?>
            <div class="table-responsive form-control-new" data-pattern="priority-columns" data-focus-btn-icon="fa-asterisk" data-sticky-table-header="true" data-add-display-all-btn="true" data-add-focus-btn="true" style="overflow: visible;">
                <table cellspacing="0" class="table table-small-font table-bordered table-striped" id="add-invoicee">
                    <thead>
                        <tr>
                            <th data-priority="3">Item</th>
                            <th data-priority="6" style="width: 23%;">Qty</th>
                            <th data-priority="6" style="width: 8%;">RATE</th>
                            <th data-priority="6" style="width: 14%;">Discount</th>
                            <th data-priority="6" style="width: 14%;">Tax</th>
                            <th data-priority="6" style="width: 8%;">Amount</th>
                            <!--<th data-priority="6" style="width: 8%;">Inventory</th>-->
                            <th data-priority="1" style="width: 50px;"></th>
                        </tr>
                        <tr>
                    </thead>
                    <tbody>
                    <input type="hidden" value="1" name="next_item_id" id="next_item_id"/>
                    <tr class="filter" id="item-row-1">
                        <td>
                            <?php $item_datas = ItemMaster::findAll(['status' => 1]); ?>
                            <select id="salesinvoicedetails-item_id-1" class="form-control salesinvoicedetails-item_id add-next" name="create[item_id][1]">
                                <option value="">-Choose a Item-</option>
                                <?php foreach ($item_datas as $item_data) {
                                    ?>
                                    <option value="<?= $item_data->id ?>"><?= $item_data->item_name ?></option>
                                <?php }
                                ?>
                            </select>
                            <input type="hidden" id="sales-item-type-1" class="sales-item-type" name="create[sales_item_type][1]"/>
                            <input type="text" value="" placeholder="Description" class="form-control salesinvoicedetails-item_comment bill-comment" id="salesinvoicedetails-item-comment-1" name="create[comment][1]" autocomplete="off" style="display: none;">
                        </td>
                        <td>
                            <div class="form-group field-salesinvoicedetails-discount_percentage has-success">
                                <div class="row" style="margin:0px;">
                                    <div class="col-md-4" style="padding:0px;">
                                        <input type="number" id="salesinvoicedetails-qty-1" value="" class="form-control salesinvoicedetails-qty" name="create[qty][1]" placeholder="Qty" min="1" aria-invalid="false" autocomplete="off"  style="display:inline-block;" step="any">
                                    </div>
                                    <div class="col-md-4" style="padding:0px;">
                                        <select id="salesinvoicedetails-type-1" class="form-control salesinvoicedetails-type" name="create[type][1]">
                                            <option value="1">Carton</option>
                                            <option value="2">Kg</option>
                                            <option value="3">Pieces</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3" style="padding:0px;">
                                        <input type="text" id="salesinvoicedetails-qty_val-1" value="" class="form-control salesinvoicedetails-qty_val" name="create[qty_val][1]" tabindex="-1" readonly>
                                    </div>
                                    <div class="col-md-1" style="padding:0px;">
                                        <p style="padding: 7px 0px 0px 3px;color: #585656;">Kg</p>
                                    </div>
                                </div>
                            </div>
                            <div id="stock-table-1" class="stock-dtl-tble">
                            </div>
                            <input type="hidden" id="salesinvoicedetails-avail_carton-1" value="" class="form-control salesinvoicedetails-avail_carton" name="create[avail_carton][1]" >
                            <input type="hidden" id="salesinvoicedetails-avail_weight-1" value="" class="form-control salesinvoicedetails-avail_weight" name="create[avail_weight][1]" >
                            <input type="hidden" id="salesinvoicedetails-avail_pieces-1" value="" class="form-control salesinvoicedetails-avail_pieces" name="create[avail_pieces][1]" >
                        </td>
                        <td>
                            <div class="form-group field-salesinvoicedetails-rate has-success">
                                <input type="number" id="salesinvoicedetails-rate-1" class="form-control salesinvoicedetails-rate" name="create[rate][]" placeholder="RATE" step="0.01" aria-invalid="false" autocomplete="off" value="" >
                            </div>
                        </td>
                        <td>
                            <div class="form-group field-salesinvoicedetails-discount_percentage has-success">
                                <div class="row" style="margin:0px;">
                                    <div class="col-md-6" style="padding:0px;">
                                        <input type="number" id="salesinvoicedetails-discount_value-1" value="" class="form-control salesinvoicedetails-discount_value" name="create[discount_value][1]" placeholder="Discount" min="1" aria-invalid="false" autocomplete="off"  style="display:inline-block;">
                                    </div>
                                    <div class="col-md-6" style="padding:0px;">
                                        <select id="salesinvoicedetails-discount_type-1" class="form-control salesinvoicedetails-discount_type" name="create[discount_type][1]">
                                            <option value="1">S$</option>
                                            <option value="2">%</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <?php
                        $taxes = Tax::findAll(['status' => 1]);
                        ?>
                        <td>
                            <div class="form-group field-salesinvoicedetails-tax has-success">

                                <select id="salesinvoicedetails-tax-1" class="form-control salesinvoicedetails-tax" name="create[tax_id][1]" aria-invalid="false">
                                    <option value="">Slelect a Tax</option>
                                    <?php
                                    foreach ($taxes as $tax) {
                                        if ($tax->type == 1) {
                                            $type = '%';
                                        } else {
                                            $type = 'S$';
                                        }
                                        ?>
                                        <option value="<?= $tax->id ?>" ><?= $tax->value . ' ' . $type ?></option>
                                    <?php }
                                    ?>
                                </select>
                                <input type="hidden" id="salesinvoicedetails-tax_value-1" value="" class="form-control salesinvoicedetails-tax_value" name="create[tax_value][1]" >
                                <input type="hidden" id="salesinvoicedetails-tax_type-1" value="" class="form-control salesinvoicedetails-tax_type" name="create[tax_type][1]" >
                            </div>
                        </td>
                        <td>
                            <div class="form-group field-salesinvoicedetails-line_total has-success">
                                <input type="text" id="salesinvoicedetails-line_total-1" value="" class="form-control salesinvoicedetails-line_total" name="create[line_total][1]" placeholder="Amount" aria-invalid="false" autocomplete="off">
                            </div>
                        </td>
<!--                        <td>
                            <div class="form-group field-salesinvoicedetails-line_total has-success" style="text-align: center;margin-top: 6px;">
                                <input type="hidden" id="sales-inventory-1" class="sales-inventory" name="create[inventory][1]" value="1" checked="checked"/>
                                <input type="checkbox" id="salesinvoicedetails-inventory-1" class="salesinvoicedetails-inventory" name="" value="" checked="checked"/>
                            </div>
                        </td>-->
                        <td>
                            <input type="hidden" id="sales-inventory-1" class="sales-inventory" name="create[inventory][1]" value="1" checked="checked"/>
                            <input type="checkbox" id="salesinvoicedetails-inventory-1" class="salesinvoicedetails-inventory" name="" value="1" checked="checked" title="Checked for Inventory" tabindex="-1"/>
                            <a id="del" class="" ><i class="fa fa-times sales-invoice-delete" title="Remove Row"></i></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table cellspacing="0" class="table table-small-font table-bordered table-striped" id="add-invoicee">
                    <thead>
                        <tr>
                            <th data-priority="3">Item Total</th>
                            <th data-priority="6" style="width: 18%;"><input type="text" id="qty_total" class="amount-receipt-1" name="qty_total" style="width: 100%;" tabindex="-1" readonly/></th>
                            <th data-priority="6" style="width: 8%;"><input type="hidden" id="sub_total" class="amount-receipt-1" name="sub_total" style="width: 100%;" tabindex="-1" readonly/></th>
                            <th data-priority="6" style="width: 14%;"><input type="text" id="discount_sub_total" class="amount-receipt-1"  name="discount_sub_total" style="width: 100%;" tabindex="-1" readonly/></th>
                            <th data-priority="6" style="width: 14%;"><input type="text" id="tax_sub_total" class="amount-receipt-1"  name="tax_sub_total" style="width: 100%;" tabindex="-1" readonly/></th>
                            <th data-priority="6" style="width: 8%;"><input type="text" id="order_sub_total" class="amount-receipt-1"  name="order_sub_total" style="width: 100%;" tabindex="-1" readonly/></th>
                            <th data-priority="1" style="width: 8%;"></th>
                            <th data-priority="1" style="width: 50px;   "></th>
                        </tr>
                    <input type="hidden" id="amount_without_tax" class="amount-receipt-1" name="amount_without_tax" style="width: 100%;" readonly/>
                    </thead>
                </table>
            </div>
            <a href="" id="add_another_line"><i class="fa fa-plus" aria-hidden="true"></i> Add Another Line</a>
            <hr class="billing-hr">
            <div class="panel-body">
                <div class="sales-invoice-master-create">
                    <div class="sales-invoice-master-form form-inline">

                        <div class='col-md-4 col-sm-6 col-xs-12'>
                            <?= $form->field($model_sales_master, 'general_terms')->textarea(['rows' => '6']) ?>
                        </div>


                        <div class='col-md-4 col-sm-6 col-xs-12' style="float:right;padding-right: 0px;">
                            <table cellspacing="0" class="table table-small-font table-bordered table-striped" style="float:right;text-align: left;">
                            <!--<table style="float:right;text-align: left;">-->
                                <tr>
                                    <td><label class="control-label">Round off</label></td>
                                    <td><input type="text" style="text-align:right" id="round_of" class="amount-receipt form-control"  name="round_of" style="width: 100%;" autocomplete="off" value="<?= sprintf('%0.2f', 0); ?>"/></td>
                                </tr>
                                <tr>
                                    <td><label class="control-label">Cash</label></td>
                                    <td><input type="text" style="text-align:right" id="cash_amount" class="amount-receipt form-control"  name="cash_amount" style="width: 100%;" autocomplete="off" value="<?= sprintf('%0.2f', 0); ?>" readonly /></td>
                                </tr>
<!--                                <tr>
                                    <td>Card</td>
                                    <td><input type="text" id="card_amount" class="amount-receipt"  name="card_amount" style="width: 100%;" autocomplete="off" value="<?= sprintf('%0.2f', 0); ?>"/></td>
                                </tr>-->

                                <tr>
                                    <td><label class="control-label">Amount Paid</label></td>
                                    <td><input type="text" style="text-align:right" id="payed_amount" class="amount-receipt form-control"  name="payed_amount" autocomplete="off" style="width: 100%;"/></td></td>
                                </tr>
                                <tr>
                                    <td> <label class="control-label">Balance</label></td>
                                    <td><input type="text" style="text-align:right" id="balance" class="amount-receipt form-control"  name="balance" style="width: 100%;" readonly/></td>
                                </tr>
                                <tr class="due-date-row">
                                    <td><label class="control-label">Due Date</label></td>
                                    <td>
                                        <?php
                                        echo DatePicker::widget([
                                            'name' => 'due_date',
                                            'id' => 'due-date',
                                            'type' => DatePicker::TYPE_INPUT,
//                                            'value' => date('d-m-Y'),
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy'
                                            ]
                                        ]);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php // Html::submitButton('Save & Print', ['class' => 'btn btn-secondary', 'name' => 'save-print', 'value' => 'save-print'])  ?>
                    <?= Html::submitButton('Save', ['class' => 'btn btn-secondary', 'name' => 'save', 'value' => 'save', 'style' => 'float:right;']) ?>
                </div>
            </div>




        </div>
        <?php //Pjax::end();                          ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<script>
    $(document).ready(function () {

        $(document).on('change', '#salesinvoicemaster-busines_partner_code', function () {
            $.ajax({
                type: 'POST',
                cache: false,
                data: {id: $(this).val()},
                url: '<?= Yii::$app->homeUrl; ?>sales/sales-invoice-details/customer-details',
                success: function (data) {
                    var res = $.parseJSON(data);
                    $("#salesinvoicemaster-ship_to_adress").val(res.result['billing_address']);
                    $("#salesinvoicemaster-delivery_address").val(res.result['delivery_address']);
                    $("#salesinvoicemaster-contact_number").val(res.result['contact_number']);
                    $("#salesinvoicemaster-email").val(res.result['email']);
                }
            });
        });
        $('#salesinvoicedetails-item_id-1').select2({
            allowClear: true
        }).on('select2-open', function ()
        {
            $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
        });
        $(document).on('change', '.salesinvoicedetails-item_id', function (e) {
            var flag = 0;
            var count = 0;
            var current_row_id = $(this).attr('id').match(/\d+/); // 123456
            var next_row_id = $('#next_item_id').val();
            var item_id = $(this).val();
            if (next_row_id > 1) {
                for (i = 1; i <= next_row_id; i++) {
                    var item_val = $('#salesinvoicedetails-item_id-' + i).val();
                    if (item_val == item_id) {
                        count = count + 1;
                    }
                }
                if (count > 1) {
                    flag = 1;
                } else {
                    flag = 0;
                }
            }
            if (flag == 0) {
                itemChange(item_id, current_row_id, next_row_id);
            } else {
                alert('This Item is already Choosed');
                $("#salesinvoicedetails-item_id-" + current_row_id).select2("val", "");
                e.preventDefault();
            }
        });
        $('#add-invoicee').on('click', '#del', function () {
            var bid = this.id; // button ID
            var trid = $(this).closest('tr').attr('id'); // table row ID
            $(this).closest('tr').remove();
            calculateSubtotal();
        });
        $(document).on('click', '#add_another_line', function (e) {
            var rowCount = $('#add-invoicee >tbody >tr').length;
            var next_row_id = $('#next_item_id').val();
            var next = parseInt(next_row_id) + 1;
            $.ajax({
                type: 'POST',
                cache: false,
                async: false,
                data: {next_row_id: next_row_id},
                url: homeUrl + 'sales/sales-invoice-details/add-another-row',
                success: function (data) {
                    var res = $.parseJSON(data);
                    console.log(res);
                    $('#add-invoicee tr:last').after(res.result['next_row_html']);
                    $("#next_item_id").val(next);
                    $('#salesinvoicedetails-item_id-' + rowCount).removeClass("add-next");
                    $('#salesinvoicedetails-item_id-' + next).select2({
                        allowClear: true
                    }).on('select2-open', function ()
                    {
                        $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                    });
                    e.preventDefault();
                }
            });
        });
        $(document).on('keyup mouseup', '.salesinvoicedetails-qty', function (e) {
            var current_row_id = $(this).attr('id').match(/\d+/); // 123456
            var qty = $(this).val();
            var inventory = $('#sales-inventory-' + current_row_id).val();
            var rate = $('#salesinvoicedetails-rate-' + current_row_id).val();
            var type = $('#salesinvoicedetails-type-' + current_row_id).val();
            var avail_carton_tot = $('#salesinvoicedetails-avail_carton-' + current_row_id).val();
            var avail_weight_tot = $('#salesinvoicedetails-avail_weight-' + current_row_id).val();
            var avail_pieces_tot = $('#salesinvoicedetails-avail_pieces-' + current_row_id).val();
            var item = $('#salesinvoicedetails-item_id-' + current_row_id).val();
            if (item == '') {
                $('.salesinvoicedetails-qty-' + current_row_id).val('');
                e.preventDefault();
            }
            if (qty != "" && item != '' && rate != '') {
                if (inventory == 1) {
                    if (type == 1) {
                        if (parseInt(qty) > parseInt(avail_carton_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_carton_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        } else {
                            setQuantityValue(item, qty, current_row_id, type);
                        }
                    } else if (type == 2) {
                        if (parseInt(qty) > parseInt(avail_weight_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_weight_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        } else {
                            var data = qty;
                            $('#salesinvoicedetails-qty_val-' + current_row_id).val(data);
                        }
                    } else if (type == 3) {
                        if (parseInt(qty) > parseInt(avail_pieces_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_pieces_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        } else {
                            setQuantityValue(item, qty, current_row_id, type);
                        }
                    }
                } else {
                    $('#salesinvoicedetails-qty_val-' + current_row_id).val(qty);
                }
                lineTotalAmount(current_row_id);
            }
        });
        $(document).on('change', '.salesinvoicedetails-type', function (e) {
            var current_row_id = $(this).attr('id').match(/\d+/); // 123456
            var qty = $('#salesinvoicedetails-qty-' + current_row_id).val();
            var inventory = $('#sales-inventory-' + current_row_id).val();
            var rate = $('#salesinvoicedetails-rate-' + current_row_id).val();
            var type = $('#salesinvoicedetails-type-' + current_row_id).val();
            var avail_carton_tot = $('#salesinvoicedetails-avail_carton-' + current_row_id).val();
            var avail_weight_tot = $('#salesinvoicedetails-avail_weight-' + current_row_id).val();
            var avail_pieces_tot = $('#salesinvoicedetails-avail_pieces-' + current_row_id).val();
            var type = $('#salesinvoicedetails-type-' + current_row_id).val();
            var item = $('#salesinvoicedetails-item_id-' + current_row_id).val();
            if (qty != "" && item != '' && rate != '') {
                if (inventory == 1) {
                    if (type == 1) {
                        if (parseInt(qty) > parseInt(avail_carton_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_carton_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        } else {
                            setQuantityValue(item, qty, current_row_id, type);
                        }
                    } else if (type == 2) {
                        if (parseInt(qty) > parseInt(avail_weight_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_weight_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        } else {
                            var data = qty;
                            $('#salesinvoicedetails-qty_val-' + current_row_id).val(data);
                        }
                    } else if (type == 3) {
                        if (parseInt(qty) > parseInt(avail_pieces_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_pieces_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        } else {
                            var item_type = $("#sales-item-type-" + current_row_id).val();
                            if (item_type == 1) {
                                setQuantityValue(item, qty, current_row_id, type);
                            } else {
                                $('#salesinvoicedetails-qty_val-' + current_row_id).val(qty);
                            }
                        }
                    }
                    lineTotalAmount(current_row_id);
                }
            }
        });
        $(document).on('keyup mouseup', '.salesinvoicedetails-rate', function () {
            var current_row_id = $(this).attr('id').match(/\d+/); // 123456
            var item = $('#salesinvoicedetails-item_id-' + current_row_id).val();
            var qty = $('#salesinvoicedetails-qty-' + current_row_id).val();
            var rate = $('#salesinvoicedetails-rate-' + current_row_id).val();
            if (qty != "" && item != "") {
                lineTotalAmount(current_row_id);
            }

        });
        $(document).on('change', '.salesinvoicedetails-discount_type', function () {
            var current_row_id = $(this).attr('id').match(/\d+/); // 123456
            var item = $('#salesinvoicedetails-item_id-' + current_row_id).val();
            var qty = $('#salesinvoicedetails-qty-' + current_row_id).val();
            var rate = $('#salesinvoicedetails-rate-' + current_row_id).val();
            if (qty != "" && rate != "" && item != "") {
                lineTotalAmount(current_row_id);
            }

        });
        /*
         * on keyup of the quantity
         * @parameter quantity,rate
         * @return total amount
         */
        $(document).on('keyup mouseup', '.salesinvoicedetails-discount_value', function () {
            var current_row_id = $(this).attr('id').match(/\d+/); // 123456
            var item = $('#salesinvoicedetails-item_id-' + current_row_id).val();
            var qty = $('#salesinvoicedetails-qty-' + current_row_id).val();
            var rate = $('#salesinvoicedetails-rate-' + current_row_id).val();
            if (qty != "" && rate != "" && item != "") {
                lineTotalAmount(current_row_id);
            }

        });
        $(document).on('change', '.salesinvoicedetails-inventory', function () {
            var current_row_id = $(this).attr('id').match(/\d+/); // 123456
            if ($(this).is(':checked')) {
                $('#sales-inventory-' + current_row_id).val(1);
                var qty = $('#salesinvoicedetails-qty-' + current_row_id).val();
                var rate = $('#salesinvoicedetails-rate-' + current_row_id).val();
                var item = $('#salesinvoicedetails-item_id-' + current_row_id).val();
                var type = $('#salesinvoicedetails-type-' + current_row_id).val();
                var avail_carton_tot = $('#salesinvoicedetails-avail_carton-' + current_row_id).val();
                var avail_weight_tot = $('#salesinvoicedetails-avail_weight-' + current_row_id).val();
                var avail_pieces_tot = $('#salesinvoicedetails-avail_pieces-' + current_row_id).val();
                if (qty != "" && item != '' && rate != '') {
                    if (type == 1) {
                        if (parseInt(qty) > parseInt(avail_carton_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_carton_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        }
                    } else if (type == 2) {
                        if (parseInt(qty) > parseInt(avail_weight_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_weight_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        }
                    } else if (type == 2) {
                        if (parseInt(qty) > parseInt(avail_pieces_tot)) {
                            $('#salesinvoicedetails-qty-' + current_row_id).val(avail_pieces_tot);
                            alert(' Quantity exeeds the Available Stock.');
                        }
                    }
                }
            } else {
                var qty = $('#salesinvoicedetails-qty-' + current_row_id).val();
                $('#salesinvoicedetails-qty_val-' + current_row_id).val(qty);
                $('#sales-inventory-' + current_row_id).val(0);
            }
            lineTotalAmount(current_row_id);

        });
        $(document).on('keyup', '#payed_amount', function () {
            balanceCalculation();
        });
        $(document).on('keyup', '#round_of', function () {
            balanceCalculation();
        });
        $(document).on('keydown', '#salesinvoicemaster-email,#salesinvoicemaster-cc,#salesinvoicemaster-contact_number,#salesinvoicemaster-po_no,#salesinvoicemaster-ship_to_adress,#salesinvoicemaster-delivery_address,.salesinvoicedetails-qty,.salesinvoicedetails-rate,#payed_amount,#balance,#due-date,.salesinvoicedetails-discount_value,.salesinvoicedetails-line_total,#round_of,#salesinvoicemaster-general_terms,.salesinvoicedetails-qty_val,#qty_total,#discount_sub_total,#tax_sub_total,#order_sub_total,.salesinvoicedetails-inventory', function (event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });
    });
    function itemChange(item_id, current_row_id, next_row_id) {
        var next = parseInt(next_row_id) + 1;
        $.ajax({
            type: 'POST',
            cache: false,
            async: false,
            data: {item_id: item_id, next_row_id: next_row_id},
            url: '<?= Yii::$app->homeUrl; ?>sales/sales-invoice-details/get-items',
            success: function (data) {
                var res = $.parseJSON(data);
                if (data != 0) {
                    if ($('#salesinvoicedetails-item_id-' + current_row_id).hasClass('add-next')) {
                        $('#salesinvoicedetails-item-comment-' + current_row_id).css('display', 'block');
                        if ($('.stock-list')[0]) {
                            $('.stock-list').hide();
                        } else {
                            $("#stock-list-" + current_row_id + " table").show();
                        }
                        $('#add-invoicee tr:last').after(res.result['next_row_html']);
                        $("#next_item_id").val(next);
                        $('.salesinvoicedetails-qty').attr('type', 'number');
                        $('.salesinvoicedetails-qty').attr('step', 'any');
                        $('.salesinvoicedetails-qty').attr('min', 1);
                        $("#stock-table-" + current_row_id).html(res.result['stock-table']);
                        $("#salesinvoicedetails-rate-" + current_row_id).val(res.result['item_rate']);
                        $("#sales-item-type-" + current_row_id).val(res.result['item_type']);
                        $("#salesinvoicedetails-avail_pieces-" + current_row_id).val(res.result['avail-pieces']);
                        $("#salesinvoicedetails-avail_weight-" + current_row_id).val(res.result['avail-weight']);
                        $("#salesinvoicedetails-avail_carton-" + current_row_id).val(res.result['avail-carton']);
                        $("#salesinvoicedetails-tax_type-" + current_row_id).val(res.result['tax_type']);
                        $("#salesinvoicedetails-tax_value-" + current_row_id).val(res.result['tax_value']);
                        var iddd = '#salesinvoicedetails-tax-' + current_row_id;
                        $("" + iddd + " option[value='" + res.result['tax_id'] + "']").prop('selected', true);
                        $('#salesinvoicedetails-item_id-' + current_row_id).removeClass("add-next");
                        $('#salesinvoicedetails-item_id-' + next).select2({
                            allowClear: true
                        }).on('select2-open', function ()
                        {
                            $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                        });
                    } else {
                        $('#salesinvoicedetails-item-comment-' + current_row_id).css('display', 'block');
                        if ($('.stock-list')[0]) {
                            $('.stock-list').hide();
                        } else {
                            $("#stock-list-" + current_row_id + " table").show();
                        }
                        $("#stock-table-" + current_row_id).html(res.result['stock-table']);
                        $("#salesinvoicedetails-rate-" + current_row_id).val(res.result['item_rate']);
                        $("#sales-item-type-" + current_row_id).val(res.result['item_type']);
                        $("#salesinvoicedetails-avail_pieces-" + current_row_id).val(res.result['avail-pieces']);
                        $("#salesinvoicedetails-avail_weight-" + current_row_id).val(res.result['avail-weight']);
                        $("#salesinvoicedetails-avail_carton-" + current_row_id).val(res.result['avail-carton']);
                        $("#salesinvoicedetails-tax_type-" + current_row_id).val(res.result['tax_type']);
                        $("#salesinvoicedetails-tax_value-" + current_row_id).val(res.result['tax_value']);
                        var iddd = '#salesinvoicedetails-tax-' + current_row_id;
                        $("" + iddd + " option[value='" + res.result['tax_id'] + "']").prop('selected', true);
                    }
                }
                if ($('#salesinvoicedetails-qty-' + current_row_id).val() != "" && $("#salesinvoicedetails-rate-" + current_row_id).val() != "") {
                    lineTotalAmount(current_row_id);
                }
                //                calculateSubtotal();
            }
        });
        return true;
    }
    function setQuantityValue(item, qty, current_row_id, type) {
        $.ajax({
            type: 'POST',
            cache: false,
            async: false,
            data: {item_id: item, qty: qty, type: type},
            url: '<?= Yii::$app->homeUrl; ?>sales/sales-invoice-details/get-sales-quantity',
            success: function (data) {
                var res = $.parseJSON(data);
                $('#salesinvoicedetails-qty_val-' + current_row_id).val(res.result['tot-weight']);
            }
        });
        return true;
    }
    function lineTotalAmount(current_row_id) {

        var tax_amount = 0;
        var discount_amount = 0;
        var qty = $('#salesinvoicedetails-qty_val-' + current_row_id).val();
        var tax_type = $('#salesinvoicedetails-tax_type-' + current_row_id).val();
        var rate = $('#salesinvoicedetails-rate-' + current_row_id).val();
        var tax = $('#salesinvoicedetails-tax_value-' + current_row_id).val();
        var discount = $('#salesinvoicedetails-discount_value-' + current_row_id).val();
        var discount_type = $('#salesinvoicedetails-discount_type-' + current_row_id).val();
        var amount = qty * rate;
        if (discount != "") {
            if (discount_type == 1) {
                var discount_amount = discount;
            } else if (discount_type == 2) {
                var discount_amount = (amount * discount) / 100;
            }
        }
        if (qty != "" && rate != "") {
            if (tax_type == 2) {
                var tax_amount = tax;
            } else if (tax_type == 1) {
                var total = amount - discount_amount;
                var tax_amount = (total * tax) / 100;
            }

        }
        var grand_total = (parseFloat(amount) + parseFloat(tax_amount)) - parseFloat(discount_amount);
        $('#salesinvoicedetails-line_total-' + current_row_id).val(grand_total.toFixed(2));
        calculateSubtotal();
    }
    function calculateSubtotal() {

        var row_count = $('#next_item_id').val();
        var sub_total = 0;
        var tax_sub_total = 0;
        var order_sub_total = 0;
        var discount_sub_total = 0;
        var rate_sub_total = 0;
        var qty_total = 0;
        var discount_amount = 0;
        var tax_amount = 0;
        for (i = 1; i <= row_count; i++) {
            var qty = $('#salesinvoicedetails-qty_val-' + i).val();
            var tax_type = $('#salesinvoicedetails-tax_type-' + i).val();
            var rate = $('#salesinvoicedetails-rate-' + i).val();
            var tax = $('#salesinvoicedetails-tax_value-' + i).val();
            var discount = $('#salesinvoicedetails-discount_value-' + i).val();
            var discount_type = $('#salesinvoicedetails-discount_type-' + i).val();
            var amount = qty * rate;
            if (discount && discount != "") {
                if (discount_type == 1) {
                    var discount_amount = discount;
                } else if (discount_type == 2) {
                    var discount_amount = (amount * discount) / 100;
                }
            }
            if (qty && qty != "" && rate && rate != "") {
                if (tax_type == 2) {
                    var tax_amount = tax;
                } else if (tax_type == 1) {
                    var total = amount - discount_amount;
                    var tax_amount = (total * tax) / 100;
                }
                var grand_total = (parseFloat(amount) + parseFloat(tax_amount)) - parseFloat(discount_amount);
                $('#salesinvoicedetails-line_total-' + i).val(grand_total.toFixed(2));
                qty_total = parseFloat(qty_total) + parseFloat(qty);
                sub_total = parseFloat(sub_total) + parseFloat(total);
                rate_sub_total = parseFloat(rate_sub_total) + parseFloat(amount);
                tax_sub_total = parseFloat(tax_sub_total) + parseFloat(tax_amount);
                order_sub_total = parseFloat(order_sub_total) + parseFloat(grand_total);
                discount_sub_total = parseFloat(discount_sub_total) + parseFloat(discount_amount);
            }

        }
        $('#sub_total').val(rate_sub_total.toFixed(2));
        $('#qty_total').val(qty_total.toFixed(2));
        $('#amount_without_tax').val(sub_total.toFixed(2));
        $('#tax_sub_total').val(tax_sub_total.toFixed(2));
        $('#discount_sub_total').val(discount_sub_total.toFixed(2));
        $('#order_sub_total').val(order_sub_total.toFixed(2));
        $('#salesinvoicemaster-amount').val(sub_total.toFixed(2));
        $('#total-order-amount').text(order_sub_total.toFixed(2));
        $('#cash_amount').val(order_sub_total.toFixed(2));
        balanceCalculation();
    }
    function balanceCalculation() {
        var order_amount = $('#order_sub_total').val();
        if (order_amount && order_amount != "") {
            var cash_amount = $('#cash_amount').val();
            var round_of_amount = $('#round_of').val();
            var paid_amount = $('#payed_amount').val();
            if (!paid_amount && paid_amount == '') {
                paid_amount = 0;
            }
            if (!cash_amount && cash_amount == '') {
                cash_amount = 0;
            }
            if (!round_of_amount && round_of_amount == '') {
                round_of_amount = 0;
            }
            var order_balance = cash_amount - (parseFloat(round_of_amount) + parseFloat(paid_amount));
//            var paid_amount = parseFloat(cash_amount);
//            $('#payed_amount').val(paid_amount.toFixed(2));
            $('#balance').val(order_balance.toFixed(2));
            if (order_balance > 0) {
                $('.due-date-row').show();
            } else {
                $('.due-date-row').hide();
            }
        }
    }
</script>

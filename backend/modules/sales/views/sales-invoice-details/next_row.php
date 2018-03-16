<tr class="filter" id="item-row-<?= $next ?>">
    <td>
        <?php $item_datas = common\models\ItemMaster::findAll(['status' => 1]); ?>
        <select id="salesinvoicedetails-item_id-<?= $next ?>" class="form-control salesinvoicedetails-item_id add-next" name="create[item_id][<?= $next ?>]">
            <option value="">-Choose a Item-</option>
            <?php foreach ($item_datas as $item_data) {
                ?>
                <option value="<?= $item_data->id ?>"><?= $item_data->item_name ?></option>
            <?php }
            ?>
        </select>
        <input type="hidden" id="sales-item-type-<?= $next ?>" class="sales-item-type" name="create[sales_item_type][<?= $next ?>]"/>
        <input type="text" value="" placeholder="Description" class="form-control salesinvoicedetails-item_comment bill-comment" id="salesinvoicedetails-item-comment-<?= $next ?>" name="create[comment][<?= $next ?>]" autocomplete="off" style="display: none;">
    </td>
    <td>
        <div class="form-group field-salesinvoicedetails-discount_percentage has-success">
            <div class="row" style="margin:0px;">
                <div class="col-md-4" style="padding:0px;">
                    <input type="number" id="salesinvoicedetails-qty-<?= $next ?>" value="" class="form-control salesinvoicedetails-qty" name="create[qty][<?= $next ?>]" placeholder="Qty" min="1" aria-invalid="false" autocomplete="off"  style="display:inline-block;" step="any">
                </div>
                <div class="col-md-4" style="padding:0px;">
                    <select id="salesinvoicedetails-type-<?= $next ?>" class="form-control salesinvoicedetails-type" name="create[type][<?= $next ?>]">
                        <option value="1">Carton</option>
                        <option value="2">Kg</option>
                        <option value="3">Pieces</option>
                    </select>
                </div>
                <div class="col-md-3" style="padding:0px;">
                    <input type="text" id="salesinvoicedetails-qty_val-<?= $next ?>" value="" class="form-control salesinvoicedetails-qty_val" name="create[qty_val][<?= $next ?>]" tabindex="-1" readonly>
                </div>
                <div class="col-md-1" style="padding:0px;">
                    <p style="padding: 7px 0px 0px 3px;color: #585656;">Kg</p>
                </div>
            </div>
            <div id="stock-table-<?= $next ?>" class="stock-dtl-tble">

            </div>
            <input type="hidden" id="salesinvoicedetails-avail_carton-<?= $next ?>" value="" class="form-control salesinvoicedetails-avail_carton" name="create[avail_carton][<?= $next ?>]" >
            <input type="hidden" id="salesinvoicedetails-avail_weight-<?= $next ?>" value="" class="form-control salesinvoicedetails-avail_weight" name="create[avail_weight][<?= $next ?>]" >
            <input type="hidden" id="salesinvoicedetails-avail_pieces-<?= $next ?>" value="" class="form-control salesinvoicedetails-avail_pieces" name="create[avail_pieces][<?= $next ?>]" >
        </div>
    </td>
    <td>
        <div class="form-group field-salesinvoicedetails-rate has-success">
            <input type="number" id="salesinvoicedetails-rate-<?= $next ?>" class="form-control salesinvoicedetails-rate" name="create[rate][<?= $next ?>]" placeholder="RATE" step="0.01" aria-invalid="false" autocomplete="off" value="" >
        </div>
    </td>
    <td>
        <div class="form-group field-salesinvoicedetails-discount_percentage has-success">
            <div class="row" style="margin:0px;">
                <div class="col-md-6" style="padding:0px;">
                    <input type="number" id="salesinvoicedetails-discount_value-<?= $next ?>" value="" class="form-control salesinvoicedetails-discount_value" name="create[discount_value][<?= $next ?>]" placeholder="Discount" min="1" aria-invalid="false" autocomplete="off"  style="display:inline-block;">
                </div>
                <div class="col-md-6" style="padding:0px;">
                    <select id="salesinvoicedetails-discount_type-<?= $next ?>" class="form-control salesinvoicedetails-discount_type" name="create[discount_type][<?= $next ?>]">
                        <option value="1">Rs.</option>
                        <option value="2">%</option>
                    </select>
                </div>
            </div>
        </div>
    </td>
    <td>
        <div class="form-group field-salesinvoicedetails-tax has-success">

            <select id="salesinvoicedetails-tax-<?= $next ?>" class="form-control salesinvoicedetails-tax" name="create[tax_id][<?= $next ?>]" aria-invalid="false">
                <option value="">Slelect a Tax</option>
                <?php
                foreach ($taxes as $tax) {
                    if ($tax->type == 1) {
                        $type = '%';
                    } else {
                        $type = 'Rs';
                    }
                    ?>
                    <option value="<?= $tax->id ?>" ><?= $tax->value . ' ' . $type ?></option>
                <?php }
                ?>
            </select>
            <input type="hidden" id="salesinvoicedetails-tax_value-<?= $next ?>" value="" class="form-control salesinvoicedetails-tax_value" name="create[tax_value][<?= $next ?>]" >
            <input type="hidden" id="salesinvoicedetails-tax_type-<?= $next ?>" value="" class="form-control salesinvoicedetails-tax_type" name="create[tax_type][<?= $next ?>]" >
        </div>
    </td>
    <td>
        <div class="form-group field-salesinvoicedetails-line_total has-success">
            <input type="text" id="salesinvoicedetails-line_total-<?= $next ?>" value="" class="form-control salesinvoicedetails-line_total" name="create[line_total][<?= $next ?>]" placeholder="Amount" aria-invalid="false" autocomplete="off">
        </div>
    </td>
    <td>
        <input type="hidden" id="sales-inventory-<?= $next ?>" class="sales-inventory" name="create[inventory][<?= $next ?>]" value="1" checked="checked"/>
        <input type="checkbox" id="salesinvoicedetails-inventory-<?= $next ?>" class="salesinvoicedetails-inventory" name="" value="1" checked="checked" title="Checked for Inventory" tabindex="-1"/>
        <a id="del" class="" ><i class="fa fa-times sales-invoice-delete" title="Remove Row"></i></a>
    </td>
</tr>
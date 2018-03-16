<?php ?>
<div id="print">
    <style type="text/css">
        tfoot{display: table-footer-group;}
        table { page-break-inside:auto;}
        tr{ page-break-inside:avoid; page-break-after:auto; }

        @page {
            size: A4;
        }
        @media print {
            thead {display: table-header-group;}
            tfoot {display: table-footer-group}
            /*tfoot {position: absolute;bottom: 0px;}*/
            .main-tabl{width: 100%}
            .footer {position: fixed ; left: 0px; bottom: 0px; right: 0px; font-size:10px; }
            .main-tabl{
                -webkit-print-color-adjust: exact;
                margin: auto;
                /*tr{ page-break-inside:avoid; page-break-after:auto; }*/
            }

        }
        @media screen{
            .main-tabl{
                width: 60%;
            }
        }
        body h6,h1,h2,h3,h4,h5,p,b,tr,td,span,div{
            color:#525252 !important;
        }
        .main-tabl{
            margin: auto;
        }
        .main-left{
            float: left;
        }
        .main-right{
            float: right;

        }
        .heading{
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 17px;
        }
        .print{
            margin-top: 20px;
            margin-left: 530px;
        }
        .save{
            margin-top: 18px;
            margin-left: 6px !important;
        }
        .heading p{
            font-size: 11px;
            line-height: 5px;
        }
        .left-address p{
            font-size: 11px;
            line-height: 5px;
        }
        .footer {
            width: 100%;
            display: inline-block;
            font-size: 15px;
            color: #4e4e4e;
            border-top: 1px solid #a09c9c;
            padding: 9px 0px 3px 0px;
        }
        .footer p {
            text-align: center;
            font-size: 9px;
            margin: 0px !important;
            color: #525252 !important;
            font-weight: 600;
        }
        .invoice-head p{
            line-height: 0px;
        }
        .invoice-head{
            padding: 0px 15px;
        }
    </style>
    <table class="main-tabl" border="0" style="font-family: Roboto, sans-serif !important;">
        <thead>
            <tr>
                <th style="width:100%">
                    <div class="header">
                        <div class="main-left">
                            <img width="" height="" src="<?= Yii::$app->homeUrl ?>images/logo.png"/>
                        </div>
                        <div class="main-right" style="padding-top: 25px;">
                            <div class="heading" style="font-weight:normal">
                                <strong style="text-transform:uppercase;font-size:11px;">REN & ZHANG WORLDWIDE TRADING (S) PTE LTD</strong>
                                <p>20, Maxwell Road, #08-01W Maxwell House Singapore 069113</p>
                                <p>Tel: +65910002892, Email: shaji.ibrahim@technologies.com</p>
                                <p>Registration No: 200301824K</p>
                                <p style="padding-top: 15px;font-weight: 700;font-size:11px;">GST No: 20-0301824-K</p>
                            </div>
                        </div>
                        <br/>
                    </div>
                </th>
            </tr>

        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="heading"><h2 style="font-size:17px;letter-spacing: 4px;padding-top: 10px;">TAX INVOICE</h2></div>
                    <br/>
                    <div class="close-estimate-heading-top">
                        <table style="width: 100%;border: 1px solid #a7a2a2;">
                            <tr>
                                <td style="width:33%;">
                                    <div class="invoice-head">
                                        <table class="tb2">
                                            <tr>
                                                <td style="">
                                                    <table>
                                                        <tr style="font-size: 11px;">
                                                            <td>Invoice To</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td colspan="3"><b><?= $model->ship_to_adress ?></b></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Attn</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>
                                                                <?php
                                                                $customer = \common\models\BusinessPartner::find()->where(['id' => $model->busines_partner_code])->one();
                                                                if (!empty($customer)) {
                                                                    echo $customer->name;
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Tel</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>
                                                                <?php
                                                                if (!empty($customer)) {
                                                                    echo $customer->phone_no;
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Fax</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>
                                                                <?php
                                                                if (!empty($customer)) {
                                                                    echo $customer->fax_no;
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td style="width:33%;">
                                    <div class="invoice-head">
                                        <table class="tb2">
                                            <tr>
                                                <td style="">
                                                    <table>
                                                        <tr style="font-size: 11px;">
                                                            <td>Delivered To</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td colspan="3"><b><?= $model->delivery_address ?></b></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Attn</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>
                                                                <?php
                                                                if (!empty($customer)) {
                                                                    echo $customer->name;
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Tel</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>
                                                                <?php
                                                                if (!empty($customer)) {
                                                                    echo $customer->phone_no;
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Fax</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>
                                                                <?php
                                                                if (!empty($customer)) {
                                                                    echo $customer->fax_no;
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                                <td>
                                    <div class="invoice-head">
                                        <table class="tb2">
                                            <tr>
                                                <td style="">
                                                    <table>
                                                        <tr style="font-size: 11px;">
                                                            <td>Ref No</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>NA</td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Inv No</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td><?= $model->sales_invoice_number ?></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Date</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td><?= $model->sales_invoice_date ?></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>PO No</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td><?= $model->po_no ?></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>PO Date</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td><?= $model->po_date ?></td>
                                                        </tr>
                                                        <tr style="font-size: 11px;">
                                                            <td>Terms</td>
                                                            <td style="padding: 4px 10px;">:</td>
                                                            <td>COD</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <br/>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="invoice-details"style="margin-top: 10px;min-height: 300px;">
                        <table style="width:100%;border-collapse: collapse;text-align: center;">
                            <tr style="background: #4e5254;color: white !important;">
                                <th style="width: 10%;font-size: 12px;padding: 10px 5px;">SL No.</th>
                                <th style="width: 21%;font-size: 12px;padding: 10px 2px;">DESCRIPTION</th>
                                <th style="width: 13%;font-size: 12px;padding: 10px 5px;text-align: right;">UNIT PRICE</th>
                                <th style="width: 11%;font-size: 12px;padding: 10px 5px;text-align: right;">QTY(Kg)</th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 5px;text-align: right;">CARTON</th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 5px;text-align: right;">AMOUNT</th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 5px;text-align: right;">TAX AMOUNT</th>
                            </tr>
                            <?php
                            $p = 0;
                            $total_amount = 0;
                            $total_tax = 0;
                            $grand_total = 0;
                            $count = count($sales_details);
                            foreach ($sales_details as $value) {
                                $p++;
                                $particulars = '';
                                $total_amount += $value->amount - $value->discount_amount;
                                $total_tax += $value->tax_amount;
                                $grand_total += $value->line_total;
                                ?>
                                <tr style="<?= $count != $p ? 'border-bottom: 1px solid #a09c9c;' : '' ?>">
                                    <td style="width: 10%;font-size: 11px;padding: 10px 5px;"><?= $p ?></td>
                                    <td style="width: 21%;font-size: 11px;padding: 10px 2px;"><?= $value->item_name ?></td>
                                    <td style="width: 13%;font-size: 11px;padding: 10px 5px;text-align: right;"><?= $value->rate ?></td>
                                    <td style="width: 11%;font-size: 11px;padding: 10px 5px;text-align: right;"><?= $value->qty ?></td>
                                    <td style="width: 15%;font-size: 11px;padding: 10px 5px;text-align: right;"><?= $value->carton ?></td>
                                    <td style="width: 15%;font-size: 11px;padding: 10px 5px;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($value->amount - $value->discount_amount, 2)); ?></td>
                                    <td style="width: 15%;font-size: 11px;padding: 10px 5px;text-align: right;"><?= $value->tax_amount ?></td>
                                </tr>

                            <?php } ?>

                            <?php
//                            if (isset($appointment_details) && $appointment_details != '') {
//                                $count = count($appointment_details);
//                                $loop_count = 3 - $count;
//                                if ($loop_count > 0) {
//                                    for ($i = 0; $i <= $loop_count; $i++) {
                            ?>
<!--                                        <tr style="border-bottom: 1px solid #a09c9c;">
                                            <td style="width: 10%;font-size: 11px;;padding: 10px 2px;"></td>
                                            <td style="width: 40%;font-size: 11px;padding: 10px 2px;"></td>
                                            <td style="width: 13%;font-size: 11px;padding: 10px 2px;"></td>
                                            <td style="width: 11%;font-size: 11px;padding: 10px 2px;"></td>
                                            <td style="width: 15%;font-size: 11px;padding: 10px 2px;"></td>
                                            <td style="width: 11%;font-size: 11px;padding: 10px 2px;"></td>
                                        </tr>-->
                            <?php
//                                    }
//                                }
//                            }
                            ?>

                        </table>
                    </div>
                    <div class="invoice-details"style="margin-top: 10px;">
                        <table style="width:100%;border-collapse: collapse;text-align: left;">
                            <tr style="border-top: 1px solid #a09c9c;">
                                <th style="width: 10%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 21%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 13%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 11%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 2px;text-align: center;">Sub Total</th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 5px;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($total_amount, 2)) . ' (S$)'; ?></th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 5px;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($total_tax, 2)) . ' (S$)'; ?></th>
                            </tr>
                            <tr style="">
                                <th style="width: 10%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 21%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 13%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 11%;font-size: 12px;padding: 10px 2px;"></th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 2px;background: #4e5254;color: white;text-align: center;">Total</th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 2px;background: #4e5254;"></th>
                                <th style="width: 15%;font-size: 12px;padding: 10px 5px;background: #4e5254;color: white;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round(($total_amount + $total_tax), 2)) . ' (S$)'; ?></th>
                            </tr>
                            <tr style="">
                                <th colspan="7" style="width: 100%;font-size: 12px;padding: 10px 2px;text-align: right;"><?php echo ucwords(Yii::$app->NumToWord->ConvertNumberToWords(round($grand_total, 2))) . ' Only'; ?></th>
                            </tr>
                            <tr style="">
                                <th colspan="7" style="width: 100%;font-size: 12px;padding: 10px 2px;text-align: left;">Goods sold are not returnable</th>
                            </tr>
                            <tr style="">
                                <th colspan="7" style="width: 100%;font-size: 12px;padding: 10px 2px;text-align: left;">All cheques must be crossed and made payable to "REN & ZHANG WORLDWIDE TRADING (S) PTE LTD" only.</th>
                            </tr>
                        </table>
                    </div>
                    <div style="clear:both"></div>
                    <div class="invoice-details"style="margin-bottom: 10px;margin-top: 20px;">
                        <table style="width:100%;">
                            <tr>
                                <th style="width: 50%;font-size: 10px;padding: 20px 0px;">GOODS RECEIVED</th>
                                <th style="width: 50%;font-size: 10px;padding: 20px 0px;">REN & ZHANG WORLDWIDE TRADING (S) PTE LTD</th>
                            </tr>
                            <tr>
                                <th style="width: 50%;font-size: 10px;padding: 10px 0px;">Authorized Signature</th>
                                <th style="width: 50%;font-size: 10px;padding: 10px 0px;">Authorized Signature</th>
                            </tr>
                        </table>
                    </div>

                </td></tr>
        </tbody>
        <tfoot>
<!--            <tr>
                <td style="width:100%">
                    <div class="footer">
                        <span>
                            <p style="font-size:11px;font-weight: 600;">Bank Account Details</p>
                            <p>Account Name : Alkhalejia, Account No : 1234567890123456, Bank Name : United Emirates Bank, Branch : UAE, IBAN N0 : 34535454, Swift Code : 546546</p>
                        </span>
                    </div>
                </td>
            </tr>-->
        </tfoot>
    </table>
</div>
<script>
    function printContent(el) {
        var restorepage = document.body.innerHTML;
        var printcontent = document.getElementById(el).innerHTML;
        document.body.innerHTML = printcontent;
        window.print();
        document.body.innerHTML = restorepage;
        window.close();
    }
</script>
<div class="print">
    <?php
    if ($print) {
        ?>
        <button onclick="printContent('print')" style="font-weight: bold !important;">Print</button>
        <?php
    }
    ?>
    <button onclick="window.close();" style="font-weight: bold !important;">Close</button>
</div>
<html>
    <head>
    </head>
    <body>
        <h4 style="text-align: center;"> Customer Sales Report</h4>
        <table style="border: 1px solid; border-collapse: collapse;width: 100%;">
            <thead>
                <tr style="border: 1px solid;">
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">Invoice No.</th>
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">Invoice Date</th>
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">Po.No.</th>
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">Po.Date</th>
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">Amount</th>
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">Discount</th>
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">GST</th>
                    <th style="border: 1px solid;font-size: 12px;padding: 5px 3px;">Sale Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grand_total_amount = 0;
                $grand_total_tax = 0;
                $grand_total_disc = 0;
                $grand_total_sale = 0;
                if (!empty($customer_model)) {
                    foreach ($customer_model as $model_customer) {
                        $query = new yii\db\Query();
                        $query->select(['sales_invoice_number', 'sales_invoice_date', 'po_no', 'po_date', 'order_amount', 'tax_amount', 'amount', 'discount_amount'])
                                ->from('sales_invoice_master')
                                ->where(['busines_partner_code' => $model_customer['id']]);
                        if ($from != '') {
                            $query->andWhere(['>=', 'sales_invoice_date', $from . '00:00:00']);
                        }
                        if ($to != '') {
                            $query->andWhere(['<=', 'sales_invoice_date', $to . '60:60:60']);
                        }
                        $command = $query->createCommand();
                        $result = $command->queryAll();
                        if (!empty($result)) {
                            ?>
                            <tr style="border: 1px solid;">
                                <th colspan="6" style="padding: 7px 3px;"><?= $model_customer['company_name'] . '-' . $model_customer['name'] ?></th>
                            </tr>
                            <?php
                            $amount_tot = 0;
                            $tax_tot = 0;
                            $dic_tot = 0;
                            $sale_tot = 0;
                            foreach ($result as $value) {
                                ?>
                                <tr style="border: 1px solid;">
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;"><?= $value['sales_invoice_number'] ?></td>
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;"><?= $value['sales_invoice_date'] ?></td>
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;"><?= $value['po_no'] ?></td>
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;"><?= $value['po_date'] ?></td>
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;text-align: right;"><?= $value['amount'] ?></td>
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;text-align: right;"><?= $value['discount_amount'] ?></td>
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;text-align: right;"><?= $value['tax_amount'] ?></td>
                                    <td style="border: 1px solid;font-size: 12px;padding: 7px 3px;text-align: right;"><?= $value['order_amount'] ?></td>
                                </tr>
                                <?php
                                $amount_tot += $value['amount'];
                                $sale_tot += $value['order_amount'];
                                $dic_tot += $value['discount_amount'];
                                $tax_tot += $value['tax_amount'];
                            }
                            $grand_total_amount += $amount_tot;
                            $grand_total_tax += $tax_tot;
                            $grand_total_disc += $dic_tot;
                            $grand_total_sale += $sale_tot;
                            ?>
                            <tr style="border: 1px solid;">
                                <th colspan="4" style="text-align: right;padding: 7px 3px;border: 1px solid;">Total Amount</th>
                                <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($amount_tot, 2)) . ' (S$)'; ?></th>
                                <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($dic_tot, 2)) . ' (S$)'; ?></th>
                                <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($tax_tot, 2)) . ' (S$)'; ?></th>
                                <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($sale_tot, 2)) . ' (S$)'; ?></th>
                            </tr>
                            <?php
                        }
                    }
                }
                ?>
                <tr style="border: 1px solid;">
                    <th colspan="4" style="text-align: right;padding: 7px 3px;border: 1px solid;">Grand Total</th>
                    <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($grand_total_amount, 2)) . ' (S$)'; ?></th>
                    <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($grand_total_disc, 2)) . ' (S$)'; ?></th>
                    <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($grand_total_tax, 2)) . ' (S$)'; ?></th>
                    <th style="padding: 7px 3px;border: 1px solid;text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($grand_total_sale, 2)) . ' (S$)'; ?></th>
                </tr>
            </tbody>
        </table>
    </body>
</html>
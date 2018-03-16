<?php

use yii\helpers\Html;
?>
<?php
$query = new yii\db\Query();
$query->select(['sales_invoice_number', 'sales_invoice_date', 'po_no', 'po_date', 'order_amount', 'tax_amount', 'amount', 'discount_amount'])
        ->from('sales_invoice_master')
        ->where(['busines_partner_code' => $model->id]);
if ($from != '') {
    $query->andWhere(['>=', 'sales_invoice_date', $from . '00:00:00']);
}
if ($to != '') {
    $query->andWhere(['<=', 'sales_invoice_date', $to . '60:60:60']);
}
$command = $query->createCommand();
$result = $command->queryAll();
?>
<?php
if (!empty($result)) {
    ?>
    <tr>
        <th colspan="6"><?= $model->company_name . ' - ' . $model->name ?></th>
    </tr>
    <?php
    $amount_tot = 0;
    $tax_tot = 0;
    $disc_tot = 0;
    $sale_tot = 0;
    foreach ($result as $value) {
        ?>
        <tr>
            <td><?= $value['sales_invoice_number'] ?></td>
            <td><?= $value['sales_invoice_date'] ?></td>
            <td><?= $value['po_no'] ?></td>
            <td><?= $value['po_date'] ?></td>
            <td style="text-align: right;"><?= $value['amount'] ?></td>
            <td style="text-align: right;"><?= $value['discount_amount'] ?></td>
            <td style="text-align: right;"><?= $value['tax_amount'] ?></td>
            <td style="text-align: right;"><?= $value['order_amount'] ?></td>
        </tr>
        <?php
        $amount_tot += $value['amount'];
        $disc_tot += $value['discount_amount'];
        $tax_tot += $value['tax_amount'];
        $sale_tot += $value['order_amount'];
    }
    ?>
    <tr>
        <th colspan="4" style="text-align: center;">Total Amount</th>
        <th style="text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($amount_tot, 2)) . ' (S$)'; ?></th>
        <th style="text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($disc_tot, 2)) . ' (S$)'; ?></th>
        <th style="text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($tax_tot, 2)) . ' (S$)'; ?></th>
        <th style="text-align: right;"><?= Yii::$app->SetValues->NumberFormat(round($sale_tot, 2)) . ' (S$)'; ?></th>
    </tr>
    <?php
}
?>

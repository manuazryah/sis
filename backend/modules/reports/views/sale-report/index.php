<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\SalesInvoiceMaster;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SalesInvoiceMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sales Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sales-invoice-master-index">

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>

                </div>
                <div class="panel-body">
                    <div class="row" style="margin-left: 0px;">
                        <div class="col-md-4">

                            <?= $this->render('_search', ['model' => $searchModel, 'from' => $from, 'to' => $to]) ?>

                        </div>
                        <div class="col-md-8">
                            <div class="row" style="">
                                <div class="col-md-12" style="padding: 5px 50px;">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th colspan="7">Report Summary</th>
                                        </tr>
                                        <tr>
                                            <th>No. Of Sale</th>
                                            <th>Amount</th>
                                            <th>Discount</th>
                                            <th>GST</th>
                                            <th>Sale Amount</th>
                                            <th>Paid</th>
                                            <th>Balance</th>
                                        </tr>
                                        <?php
                                        $sale_total = SalesInvoiceMaster::getTotalCount($from, $to, '');
                                        $amount_total = SalesInvoiceMaster::getSaleTotal($from, $to, '', 'amount');
                                        $discount_total = SalesInvoiceMaster::getSaleTotal($from, $to, '', 'discount_amount');
                                        $tax_total = SalesInvoiceMaster::getSaleTotal($from, $to, '', 'tax_amount');
                                        $nettotal = SalesInvoiceMaster::getSaleTotal($from, $to, '', 'order_amount');
                                        $paid_total = SalesInvoiceMaster::getSaleTotal($from, $to, '', 'amount_payed');
                                        $balance_total = SalesInvoiceMaster::getSaleTotal($from, $to, '', 'due_amount');
                                        ?>
                                        <tr>
                                            <th><?= $sale_total ?></th>
                                            <th><?= Yii::$app->SetValues->NumberFormat(round($amount_total, 2)) . ' (S$)'; ?></th>
                                            <th><?= Yii::$app->SetValues->NumberFormat(round($discount_total, 2)) . ' (S$)'; ?></th>
                                            <th><?= Yii::$app->SetValues->NumberFormat(round($tax_total, 2)) . ' (S$)'; ?></th>
                                            <th><?= Yii::$app->SetValues->NumberFormat(round($nettotal, 2)) . ' (S$)'; ?></th>
                                            <th><?= Yii::$app->SetValues->NumberFormat(round($paid_total, 2)) . ' (S$)'; ?></th>
                                            <th><?= Yii::$app->SetValues->NumberFormat(round($balance_total, 2)) . ' (S$)'; ?></th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                    $gridColumns = [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'sales_invoice_number',
                        'sales_invoice_date',
                        'po_no',
                        'po_date',
                        [
                            'attribute' => 'amount',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        [
                            'attribute' => 'discount_amount',
                            'label' => 'Discount',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($data) {
                                return $data->discount_amount;
                            },
                        ],
                        [
                            'attribute' => 'tax_amount',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        [
                            'attribute' => 'order_amount',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        [
                            'attribute' => 'amount_payed',
                            'label' => 'Paid',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($data) {
                                if ($data->amount_payed != '') {
                                    return $data->amount_payed;
                                } else {
                                    return '';
                                }
                            },
                        ],
                        [
                            'attribute' => 'due_amount',
                            'label' => 'Balance',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($data) {
                                return $data->due_amount;
                            },
                        ],
                    ];
                    echo GridView::widget([
                        'dataProvider' => $dataProvider,
//                        'filterModel' => $searchModel,
                        'columns' => $gridColumns,
                        'containerOptions' => ['style' => 'overflow: auto'], // only set when $responsive = false
                        'toolbar' => [
                            '{export}',
                            '{toggleData}'
                        ],
//                        'pjax' => true,
                        'bordered' => true,
                        'striped' => false,
                        'condensed' => false,
                        'responsive' => true,
                        'hover' => true,
                        'floatHeader' => true,
//                        'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
//                        'showPageSummary' => true,
                        'panel' => [
                            'type' => GridView::TYPE_PRIMARY
                        ],
                        'caption' => 'Sales Report'
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


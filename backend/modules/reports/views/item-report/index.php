<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\SalesInvoiceDetails;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SalesInvoiceMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item Wise Sales Report';
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
                        <div class="col-md-5">

                            <?= $this->render('_search', ['model' => $searchModel, 'from' => $from, 'to' => $to, 'item_code' => $item_code]) ?>

                        </div>
                        <div class="col-md-7" style="padding-right: 45px;">
                            <div class="row" style="margin: 0px;">
                                <table class="table table-bordered">
                                    <tr>
                                        <th colspan="6">Report Summary</th>
                                    </tr>
                                    <tr>
                                        <th>No. Of Sale</th>
                                        <th>KG Sold</th>
                                        <th>Cartons Sold</th>
                                        <th>Pieces Sold</th>
                                        <th>GST</th>
                                        <th>Sale Amount</th>
                                    </tr>
                                    <?php
                                    $sale_total = SalesInvoiceDetails::getTotalCount($from, $to, $item_code);
                                    $total_kg = SalesInvoiceDetails::getSaleTotal($from, $to, $item_code, 'qty');
                                    $total_carton = SalesInvoiceDetails::getSaleTotal($from, $to, $item_code, 'carton');
                                    $total_pieces = SalesInvoiceDetails::getSaleTotal($from, $to, $item_code, 'pieces');
                                    $amount_total = SalesInvoiceDetails::getSaleTotal($from, $to, $item_code, 'line_total');
                                    $tax_total = SalesInvoiceDetails::getSaleTotal($from, $to, $item_code, 'tax_amount');
                                    ?>
                                    <tr>
                                        <th><?= $sale_total ?></th>
                                        <th><?= $total_kg ?></th>
                                        <th><?= $total_carton ?></th>
                                        <th><?= $total_pieces ?></th>
                                        <th><?= Yii::$app->SetValues->NumberFormat(round($tax_total, 2)) . ' (S$)'; ?></th>
                                        <th><?= Yii::$app->SetValues->NumberFormat(round($amount_total, 2)) . ' (S$)'; ?></th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php
                    $gridColumns = [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'item_code',
                        'item_name',
                        ['attribute' => 'qty',
                            'header' => 'KG Sold',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        ['attribute' => 'carton',
                            'header' => 'Carton Sold',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        ['attribute' => 'pieces',
                            'header' => 'Pieces Sold',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        ['attribute' => 'amount',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        ['attribute' => 'discount_amount',
                            'header' => 'Discount',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        ['attribute' => 'tax_amount',
                            'header' => 'GST',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        ['attribute' => 'line_total',
                            'header' => 'Sale Amount',
                            'contentOptions' => ['style' => 'text-align: right;'],
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
                        'caption' => 'Item Wise Sales Report'
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>



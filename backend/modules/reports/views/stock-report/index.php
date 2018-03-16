<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\SalesInvoiceDetails;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SalesInvoiceMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Stock Register';
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
                        <div class="col-md-6">

                            <?= $this->render('_search', ['model' => $searchModel, 'from' => $from, 'to' => $to, 'item_code' => $item_code]) ?>

                        </div>
                        <div class="col-md-6">
                        </div>
                    </div>
                    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

                    <?php
                    $gridColumns = [
                        ['class' => 'kartik\grid\SerialColumn'],
                        'document_date',
                        'item_code',
                        'item_name',
                        [
                            'attribute' => 'transaction',
                            'attribute' => 'Type',
                            'value' => function ($model) {
                                if ($model->transaction == 1) {
                                    return 'Opening Stock';
                                } elseif ($model->transaction == 2) {
                                    return 'Stock Adjustment';
                                } elseif ($model->transaction == 3) {
                                    return 'Stock Sale';
                                } else {
                                    return '';
                                }
                            },
                        ],
                        [
                            'attribute' => 'price',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($model) {
                                $info = \common\models\ItemMaster::findOne(['id' => $model->item_id]);

                                return isset($info->MRP) ? $info->MRP : '';
                            },
                        ],
                        [
                            'attribute' => 'batch_no',
                            'value' => function ($model) {
                                return isset($model->batch_no) ? $model->batch_no : '';
                            },
                        ],
                        [
                            'attribute' => 'weight_in',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($model) {
                                return isset($model->weight_in) ? $model->weight_in : '';
                            },
                        ],
                        [
                            'attribute' => 'weight_out',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($model) {
                                return isset($model->weight_out) ? $model->weight_out : '';
                            },
                        ],
                        [
                            'attribute' => 'cartoon_in',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'label' => 'Cartons In',
                            'value' => function ($model) {
                                return isset($model->cartoon_in) ? $model->cartoon_in : '';
                            },
                        ],
                        [
                            'attribute' => 'cartoon_out',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'label' => 'Cartons Out',
                            'value' => function ($model) {
                                return isset($model->cartoon_out) ? $model->cartoon_out : '';
                            },
                        ],
                        [
                            'attribute' => 'piece_in',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($model) {
                                return isset($model->piece_in) ? $model->piece_in : '';
                            },
                        ],
                        [
                            'attribute' => 'piece_out',
                            'contentOptions' => ['style' => 'text-align: right;'],
                            'value' => function ($model) {
                                return isset($model->piece_out) ? $model->piece_out : '';
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
                        'caption' => 'Stock Register'
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>



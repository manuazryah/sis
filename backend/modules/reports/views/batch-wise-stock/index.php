<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SalesInvoiceMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Batch Wise Stock Report';
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

                            <?= $this->render('_search', ['model' => $searchModel, 'batch' => $batch]) ?>

                        </div>
                        <div class="col-md-7">
                        </div>
                    </div>
                    <?php
                    $gridColumns = [
                        ['class' => 'kartik\grid\SerialColumn'],
                        [
                            'attribute' => 'item_id',
                            'label' => 'Item Name',
                            'filter' => Html::activeDropDownList($searchModel, 'item_id', ArrayHelper::map(common\models\ItemMaster::find()->all(), 'id', 'item_name'), ['class' => 'form-control', 'id' => 'item-name', 'prompt' => '']),
                            'value' => function ($data) {
                                $item = common\models\ItemMaster::findOne($data->item_id);
                                if (isset($item))
                                    return $item->item_name;
                            },
                        ],
                        'item_code',
                        'batch_no',
                        [
                            'attribute' => 'available_carton',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        [
                            'attribute' => 'available_weight',
                            'contentOptions' => ['style' => 'text-align: right;'],
                        ],
                        [
                            'attribute' => 'available_pieces',
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
                        'caption' => 'Batch Wise Stock Report'
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>



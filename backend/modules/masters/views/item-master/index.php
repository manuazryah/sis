<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use common\components\ModalViewWidget;
use yii\helpers\ArrayHelper;
use common\models\Locations;
use common\models\Tax;
use common\models\BaseUnit;

/* @var $this yii\web\View */
/* @var $searchModel common\models\ItemMasterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Item Masters';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="item-master-index">

    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>


                </div>
                <div class="panel-body">
                    <?= Html::button('<i class="fa-th-list"></i><span> Add New</span>', ['value' => Url::to('create'), 'class' => 'btn btn-warning  btn-icon btn-icon-standalone modalButton']) ?>
                    <?= \common\widgets\Alert::widget(); ?>
                    <?php
                    echo ModalViewWidget::widget();
                    ?>
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
//                            'id',
//                            [
//                                'attribute' => 'location',
//                                'format' => 'raw',
//                                'filter' => Html::activeDropDownList($searchModel, 'location', ArrayHelper::map(Locations::find()->all(), 'id', 'location_name'), ['class' => 'form-control', 'id' => 'name', 'prompt' => '']),
//                                'value' => function ($data) {
//                                    return Locations::findOne($data->location)->location_name;
//                                },
//                            ],
                            'item_code',
                            'item_name',
//                            'item_type',
//                            'tax_id',
//                            [
//                                'attribute' => 'base_unit_id',
//                                'format' => 'raw',
//                                'filter' => Html::activeDropDownList($searchModel, 'base_unit_id', ArrayHelper::map(BaseUnit::find()->all(), 'id', 'name'), ['class' => 'form-control', 'id' => 'name', 'prompt' => '']),
//                                'value' => function ($data) {
//                                    return BaseUnit::findOne($data->base_unit_id)->name;
//                                },
//                            ],
                            [
                                'attribute' => 'MRP',
                                'contentOptions' => ['style' => 'text-align: right;'],
                            ],
                            // 'retail_price',
                            // 'purchase_price',
//                            'item_cost',
                            // 'whole_sale_price',
                            // 'hsn',
                            // 'location',
                            [
                                'attribute' => 'status',
                                'filter' => ['1' => 'Enable', '0' => 'Disable'],
                                'value' => function($data) {
                                    return $data->status == 1 ? 'Enable' : 'Disable';
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
//                                    'contentOptions' => ['style' => 'width:100px;'],
                                'header' => 'Actions',
                                'template' => '{update}{view}',
                                'buttons' => [
                                    'update' => function ($url, $model) {
                                        return Html::button('<i class="fa fa-pencil"></i>', ['value' => Url::to(['update', 'id' => $model->id]), 'class' => 'modalButton edit-btn']);
                                    },
                                    'view' => function ($url, $model) {
                                        return Html::button('<i class="fa fa-eye"></i>', ['value' => Url::to(['view', 'id' => $model->id]), 'class' => 'modalButton edit-btn']);
                                    },
                                ],
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>



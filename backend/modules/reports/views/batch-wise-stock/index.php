<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use common\models\StockView;
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
                            <div class="col-md-2">
                                <div class="sales-invoice-master-search" style="margin-right: 15px;float: left;">

                                    <?= Html::beginForm(['batch-wise-stock/reports'], 'post', ['target' => 'print_popup', 'id' => "epda-form", 'style' => 'margin-bottom: 0px;']) ?>
                                    <input type="hidden" value="<?= $batch ?>" name="batch"/>
                                    <?= Html::submitButton('<i class="fa fa-file-pdf-o" style="padding-right: 10px;"></i><span>PDF</span>', ['class' => 'btn btn-default', 'id' => 'pdf-btn', 'name' => 'pdf', 'style' => 'background-color: #337ab7;border-color: #2e6da4;color:white;', 'formtarget' => '_blank']) ?>

                                    <?= Html::endForm() ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
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
                            'available_carton',
                            'available_weight',
                            'available_pieces',
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>


<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Locations;
use common\models\Tax;
use common\models\BaseUnit;

/* @var $this yii\web\View */
/* @var $model common\models\ItemMaster */

$this->title = $model->item_code;
$this->params['breadcrumbs'][] = ['label' => 'Item Masters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>


            </div>
            <div class="panel-body">
                <div class="panel-body"><div class="item-master-view">
                        <?=
                        DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                'id',
//                                [
//                                    'attribute' => 'location',
//                                    'value' => function ($data) {
//                                        return Locations::findOne($data->location)->location_name;
//                                    },
//                                ],
                                'item_code',
                                'item_name',
//                                'item_type',
                                [
                                    'attribute' => 'tax_id',
                                    'value' => function ($data) {
                                        return Tax::findOne($data->tax_id)->name;
                                    },
                                ],
//                                [
//                                    'attribute' => 'base_unit_id',
//                                    'value' => function ($data) {
//                                        return BaseUnit::findOne($data->base_unit_id)->name;
//                                    },
//                                ],
                                'MRP',
//                                'retail_price',
                                'purchase_price',
//                                'item_cost',
//                                'whole_sale_price',
//                                'hsn',
                                [
                                    'attribute' => 'status',
                                    'value' => function($data) {
                                        return $data->status == 1 ? 'Enable' : 'Disable';
                                    }
                                ],
//                                'CB',
//                                'UB',
//                                'DOC',
//                                'DOU',
                            ],
                        ])
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



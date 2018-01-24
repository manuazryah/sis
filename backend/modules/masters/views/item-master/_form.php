<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\Tax;
use common\models\Locations;
use common\models\BaseUnit;
use common\models\ProductCategory;

/* @var $this yii\web\View */
/* @var $model common\models\ItemMaster */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="item-master-form form-inline">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?php $locations = ArrayHelper::map(Locations::findAll(['status' => 1]), 'id', 'location_name'); ?>
            <?= $form->field($model, 'location')->dropDownList($locations, ['prompt' => '-Choose a Location-']) ?>
        </div>
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?php $product_category = ArrayHelper::map(ProductCategory::findAll(['status' => 1]), 'id', 'category_name'); ?>
            <?= $form->field($model, 'item_type')->dropDownList($product_category, ['prompt' => '-Choose a Category-']) ?>
        </div>
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?php
            if ($model->isNewRecord) {
                $item_code = $this->context->generateItemCode();
                $model->item_code = $item_code;
            }
            ?>
            <?= $form->field($model, 'item_code')->textInput(['maxlength' => true, 'readonly' => TRUE]) ?>

        </div>
    </div>
    <div class="row">
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?= $form->field($model, 'item_name')->textInput(['maxlength' => true]) ?>

        </div>
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?php $taxes = ArrayHelper::map(Tax::findAll(['status' => 1]), 'id', 'name'); ?>
            <?= $form->field($model, 'tax_id')->dropDownList($taxes, ['prompt' => '-Choose a Tax-']) ?>

        </div>
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?php $uom = ArrayHelper::map(BaseUnit::findAll(['status' => 1]), 'id', 'value'); ?>
            <?= $form->field($model, 'base_unit_id')->dropDownList($uom, ['prompt' => '-Choose a UOM-']) ?>

        </div>
    </div>
    <div class="row">
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?= $form->field($model, 'MRP')->textInput(['maxlength' => true]) ?>

        </div>
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?= $form->field($model, 'purchase_price')->textInput(['maxlength' => true]) ?>

        </div>
        <div class='col-md-4 col-sm-4 col-xs-12 left_padd'>
            <?= $form->field($model, 'status')->dropDownList(['1' => 'Enable', '0' => 'Disable']) ?>

        </div>
    </div>
    <div class="row">
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'style' => 'margin-top: 18px; height: 36px; width:100px;float:right;']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

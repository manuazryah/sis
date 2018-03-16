<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "item_master".
 *
 * @property int $id
 * @property string $item_code
 * @property string $item_name
 * @property int $item_type
 * @property int $tax_id
 * @property int $base_unit_id
 * @property string $MRP
 * @property string $retail_price
 * @property string $purchase_price
 * @property string $item_cost
 * @property string $whole_sale_price
 * @property int $hsn
 * @property int $location
 * @property int $status
 * @property int $CB
 * @property int $UB
 * @property string $DOC
 * @property string $DOU
 */
class ItemMaster extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'item_master';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['MRP', 'item_type', 'tax_id', 'item_name', 'purchase_price'], 'required'],
            [['item_type', 'tax_id', 'base_unit_id', 'hsn', 'location', 'status', 'CB', 'UB'], 'integer'],
            [['MRP', 'retail_price', 'purchase_price', 'item_cost', 'whole_sale_price'], 'number'],
            [['DOC', 'DOU'], 'safe'],
            [['item_code', 'item_name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'item_code' => 'Item Code',
            'item_name' => 'Item Name',
            'item_type' => 'Category',
            'tax_id' => 'Tax',
            'base_unit_id' => 'UOM',
            'MRP' => 'Price',
            'retail_price' => 'Retail Price',
            'purchase_price' => 'Purchase Price',
            'item_cost' => 'Item Cost',
            'whole_sale_price' => 'Whole Sale Price',
            'hsn' => 'Hsn',
            'location' => 'Location',
            'status' => 'Status',
            'CB' => 'Cb',
            'UB' => 'Ub',
            'DOC' => 'Doc',
            'DOU' => 'Dou',
        ];
    }

    public function getName() {
        return $this->item_name . " (" . $this->item_code . ") ";
    }

}

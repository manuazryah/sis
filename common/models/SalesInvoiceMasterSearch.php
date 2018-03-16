<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\SalesInvoiceMaster;

/**
 * SalesInvoiceMasterSearch represents the model behind the search form about `common\models\SalesInvoiceMaster`.
 */
class SalesInvoiceMasterSearch extends SalesInvoiceMaster {

    /**
     * @var string
     */
    public $createdFrom;

    /**
     * @var string
     */
    public $createdTo;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'order_type', 'busines_partner_code', 'salesman', 'terms', 'payment_status', 'receipt_id', 'status', 'CB', 'UB'], 'integer'],
            [['sales_invoice_number', 'sales_invoice_date', 'payment_terms', 'delivery_terms', 'general_terms', 'ship_to_adress', 'delivery_address', 'contact_number', 'po_no', 'po_date', 'email', 'due_date', 'reference', 'receipt_no', 'error_message', 'DOC', 'DOU'], 'safe'],
            [['amount', 'tax_amount', 'order_amount', 'discount_amount', 'cash_amount', 'card_amount', 'round_of_amount', 'amount_payed', 'due_amount', 'goods_total', 'service_total'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = SalesInvoiceMaster::find()->orderBy(['id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sales_invoice_date' => $this->sales_invoice_date,
            'order_type' => $this->order_type,
            'busines_partner_code' => $this->busines_partner_code,
            'salesman' => $this->salesman,
            'amount' => $this->amount,
            'tax_amount' => $this->tax_amount,
            'order_amount' => $this->order_amount,
            'terms' => $this->terms,
            'po_date' => $this->po_date,
            'discount_amount' => $this->discount_amount,
            'cash_amount' => $this->cash_amount,
            'card_amount' => $this->card_amount,
            'round_of_amount' => $this->round_of_amount,
            'amount_payed' => $this->amount_payed,
            'due_amount' => $this->due_amount,
            'due_date' => $this->due_date,
            'payment_status' => $this->payment_status,
            'receipt_id' => $this->receipt_id,
            'goods_total' => $this->goods_total,
            'service_total' => $this->service_total,
            'status' => $this->status,
            'CB' => $this->CB,
            'UB' => $this->UB,
            'DOC' => $this->DOC,
            'DOU' => $this->DOU,
        ]);

        $query->andFilterWhere(['like', 'sales_invoice_number', $this->sales_invoice_number])
                ->andFilterWhere(['like', 'payment_terms', $this->payment_terms])
                ->andFilterWhere(['like', 'delivery_terms', $this->delivery_terms])
                ->andFilterWhere(['like', 'general_terms', $this->general_terms])
                ->andFilterWhere(['like', 'ship_to_adress', $this->ship_to_adress])
                ->andFilterWhere(['like', 'delivery_address', $this->delivery_address])
                ->andFilterWhere(['like', 'contact_number', $this->contact_number])
                ->andFilterWhere(['like', 'po_no', $this->po_no])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'reference', $this->reference])
                ->andFilterWhere(['like', 'receipt_no', $this->receipt_no])
                ->andFilterWhere(['like', 'error_message', $this->error_message]);

        return $dataProvider;
    }

}

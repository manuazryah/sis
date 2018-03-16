<?php

namespace backend\modules\sales\controllers;

use Yii;
use common\models\SalesInvoiceDetails;
use common\models\SalesInvoiceDetailsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\SalesInvoiceMaster;
use common\models\SalesInvoiceMasterSearch;
use common\models\BusinessPartner;
use yii\helpers\Json;
use common\models\StockView;
use common\models\Payments;

/**
 * SalesInvoiceDetailsController implements the CRUD actions for SalesInvoiceDetails model.
 */
class SalesInvoiceDetailsController extends Controller {

    public function beforeAction($action) {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (Yii::$app->user->isGuest) {
            $this->redirect(['/site/index']);
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SalesInvoiceDetails models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SalesInvoiceMasterSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = 40;

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SalesInvoiceDetails model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = SalesInvoiceMaster::findOne(['id' => $id]);
        $sales_details = SalesInvoiceDetails::findAll(['sales_invoice_master_id' => $id]);
        return $this->render('view', [
                    'model' => $model,
                    'sales_details' => $sales_details,
        ]);
    }

    /**
     * Creates a new SalesInvoiceDetails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new SalesInvoiceDetails();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SalesInvoiceDetails model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SalesInvoiceDetails model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SalesInvoiceDetails model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SalesInvoiceDetails the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = SalesInvoiceDetails::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new SalesInvoiceDetails model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionAdd($id = NULL) {
        if (isset($id)) {
            $model = new SalesInvoiceDetails();
            $model_sales_master = SalesInvoiceMaster::findOne($id);
        } else {
            $model = new SalesInvoiceDetails();
            $model_sales_master = new SalesInvoiceMaster();
        }

        if ($model_sales_master->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            $model_sales_master = $this->SaveSalesMaster($model_sales_master, $data);
            if ($model_sales_master->order_amount > 0) {

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model_sales_master->save() && $this->SalesCreate($model_sales_master) && $this->SavePayments($model_sales_master)) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', "Invoice Created successfully");
                    } else {
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('error', "There was a problem creating new invoice. Please try again.");
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', "There was a problem creating new invoice. Please try again.");
                }
            } else {
                $model_sales_master->save();
            }
            return $this->redirect(['index']);
        }
        return $this->render('add', [
                    'model' => $model,
                    'model_sales_master' => $model_sales_master,
                    'id' => $id,
        ]);
    }

    /**
     * To save data into Payment model.
     * @return
     */
    public function SavePayments($model_sales_master) {
        $flag = 0;
        if ($model_sales_master->amount_payed > 0) {
            $payment = new Payments();
            $payment->invoice_id = $model_sales_master->id;
            $payment->invoice_number = $model_sales_master->sales_invoice_number;
            $payment->payment_date = date('Y-m-d');
            $payment->invoice_type = 1;
            $payment->amount = $model_sales_master->amount_payed;
            $payment->status = 1;
            Yii::$app->SetValues->Attributes($payment);
            if ($payment->save()) {
                $flag = 1;
            }
        } else {
            $flag = 1;
        }
        if ($flag == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * To set data into SalesInvoiceMaster model.
     * @return SalesInvoiceMaster model
     */
    public function SaveSalesMaster($model_sales_master, $data) {
        $model_sales_master->sales_invoice_date = date("Y-m-d H:i:s");
        $model_sales_master->po_date = date("Y-m-d", strtotime(str_replace('/', '-', $model_sales_master->po_date)));
        $model_sales_master->salesman = Yii::$app->user->identity->id;
        if ($data['order_sub_total'] > 0) {
            $model_sales_master->amount = $data['amount_without_tax'];
            $model_sales_master->tax_amount = $data['tax_sub_total'];
            $model_sales_master->order_amount = $data['order_sub_total'];
            $model_sales_master->cash_amount = $data['cash_amount'];
            $model_sales_master->round_of_amount = $data['round_of'];
            $model_sales_master->discount_amount = $data['discount_sub_total'];
            $model_sales_master->amount_payed = $data['payed_amount'];
            $model_sales_master->due_amount = $data['balance'];
            if ($data['balance'] > 0) {
                if ($data['due_date'] != '') {
                    $model_sales_master->due_date = date("Y-m-d", strtotime($data['due_date']));
                }
            }
        }
        $model_sales_master->status = 1;
        Yii::$app->SetValues->Attributes($model_sales_master);
        return $model_sales_master;
    }

    /**
     * To set sales details into an array.
     */
    public function SalesCreate($model_sales_master) {
        $flag = 0;
        if (isset($_POST['create']) && $_POST['create'] != '') {
            $create = $_POST['create'];
            $arr = [];
            $i = 0;
            foreach ($create['item_id'] as $val) {
                $arr[$i]['item_id'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['comment'] as $val) {
                $arr[$i]['comment'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['qty'] as $val) {
                $arr[$i]['qty'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['type'] as $val) {
                $arr[$i]['type'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['avail_carton'] as $val) {
                $arr[$i]['avail_carton'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['avail_weight'] as $val) {
                $arr[$i]['avail_weight'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['avail_pieces'] as $val) {
                $arr[$i]['avail_pieces'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['rate'] as $val) {
                $arr[$i]['rate'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['discount_value'] as $val) {
                $arr[$i]['discount_value'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['discount_type'] as $val) {
                $arr[$i]['discount_type'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['tax_id'] as $val) {
                $arr[$i]['tax_id'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['tax_value'] as $val) {
                $arr[$i]['tax_value'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['tax_type'] as $val) {
                $arr[$i]['tax_type'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['line_total'] as $val) {
                $arr[$i]['line_total'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['inventory'] as $val) {
                $arr[$i]['inventory'] = $val;
                $i++;
            }
            $i = 0;
            foreach ($create['comment'] as $val) {
                $arr[$i]['comment'] = $val;
                $i++;
            }
            if ($this->AddSalesDetails($arr, $model_sales_master)) {
                $flag = 1;
            }
        }
        if ($flag == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This function save sales invoice details.
     */
    public function AddSalesDetails($arr, $model_sales_master) {
        $flag = 0;
        foreach ($arr as $val) {
            $aditional = new SalesInvoiceDetails();
            $item_datas = \common\models\ItemMaster::find()->where(['id' => $val['item_id']])->one();
            if (!empty($item_datas)) {
                $aditional->sales_invoice_master_id = $model_sales_master->id;
                $aditional->sales_invoice_number = $model_sales_master->sales_invoice_number;
                $aditional->sales_invoice_date = $model_sales_master->sales_invoice_date;
                $aditional->busines_partner_code = $model_sales_master->busines_partner_code;
                $aditional->salesman = $model_sales_master->salesman;
                $aditional->item_id = $item_datas->id;
                $aditional->item_code = $item_datas->item_code;
                $aditional->item_name = $item_datas->item_name;
                $aditional->base_unit = $item_datas->base_unit_id;
                $aditional->type = $val['type'];
                $aditional->inventory = $val['inventory'];
                if ($aditional->inventory == 1) {
                    $quantity = $this->SaleQuantity($aditional->item_id, $aditional->type, $val['qty']);
                    if ($item_datas->item_type == 1) {
                        $aditional->qty = $quantity['tot-weight'];
                        $no_of_qty = $aditional->qty;
                        $aditional->carton = $quantity['tot-cart'];
                        $aditional->pieces = $quantity['tot-pieces'];
                    } elseif ($item_datas->item_type == 2) {
                        $aditional->pieces = $quantity['tot-pieces'];
                        $no_of_qty = $aditional->pieces;
                    }
                    $aditional->qty_description = Json::encode($quantity['arr']);
                } elseif ($aditional->inventory == 0) {
                    if ($aditional->type == 1) {
                        $aditional->carton = $val['qty'];
                        $no_of_qty = $val['qty'];
                    } elseif ($aditional->type == 2) {
                        $aditional->qty = $val['qty'];
                        $no_of_qty = $val['qty'];
                    } elseif ($aditional->type == 3) {
                        $aditional->pieces = $val['qty'];
                        $no_of_qty = $val['qty'];
                    }
                }
                $aditional->rate = $val['rate'];
                $aditional->amount = $no_of_qty * $aditional->rate;
                $aditional->discount_type = $val['discount_type'];
                $aditional->discount_value = $val['discount_value'];
                if ($aditional->discount_type == 1) {
                    $aditional->discount_amount = $val['discount_value'];
                } else {
                    $aditional->discount_amount = ($aditional->amount * $val['discount_value']) / 100;
                }
                $aditional->net_amount = $aditional->amount - $aditional->discount_amount;
                $aditional->line_total = $val['line_total'];
                $aditional->tax_id = $val['tax_id'];
                $tax = \common\models\Tax::findOne(['id' => $aditional->tax_id]);
                if ($tax->type == 2) {
                    $tax_amount = $tax->value;
                } else {
                    $tax_amount = ($aditional->net_amount * $tax->value) / 100;
                }
                $aditional->tax_amount = $tax_amount;
                $aditional->tax_type = $tax->type;
                $aditional->tax_percentage = $tax->value;
                $aditional->comments = $val['comment'];
                $aditional->status = 1;
                $aditional->CB = Yii::$app->user->identity->id;
                $aditional->UB = Yii::$app->user->identity->id;
                $aditional->DOC = date('Y-m-d');
                if ($aditional->save()) {
                    if ($aditional->inventory == 1) {
                        if ($this->AddStockRegister($aditional)) {
                            $flag = 1;
                        } else {
                            $flag = 0;
                        }
                    } else {
                        $flag = 1;
                    }
                }
            }
        }
        if ($flag == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This function add each sales details into stock register.
     */
    public function AddStockRegister($aditional) {
        $flag = 0;
        $datas = Json::decode($aditional->qty_description);
        foreach ($datas as $value) {
            $item_datas = \common\models\ItemMaster::find()->where(['id' => $aditional->item_id])->one();
            $stock = new \common\models\StockRegister();
            $stock->transaction = 3;
            $stock->document_no = $aditional->sales_invoice_number;
            $stock->document_date = $aditional->sales_invoice_date;
            $stock->item_id = $aditional->item_id;
            $stock->item_code = $aditional->item_code;
            $stock->item_name = $aditional->item_name;
            $stock->batch_no = $value['batch_name'];
            if ($item_datas->item_type == 1) {
                $stock->cartoon_out = $value['no_of_carton'];
                $stock->piece_out = $value['no_of_pieces'];
                $stock->weight_out = $value['no_of_weight'];
            } elseif ($item_datas->item_type == 2) {
                $stock->piece_out = $value['no_of_pieces'];
            }
            $stock->location_code = '';
            $stock->item_cost = $aditional->rate;
            $stock->balance_qty = 0;
            $stock->total_cost = $aditional->line_total;
            $stock->status = 1;
            $stock->CB = Yii::$app->user->identity->id;
            $stock->UB = Yii::$app->user->identity->id;
            $stock->DOC = date('Y-m-d');
            if ($stock->save()) {
                if ($this->AddStockView($stock)) {
                    $flag = 1;
                }
            }
        }
        if ($flag == 1) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This function update stock details.
     */
    public function AddStockView($stock) {
        $stock_view = StockView::find()->where(['batch_no' => $stock->batch_no, 'item_id' => $stock->item_id])->one();
        $item_datas = \common\models\ItemMaster::find()->where(['id' => $stock->item_id])->one();
        if ($item_datas->item_type == 1) {
            $stock_view->available_carton -= $stock->cartoon_out;
            $stock_view->available_weight -= $stock->weight_out;
            $stock_view->available_pieces -= $stock->piece_out;
        } elseif ($item_datas->item_type == 2) {
            $stock_view->available_pieces -= $stock->piece_out;
        }
        if ($stock_view->save()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This function calculate sale quantity in kg,cartons and pieces.
     */
    public function SaleQuantity($item_id, $type, $qty) {
        $items = \common\models\StockView::find()->where(['item_id' => $item_id])->all();
        $arr = array();
        $total_wgt = 0;
        $total_crt = 0;
        $total_pieces = 0;
        if ($type == 1) {
            $q = $qty;
            $i = 0;
            foreach ($items as $item) {
                $weight = 0;
                if ($q == $item->available_carton) {
                    $weight = $item->available_weight;
                    $arr[$i]['no_of_carton'] = $q;
                    $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                    $arr[$i]['batch_name'] = $item->batch_no;
                    if ($item->available_pieces > 0 && $item->available_pieces != '') {
                        $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                        $total_pieces += sprintf('%0.2f', $item->available_pieces);
                    } else {
                        $arr[$i]['no_of_pieces'] = 0;
                    }
                    $total_wgt += sprintf('%0.2f', $weight);
                    $total_crt += $q;
                    break;
                } elseif ($q < $item->available_carton) {
                    $weight = $q * $item->weight_per_carton;
                    $arr[$i]['no_of_carton'] = (int) $q;
                    $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                    $arr[$i]['batch_name'] = $item->batch_no;
                    if ($item->available_pieces > 0 && $item->available_pieces != '') {
                        $pieces = $q * $item->piece_per_carton;
                        if ($pieces < $item->available_pieces) {
                            $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $pieces);
                            $total_pieces += sprintf('%0.2f', $pieces);
                        } else {
                            $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                            $total_pieces += sprintf('%0.2f', $item->available_pieces);
                        }
                    } else {
                        $arr[$i]['no_of_pieces'] = 0;
                    }
                    $total_wgt += sprintf('%0.2f', $weight);
                    $total_crt += (int) $q;
                    break;
                } elseif ($q > $item->available_carton) {
                    $weight = $item->available_carton * $item->weight_per_carton;
                    if ($weight > $item->available_weight) {
                        $weight = $item->available_weight;
                    }
                    $arr[$i]['no_of_carton'] = (int) $item->available_carton;
                    $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                    $arr[$i]['batch_name'] = $item->batch_no;
                    if ($item->available_pieces > 0 && $item->available_pieces != '') {
                        if ($item->piece_per_carton > $item->available_pieces) {
                            $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                            $total_pieces += sprintf('%0.2f', $item->available_pieces);
                        } else {
                            $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->piece_per_carton);
                            $total_pieces += sprintf('%0.2f', $item->piece_per_carton);
                        }
                    } else {
                        $arr[$i]['no_of_pieces'] = 0;
                    }
                    $q = $q - $item->available_carton;
                    $total_crt += (int) $item->available_carton;
                    $total_wgt += sprintf('%0.2f', $weight);
                }
                $i++;
            }
        } elseif ($type == 2) {
            $q = $qty;
            $i = 0;
            foreach ($items as $item) {
                $weight = 0;
                if ($q == $item->available_weight) {
                    $weight = $item->available_weight;
                    if ($item->available_carton > 0 && $item->available_carton != '') {
                        $arr[$i]['no_of_carton'] = (int) $item->available_carton;
                        $total_crt += (int) $item->available_carton;
                    } else {
                        $arr[$i]['no_of_carton'] = 0;
                    }
                    if ($item->available_pieces > 0 && $item->available_pieces != '') {
                        $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                        $total_pieces += sprintf('%0.2f', $item->available_pieces);
                    } else {
                        $arr[$i]['no_of_pieces'] = 0;
                    }
                    $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                    $arr[$i]['batch_name'] = $item->batch_no;
                    $total_wgt += sprintf('%0.2f', $weight);
                    break;
                } elseif ($q < $item->available_weight) {
                    $weight = $q;
                    if ($item->available_carton > 0 && $item->available_carton != '') {
                        $rem = $q / $item->weight_per_carton;
                        $arr[$i]['no_of_carton'] = (int) ($rem);
                        $total_crt += (int) ($rem);
                    } else {
                        $arr[$i]['no_of_carton'] = 0;
                    }
                    if ($item->available_pieces > 0 && $item->available_pieces != '') {
                        $weight_kg = $item->piece_per_carton / $item->weight_per_carton;
                        $pieces = $q * $weight_kg;
                        if ($pieces < $item->available_pieces) {
                            $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $pieces);
                            $total_pieces += sprintf('%0.2f', $pieces);
                        } else {
                            $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                            $total_pieces += sprintf('%0.2f', $item->available_pieces);
                        }
                    } else {
                        $arr[$i]['no_of_pieces'] = 0;
                    }
                    $arr[$i]['no_of_weight'] = sprintf('%0.2f', $q);
                    $arr[$i]['batch_name'] = $item->batch_no;
                    $total_wgt += sprintf('%0.2f', $weight);
                    break;
                } elseif ($q > $item->available_weight) {
                    $weight = $item->available_weight;
                    if ($item->available_carton > 0 && $item->available_carton != '') {
                        $arr[$i]['no_of_carton'] = (int) $item->available_carton;
                        $total_crt += (int) $item->available_carton;
                    } else {
                        $arr[$i]['no_of_carton'] = 0;
                    }
                    if ($item->available_pieces > 0 && $item->available_pieces != '') {
                        $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                        $total_pieces += sprintf('%0.2f', $item->available_pieces);
                    } else {
                        $arr[$i]['no_of_pieces'] = 0;
                    }
                    $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                    $arr[$i]['batch_name'] = $item->batch_no;
                    $q = $q - $item->available_weight;
                    $total_wgt += sprintf('%0.2f', $weight);
                }
                $i++;
            }
        } elseif ($type == 3) {
            $q = $qty;
            $i = 0;
            foreach ($items as $item) {
                $item_data = \common\models\ItemMaster::find()->where(['id' => $item->item_id])->one();
                $arr[$i]['no_of_weight'] = '';
                $arr[$i]['no_of_carton'] = '';
                if ($q == $item->available_pieces) {
                    $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                    if ($item_data->item_type == 1) {
                        $weight_kg = $item->weight_per_carton / $item->piece_per_carton;
                        $weight = $q * $weight_kg;
                        $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                        $carton = $q / $item->piece_per_carton;
                        if ($item->available_carton > 0 && $item->available_carton != '') {
                            $arr[$i]['no_of_carton'] = (int) ($carton);
                            $total_crt += (int) ($carton);
                        }
                    } else {
                        $weight = $q;
                    }
                    $arr[$i]['batch_name'] = $item->batch_no;
                    $total_wgt += sprintf('%0.2f', $weight);
                    $total_pieces += sprintf('%0.2f', $q);
                    break;
                } elseif ($q < $item->available_pieces) {
                    $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $q);
                    if ($item_data->item_type == 1) {
                        $weight_kg = $item->weight_per_carton / $item->piece_per_carton;
                        $weight = $q * $weight_kg;
                        if ($weight < $item->available_weight) {
                            $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                        } else {
                            $arr[$i]['no_of_weight'] = sprintf('%0.2f', $item->available_weight);
                        }
                        $carton = $q / $item->piece_per_carton;
                        if ($item->available_carton > 0 && $item->available_carton != '') {
                            $arr[$i]['no_of_carton'] = (int) ($carton);
                            $total_crt += (int) ($carton);
                        }
                    } else {
                        $weight = $q;
                    }
                    $arr[$i]['batch_name'] = $item->batch_no;
                    $total_wgt += sprintf('%0.2f', $weight);
                    $total_pieces += sprintf('%0.2f', $q);
                    break;
                } elseif ($q > $item->available_pieces) {
                    $arr[$i]['no_of_pieces'] = sprintf('%0.2f', $item->available_pieces);
                    if ($item_data->item_type == 1) {
                        $weight = $item->available_weight;
                        $arr[$i]['no_of_weight'] = sprintf('%0.2f', $weight);
                        $carton = $item->available_carton;
                        if ($item->available_carton > 0 && $item->available_carton != '') {
                            $arr[$i]['no_of_carton'] = (int) ($carton);
                            $total_crt += (int) ($carton);
                        }
                    } else {
                        $weight = $item->available_pieces;
                    }
                    $arr[$i]['batch_name'] = $item->batch_no;
                    $total_wgt += sprintf('%0.2f', $weight);
                    $total_pieces += sprintf('%0.2f', $item->available_pieces);
                    $q -= $item->available_pieces;
                }
                $i++;
            }
        }
        $document_data = array('arr' => $arr, 'tot-weight' => sprintf('%0.2f', $total_wgt), 'tot-cart' => $total_crt, 'tot-pieces' => sprintf('%0.2f', $total_pieces));
        return $document_data;
    }

    /**
     * Generate item code
     */
    public function generateInvoiceNo() {
        $last_item = SalesInvoiceMaster::find()->orderBy(['id' => SORT_DESC])->one();
        if (empty($last_item)) {
            $code = 'INV' . date('Y') . '-' . sprintf('%04d', 1);
        } else {
            $last = $last_item->id;
            $code = 'INV' . date('Y') . '-' . (sprintf('%04d', ++$last));
        }
        return $code;
    }

    /**
     * Get Customer Details
     */
    public function actionCustomerDetails() {
        if (Yii::$app->request->isAjax) {
            $customer_id = $_POST['id'];
            $billing_address = '';
            $delivery_address = '';
            $contact_number = '';
            $email = '';
            $customer = BusinessPartner::find()->where(['id' => $customer_id])->one();
            if (!empty($customer)) {
                $billing_address = $customer->billing_address;
                $delivery_address = $customer->shipping_address;
                $contact_number = $customer->phone_no;
                $email = $customer->email;
            }
            $arrr_variable = array('billing_address' => $billing_address, 'delivery_address' => $delivery_address, 'contact_number' => $contact_number, 'email' => $email);
            $data['result'] = $arrr_variable;
            echo json_encode($data);
        }
    }

    /**
     * This function find selected item details.
     * return item details as json array
     */
    public function actionGetItems() {
        if (Yii::$app->request->isAjax) {
            $item_id = $_POST['item_id'];
            $next_row_id = $_POST['next_row_id'];
            $next = $next_row_id + 1;
            $items = \common\models\ItemMaster::find()->where(['status' => 1])->all();
            if ($item_id == '') {
                echo '0';
                exit;
            } else {
                $item_datas = \common\models\ItemMaster::find()->where(['id' => $item_id])->one();
                $stock_details = \common\models\StockView::find()->where(['item_id' => $item_id])->all();
                $options = '';
                $avail_carton = 0;
                $avail_weight = 0;
                $avail_pieces = 0;
                if (empty($item_datas)) {
                    echo '0';
                    exit;
                } else {
                    if (!empty($stock_details)) {
                        $options = '<table id="stock-list-' . $next_row_id . '" class="stock-list stock-list-disp"><tr><th rowspan="2">Batch</th><th colspan="3">Available</th></tr><tr><th>Carton</th><th>Weight</th><th>Pieces</th></tr>';
                        foreach ($stock_details as $stock_detail) {
                            if ($stock_detail->available_carton > 0 || $stock_detail->available_weight > 0 || $stock_detail->available_pieces > 0) {
                                $options .= "<tr><td>" . $stock_detail->batch_no . "</td><td>" . $stock_detail->available_carton . "</td><td>" . $stock_detail->available_weight . "</td><td>" . $stock_detail->available_pieces . "</td></tr>";
                            }
                            $avail_carton += $stock_detail->available_carton;
                            $avail_weight += $stock_detail->available_weight;
                            $avail_pieces += $stock_detail->available_pieces;
                        }
                        $options .= '</table>';
                    }
                    $taxes = \common\models\Tax::findAll(['status' => 1]);
                    $next_row = $this->renderPartial('next_row', [
                        'next' => $next,
                        'items' => $items,
                        'taxes' => $taxes,
                    ]);
                    $tax = \common\models\Tax::findOne($item_datas->tax_id);
                    $arr_variable1 = array('next_row_html' => $next_row, 'next' => $next, 'item_rate' => $item_datas->MRP, 'item_type' => $item_datas->item_type, 'tax_id' => $item_datas->tax_id, 'tax_type' => $tax->type, 'tax_value' => $tax->value, 'stock-table' => $options, 'avail-carton' => $avail_carton, 'avail-weight' => $avail_weight, 'avail-pieces' => $avail_pieces);
                    $data1['result'] = $arr_variable1;
                    return json_encode($data1);
                }
            }
        }
    }

    /**
     * This function add another row in sales invoice form.
     * return next row html to form
     */
    public function actionAddAnotherRow() {
        if (Yii::$app->request->isAjax) {
            $next_row_id = $_POST['next_row_id'];
            $next = $next_row_id + 1;
            $items = \common\models\ItemMaster::find()->where(['status' => 1])->all();
            $next_row = $this->renderPartial('next_row', [
                'next' => $next,
                'items' => $items,
            ]);
            $new_row = array('next_row_html' => $next_row);
            $data['result'] = $new_row;
            return json_encode($data);
        }
    }

    /**
     * This function calculate the weight based on cartons and pieces.
     * return weight in kg
     */
    public function actionGetSalesQuantity() {
        if (Yii::$app->request->isAjax) {
            $item_id = $_POST['item_id'];
            $qty = $_POST['qty'];
            $type = $_POST['type'];
            $datas = $this->SaleQuantity($item_id, $type, $qty);
//            $weight = 0;
//            $items = \common\models\StockView::find()->where(['item_id' => $item_id])->all();
//            $item_datas = \common\models\ItemMaster::find()->where(['id' => $item_id])->one();
//            if ($type == 1) {
//                $q = $qty;
//                foreach ($items as $item) {
//                    if ($q == $item->available_carton) {
//                        $weight += $item->available_weight;
//                        break;
//                    } elseif ($q < $item->available_carton) {
//                        $weight += $q * $item->weight_per_carton;
//                        break;
//                    } elseif ($q > $item->available_carton) {
//                        $weight += $item->available_carton * $item->weight_per_carton;
//                        if ($weight > $item->available_weight) {
//                            $weight = $item->available_weight;
//                        }
//                        $q = $q - $item->available_carton;
//                    }
//                }
//            } elseif ($type == 3) {
//                $q = $qty;
//                foreach ($items as $item) {
//                    $weight_kg = $item->weight_per_carton / $item->piece_per_carton;
//                    if ($q == $item->available_pieces) {
//                        if ($item_datas->item_type == 1) {
//                            $weight += $q * $weight_kg;
//                        } else {
//                            $weight = $q;
//                        }
//                        break;
//                    } elseif ($q < $item->available_pieces) {
//                        if ($item_datas->item_type == 1) {
//                            $weight += $q * $weight_kg;
//                        } else {
//                            $weight = $q;
//                        }
//                        break;
//                    } elseif ($q > $item->available_pieces) {
//                        if ($item_datas->item_type == 1) {
//                            $weight += $item->available_pieces * $weight_kg;
//                        } else {
//                            $weight = $item->available_pieces;
//                        }
//                        $q = $q - $item->available_pieces;
//                    }
//                }
//            }
            $data['result'] = $datas;
            echo json_encode($data);
        }
    }

    /*
     * Generate report
     */

    public function actionReport($id) {
        $model = SalesInvoiceMaster::findOne(['id' => $id]);
        $sales_details = SalesInvoiceDetails::findAll(['sales_invoice_master_id' => $id]);
        echo $this->renderPartial('report', [
            'model' => $model,
            'sales_details' => $sales_details,
            'print' => true,
        ]);

        exit;
    }

    /*
     * Send Sales invoice
     */

    public function actionSendInvoice($id) {
        $model = SalesInvoiceMaster::findOne(['id' => $id]);
        $sales_details = SalesInvoiceDetails::findAll(['sales_invoice_master_id' => $id]);
        $message = $this->renderPartial('send-invoice', [
            'model' => $model,
            'sales_details' => $sales_details,
            'print' => true,
        ]);
        $to = 'manu@azryah.com';
        $subject = 'Sales Invoice';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n" .
                "From: 'no-reply@sis.innoq.com";
        if (mail($to, $subject, $message, $headers)) {
            Yii::$app->getSession()->setFlash('success', 'Mail has been send');
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Creates a new Payment model.
     * If creation is successful, the browser will be redirected to the 'same' page.
     * @return mixed
     */
    public function actionPayment($id) {
        $sale = SalesInvoiceMaster::findOne($id);
        $payment_details = Payments::find()->where(['invoice_id' => $id, 'invoice_type' => 1])->all();
        $model = new Payments();

        if ($model->load(Yii::$app->request->post()) && Yii::$app->SetValues->Attributes($model) && $model->validate()) {
            $model->invoice_id = $sale->id;
            $model->invoice_type = 1;
            if ($model->amount > 0) {
                if ($model->save()) {
                    $sale->due_amount = $sale->due_amount - $model->amount;
                    $sale->amount_payed = $sale->amount_payed + $model->amount;
                    $sale->update();
                    Yii::$app->getSession()->setFlash('success', 'Payment Successfully');
                }
            }
            return $this->redirect(['index']);
        } return $this->renderAjax('payment', [
                    'model' => $model,
                    'sale' => $sale,
                    'payment_details' => $payment_details,
        ]);
    }

}

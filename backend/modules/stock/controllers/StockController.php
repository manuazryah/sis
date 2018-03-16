<?php

namespace backend\modules\stock\controllers;

use Yii;
use common\models\Stock;
use common\models\StockSearch;
use common\models\StockView;
use common\models\ItemMaster;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\StockRegister;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends Controller {

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
     * Lists all Stock models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['type' => 1]);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Stock model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Stock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Stock();
        $stock_view = new StockView();
        $stock_register = new StockRegister();
        $model->setScenario('create');


        if ($model->load(Yii::$app->request->post())) {

            $item_deatils = ItemMaster::findOne($model->item_id);
            $model->type = 1;
            $model->item_name = $item_deatils->item_name;
            $model->slaughter_date_from = date('Y-m-d', strtotime($model->slaughter_date_from));
            $model->slaughter_date_to = date('Y-m-d', strtotime($model->slaughter_date_to));
            $model->production_date = date('Y-m-d', strtotime($model->production_date));
            $model->due_date = date('Y-m-d', strtotime($model->due_date));
            Yii::$app->SetValues->Attributes($model);

            $transaction = Yii::$app->db->beginTransaction();
            if ($model->save() && $this->StockView($stock_view, $model) && $this->StockRegister($stock_register, $model)) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }
            Yii::$app->session->setFlash('success', "Stock added successfully");
            return $this->redirect(['create']);
        }
        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    public function StockView($stock_view, $model) {

        $stock_view->item_id = $model->item_id;
        $stock_view->item_code = $model->item_code;
        $stock_view->item_name = $model->item_name;
        $stock_view->batch_no = $model->batch_no;
        $stock_view->location_code = $model->location;
        $stock_view->available_carton = $model->cartons;
        $stock_view->available_weight = $model->total_weight;
        $stock_view->available_pieces = $model->pieces;
        $stock_view->opening_carton = $model->cartons;
        $stock_view->opening_weight = $model->total_weight;
        $stock_view->opening_piece = $model->pieces;
        $item_details = ItemMaster::findOne($model->item_id);
        if ($item_details->item_type == 1) {
            if (isset($model->total_weight) && $model->total_weight != '') {
                $stock_view->weight_per_carton = $model->total_weight / $model->cartons;
            } if (isset($model->pieces)) {
                $stock_view->piece_per_carton = $model->pieces / $model->cartons;
            }
        }
        $stock_view->average_cost = $model->cost;
        $stock_view->due_date = $model->due_date;
        if ($stock_view->save()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function StockRegister($stock_register, $model) {
        $stock_register->transaction = 1;
        $stock_register->document_line_id = $model->id;
        $stock_register->document_no = '';
        $stock_register->document_date = date('Y-m-d');
        $stock_register->item_id = $model->item_id;
        $stock_register->item_code = $model->item_code;
        $stock_register->item_name = $model->item_name;
        $stock_register->batch_no = $model->batch_no;
        $stock_register->location_code = $model->location;
        $stock_register->item_cost = '';
        $stock_register->cartoon_in = $model->cartons;
        $stock_register->weight_in = $model->total_weight;
        $stock_register->piece_in = $model->pieces;
        $stock_register->status = 1;
        $stock_register->CB = Yii::$app->user->identity->id;
        $stock_register->UB = Yii::$app->user->identity->id;
        $stock_register->DOC = date('Y-m-d');
        if ($stock_register->save()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Updates an existing Stock model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id = null) {
        if (!empty($id))
            $model = $this->findModel($id);
        else
            $model = new Stock();
        $model->setScenario('update');

        if ($model->load(Yii::$app->request->post())) {
            $stock_adjust = new Stock;
            $stock_adjust->attributes = $model->attributes;
            $stock_adjust->adjust_cartons = Yii::$app->request->post()['Stock']['adjust_cartons'];
            $stock_adjust->adjust_weight = Yii::$app->request->post()['Stock']['adjust_weight'];
            $stock_adjust->adjust_pieces = Yii::$app->request->post()['Stock']['adjust_pieces'];
            $item_deatils = ItemMaster::findOne($stock_adjust->item_id);

            $stock_adjust->type = 2;
            $stock_adjust->item_name = $item_deatils->item_name;

            $stock_adjust->slaughter_date_from = date('Y-m-d', strtotime($stock_adjust->slaughter_date_from));
            $stock_adjust->slaughter_date_to = date('Y-m-d', strtotime($stock_adjust->slaughter_date_to));
            $stock_adjust->production_date = date('Y-m-d', strtotime($stock_adjust->production_date));
            $stock_adjust->due_date = date('Y-m-d', strtotime($stock_adjust->due_date));
            //  $this->StockRegisterAdjustment($stock_adjust);
            $stockview = StockView::find()->where(['item_id' => $stock_adjust->item_id, 'batch_no' => $stock_adjust->batch_no])->one();
            if (!empty($stockview)) {
                $stockview->available_carton = $stock_adjust->adjust_cartons;
                $stockview->available_weight = $stock_adjust->adjust_weight;
                $stockview->available_pieces = $stock_adjust->adjust_pieces;
                $stockview->location_code = $stock_adjust->location;
                $stockview->origin = $stock_adjust->origin;
                if ($item_deatils->item_type == 1) {
                    if (isset($stockview->available_weight) && $stockview->available_weight != '') {
                        $stockview->weight_per_carton = $stockview->available_weight / $stockview->available_carton;
                    } if (isset($stockview->available_pieces)) {
                        $stockview->piece_per_carton = $stockview->available_pieces / $stockview->available_carton;
                    }
                }
                //  $stockview->save();
            } else {
                $stockview = new StockView;
                $this->StockView($stockview, $stock_adjust);
            }

            //$stock_adjust->save();

            $transaction = Yii::$app->db->beginTransaction();
            if ($stock_adjust->save() && $this->StockRegisterAdjustment($stock_adjust) && $stockview->save()) {
                $transaction->commit();
            } else {
                $transaction->rollBack();
            }


            Yii::$app->session->setFlash('success', "Stock updated successfully");
            return $this->redirect(['create']);
        }
        return $this->render('update', [
                    'model' => $model,
                    'id' => $id,
        ]);
    }

    public function StockRegisterAdjustment($model) {
        $stock_register = new StockRegister();
        $stock_register->transaction = 2;
        $stock_register->document_line_id = $model->id;
        $stock_register->document_no = '';
        $stock_register->document_date = date('Y-m-d');
        $stock_register->item_id = $model->item_id;
        $stock_register->item_code = $model->item_code;
        $stock_register->item_name = $model->item_name;
        $stock_register->batch_no = $model->batch_no;
        $stock_register->location_code = $model->location;
        $stock_register->item_cost = '';
        $stockview = StockView::find()->where(['item_id' => $model->item_id, 'batch_no' => $model->batch_no])->one();
        if ($model->adjust_cartons > $stockview->available_carton) {
            $stock_register->cartoon_in = $model->adjust_cartons - $stockview->available_carton;
        } else {
            $stock_register->cartoon_out = $stockview->available_carton - $model->adjust_cartons;
        }

        if ($model->adjust_weight > $stockview->available_weight) {
            $stock_register->weight_in = $model->adjust_weight - $stockview->available_weight;
        } else {
            $stock_register->weight_out = $stockview->available_weight - $model->adjust_weight;
        }

        if ($model->adjust_pieces > $stockview->available_pieces) {
            $stock_register->piece_in = $model->adjust_pieces - $stockview->available_pieces;
        } else {
            $stock_register->piece_out = $stockview->available_pieces - $model->adjust_pieces;
        }

        $stock_register->status = 1;
        $stock_register->CB = Yii::$app->user->identity->id;
        $stock_register->UB = Yii::$app->user->identity->id;
        $stock_register->DOC = date('Y-m-d');
        if ($stock_register->save()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Deletes an existing Stock model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Stock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Stock::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionItemDetails() {
        if (Yii::$app->request->isAjax) {
            $avilable_label = '';
            $category = '';
            $item = $_POST['item'];
            $item_details = \common\models\ItemMaster::findOne($item);
            if (isset($item_details->item_type) && $item_details->item_type != '')
                $category = $item_details->item_type;
            if ($item_details->item_type == 1) {
                $available_stock = StockView::find()->where(['item_id' => $item])->sum('available_weight');
                $avilable_label = 'Kg';
            } else {
                $available_stock = StockView::find()->where(['item_id' => $item])->sum('available_pieces');
                $avilable_label = 'Pieces';
            }
            if (empty($available_stock))
                $available_stock = 0;
            //            $unit = \common\models\BaseUnit::findOne($item_details->base_unit_id);
            $data = ['item_code' => $item_details->item_code, 'price' => $item_details->purchase_price, 'available_stock' => $available_stock, 'unit_label' => $avilable_label, 'category' => $category];
            echo json_encode($data);
        }
    }

    public function actionItemCategory() {
        if (Yii::$app->request->isAjax) {
            $item = $_POST['item'];
            $item_details = \common\models\ItemMaster::findOne($item);
            if (isset($item_details)) {
                echo $item_details->item_type;
            }
        }
    }

    public function actionBatches() {
        if (Yii::$app->request->isAjax) {
            $item = $_POST['selected'];
            $stockview = StockView::find()->where(['item_id' => $item])->groupBy(['batch_no'])->all();
            $list = $this->renderPartial('batches', ['batches' => $stockview]);
            return $list;
        }
    }

    public function actionStockViewDetails() {
        if (Yii::$app->request->isAjax) {
            $category = '';
            $avilable_label = '';
            $slaughter_date_from = '';
            $slaughter_date_to = '';
            $production_date = '';
            $due_date = '';
            $stock_view_id = $_POST['stock_view_id'];
            $stock_view_details = StockView::findOne($stock_view_id);
            $stock = Stock::find()->where(['batch_no' => $stock_view_details->batch_no, 'item_id' => $stock_view_details->item_id])->one();
            $item_details = ItemMaster::findOne($stock_view_details->item_id);
            $unit = \common\models\BaseUnit::findOne($item_details->base_unit_id);
            if (isset($item_details->item_type) && $item_details->item_type != '')
                $category = $item_details->item_type;
            if ($item_details->item_type == 1) {
                $avilable_label = 'Kg';
            } else {
                $avilable_label = 'Pieces';
            }
            if (isset($stock->slaughter_date_from) && $stock->slaughter_date_from != '')
                $slaughter_date_from = date('d-m-Y', strtotime($stock->slaughter_date_from));
            if (isset($stock->slaughter_date_to) && $stock->slaughter_date_to != '')
                $slaughter_date_to = date('d-m-Y', strtotime($stock->slaughter_date_to));
            if (isset($stock->production_date) && $stock->production_date != '')
                $production_date = date('d-m-Y', strtotime($stock->production_date));
            if (isset($stock_view_details->due_date) && $stock_view_details->due_date != '')
                $due_date = date('d-m-Y', strtotime($stock_view_details->due_date));
            $data = ['item_code' => $item_details->item_code, 'price' => $item_details->purchase_price, 'UOM' => $unit->name,
                'unit_label' => $avilable_label, 'category' => $category, 'batch' => $stock_view_details->batch_no,
                'slaughter_date_from' => $slaughter_date_from, 'slaughter_date_to' => $slaughter_date_to, 'production_date' => $production_date,
                'due_date' => $due_date, 'plant' => $stock->plant, 'location' => $stock->location, 'warehouse' => $stock->warehouse, 'supplier' => $stock->supplier, 'cost' => $stock_view_details->average_cost,
                'origin' => $stock->origin, 'cartoons' => $stock_view_details->available_carton, 'weight' => $stock_view_details->available_weight, 'piecse' => $stock_view_details->available_pieces, 'item-type' => $item_details->item_type
            ];
            echo json_encode($data);
        }
    }

}

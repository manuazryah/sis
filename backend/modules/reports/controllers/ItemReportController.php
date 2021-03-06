<?php

namespace backend\modules\reports\controllers;

use yii;
use kartik\mpdf\Pdf;
use common\models\SalesInvoiceDetailsSearch;
use yii\data\ArrayDataProvider;

class ItemReportController extends \yii\web\Controller {

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
     * Lists Item wise sales report.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SalesInvoiceDetailsSearch();
        $query = new yii\db\Query();
        $query->select(['item_code,item_name,SUM(CASE WHEN qty != "" THEN qty ELSE 0 END) as qty,SUM(CASE WHEN carton != "" THEN carton ELSE 0 END) as carton,SUM(CASE WHEN tax_amount != "" THEN tax_amount ELSE 0 END) as tax_amount,SUM(CASE WHEN line_total != "" THEN line_total ELSE 0 END) as line_total,SUM(CASE WHEN amount != "" THEN amount ELSE 0 END) as amount,SUM(CASE WHEN discount_amount != "" THEN discount_amount ELSE 0 END) as discount_amount,SUM(CASE WHEN pieces != "" THEN pieces ELSE 0 END) as pieces'])
                ->from('sales_invoice_details')
                ->groupBy('item_id');
        if (Yii::$app->request->post()) {
            if (isset($_POST['SalesInvoiceDetailsSearch']['item_id']) && $_POST['SalesInvoiceDetailsSearch']['item_id'] != '') {
                $item_code = $_POST['SalesInvoiceDetailsSearch']['item_id'];
                $query->andWhere(['item_id' => $item_code]);
            } else {
                $item_code = '';
            }
            if (isset($_POST['SalesInvoiceDetailsSearch']['createdFrom']) && $_POST['SalesInvoiceDetailsSearch']['createdFrom'] != '') {
                $from = $_POST['SalesInvoiceDetailsSearch']['createdFrom'];
                $query->andWhere(['>=', 'sales_invoice_date', $from . '00:00:00']);
            } else {
                $from = '';
            }
            if (isset($_POST['SalesInvoiceDetailsSearch']['createdTo']) && $_POST['SalesInvoiceDetailsSearch']['createdTo'] != '') {
                $to = $_POST['SalesInvoiceDetailsSearch']['createdTo'];
                $query->andWhere(['<=', 'sales_invoice_date', $to . '60:60:60']);
            } else {
                $to = '';
            }
        } else {
            $from = '';
            $to = '';
            $item_code = '';
        }
        $command = $query->createCommand();
        $result = $command->queryAll();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'from' => $from,
                    'to' => $to,
                    'item_code' => $item_code,
        ]);
    }

    /**
     * Generate Item wise sales report pdf.
     * @return mixed
     */
    public function actionReports() {
        $query = new yii\db\Query();
        $query->select(['item_code,item_name,SUM(CASE WHEN qty != "" THEN qty ELSE 0 END) as qty,SUM(CASE WHEN carton != "" THEN carton ELSE 0 END) as carton,SUM(CASE WHEN tax_amount != "" THEN tax_amount ELSE 0 END) as tax_amount,SUM(CASE WHEN line_total != "" THEN line_total ELSE 0 END) as line_total,SUM(CASE WHEN amount != "" THEN amount ELSE 0 END) as amount,SUM(CASE WHEN discount_amount != "" THEN discount_amount ELSE 0 END) as discount_amount,SUM(CASE WHEN pieces != "" THEN pieces ELSE 0 END) as pieces'])
                ->from('sales_invoice_details')
                ->groupBy('item_id');
        if (isset($_POST['item_code']) && $_POST['item_code'] != '') {
            $id = $_POST['item_code'];
            $query->andWhere(['item_id' => $id]);
        } else {
            $id = '';
        }
        if (isset($_POST['from_date']) && $_POST['from_date'] != '') {
            $from = $_POST['from_date'];
            $query->andWhere(['>=', 'sales_invoice_date', $from . '00:00:00']);
        } else {
            $from = '';
        }
        if (isset($_POST['to_date']) && $_POST['to_date'] != '') {
            $to = $_POST['to_date'];
            $query->andWhere(['<=', 'sales_invoice_date', $to . '60:60:60']);
        } else {
            $to = '';
        }
        $command = $query->createCommand();
        $model_report = $command->queryAll();
        $content = $this->renderPartial('item_report', [
            'model_report' => $model_report,
        ]);
        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Item Sales Report'],
            'methods' => [
                'SetHeader' => ['Sale Invoice System'],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/pdf');
        return $pdf->render();
    }

}

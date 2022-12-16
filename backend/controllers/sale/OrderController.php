<?php

namespace backend\controllers\sale;

use Yii;
use yii\helpers\Url;
use app\models\Order;
use app\models\OrderHistory;
use app\models\OrderProduct;
use app\models\OrderSearch;
use app\models\OrderStatus;
use app\models\OrderTotal;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
            'products' => OrderProduct::productsList($id),
            'totals' => OrderTotal::totalsList($id),
            'order_statuses' => OrderStatus::getAllStatuses(),
            'order_history' => OrderHistory::historyList($id),
        ]);
    }

    public function actionAddHistory($id)
    {

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
          $model = $this->findModel($id);

          $order_data = $model->attributes;
          $order_data['order_id'] = $id;
          $order_data['products'] = OrderProduct::productsList($id);

          $order_status_id = (int)Yii::$app->request->post('order_status_id', 0);
          $comment = Yii::$app->request->post('comment', '');
          $notify = (int)Yii::$app->request->post('notify', 0);

          OrderHistory::addHistory($order_data, $order_status_id, $comment, $notify);

          return [
            'success' => YII::t('order', 'Order history successfully updated'),
          ];
        } catch (\Exception $e) {
          return [
            'error' => $e->getMessage()
          ];
        }
    }

    public function actionHistory($id)
    {
        return $this->renderAjax('info/_history', [
            'model' => $this->findModel($id),
            'order_statuses' => OrderStatus::getAllStatuses(),
            'order_history' => OrderHistory::historyList($id),
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->get('inplace_edit', '0') == '1') {
                $field = '';
                foreach (Yii::$app->request->post() as $arr) {
                    if (is_array($arr)) {
                        foreach ($arr as $value) {
                            $field = $value;
                        }
                    }
                }
                return \yii\helpers\Json::encode(['success' => '1', 'value' => $field]);
            } else {
                return $this->goBack();
            }
        }

        return $this->render('update', [
          'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

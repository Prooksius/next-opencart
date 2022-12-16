<?php

namespace backend\controllers\localisation;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\OrderStatus;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * OrderStatusController implements the CRUD actions for OrderStatus model.
 */
class OrderStatusController extends Controller
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
     * Lists all Bot models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $query = OrderStatus::find()
            ->alias('os')
            ->select([
              'os.*', 
              'osd.*',
            ])
            ->leftJoin('oc_order_status_description osd', '(osd.order_status_id = os.id AND osd.language_id = "' . \Yii::$app->language . '")');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'attributes' => [
                'id',
                'name',
              ],
              'defaultOrder'=>[
                'name'=>SORT_ASC
              ],
            ],
        ]);

        return $this->render('index', [
          'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Bot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new OrderStatus();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }


        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Bot model.
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
     * Deletes an existing Bot model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->goBack();
    }

    /**
     * Finds the StockStatus model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockStatus the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrderStatus::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

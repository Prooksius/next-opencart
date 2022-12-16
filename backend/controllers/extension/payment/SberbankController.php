<?php

namespace backend\controllers\extension\payment;

use app\models\ModuleGroup;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use common\components\Rbs;

/**
 * SberbankController implements the CRUD actions for Payment model.
 */
class SberbankController extends Controller
{

    public $groupClass = 'Sberbank';
  
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
     * Список валют в ISO 4217
     * @return array
     */
    private function _getCurrencyList()
    {
        $rbs = new Rbs();
        return array_flip($rbs->currency_code2num);
    }

    /**
     * Lists all ModuleGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $query = ModuleGroup::find()
          ->where(['category' => 'payment', 'code' => $this->groupClass]);

        $dataProvider = new ActiveDataProvider([
          'query' => $query,
          'sort' => [
            'attributes' => [
              'name',
              'status',
            ],
            'defaultOrder'=>[
              'name' => SORT_ASC
            ],
          ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'rbsCurrenciesList' => $this->_getCurrencyList(),
        ]);
    }

    /**
     * Creates a new ModuleGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        return $this->redirect(['index']);
    }

    /**
     * Updates an existing ModuleGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate()
    {
        $model = $this->findGroup();

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
            'rbsCurrenciesList' => $this->_getCurrencyList(),
        ]);
    }

    /**
     * Deletes an existing ModuleGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        // $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ModuleGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModuleGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findGroup()
    {
        if (($model = ModuleGroup::findOne(['category' => 'payment', 'code' => $this->groupClass])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

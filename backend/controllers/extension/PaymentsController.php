<?php

namespace backend\controllers\extension;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use app\models\ModuleGroup;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * PaymentsController implements the CRUD actions for ModuleGroup model.
 */
class PaymentsController extends Controller
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
     * Lists all ModuleGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $query = ModuleGroup::find()
          ->where(['category' => 'payment']);

        $dataProvider = new ActiveDataProvider([
          'query' => $query,
           'sort' => [
              'attributes' => [
                'name',
                'sort_order',
                'status',
              ],
              'defaultOrder'=>[
                'sort_order' => SORT_ASC,
                'name' => SORT_ASC,
              ],
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ModuleGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ModuleGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ModuleGroup model.
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
     * Deletes an existing ModuleGroup model.
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
     * Finds the ModuleGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ModuleGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ModuleGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

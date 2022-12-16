<?php

namespace backend\controllers\localisation;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\TranslateGroup;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TranslateGroupController implements the CRUD actions for TranslateGroup model.
 */
class TranslateGroupController extends Controller
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
     * Lists all TranslateGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $query = TranslateGroup::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'attributes' => [
                'code',
                'name',
              ],
              'defaultOrder'=>[
                'code'=>SORT_ASC
              ],
            ],
        ]);

        return $this->render('index', [
          'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TranslateGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TranslateGroup();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TranslateGroup model.
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
     * Deletes an existing TranslateGroup model.
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
     * Finds the TranslateGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TranslateGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TranslateGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

namespace backend\controllers\localisation;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\WeightClass;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WeightClassController implements the CRUD actions for WeightClass model.
 */
class WeightClassController extends Controller
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
     * Lists all WeightClass models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $query = WeightClass::find()
            ->alias('wc')
            ->select([
              'wc.*', 
              'wcd.*',
            ])
            ->leftJoin('oc_weight_class_description wcd', '(wcd.weight_class_id = wc.id AND wcd.language_id = "' . \Yii::$app->language . '")');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'attributes' => [
                'id',
                'title',
                'unit',
                'value',
              ],
              'defaultOrder'=>[
                'title'=>SORT_ASC
              ],
            ],
        ]);

        return $this->render('index', [
          'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new WeightClass model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WeightClass();

        $languages = Language::find()->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }


        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
        ]);
    }

    /**
     * Updates an existing WeightClass model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $languages = Language::find()->all();

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
            'languages' => $languages,
        ]);
    }

    /**
     * Deletes an existing WeightClass model.
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
     * Finds the WeightClass model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WeightClass the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WeightClass::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

<?php

namespace backend\controllers\catalog;

use app\models\Settings;
use common\models\Language;
use Yii;
use yii\helpers\Url;
use app\models\AttributeGroup;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AttributeGroupController implements the CRUD actions for AttributeGroup model.
 */
class AttributeGroupController extends Controller
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

        $query = AttributeGroup::find()
            ->alias('ag')
            ->select([
              'ag.*', 
              'agd.*',
            ])
            ->leftJoin('oc_attribute_group_description agd', '(agd.attribute_group_id = ag.id AND agd.language_id = "' . \Yii::$app->language . '")');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'attributes' => [
                'id',
                'name',
                'sort_order',
              ],
              'defaultOrder'=>[
                'sort_order'=>SORT_ASC
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
        $model = new AttributeGroup();
        $max_sort = AttributeGroup::find()
            ->select(['MAX(sort_order)'])
            ->scalar();

        $model->sort_order = $max_sort + 10;

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
     * Updates an existing Bot model.
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
     * Deletes an existing Bot model.
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
     * Finds the Bot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AttributeGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

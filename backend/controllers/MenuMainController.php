<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use app\models\MenuMain;
use common\models\Language;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MenuAboutController implements the CRUD actions for MenuAbout model.
 */
class MenuMainController extends Controller
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
     * Lists all MenuAbout models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $dataProvider = new ActiveDataProvider([
            'query' => MenuMain::find()
              ->alias('mm')
              ->select('mm.*, mmd.name AS name')
              ->leftJoin('menu_main_desc mmd ON (mmd.menu_main_id = mm.id AND mmd.language_id = "' . \Yii::$app->language  . '")'),
            'sort' => [
              'attributes' => [
                'sort_order'
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
     * Creates a new MenuAbout model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MenuMain();
        $languages = Language::find()->all();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    return $this->goBack();
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
        ]);
    }

    /**
     * Updates an existing MenuAbout model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $languages = Language::find()->all();

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
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
            }
        }

        return $this->render('update', [
            'model' => $model,
            'languages' => $languages,
        ]);
    }

    /**
     * Deletes an existing MenuAbout model.
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
     * Finds the MenuAbout model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MenuAbout the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MenuMain::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
  }
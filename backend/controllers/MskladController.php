<?php

namespace backend\controllers;

use app\models\Settings;
use backend\components\MoySklad;
use Yii;
use yii\helpers\Url;
use app\models\Bot;
use backend\components\MoySkladDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BotController implements the CRUD actions for Bot model.
 */
class MskladController extends Controller
{
    public $groups;
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
    public function actionIndex($group_filter = '')
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $msklad_obj = new MoySklad();
        $this->groups = $msklad_obj->getGroups();

        $dataProvider = new MoySkladDataProvider([
            'moy_sklad' => $msklad_obj,
            'key' => 'id',
            'sort' => [
                'attributes' => [
                    'id',
                    'name',
                ],
                'defaultOrder'=>[
                    'id' => SORT_ASC
                ],
            ],
        ]);

        $path_arr = [];
        if ($group_filter) {
            $filter_name = 'productFolder';
            $filter_value = $group_filter;
            $filter_sign = '=';
            $path_arr = explode('/', $filter_value);
            $str1 = '["' . implode('"]["', $path_arr) . '"]';
            eval('$filter_value = $this->groups' . $str1 . '["href"];');

            $msklad_obj->set_filter($filter_name, $filter_sign, $filter_value);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'groups' => $this->groups,
            'path_arr' => $path_arr,
        ]);
    }

    /**
     * Displays a single Bot model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Bot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bot();

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
            return $this->goBack();
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
        if (($model = Bot::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

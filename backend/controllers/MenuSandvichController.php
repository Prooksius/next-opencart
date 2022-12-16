<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use app\models\Menu;
use app\models\Language;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MenuController implements the CRUD actions for Menu model.
 */
class MenuSandvichController extends Controller
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

    public function actions() {
        return [
            'nodeMove' => [
                'class' => 'klisl\nestable\NodeMoveAction',
                'modelName' => Menu::className(),
            ],
        ];
    }

    /**
     * Lists all Menu models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        //объект ActiveQuery содержащий данные для дерева. depth = 0 - корень.
        $query = Menu::find()->where(['depth' => '0']);
//        var_dump($query->all());
        return $this->render('index', [
            'query' => $query,
        ]);
    }

    /**
     * Displays a single Menu model.
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
     * Creates a new Menu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        /** @var  $model Menu|NestedSetsBehavior */
        $model = new Menu();

        $languages = Language::find()->all();

        //Поиск корневого элемента
        $root = $model->find()->where(['depth' => '0'])->one();

        if ($model->load(Yii::$app->request->post())) {
            //Если нет корневого элемента (пустая таблица)
            if (!$root) {
                /** @var  $rootModel Menu|NestedSetsBehavior */
                $rootModel = new Menu(['name' => 'root', 'description' => 'empty']);
                $rootModel->makeRoot(); //делаем корневой
                $model->appendTo($rootModel);
            } else {
                $model->appendTo($root); //вставляем в конец корневого элемента
            }

            if ($model->save()){
                return $this->goBack();
            }
        }

        return $this->render('create', [
            'model' => $model,
            'root' => $root,
            'languages' => $languages
        ]);

    }

    /**
     * Updates an existing Menu model.
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
            'languages' => $languages
        ]);
    }

    /**
     * Deletes an existing Menu model.
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
     * Finds the Menu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Menu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

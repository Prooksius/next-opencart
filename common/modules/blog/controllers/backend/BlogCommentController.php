<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

namespace common\modules\blog\controllers\backend;

use common\modules\blog\models\BlogComment;
use common\modules\blog\models\BlogCommentSearch;
use common\modules\blog\Module;
use common\modules\blog\traits\IActiveStatus;
use Yii;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * CommentController implements the CRUD actions for Comment model.
 */
class BlogCommentController extends BaseAdminController
{
    /**
     * Lists all Comment models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $searchModel = new BlogCommentSearch();
        $searchModel->scenario = BlogCommentSearch::SCENARIO_ADMIN;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Comment model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Comment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Comment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BlogComment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Creates a new Comment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BlogComment();
        $model->scenario = BlogComment::SCENARIO_ADMIN;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Comment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = BlogComment::SCENARIO_ADMIN;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Comment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithChildren();

        return $this->goBack();
    }

    /**
     * Mass action with comment
     *
     * @return \yii\web\Response
     */
    public function actionBulk()
    {
        $action = Yii::$app->request->post('action');
        $selection = (array)Yii::$app->request->post('selection');//typecasting
        switch ($action) {
            case 'd':
                if ($this->deleteAll($selection)) {
                    $message = Module::t('blog', 'Successfully delete');
                }
                break;
            case 'c':
                if ($this->confirmAll($selection)) {
                    $message = Module::t('blog', 'Successfully confirm');
                }
                break;
            default:
                $message = Module::t('blog', 'Action not found');
        }

        Yii::$app->session->setFlash('warning', $message);

        return $this->redirect('index');
    }

    /**
     * @param $selection
     * @return int
     */
    private function deleteAll($selection)
    {
        return BlogComment::deleteAll(['id' => $selection]);
    }

    /**
     * @param $selection
     * @return int
     */
    private function confirmAll($selection)
    {
        return BlogComment::updateAll(['status' => IActiveStatus::STATUS_ACTIVE], ['id' => $selection]);
    }
}

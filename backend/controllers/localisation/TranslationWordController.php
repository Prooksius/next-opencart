<?php

namespace backend\controllers\localisation;

use app\models\TranslateResult;
use app\models\TranslateWord;
use app\models\TranslateWordSearch;
use Yii;
use yii\helpers\Url;
use common\models\Language;
use yii\web\NotFoundHttpException;
use yii\web\Controller;

/**
 * SpecialityController implements the CRUD actions for TranslationWord model.
 */
class TranslationWordController extends Controller
{
    /**
     * Lists all TranslateWord models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Url::current([], true);

        $searchModel = new TranslateWordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new TranslateWord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TranslateWord();
        $languages = Language::find()->all();
        $translate_group_id = (int)Yii::$app->request->get('translate_group_id', '');
        if ($translate_group_id) {
            $model->translate_group_id = $translate_group_id;
        }

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
     * Creates a new TranslateWord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionClone()
    {
        if (Yii::$app->request->get('selection', '')) {

            $ids = explode(',', Yii::$app->request->get('selection'));

            foreach ($ids as $id) {
                $new_word = new TranslateWord();
                $new_word->attributes = $this->findModel($id)->attributes;
                $new_word->id = null;
                $new_word->phrase = $new_word->phrase . '_clone';
                $new_word->save();
                $new_word->refresh();

                $new_id = $new_word->id;

                $new_word->phrase .= $new_word->id;
                $new_word->save();

                $translations = TranslateResult::find()
                    ->where(['translate_word_id' => $id])
                    ->all();

                foreach ($translations as $translation) {
                    $new_tranlation = new TranslateResult();
                    $new_tranlation->attributes = $translation->attributes;
                    $new_tranlation->translate_word_id = $new_id;
                    $new_tranlation->save();
                }
            }
        }
        return $this->goBack();
    }

    /**
     * Updates an existing TranslateWord model.
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
     * Deletes an existing TranslateWord model.
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

    public function actionBatchdelete()
    {
        if (Yii::$app->request->get('selection', '')) {
            TranslateWord::getDb()
                ->createCommand()
                ->delete('translate_word', 'id IN (' . Yii::$app->request->get('selection', '') . ')')
                ->execute();

            TranslateWord::getDb()
                ->createCommand()
                ->delete('translate_result', 'translate_word_id IN (' . Yii::$app->request->get('selection', '') . ')')
                ->execute();
        }
        return $this->goBack();
    }

    /**
     * Finds the Speciality model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TranslateWord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TranslateWord::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
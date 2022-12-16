<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 08.09.2021
 * Time: 15:08
 */

namespace frontend\controllers\api\v1;

use frontend\models\Translation;
use Yii;
use frontend\components\ApiController;
use frontend\models\Workspace;
use yii\data\ActiveDataProvider;

class WorkspaceController extends ApiController
{
    public function actionIndex()
    {
        $query = Workspace::find()
            ->alias('ws')
            ->leftJoin('workspace_person wsp', 'wsp.workspace_id = ws.id')
            ->where(['or', ['ws.customer_id' => Yii::$app->user->id], ['wsp.customer_id' => Yii::$app->user->id]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort_order' => SORT_ASC
                ],
            ],
        ]);

        $dataProvider->prepare();
        return [
            'success' => true,
            'lang' => Yii::$app->user->identity->language_id,
            'pagination' => [
                'page' => $dataProvider->pagination->page,
                'pageSize' => $dataProvider->pagination->pageSize,
                'totalPages' => $dataProvider->pagination->pageCount,
                'totalRows' => $dataProvider->pagination->totalCount,
            ],
            'data' => $dataProvider->models,
        ];
    }

    public function actionCreate()
    {

        $max_sort = Workspace::find()
            ->select(['MAX(sort_order)'])
            ->scalar();

        try {
            $new_workspace = new Workspace();
            $new_workspace->customer_id = Yii::$app->user->id;
            $new_workspace->name = Yii::$app->request->post('name', 'Новая рабочая среда');
            $new_workspace->type = Yii::$app->request->post('type', 1);
            $new_workspace->sort_order = $max_sort + 10;

            if ($new_workspace->save()) {
                $new_workspace->refresh();

                return [
                    'success' => true,
                    'lang' => Yii::$app->user->identity->language_id,
                    'item' => $new_workspace,
                ];
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'lang' => Yii::$app->user->identity->language_id,
                'errors' => [
                    'general' => $e->getMessage()
                ],
            ];
        }

        return [
            'success' => false,
            'lang' => Yii::$app->user->identity->language_id,
            'errors' => $new_workspace->errors,
        ];
    }

    public function actionUpdate($id)
    {

        try {
            $workspace = Workspace::findOne(strip_tags($id));
            if ($workspace instanceof Workspace) {
                $workspace->name = Yii::$app->request->post('name', $workspace->name);
                $workspace->type = Yii::$app->request->post('type', $workspace->type);
                $workspace->sort_order = Yii::$app->request->post('sort_order', $workspace->sort_order);
                if ($workspace->save()) {
                    return [
                        'success' => true,
                        'lang' => Yii::$app->user->identity->language_id,
                        'item' => $workspace,
                    ];
                }
            } else {
                throw new \Exception(Translation::getTranslation('ObjectNotFound'));
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'lang' => Yii::$app->user->identity->language_id,
                'errors' => [
                    'general' => $e->getMessage()
                ],
            ];
        }

        return [
            'success' => false,
            'lang' => Yii::$app->user->identity->language_id,
            'errors' => $workspace->errors,
        ];
    }

    public function actionDelete($id)
    {
        try {
            $workspace = Workspace::findOne(strip_tags($id));
            if ($workspace instanceof Workspace) {

                if ($workspace->customer_id == Yii::$app->user->id) {

                    // Здесь нужно проверять, нет ли уже созданных юзеров, проектво, целей и задач для этого workspace

                    if ($workspace->delete()) {
                        return [
                            'success' => true,
                            'lang' => Yii::$app->user->identity->language_id,
                            'item' => null,
                        ];
                    }
                } else {
                    throw new \Exception(Translation::getTranslation('YouAreNotObjectOwner'));
                }
            } else {
                throw new \Exception(Translation::getTranslation('ObjectNotFound'));
            }

        } catch (\Exception $e) {
            return [
                'success' => false,
                'lang' => Yii::$app->user->identity->language_id,
                'errors' => [
                    'general' => $e->getMessage()
                ],
            ];
        }

        return [
            'success' => false,
            'lang' => Yii::$app->user->identity->language_id,
            'errors' => $workspace->errors,
        ];

    }
}
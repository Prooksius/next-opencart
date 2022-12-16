<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use frontend\models\Pages;
use Yii;
use frontend\components\ApiController;
use yii\data\ActiveDataProvider;

class PageController extends ApiController
{

    public function actionIndex($page = 1)
    {
        try {
            $pagesize = (int)Yii::$app->request->post('pagesize');

            $query = Pages::find()
                ->alias('p')
                ->select(['p.*', 'pd.title AS name', 'se.link_sef AS href'])
                ->leftJoin('pages_desc pd', 'pd.page_id = p.id AND pd.language_id = "' . Yii::$app->language . '"')
                ->leftJoin('sef se', 'se.link = CONCAT("site/pages?id=", p.id)');

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => $pagesize,
                ],
                'sort' => [
                    'defaultOrder'=>[
                        'id'=>SORT_ASC
                    ],
                ],
            ]);

            $models = $dataProvider->getModels();
            $pages_cont = [];
            foreach ($models as $item) {
                $pages_cont[] = [
                    'id' => $item->id,
                    'title' => $item['name'],
                    'href' => $item['href'] ? $item['href'] : '',
                ];
            }
            $count = $dataProvider->getTotalCount();

            return [
                'success' => 1,
                'pages' => [
                    'list' => $pages_cont,
                    'page' => (int)$page,
                    'count' => $count,
                ],
            ];
        } catch (\Exception $e) {
            Yii::$app->response->setStatusCode(500);
            return [
                'success' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function actionPageByLink()
    {
        $page_contents = Pages::getPageByLink(trim(Yii::$app->request->post('link'), "/"));

        return [
            'success' => 1,
            'page' => [
                'title' => $page_contents->title,
                'subtitle' => $page_contents->subtitle,
                'image' => $page_contents['image'],
                'text' => $page_contents->text,
                'meta_title' => $page_contents['page_title'],
                'meta_desc' => $page_contents['page_desc'],
                'meta_keywords' => $page_contents['page_kwords'],
            ],
        ];
    }

    public function actionPageById($id)
    {
        $page_contents = Pages::getPageById($id);

        return [
            'success' => 1,
            'page' => [
                'title' => $page_contents->title,
                'subtitle' => $page_contents->subtitle,
                'image' => $page_contents['image'],
                'text' => $page_contents->text,
                'meta_title' => $page_contents['page_title'],
                'meta_desc' => $page_contents['page_desc'],
                'meta_keywords' => $page_contents['page_kwords'],
            ],
        ];
    }
}
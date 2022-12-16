<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use Yii;
use frontend\components\ApiController;
use frontend\components\Helper;
use yii\data\ActiveDataProvider;
use frontend\models\Marketing;
use himiklab\thumbnail\EasyThumbnailImage;

class DocumentController extends ApiController
{

    public function actionIndex($page = 1)
    {
        try {
            $pagesize = (int)Yii::$app->request->post('pagesize');
            $doc_type = Helper::cleanData(Yii::$app->request->post('doc_type', null));
            $doc_type = !$doc_type ? null : $doc_type;

            $query = Marketing::find()
                ->alias('m')
                ->select('m.*, md.*')
                ->leftJoin('marketing_desc md ON (md.marketing_id = m.id AND md.language_id = "' . \Yii::$app->language . '")')
                ->where(['m.status' => 1])
                ->andFilterWhere(['m.doc_type' => $doc_type]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => $pagesize,
                ],
                'sort' => [
                    'defaultOrder'=>[
                        'sort_order'=>SORT_ASC
                    ],
                ],
            ]);

            $models = $dataProvider->getModels();
            $docs = [];
            foreach ($models as $item) {

                $video_str = str_replace(array('https://www.youtube.com/watch?v=', 'https://youtu.be/'), 'https://www.youtube.com/embed/', $item->video);

                $thumb = EasyThumbnailImage::thumbnailFileUrl(
                    '@root' . $item->img,
                    600,
                    800,
                    EasyThumbnailImage::THUMBNAIL_INSET
                );

                $docs[] = [
                    'id' => $item->id,
                    'doc_type' => $item->doc_type,
                    'name' => $item->name,
                    'thumb' => $thumb,
                    'video' => $video_str,
                    'file' => $item->file,
                ];
            }
            $count = $dataProvider->getTotalCount();

            return [
                'success' => 1,
                'docs' => [
                    'list' => $docs,
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
}
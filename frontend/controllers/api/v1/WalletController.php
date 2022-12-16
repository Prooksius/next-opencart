<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use frontend\models\Invoice;
use frontend\models\Movement;
use frontend\models\Translation;
use Yii;
use frontend\components\ApiController;
use frontend\components\Helper;
use yii\data\ActiveDataProvider;

class WalletController extends ApiController
{

    public function actionIndex()
    {
        return [
            'success' => 1,
        ];
    }

    public function actionInvoice($page = 1)
    {
        try {
            $pagesize = (int)Yii::$app->request->post('pagesize');
            $payed = Helper::cleanData(Yii::$app->request->post('payed', 0));
            $payed = !$payed ? null : $payed;

            $query = Invoice::find()
                ->where(['customer_id' => Yii::$app->user->id])
                ->andFilterWhere(['payed' => $payed]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => $pagesize,
                ],
                'sort' => [
                    'defaultOrder'=>[
                        'created_at'=>SORT_DESC
                    ],
                ],
            ]);

            $models = $dataProvider->getModels();
            $items = [];
            foreach ($models as $item) {

                $items[] = [
                    'id' => $item->id,
                    'date' => date('d.m.Y', $item->created_at),
                    'payed' => (int)$item->payed,
                ];
            }
            $count = $dataProvider->getTotalCount();

            return [
                'success' => 1,
                'invoices' => [
                    'list' => $items,
                    'page' => (int)$page,
                    'count' => $count,
                    'payed' => $payed,
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

    public function actionMovement($page = 1)
    {
        try {
            $pagesize = (int)Yii::$app->request->post('pagesize');
            $move_type = Helper::cleanData(Yii::$app->request->post('move_type', null));
            $move_type = $move_type != 'null' ? $move_type : null;

            $move_types = [
              Movement::TYPE_PAYOUT => Translation::getTranslation('MoveTypePayout'),
              Movement::TYPE_ADD => Translation::getTranslation('MoveTypeAdd'),
              Movement::TYPE_MOVE_TO_OTHER => Translation::getTranslation('MoveTypeMoveToOther'),
            ];

            $query = Movement::find()
                ->alias('m')
                ->select([
                    'm.*',
                    'dc.username AS destusername'
                ])
                ->leftJoin('customer dc', 'm.dest_customer_id = dc.id')
                ->where(['m.customer_id' => Yii::$app->user->id])
                ->andWhere(['m.move_type' => [Movement::TYPE_ADD, Movement::TYPE_PAYOUT, Movement::TYPE_MOVE_TO_OTHER]])
                ->andFilterWhere(['m.move_type' => $move_type]);

            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => [
                    'pageSize' => $pagesize,
                ],
                'sort' => [
                    'defaultOrder'=>[
                        'created_at'=>SORT_DESC
                    ],
                ],
            ]);

            $models = $dataProvider->getModels();
            $items = [];
            foreach ($models as $item) {

                $items[] = [
                    'id' => $item->id,
                    'date' => date('d.m.Y', $item->created_at),
                    'amount_num' => $item->amount,
                    'amount' => number_format(abs((float)$item->amount), 2, ',', ' '),
                    'status' => (int)$item->status,
                    'move_type' => (int)$item->move_type,
                    'move_type_text' => $move_types[(int)$item->move_type],
                    'dest_user_name' => $item->destusername,
                ];
            }
            $count = $dataProvider->getTotalCount();

            return [
                'success' => 1,
                'movements' => [
                    'list' => $items,
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
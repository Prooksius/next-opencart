<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use frontend\components\Helper;
use frontend\components\PublicApiController;
use frontend\models\LayoutModule;
use Yii;
use yii\helpers\Json;

class ModuleController extends PublicApiController
{
  public function actionIndex($page)
  {
    $params = Yii::$app->request->post('params', null);
    if ($params) {
      $params = Json::decode($params, true);
    }

    return [
      'success' => 1,
      'content' => LayoutModule::getPageModules($page, $params),
      'params' => $params,
    ];
  }
}
<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use frontend\models\Trip;
use frontend\components\ApiController;

class TripController extends ApiController
{
  public function actionIndex()
  {
    return [
      'success' => 1,
      'trips' => [
        'active' => Trip::getActiveTrips(),
        'past' => Trip::getPrevTrips(), 
      ],
    ];
  }

  public function actionActiveTrip()
  {
    return [
      'success' => 1,
      'active_trip' => Trip::getActiveTrips(1),
    ];
  }
}
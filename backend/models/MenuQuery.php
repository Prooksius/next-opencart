<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 12.02.2019
 * Time: 9:03
 */

namespace app\models;

use yii\db\ActiveQuery;
use creocoder\nestedsets\NestedSetsQueryBehavior;

class MenuQuery extends ActiveQuery {
    public function behaviors() {
        return [
            NestedSetsQueryBehavior::className(),
        ];
    }
}
<?php

namespace common\modules\blog\traits;

use common\modules\blog\Module;

trait ModuleTrait
{
    /**
     * @return Module
     */
    public function getModule()
    {
        return \Yii::$app->getModule('blog');
    }

}

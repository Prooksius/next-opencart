<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

namespace common\modules\blog;

use common\modules\blog\traits\ModuleTrait;
use yii\base\BootstrapInterface;

/**
 * Blogs module bootstrap class.
 */
class Bootstrap implements BootstrapInterface
{
    use ModuleTrait;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {

    }
}

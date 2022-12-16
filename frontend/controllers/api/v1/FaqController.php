<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 15.01.2022
 * Time: 10:26
 */

namespace frontend\controllers\api\v1;

use frontend\models\Faq;
use frontend\components\ApiController;

class FaqController extends ApiController
{
    public function actionIndex()
    {
        return [
            'success' => 1,
            'faqs' => Faq::getAllFaqs(),
        ];
    }
}
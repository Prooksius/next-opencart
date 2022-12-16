<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 05.06.2019
 * Time: 11:27
 */

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;

$lang_tabs = [];
foreach ($languages as $language) {
    $lang_tabs[] = [
        'label' => Html::tag('span', '', ['class' => 'flag inline flag-' . $language->code]) . ' ' . $language->name,
        'encode' => false,
        'active' => (\Yii::$app->language == $language->locale ? true : false),
        'content' => $this->render('mainpage_lang', [
            'main' => $mainpage,
            'locale' => $language->locale,
        ]),
    ];
}

?>

<div class="row">
    <div class="col-sm-12">
        <?= Tabs::widget([
            'items' => $lang_tabs,
        ]);?>
    </div>
</div>

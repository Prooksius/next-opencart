<?php

/* @var $this yii\web\View */

use yii\web\View;

$this->title = 'Спасибо' . ' | ' . \Yii::$app->name;
$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Спасибо',
]);

if ($thanks_page == 'brief') {
    echo $this->render('thanks/thanks-brief', ['active' => 1]);
} elseif ($thanks_page == 'about') {
    echo $this->render('thanks/thanks-about', ['active' => 1]);
} elseif ($thanks_page == 'price') {
    echo $this->render('thanks/thanks-price', ['active' => 1]);
} elseif ($thanks_page == 'payout-request') {
    echo $this->render('thanks/thanks-payout-request');
} elseif ($thanks_page == 'partner-request') {
    echo $this->render('thanks/thanks-partner');
} elseif ($thanks_page == 'test-request') {
    echo $this->render('thanks/thanks-test-request');
} else {
    echo $this->render('thanks/thanks');
}

?>
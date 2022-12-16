<?php

/* @var $this yii\web\View */

use yii\web\View;
use yii\helpers\Html;

$this->title = $seo['meta_title'][\Yii::$app->language];

?>
<?= $this->render('frontpage/first', ['general' => $general, 'social' => $social]); ?>
<?//= $this->render('frontpage/aboutwork'); ?>
<?//= $this->render('frontpage/advantages'); ?>
<?//= $this->render('frontpage/info', ['query' => $infosteps]); ?>
<?//= $this->render('frontpage/bot',   ['query' => $bot, 'model' => $model]); ?>
<?//= $this->render('frontpage/tradecase', ['query' => $tradecase]); ?>
<?//= $this->render('frontpage/review', ['query' => $review]); ?>
<?//= $this->render('frontpage/payment'); ?>
<?//= $this->render('frontpage/faq', ['query' => $faq]); ?>
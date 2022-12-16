<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = $seo['meta_title'] . ' | ' . \Yii::$app->name;
$this->registerMetaTag([
    'name' => 'description',
    'content' => $seo['meta_desc'],
]);?>
<div class="site-full-pages" data-back="/" data-further="/portfolio"><?
echo $this->render('home/home0',   ['active' => 1]);
echo $this->render('about/about1', ['active' => 0, 'members' => $members]);
echo $this->render('about/about2', ['active' => 0]);
echo $this->render('about/about3', ['active' => 0]);
echo $this->render('about/about4', ['active' => 0, 'cat_services' => $services]);
echo $this->render('about/about4-1', ['active' => 0]);
echo $this->render('about/about5', ['active' => 0]);
?>
</div>

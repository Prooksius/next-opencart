<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = $seo['meta_title'] . ' | ' . \Yii::$app->name;
$this->registerMetaTag([
    'name' => 'description',
    'content' => $seo['meta_desc'],
]);?>
<div class="site-full-pages" data-back="/portfolio" data-further="/blog"><?
echo $this->render('home/home0', ['active' => 1]);
echo $this->render('prices/prices1', ['active' => 0]);
echo $this->render('prices/prices2', ['active' => 0 , 'hourly' => $hourly]);
echo $this->render('prices/prices3', ['active' => 0, 'turnkey' => $turnkey[0]]);
echo $this->render('prices/prices3-1', ['active' => 0, 'turnkey' => $turnkey[1]]);
echo $this->render('prices/prices3-2', ['active' => 0, 'turnkey' => $turnkey[2]]);
echo $this->render('prices/prices4', ['active' => 0, 'prices' => $prices]);
echo $this->render('prices/prices5', ['active' => 0]);
?>
</div>

<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

use common\modules\blog\Module;
use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = 'Все статьи блога | ' . \Yii::$app->name;
Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $seo['meta_desc']
]);
Yii::$app->view->registerMetaTag([
    'name' => 'keywords',
    'content' => $seo['meta_keywords']
]);

if (Yii::$app->get('opengraph', false)) {
    Yii::$app->opengraph->set([
        'title' => $this->title,
        'description' => $seo['meta_desc'],
        //'image' => '',
    ]);
}
//$this->params['breadcrumbs'][] = '文章';

/*$this->breadcrumbs=[
    //$post->category->title => Yii::app()->createUrl('post/category', array('id'=>$post->category->id, 'slug'=>$post->category->slug)),
    '文章',
];*/

?>
<div class="section-portfolio section-blog">
    <div class="container">
        <h1><?= $seo['h1']; ?></h1>
        <h2><?= $seo['h2']; ?></h2>
        <p class="anons-p"><?= $seo['anons']; ?></p>
        <div class="works-container blog-container">
            <? foreach ($query->all() as $post) { ?>
                <div class="work-simple-area">
                    <a href="<?= $post->url; ?>">
                        <span class="work-link"><? echo $post->title; ?></span>
                        <span class="work-arrow"><svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.87786 5.72339L1.27452 0.120322C1.19434 0.0402196 1.10206 0 0.997912 0C0.893719 0 0.801488 0.0402196 0.721343 0.120322L0.120238 0.721551C0.039967 0.801696 0 0.893759 0 0.998119C0 1.10248 0.039967 1.19454 0.120238 1.27469L4.84569 6.00013L0.120238 10.7257C0.039967 10.8058 0 10.898 0 11.0021C0 11.1064 0.039967 11.1986 0.120238 11.2788L0.721385 11.8798C0.80153 11.96 0.893761 12 0.997954 12C1.1021 12 1.19434 11.9599 1.27452 11.8798L6.87757 6.27669C6.95767 6.19659 6.99776 6.10432 6.99776 6.00013C6.99776 5.89593 6.95796 5.8037 6.87786 5.72339Z" fill="#868686"/></svg></span>
                    </a>
                </div>
            <? } ?>
        </div>
        <div class="hidden">
            <div class="uSocial-Share" data-pid="fe5a870f780a0b8124078708b533fee2" data-type="share" data-options="round,style1,default,absolute,horizontal,size32,counter0,nomobile" data-social="vk,twi,fb,telegram"></div>
        </div>
    </div>
</div>

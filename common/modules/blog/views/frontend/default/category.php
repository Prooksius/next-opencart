<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

use common\modules\blog\Module;
use yii\widgets\ListView;

$this->title = $cat_query->title . ' | ' . $seo['meta_title'] . ' | ' . \Yii::$app->name;
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
        <h2><?= $cat_query->title; ?></h2>
        <p class="anons-p"><?= $seo['anons']; ?></p>
        <div class="works-container blog-container">
            <?= $this->render('blogposts', ['query' => $query, 'page' => $page]); ?>
        </div>
        <? if ($next && $query->count()) { ?>
            <div class="row load-block">
                <div class="col-sm-12 text-center">
                    <a data-pjax="0" href="javascript:void" class="load-next-page" title="Загрузить еще" data-container=".blog-container" data-action="/blog/default/catpage?category_id=<?= $cat_query->id; ?>">
                        <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="25" cy="25" r="24.5" stroke="#C4C4C4"/><path d="M33.5432 16.8084C33.223 16.6758 32.9496 16.7303 32.7229 16.9725L31.1995 18.4844C30.3635 17.6953 29.4085 17.084 28.3342 16.6504C27.26 16.2168 26.1486 16 25.0001 16C23.7814 16 22.6173 16.2384 21.508 16.7149C20.3985 17.1914 19.4414 17.8319 18.6368 18.6366C17.8321 19.4414 17.1914 20.3984 16.7149 21.5078C16.2384 22.6172 16 23.7811 16 25C16 26.2185 16.2384 27.3827 16.7149 28.4921C17.1916 29.6016 17.8321 30.5586 18.6368 31.3633C19.4415 32.1678 20.3985 32.8084 21.508 33.285C22.6174 33.7616 23.7814 34 25.0002 34C26.3438 34 27.6215 33.7168 28.8323 33.1504C30.0432 32.5841 31.0744 31.7852 31.9262 30.7539C31.9886 30.6758 32.0179 30.5878 32.0139 30.4902C32.0102 30.3926 31.9729 30.3125 31.9026 30.25L30.2974 28.6328C30.2112 28.5626 30.1138 28.5275 30.0044 28.5275C29.8794 28.5431 29.7896 28.5901 29.7349 28.6683C29.1645 29.4106 28.4653 29.9847 27.6371 30.3912C26.8091 30.7973 25.9302 31.0003 25.0005 31.0003C24.1881 31.0003 23.4127 30.8421 22.6744 30.5256C21.936 30.2094 21.2973 29.7816 20.7583 29.2425C20.2194 28.7034 19.7916 28.0649 19.4752 27.3264C19.1588 26.5881 19.0006 25.8129 19.0006 25.0003C19.0006 24.1878 19.1589 23.4122 19.4752 22.6741C19.7915 21.9359 20.2192 21.2971 20.7583 20.7582C21.2974 20.2191 21.936 19.7913 22.6744 19.4749C23.4125 19.1585 24.1881 19.0003 25.0005 19.0003C26.5709 19.0003 27.9342 19.5355 29.0905 20.6058L27.473 22.2228C27.2307 22.4572 27.1762 22.7268 27.3087 23.0314C27.4418 23.3439 27.6722 23.5001 28.0005 23.5001H33.2505C33.4536 23.5001 33.6294 23.426 33.7779 23.2775C33.9262 23.1291 34.0003 22.9533 34.0003 22.7502V17.5001C34.0002 17.1721 33.8483 16.9416 33.5432 16.8084Z" fill="#C4C4C4"/></svg>
                    </a>
                </div>
            </div>
        <? } ?>
        <div class="row">
            <div class="col-sm-12 sm-text-right">
                <a href="/blog/all" class="back-works"><span class="hidden-xs">Все статьи</span>
                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><circle r="24.5" transform="matrix(-1 0 0 1 25 25)" stroke="#C4C4C4"/><path d="M30.5054 25.3688L23.0343 32.8396C22.9274 32.9464 22.8044 33 22.6655 33C22.5266 33 22.4036 32.9464 22.2968 32.8396L21.4953 32.0379C21.3883 31.9311 21.335 31.8083 21.335 31.6692C21.335 31.53 21.3883 31.4073 21.4953 31.3004L27.7959 24.9998L21.4953 18.6991C21.3883 18.5923 21.335 18.4693 21.335 18.3305C21.335 18.1915 21.3883 18.0685 21.4953 17.9616L22.2968 17.1603C22.4037 17.0533 22.5266 17 22.6656 17C22.8044 17 22.9274 17.0534 23.0343 17.1603L30.505 24.6311C30.6119 24.7379 30.6653 24.8609 30.6653 24.9998C30.6653 25.1388 30.6122 25.2617 30.5054 25.3688Z" fill="#C4C4C4"/></svg>
                </a>
            </div>
        </div>
        <div class="hidden">
            <div class="uSocial-Share" data-pid="fe5a870f780a0b8124078708b533fee2" data-type="share" data-options="round,style1,default,absolute,horizontal,size32,counter0,nomobile" data-social="vk,twi,fb,telegram"></div>
        </div>
    </div>
</div>

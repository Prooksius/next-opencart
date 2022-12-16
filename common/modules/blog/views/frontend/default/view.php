<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */
/* @var $this \yii\web\View */
/* @var $post \akiraz2\blog\models\BlogPost */

/* @var $dataProvider \yii\data\ActiveDataProvider */

use common\modules\blog\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use himiklab\thumbnail\EasyThumbnailImage;
use yii\widgets\ListView;
use yii\helpers\ArrayHelper;
use frontend\assets\UsocialAsset;

\common\modules\blog\assets\AppAsset::register($this);
UsocialAsset::register($this);

$this->title = $post->title . ' | ' . \Yii::$app->name;
Yii::$app->view->registerMetaTag([
    'name' => 'description',
    'content' => $post->brief
]);
Yii::$app->view->registerMetaTag([
    'name' => 'keywords',
    'content' => $this->title
]);

if (Yii::$app->get('opengraph', false)) {
    Yii::$app->opengraph->set([
        'title' => $this->title,
        'description' => $post->brief,
        'image' => $post->banner,
    ]);
}

$this->params['breadcrumbs'][] = [
    'label' => Module::t('blog', 'Blog'),
    'url' => ['default/index']
];
$this->params['breadcrumbs'][] = $this->title;
$post_user = $post->publisher;
$username_attribute = Module::getInstance()->userName;
?>

<div class="section-portfolio-work section-blogpost">
    <div class="container">
        <div class="row work-heading-row">
            <div class="col-md-8 col-sm-6">
                <h1 itemprop="headline"><?= $this->title; ?></h1>
                <p class="blogpost-stats">
                    <span class="datetime-author">
                        <time title="<?= Module::t('blog', 'Create Time'); ?>" itemprop="datePublished" datetime="<?= date_format(date_timestamp_set(new DateTime(), $post->created_at), 'c') ?>">
                            <?= Yii::$app->formatter->asDate($post->created_at, 'php:j F Y'); ?>&nbsp;года
                        </time><br />
                        <?= $post->publishername; ?>
                    </span>
                    <span title="Просмотры">
                        <i class="fa fa-eye" aria-hidden="true"></i>&nbsp;&nbsp;<?= $post->click; ?>
                    </span>
                    <span title="Комментарии">
                        <i class="fa fa-comment" aria-hidden="true"></i>&nbsp;&nbsp;<?= $post->commentsCount; ?>
                    </span>
                </p>
            </div>
            <div class="col-md-4 col-sm-6 sm-text-right">
                <a href="/blog" class="back-works"><span class="hidden-xs">Назад</span>&nbsp;
                    <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="25" cy="25" r="24.5" stroke="#C4C4C4"/><path d="M19.4946 25.3688L26.9657 32.8396C27.0726 32.9464 27.1956 33 27.3345 33C27.4734 33 27.5964 32.9464 27.7032 32.8396L28.5047 32.0379C28.6117 31.9311 28.665 31.8083 28.665 31.6692C28.665 31.53 28.6117 31.4073 28.5047 31.3004L22.2041 24.9998L28.5047 18.6991C28.6117 18.5923 28.665 18.4693 28.665 18.3305C28.665 18.1915 28.6117 18.0685 28.5047 17.9616L27.7032 17.1603C27.5963 17.0533 27.4734 17 27.3344 17C27.1956 17 27.0726 17.0534 26.9657 17.1603L19.495 24.6311C19.3881 24.7379 19.3347 24.8609 19.3347 24.9998C19.3347 25.1388 19.3878 25.2617 19.4946 25.3688Z" fill="#C4C4C4"/></svg>
                </a>
            </div>
        </div>
    </div>
    <div class="works-container">
        <div class="container">
            <article class="blog-post" itemscope itemtype="http://schema.org/Article">
                <meta itemprop="author" content="<?= $post_user->{$username_attribute}; ?>">
                <meta itemprop="dateModified" content="<?= date_format(date_timestamp_set(new DateTime(), $post->updated_at), 'c') ?>"/>
                <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="<?= $post->getAbsoluteUrl(); ?>"/>
                <meta itemprop="commentCount" content="<?= $dataProvider->getTotalCount(); ?>">
                <meta itemprop="genre" content="<?= $post->category->title; ?>">
                <meta itemprop="articleSection" content="<?= $post->category->title; ?>">
                <meta itemprop="inLanguage" content="<?= Yii::$app->language; ?>">
                <meta itemprop="discussionUrl" content="<?= $post->getAbsoluteUrl(); ?>">
                <div class="row work-container blogpost">
                    <div class="col-sm-12">
                        <?
                        $images = [];
                        preg_match_all('/<img[^>]+>/i',$post->content, $images);
                        $img = [];
                        $img_class = [];
                        $images2 = [];
                        foreach( $images[0] as $key => $img_tag) {
                            $images2[$key] = [
                                'tag' => $img_tag,
                                'src' => '',
                                'size' => '',
                            ];
                        }
                        $new_descr = $post->content;
                        foreach ($images2 as $key => $img_arr) {
                            $img_size = '';
                            $width = 750;
                            $height = 470;

                            preg_match_all('/src="[^"]*"/i', $img_arr['tag'], $img);
                            preg_match_all('/class="[^"]*"/i', $img_arr['tag'], $img_class);
                            $img_size = trim(str_replace(['img-responsive', 'class="', '"'], '', $img_class[0][0]));
                            $a_class = 'pic-link';
                            if ($img_size == 'gallery-w1-h1') {
                                $width = 240;
                                $height = 180;
                            } elseif ($img_size == 'gallery-w1-h2') {
                                $width = 240;
                                $height = 370;
                            } elseif ($img_size == 'gallery-w2-h1') {
                                $width = 490;
                                $height = 180;
                            } elseif ($img_size == 'gallery-w2-h2') {
                                $width = 490;
                                $height = 370;
                            } elseif ($img_size == 'full-size') {
                                $width = 1140;
                                $height = 540;
                                $a_class = 'full-size';
                            } else {
                                $a_class = 'full-size';
                            }

                            $images2[$key]['src'] = str_replace(['src="', '"'], '', str_replace(['""', '"""'], '', $img[0][0]));
                            if ($img_size == 'trigger-svg') {
                                $img_thumb_a = Html::img($images2[$key]['src'], ['class' => 'img-responsive', 'style' => 'width: 74px; height: 74px;']);
                            } elseif ($a_class == 'full-size') {
                                $img_thumb_a = EasyThumbnailImage::thumbnailImg(
                                    '@root' . $images2[$key]['src'],
                                    $width,
                                    $height,
                                    EasyThumbnailImage::THUMBNAIL_OUTBOUND,
                                    ['class' => 'img-responsive']
                                );
                            } else {
                                $img_thumb_a = Html::a(EasyThumbnailImage::thumbnailImg(
                                    '@root' . $images2[$key]['src'],
                                    $width,
                                    $height,
                                    EasyThumbnailImage::THUMBNAIL_OUTBOUND,
                                    ['class' => 'img-responsive']
                                ), $images2[$key]['src'], ['data-fancybox' => 'gallery', 'class' => $a_class]);
                            }

                            if ($img_size == 'full-size') {
                                $new_descr = $img_thumb_a;
                            } else {
                                $new_descr = str_replace($images2[$key]['tag'], $img_thumb_a, $new_descr);
                            }
                        }
                        ?>
                        <?= $new_descr; ?>
                        <?php if (isset($post->module->schemaOrg) && isset($post->module->schemaOrg['publisher'])) : ?>
                            <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization" class="blog-post__publisher">
                                <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                                    <meta itemprop="url image" content="<?= Yii::$app->urlManager->createAbsoluteUrl($post->module->schemaOrg['publisher']['logo']); ?>"/>
                                    <meta itemprop="width" content="<?= $post->module->schemaOrg['publisher']['logoWidth']; ?>">
                                    <meta itemprop="height" content="<?= $post->module->schemaOrg['publisher']['logoHeight']; ?>">
                                </div>
                                <meta itemprop="name" content="<?= $post->module->schemaOrg['publisher']['name'] ?>">
                                <meta itemprop="telephone" content="<?= $post->module->schemaOrg['publisher']['phone']; ?>">
                                <meta itemprop="address" content="<?= $post->module->schemaOrg['publisher']['address']; ?>">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <? if (!empty($post->tagLinks)) { ?>
                        <div class="col-sm-6 sm-text-left">
                            <div class="tags-list">
                                <? foreach ($post->tagLinks as $tagName) { ?>
                                    <?= $tagName ?>
                                <? } ?>
                            </div>
                        </div>
                        <div class="col-sm-6 social-share sm-text-right">
                            <div class="uSocial-Share" data-pid="fe5a870f780a0b8124078708b533fee2" data-type="share" data-options="round,style1,default,absolute,horizontal,size32,counter0,nomobile" data-social="vk,twi,fb,telegram"></div>
                        </div>
                    <? } else { ?>
                        <div class="col-sm-12 social-share text-center">
                            <div class="uSocial-Share" data-pid="fe5a870f780a0b8124078708b533fee2" data-type="share" data-options="round,style1,default,absolute,horizontal,size32,counter0,nomobile" data-social="vk,twi,fb,telegram"></div>
                        </div>
                    <? } ?>
                </div>
            </article>
        </div>
    </div>
</div>

<? if ($post->module->enableComments) { ?>
    <section id="comments" class="blog-comments">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <h2 class="blog-comments__header title title--2"><?= Module::t('blog', 'Comments'); ?> (<?= $comment->getCommentsCount(); ?>)</h2>

                    <div class="row">
                        <div class="col-md-12">
                        <?= ListView::widget([
                            'dataProvider' => $dataProvider,
                            'itemView' => '_comment',
                            'layout' => "{items}\n{pager}",
                        ]) ?>
                        </div>
                        <div class="col-md-12">
                            <div id="add-comment-form">
                                <div class="comment-form-cont">
                                    <h3><?= Module::t('blog', 'Write comments'); ?></h3>
                                    <?= $this->render('_form', [
                                        'model' => $comment,
                                        'maxLevel' => $maxLevel,
                                        'parentId' => '',
                                        'post_id' => $post->id,
                                        'cancel' => false,
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- comments -->
    <div class="reply-form-cont hidden">
        <div id="reply-form">
            <div class="comment-form-cont">
                <h3><?= Module::t('blog', 'Reply comment'); ?></h3>
                <?= $this->render('_form', [
                    'model' => $comment,
                    'maxLevel' => $maxLevel,
                    'parentId' => '',
                    'post_id' => $post->id,
                    'cancel' => true,
                ]); ?>
            </div>
        </div>
    </div>
<? } ?>
<div class="section-portfolio section-blog">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h2 class="blog-comments__header title title--2"><?= Module::t('blog', 'Other posts'); ?></h2>
            </div>
        </div>
        <div class="works-container blog-container">
            <?= $this->render('blogposts', ['query' => $other_posts, 'page' => 1]); ?>
        </div>
    </div>
</div>


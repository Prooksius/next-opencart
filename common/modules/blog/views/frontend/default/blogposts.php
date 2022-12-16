<?php

/* @var $this yii\web\View */

use yii\helpers\Url;
use yii\helpers\Html;
use himiklab\thumbnail\EasyThumbnailImage;

function echoTile($post, $post_num, $post_special, $post_side) {

    ?>
    <a href="<?= $post->url; ?>" class="blog-tile <?= ($post_num == $post_special ? 'tall' : 'short'); ?> wow fadeInUp" data-wow-delay="<?= 300 + rand(1, 15) * 150; ?>ms">
        <span class="blogpost-picture-cont">
            <span class="blogpost-picture" style="background-image: url(/<?=
        EasyThumbnailImage::thumbnailFileUrl(
            '@root' . $post->banner,
            500,
            500,
            EasyThumbnailImage::THUMBNAIL_OUTBOUND
        ); ?>)">
                <span class="dark_fon"></span>
            </span>
        </span>
        <span class="blogpost-date"><?= Yii::$app->formatter->asDate($post->created_at, 'php:j F Y'); ?>, <?= $post->publishername; ?></span>
        <span class="blogpost-title"><?= $post->title; ?></span>
        <? if ($post_num == $post_special) { ?>
            <span class="blogpost-anons"><?= $post->brief; ?></span>
        <? } ?>
        <span class="blogpost-stats">
            <span title="Просмотры">
                <i class="fa fa-eye" aria-hidden="true"></i>&nbsp;&nbsp;<?= $post->click; ?>
            </span>
            <span title="Комментарии">
               <i class="fa fa-comment" aria-hidden="true"></i>&nbsp;&nbsp;<?= $post->commentsCount; ?>
            </span>
        </span>
    </a>
<?
}
?>
<? $block_count = 0; ?>
<? foreach (array_chunk($query->all(), 5) as $post_block) { ?>
    <? if ($block_count % 2 != 0) { ?>
        <? $post_num = 1; ?>
        <? $post_side = 1; ?>
        <div class="row blogpost-row">
        <? foreach ($post_block as $post) { ?>
            <? if ($post_num == 1) { ?>
            <div class="col-md-8 col-sm-12">
                <div class="row">
                    <div class="col-md-6 col-sm-6">
            <? } elseif ($post_num == 5) { ?>
            <div class="col-md-4 col-sm-12">
            <? } else { ?>
            <div class="col-md-6 col-sm-6">
            <? } ?>
            <? echoTile($post, $post_num, 5, $post_side); ?>
            <? if ($post_num == 4) { ?>
                    </div>
                </div>
            </div>
            <? } else { ?>
            </div>
            <? } ?>
            <? $post_num++; ?>
        <? } ?>
        </div>
    <? } else { ?>
        <? $post_num = 1; ?>
        <? $post_side = 2; ?>
        <div class="row blogpost-row">
            <? foreach ($post_block as $post) { ?>
                <? if ($post_num == 1) { ?>
                <div class="col-md-4 col-sm-12">
                <? } elseif ($post_num == 2) { ?>
                <div class="col-md-8 col-sm-12">
                    <div class="row">
                        <div class="col-md-6 col-sm-6">
                <? } else { ?>
                <div class="col-md-6 col-sm-6">
                <? } ?>
                <? echoTile($post, $post_num, 1, $post_side); ?>
                <? if ($post_num == 5) { ?>
                        </div>
                    </div>
                </div>
                <? } else { ?>
                </div>
                <? } ?>
                <? $post_num++; ?>
            <? } ?>
        </div>
    <? } ?>
<? $block_count++; ?>
<? } ?>

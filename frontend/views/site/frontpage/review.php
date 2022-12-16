<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 26.04.2020
 * Time: 21:28
 */

use yii\helpers\Json;
use frontend\components\Helper;
use himiklab\thumbnail\EasyThumbnailImage;

$reviews_all = $query->all();
if (!empty($reviews_all)) { // не отображать ничего, если нет ни одного видимого отзыва
$days = ['день', 'дня', 'дней'];
?>
<div class="reviews scroll" id="reviews">
    <div class="container">
        <h2>Отзывы</h2>
        <div class="reviews-slider">
            <div class="swiper-container swiper-container2">
                <div class="swiper-wrapper">
                    <? foreach ($reviews_all as $review) { ?>
                    <div class="swiper-slide">
                        <div class="reviews-item">
                            <div class="reviews-item-wrap1">
                                <ul class="reviews-list">
                                    <li>
                                        <span>Депозит</span>
                                        <b>$ <?= number_format($review->deposit, 2, '.', ' '); ?></b>
                                    </li>
                                    <li>
                                        <span>Период дней</span>
                                        <b><?= $review->period . ' ' . $days[Helper::plural_type($review->period)]; ?></b>
                                    </li>
                                    <li>
                                        <span>Прибыль</span>
                                        <b>$ <?= $review->income ?></b>
                                    </li>
                                    <li>
                                        <div class="reviews-ava">
                                            <div class="reviews-ava-img">
                                                <?= EasyThumbnailImage::thumbnailImg(
                                                    '@root' . $review->photo,
                                                    150,
                                                    150,
                                                    EasyThumbnailImage::THUMBNAIL_OUTBOUND,
                                                    ['style' => ['border-radius' => '7px', 'width' => '48px']]
                                                ) ?>
                                            </div>
                                            <div class="reviews-ava-text">
                                                <b><?= $review->name; ?></b>
                                                <span><?= $review->city; ?></span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="reviews-item-wrap2">
                                <a href="<?= $review->picture; ?>" data-fancybox="">
                                    <?= EasyThumbnailImage::thumbnailImg(
                                        '@root' . $review->picture,
                                        324,
                                        239,
                                        EasyThumbnailImage::THUMBNAIL_OUTBOUND
                                    ) ?>
                                </a>
                            </div>
                            <div class="reviews-item-wrap3">
                                <div class="reviews-dotted">
                                    <i></i>
                                    <i></i>
                                    <i style="opacity: 0;"></i>
                                    <i></i>
                                </div>
                                <? if ($review->info_type == 0) { ?>
                                    <div class="reviews-text">
                                        <p><?= str_replace("\r\n", '</p><p>', trim($review->description)) ?></p>
                                    </div>
                                <? } else { ?>
                                    <a href="<?= $review->video ?>" class="reviews-video" data-fancybox>
                                        <div class="reviews-video-play">
                                            <img src="/img/play-icon.svg" alt="">
                                        </div>
                                        <img src="/img/reviews-video.jpg" alt="">
                                    </a>
                                <? } ?>
                            </div>
                        </div>
                    </div>
                    <? } ?>
                </div>
            </div>
            <div class="reviews-slider-nav">
                <div class="swiper-button-prev swiper-button-prev2 swiper-button-prev-style2"></div>
                <div class="swiper-button-next swiper-button-next2 swiper-button-next-style2"></div>
            </div>
            <div class="swiper-pagination swiper-pagination2 swiper-pagination-style2"></div>
        </div>
    </div>
</div>
<? } ?>
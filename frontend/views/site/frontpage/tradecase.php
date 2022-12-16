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
use yii\helpers\Url;

$days = ['день', 'дня', 'дней'];

?>
<section class="cases scroll" id="cases">
    <div class="container">
        <div class="cases-title">
            <h2>Кейсы</h2>
            <h3>Посмотрите на успешные кейсы в различных ситуациях и трендах рынка</h3>
        </div>
        <div class="cases-slider">
            <div class="swiper-container swiper-container1">
                <div class="swiper-wrapper">
                    <? foreach ($query->all() as $case) { ?>
                    <? $pictures = Json::decode($case->pictures); ?>
                    <? $picture = array_shift($pictures)['image']; ?>
                    <div class="swiper-slide">
                        <div class="cases-item">
                            <div class="cases-img-wrap">
                                <a href="<?= $picture; ?>" class="cases-img" data-fancybox="">
                                    <?= EasyThumbnailImage::thumbnailImg(
                                    '@root' . $picture,
                                    582,
                                    397,
                                    EasyThumbnailImage::THUMBNAIL_INSET
                                    ) ?>
                                </a>
                            </div>
                            <div class="cases-text">
                                <div class="cases-text-wrap">
                                    <h4><?= $case->name; ?></h4>
                                    <p><?= nl2br($case->description); ?></p>
                                    <ul>
                                        <li>
                                            <b>$ <?= number_format($case->param1, 0, '', ' '); ?></b>
                                            <span>Депозит</span>
                                        </li>
                                        <li>
                                            <b><?= $case->param2 . ' ' . $days[Helper::plural_type($case->param2)]; ?></b>
                                            <span>Период дней</span>
                                        </li>
                                        <li>
                                            <b>$ <?= $case->param3 ?></b>
                                            <span>Прибыль</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <? } ?>
                </div>
            </div>
            <div class="cases-dotted">
                <i></i>
                <i></i>
                <i style="opacity: 0;"></i>
                <i></i>
            </div>
            <div class="cases-slider-nav">
                <div class="swiper-button-prev swiper-button-prev1 swiper-button-prev-style1"></div>
                <div class="swiper-button-next swiper-button-next1 swiper-button-next-style1"></div>
            </div>
        </div>
    </div>
</section>
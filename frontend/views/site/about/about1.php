<?php

/* @var $this yii\web\View */

use himiklab\thumbnail\EasyThumbnailImage;

?>
<div class="site-about site-page page1<? echo ($active ? ' open' : ''); ?>" style="background-image: url(/img/about_map.jpg); background-color: #222;" data-background="black-bg">
    <div class="body-content big-heading">
        <h1 class="home-h1">Команда<span class="lesser">объединенная</span></h1>
        <h3>Вне зависимости от бюджета мы стараемся сделать сайт максимально высокого уровня. Лучший в своей нише</h3>
    </div>
    <div class="members-container">
        <? foreach ($members->all() as $member) { ?>
        <div style="left: 50%; margin-left: <?= $member->position_x ?>px;top: <?= $member->position_y ?>%;" class="member-photo" rel="tooltip" data-html="true" data-toggle="tooltip" data-placement="bottom" data-original-title="<b><?= $member->name?></b><br /><?= $member->speciality?>">
            <div class="member-area">
                <?= EasyThumbnailImage::thumbnailImg(
                    '@root' . $member->photo,
                    40,
                    40,
                    EasyThumbnailImage::THUMBNAIL_INSET,
                    ['class' => 'img-responsive photo-pic']
                ); ?>
                <img class="speciality-pic" src="<?= $member->speciality_pic ?>" />
            </div>
        </div>
        <? } ?>
    </div>
</div>

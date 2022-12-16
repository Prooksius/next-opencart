<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 30.04.2020
 * Time: 8:30
 */

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Pjax;
use himiklab\thumbnail\EasyThumbnailImage;

?>

<? Pjax::begin(['enablePushState' => false]); ?>
<? $form = ActiveForm::begin([
    'id' => 'photo-upload-form',
    'options' => [
        'data' => ['pjax' => true],
    ],
]) ?>

<div class="profile-img">
    <? if ($model->picture) { ?>
    <?= EasyThumbnailImage::thumbnailImg(
        '@root' . $model->picture,
        192,
        192,
        EasyThumbnailImage::THUMBNAIL_OUTBOUND
    ) ?>
    <? } else { ?>
    <?= EasyThumbnailImage::thumbnailImg(
        '@root/frontend/web/img/no-avatar.jpg',
        192,
        192,
        EasyThumbnailImage::THUMBNAIL_OUTBOUND
    ) ?>
    <? } ?>
    <i>
        <img src="/img/profile-icon.png" alt="">
        <?= $form->field($model, 'picture')->fileInput(['accept' => 'image/jpeg,image/png'])->label(false) ?>
    </i>

    <? ActiveForm::end() ?>
</div>
<? Pjax::end() ?>

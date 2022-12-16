<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

use common\modules\blog\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>

<div class="blog-comment-form">
    <?php $form = ActiveForm::begin(['options' => ['data-post-id' => $post_id]]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'author')->textInput((['maxlength' => 32])); ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'email')->textInput((['maxlength' => 32])); ?>
        </div>
    </div>

    <?= $form->field($model, 'content')->textarea(['rows' => 3]); ?>

    <?= Html::activeHiddenInput($model, 'parentId', ['class' => "parent-id-input", 'value' => $parentId]); ?>

    <? if ($cancel) { ?>
    <?= Html::Button(Module::t('blog', 'Reply'), ['class' => 'btn btn-primary submit-comment']) ?>
    <?= Html::Button(Module::t('blog', 'Cancel'), ['class' => 'btn btn-primary cancel-reply-btn']) ?>
    <? } else { ?>
    <?= Html::Button(Module::t('blog', 'Add comments'), ['class' => 'btn btn-primary submit-comment']) ?>
    <? } ?>
    <?php ActiveForm::end(); ?>
</div>

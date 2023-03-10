<?php
/**
 * Project: yii2-blog for internal using
 * Author: akiraz2
 * Copyright (c) 2018.
 */

use yii\helpers\Html;
use common\modules\blog\traits\IActiveStatus;
use common\modules\blog\Module;

?>
<div class="blog-comment<?= $model->status == IActiveStatus::STATUS_INACTIVE ? ' blog-comment--inactive' : '' ?>" id="c<?= $model->id; ?>" data-id="<?= $model->id; ?>">
    <p class="blog-comment__top">
        <span class="blog-comment__avatar">
            <svg version="1.1" width="42" height="42" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 1000 1000" enable-background="new 0 0 1000 1000" xml:space="preserve"><g><path d="M482.3,990.4c-11.8-1.3-23.7-2.5-35.5-3.8c-107.7-11.9-201.4-54.6-280.6-128.3C83.6,781.3,32.2,686.7,16,575.1C-6.6,420.2,34.6,283,140.3,167.2C220.3,79.5,320.6,28.2,438.3,13.5C699.9-19.1,929.7,156,978.5,397.3c35.4,174.7-11.5,327.2-137.4,453.4c-74.9,75-166.7,118.8-271.8,133.7c-17.2,2.4-34.6,4-51.9,6C505.7,990.4,494,990.4,482.3,990.4z M149.6,792.9c0.8-1,1.7-1.8,2.4-2.8c13.1-21.4,31.2-37.8,54-47.5c25.6-10.8,52.4-18.6,78.4-28.6c25.3-9.7,50.9-19.2,75.1-31.4c21.7-10.9,33.4-30.2,33.8-55.2c0.1-3.8-1.7-8.2-3.9-11.3c-11.3-16.2-23.1-32.1-34.8-48c-1.9-2.6-3.9-5.2-6.5-7.1c-26.3-19.6-42.4-45.8-51.1-77.2c-4.4-15.8-2.3-30.6,4.3-45.4c2.3-5.1,3.5-10.9,4-16.5c2.1-26.1,1.6-52.7,6.2-78.4c8.2-45.5,27-84.8,74.7-102.2c2.7-1,5.1-2.8,7.7-4.1C439,215,486.5,201.5,537,202.6c38.8,0.8,71.9,13.6,90.5,51.2c1.1,2.2,4,4.3,6.4,4.9c31.4,7.6,47.8,29.5,56.3,58.7c10.2,35.3,8.2,71.2,4.2,107.1c-0.5,4.2,0.5,9,2.3,12.8c6.1,12.6,9.6,25.7,6.7,39.5c-5.4,25.9-15.6,50.3-35.2,68.1c-17.4,15.7-30.4,33.9-43.3,53c-7.2,10.7-16,20.4-24.4,30.2c-9,10.4-9.8,15.7-1.9,27.2c3.1,4.5,6.9,8.6,11,12.3c16,14.5,35.5,22.3,55.6,29.2c37.3,12.9,75,24.9,111.7,39.3c23.7,9.3,46.6,21.6,64.6,40.7c4.1,4.3,7.8,9,11.7,13.7c147.5-174.9,145.7-455.1-43.9-628.2C616.6-13.7,321.8,4.2,152.7,201C-11.2,391.6,22.2,651.1,149.6,792.9z M508,958.2c13.9-1.2,36.4-2,58.5-5.3c97.6-14.6,181.8-56.7,253-124.9c11.6-11.1,12.1-14,1.5-26c-4.6-5.3-10-10.3-16-14.1c-11.8-7.4-23.5-15.6-36.4-20.4c-37.2-14.1-74.9-27-112.5-39.9c-27.8-9.6-54.5-21.1-75.2-43c-15.5-16.5-23-35.8-19-58.7c0.5-2.9,2-6.3,4.1-8.3c22.4-20.8,39.6-45.4,54.3-71.9c1.5-2.7,3.7-5.5,6.3-7c21.8-13,33.9-33.3,42.6-56.2c5.2-13.6,3.4-25.8-5.7-37.4c-2.3-2.9-3.4-7.7-3.1-11.4c1-11.3,3.5-22.4,4.3-33.7c2-28.3,2.2-56.5-8.9-83.4c-7.4-17.9-20.4-28.5-40.5-27c-8.4,0.6-11-2.8-12.9-10.2c-6-24.4-22.4-38.7-46.6-42.3c-15.5-2.3-31.8-2.7-47.4-1.1c-44.9,4.6-85.9,21-125.1,42.9c-11.3,6.3-20.2,14.3-25.2,26c-5.4,12.5-10.7,25.1-14.2,38.2c-8,29.1-6.3,59.1-6.4,88.9c0,5.4-2.7,10.7-4.3,16.1c-2.2,7.6-7.5,15.7-6.2,22.7c5.1,27,18.6,49.5,41.4,65.8c4,2.9,7.7,6.7,10.7,10.7c13.2,17.6,26.3,35.3,38.9,53.4c3.6,5.2,6.9,11.8,7.2,17.9c2.1,46.5-20.3,78.9-60.9,98.6c-22.9,11.1-47.5,18.9-71.4,27.8c-24.9,9.2-50.4,16.9-74.7,27.4c-18.4,7.9-32.9,21.6-41.9,40.3c-2.8,5.9-2.1,9.8,2.7,14.4C268.8,912.5,375.5,956.1,508,958.2z" fill="#C4C4C4" /></g></svg>
        </span>
        <span class="blog-comment__author_date">
            <span class="blog-comment__author">
                <?= $model->authorLink; ?>
            </span><br />
            <time class="blog-comment__date" datetime="<?= date_format(date_timestamp_set(new DateTime(), $model->created_at), 'c') ?>" itemprop="datePublished">
                <?= Yii::$app->formatter->asDatetime($model->created_at, 'php:j F Y ' . Module::t('blog', 'at') . ' h:m'); ?>
            </time>
        </span>
    </p>
    <div class="blog-comment__content">
        <?= nl2br(Html::encode($model->getContent())); ?>
    </div>
    <? if ($model->status !== IActiveStatus::STATUS_INACTIVE) { ?>
    <div class="blog-reply-comment">
        <a data-pjax="0" href="javascript:void(0)" class="reply-comment-link"><?= Module::t('blog', 'Reply'); ?></a>
    </div>
    <? } ?>
    <? if ($model->hasChildren()) { ?>
    <div class="blog-comment-children">
        <?php foreach ($model->getChildren() as $children) : ?>
            <?php echo $this->render('_comment', ['model' => $children, 'maxLevel' => $maxLevel]); ?>
        <?php endforeach; ?>
    </div>
    <? } ?>
</div>

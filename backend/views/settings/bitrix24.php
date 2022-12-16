<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $bitrix24 array */

?>
<div class="row">
    <div class="col-sm-6">
        <?= MyHtml::formGroup('bitrix24', 'webhook', 'Код вебхука', $bitrix24['webhook'])?>
    </div>
    <div class="col-sm-6">
        <?= MyHtml::formGroup('bitrix24', 'user_id', 'ID пользователя', $bitrix24['user_id'])?>
    </div>
</div>

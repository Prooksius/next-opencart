<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Speciality */

?>
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <td colspan="2"><?= YII::t('app', 'Browser') ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?= YII::t('order', 'IP') ?></td>
          <td><?= $model->ip; ?></td>
        </tr>
        <tr>
          <td><?= YII::t('order', 'User agent') ?></td>
          <td><?= $model->user_agent; ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 07.05.2020
 * Time: 20:29
 */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Редактор файлов темы';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="theme-edit">
    <div class="row">
        <div class="col-sm-12">
            <h3> <span class="label label-danger ">Будьте аккуратны, редактируйте только текст, html-код менять нельзя, иначе сайт перестанет работать!!!</span></h3>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <? foreach ($files as $key => $file) { ?>
                <p><?= Html::a($file['title'], Url::to(['update', 'id' => $key])); ?></p>
            <? } ?>
        </div>
    </div>
</div>

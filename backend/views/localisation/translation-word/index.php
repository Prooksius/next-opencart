<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = YII::t('app', 'Translations');
$this->params['breadcrumbs'][] = $this->title;

$jsCode = <<<JS
    $('.batch-delete').on('click', function () {
        let keys = $('#grid').yiiGridView('getSelectedRows').join(',');
        if (keys && confirm('Вы уверены, что хотите удалить выбранные записи?')) {
            location.href = '/admin/localisation/translation-word/batchdelete?selection='+keys;
        }
    }); 
    $('.batch-clone').on('click', function () {
        let keys = $('#grid').yiiGridView('getSelectedRows').join(',');
        if (keys && confirm('Вы уверены, что хотите клонировать выбранные записи?')) {
            location.href = '/admin/localisation/translation-word/clone?selection='+keys;
        }
    }); 
JS;
$this->registerJs($jsCode, \yii\web\View::POS_READY);
?>
<div class="speciality-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php  $search = Yii::$app->request->get();
        $search = $search['TranslateWordSearch'];
        $create_opts = ['create'];
        if (isset($search['translate_group_id']) && $search['translate_group_id']) {
            $create_opts['translate_group_id'] = $search['translate_group_id'];
        }
        ?>
        <?= Html::a(YII::t('app', 'Add translation'), $create_opts, ['class' => 'btn btn-success']) ?>
        
        <?= Html::a(YII::t('app', 'Clone selected'), false, ['class' => 'btn btn-warning batch-clone']) ?>
        <?= Html::a(YII::t('app', 'Delete selected'), false, ['class' => 'btn btn-danger batch-delete']) ?>
    </p>
    <div class="table-responsive">
    <?= GridView::widget([
        'id' => 'grid',
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($data) {
                    return ['value' => $data->id];
                }
            ],
            [
                'attribute' => 'phrase',
                'value' => function ($data) {
                    return html::a($data->phrase, ['update', 'id' => $data->id], ['data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'group',
                'value' => function ($data) {
                    return html::a($data->group, ['/localisation/translate-group/update', 'id' => $data->translate_group_id], ['data-pjax' => 0]);
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'name',
                'headerOptions' => ['class' => 'text-left wrapped'],
                'contentOptions' => ['class' => 'text-left wrapped'],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
        ],
    ]); ?>
    </div>
</div>

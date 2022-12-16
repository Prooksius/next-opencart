<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SefSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'СЕО-ссылки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sef-index">

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Новая СЕО-ссылка', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
 //       'filterModel' => $searchModel,
        'columns' => [
            'id',
            'link',
            'link_sef',
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}&nbsp;&nbsp;&nbsp;{delete}',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
            ],
        ],
    ]); ?>
</div>

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div style="display:none" class="fancybox-hidden">
    <div class="callback-form fancybox-popup" id="customer-choose-form">

        <?php Pjax::begin(); ?>
        <?php echo $this->render('_search', ['model' => $searchModel]); ?>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'username',
                'first_name',
                [
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        return date('d.m.Y г. в G:i', $model->created_at);
                    },
                ],
                'email',
                //'status',
                //'first_name',
                //'last_name',
                'phone',
                //'telegram',
                //'user_type',
                //'ref_link',
                //'ref_discount',
                //'promocode',
                //'promo_discount',
                //'updated_at',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>

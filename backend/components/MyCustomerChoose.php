<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 21.04.2020
 * Time: 8:11
 */

namespace backend\components;

use Yii;
use yii\bootstrap\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\base\Exception;
use yii\helpers\Url;
use app\models\CustomerChoose;
use common\models\Customer;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\widgets\Pjax;
use himiklab\thumbnail\EasyThumbnailImage;

class MyCustomerChoose extends Widget
{
    private const CUST_NO_PICTURE = '/backend/components/no_avatar.png';
    public $model;
    public $attribute;

    public function run() {
        $this->getView()->registerJs("
            var no_pic = '".self::CUST_NO_PICTURE."';
            $(document).delegate('#".$this->id." .clear-person-btn', 'click', function() {
                console.log(222)
                $('#".$this->id." .customer-name').text('');
                $('#".$this->id."-hinput').val('');
                $('#".$this->id."-picture').attr('src', no_pic);
            });
            $(document).on('afterShow.fb', function( e, instance, slide ) {
              $('#".$this->id."-form input[name=\"CustomerChoose[username]\"]').focus();
            });
            $(document).delegate('#".$this->id."-form .choose-customer-btn', 'click', function() {
                var sel_id = $(this).closest('tr').attr('data-key');
                var sel_username = $(this).closest('tr').find('td:nth-child(2)').text();
                var sel_userpic = $(this).closest('tr').find('td:nth-child(1) img').attr('src');
                $('#".$this->id." .customer-name').text(sel_username);
                $('#".$this->id."-hinput').val(sel_id);
                $('#".$this->id."-picture').attr('src', sel_userpic);
                $.fancybox.close();
            });
        ");
        $id_attr = $this->attribute;
        $customer_id = $this->model->$id_attr;
        $customer_model = Customer::findOne($customer_id);
        if (!$customer_model->picture) {
            $pict = self::CUST_NO_PICTURE;
        } else {
            $pict = $customer_model->picture;
        }

        $searchModel = new CustomerChoose();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false; ?>
        <div class="customer-choose-widget" id="<?= $this->id; ?>">
            <div class="customer-choose-widget__cont">
                <div class="customer-choose-widget__info">
					<?php if ($pict == self::CUST_NO_PICTURE) { ?>
						<?= Html::img($pict, ['id' => $this->id . '-picture', 'class' => 'img-responsive', 'style' => ['max-width' => '34px', 'border-radius' => '50%']]); ?>
					<?php } else { ?>
						<?=  EasyThumbnailImage::thumbnailImg(
							'@root' . $pict,
							50,
							50,
							EasyThumbnailImage::THUMBNAIL_INSET,
							['id' =>  $this->id . '-picture', 'class' => 'img-responsive', 'style' => ['max-width' => '34px', 'border-radius' => '50%']]
						) ?>
					<?php } ?>
					<span class="customer-name"><?= ($customer_model->username ? $customer_model->username : '- пусто -'); ?></span>
                </div>
                <div class="customer-choose-widget__btns">
                    <?= Html::a('<i class="fa fa-user-circle-o" aria-hidden="true"></i>', '#'.$this->id.'-form', ['title' => 'Выбрать', 'class' => 'btn btn-success', ' data-options' => '{"touch" : false}', 'data-fancybox' => '']) ?>
                    <?= Html::button('<i class="fa fa-eraser" aria-hidden="true"></i>', ['title' => 'Очистить', 'class' => 'btn btn-warning clear-person-btn']) ?>
                </div>
            </div>
            <?= html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->id . '-hinput']); ?>
            <div style="display:none" class="fancybox-hidden">
                <div class="callback-form fancybox-popup" id="<?= $this->id; ?>-form">

                    <?php Pjax::begin(['enablePushState' => false]); ?>
                    <h3 class="text-center">Выберите пользователя</h3>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'label' => 'Фото',
                                'headerOptions' => ['class' => 'text-left'],
                                'contentOptions' => ['class' => 'text-left'],
                                'value' => function ($data) {
                                    if ($data->picture) {
                                        return EasyThumbnailImage::thumbnailImg(
                                            '@root' . $data->picture,
                                            50,
                                            50,
                                            EasyThumbnailImage::THUMBNAIL_INSET,
                                            ['class' => 'img-responsive', 'style' => ['max-width' => '34px', 'border-radius' => '50%']]
                                        );
                                    } else {
                                        return Html::img('/backend/components/no_avatar.png', ['class' => 'img-responsive', 'style' => ['max-width' => '34px']]);
                                    }
                                },
                                'format' => 'raw',
                            ],
                            'username',
                            [
                                'attribute' => 'first_name',
                                'contentOptions' => ['style' => 'white-space: normal'],
                                'filter' => false,
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => function ($model) {
                                    return date('d.m.Y г. в G:i', $model->created_at);
                                },
                                'filter' => false,
                            ],
                            'email',
                            'phone',
                            [
                                'class' => ActionColumn::className(),
                                'template' => '<button type="button" class="btn btn-success choose-customer-btn" title="Выбрать"><i class="fa fa-check" aria-hidden="true"></i></button>',
                            ],
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div><?
    }
}
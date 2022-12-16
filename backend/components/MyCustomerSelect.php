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

class MyCustomerSelect extends Widget
{
    const CUST_NO_PICTURE = '/upload/image/no_avatar.png';
    public $model;
    public $attribute;
    public $ids = [];

    public function run() {
        $this->getView()->registerJs("
            var no_pic = '".self::CUST_NO_PICTURE."';
            $(document).delegate('#".$this->id." .clear-person-btn', 'click', function() {
                $('#".$this->id." .selections').html('<div><span class=\"customer-name\">Выберите клиентов</span></div>');
                $('#".$this->id."-hinput').val('');
                $('#".$this->id."-picture').attr('src', no_pic);
            });
            $(document).delegate('#".$this->id."-form .choose-customer-btn', 'click', function() {
                var sel_id = $(this).closest('tr').attr('data-key');
                var sel_username = $(this).closest('tr').find('td:nth-child(2)').text();
                var selections = $('#".$this->id."-hinput').val();
                if (!selections) {
                    $('#".$this->id." .selections').html('');
                }
                if (selections) {
                    selections = selections.split(',');
                } else {
                    selections = [];
                }
                selections.push(sel_id);
                $('<div><span class=\"delete\" data-id=\"' + sel_id + '\"><i class=\"fa fa-minus-square\" aria-hidden=\"true\"></i></span> <span class=\"customer-name\">' + sel_username + '</span></div>').appendTo($('#".$this->id." .selections'));
                $('#".$this->id."-hinput').val(selections.join(','));
                $.fancybox.close();
            });
            $(document).delegate('#".$this->id." .selections .delete', 'click', function() {
                var sel_id = $(this).attr('data-id');
                var selections = $('#".$this->id."-hinput').val();
                selections = selections.split(',');
                selections.splice(selections.indexOf(sel_id), 1);
                $('#".$this->id."-hinput').val(selections.join(','));
                $(this).closest('div').remove();
                console.log($('#".$this->id." .selections').length);
                if (!$('#".$this->id." .selections > div').length) {
                    $('#".$this->id." .selections').html('<div><span class=\"customer-name\">Выберите клиентов</span></div>');
                }
            });
        ");
        $id_attr = $this->attribute;
        $customer_ids = $this->model->$id_attr;
        if ($customer_ids) {
            $this->ids = explode(',', $customer_ids);
            $customers_query = Customer::find()
                ->where(['id' => $this->ids])
                ->all();
        } else {
            $this->ids = [];
        }
        $searchModel = new CustomerChoose();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false; ?>
        <div class="customer-choose-widget" id="<?= $this->id; ?>">
            <div style="display: flex; align-items: flex-start">
                <div class="selections">
                    <?php if (!empty($this->ids)) { ?>
                    <?php foreach ($customers_query as $customer) { ?>
                        <div><span class="delete" data-id="<?= $customer->id ?>"><i class="fa fa-minus-square" aria-hidden="true"></i></span> <span class="customer-name"><?= ($customer->username ? $customer->username : ''); ?></span></div>
                    <?php } ?>
                    <?php } else { ?>
                        <div><span class="customer-name">Выберите клиентов</span></div>
                    <?php } ?>
                </div>
                <div>
                    <?= Html::a('<i class="fa fa-users" aria-hidden="true"></i>', '#'.$this->id.'-form', ['title' => 'Выбрать', 'class' => 'btn btn-success', ' data-options' => '{"touch" : false}', 'data-fancybox' => '']) ?>
                    <?= Html::button('<i class="fa fa-eraser" aria-hidden="true"></i>', ['title' => 'Очистить', 'class' => 'btn btn-warning clear-person-btn']) ?>
                </div>
            </div>
            <?= html::activeHiddenInput($this->model, $this->attribute, ['id' => $this->id . '-hinput']); ?>
            <div style="display:none" class="fancybox-hidden">
                <div class="callback-form fancybox-popup" id="<?= $this->id; ?>-form">

                    <?php Pjax::begin(['enablePushState' => false]); ?>
                    <h3 class="text-center">Выберите <?= (isset($this->type) ? ($this->type == Customer::CUSTOMER_MANAGER ? 'менеджера' : 'клиента') : 'пользователя') ?></h3>
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
                                            ['class' => 'img-responsive', 'style' => ['max-width' => '34px', 'border-radius' => '50%', 'margin-right' => '10px']]
                                        );
                                    } else {
                                        return Html::img('/upload/image/no_avatar.png', ['class' => 'img-responsive', 'style' => ['max-width' => '34px']]);
                                    }
                                },
                                'format' => 'raw',
                            ],
                            'username',
                            [
                                'attribute' => 'first_name',
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
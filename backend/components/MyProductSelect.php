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
use app\models\ProductChoose;
use app\models\Product;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\widgets\Pjax;
use himiklab\thumbnail\EasyThumbnailImage;
use yii\grid\CheckboxColumn;
use yii\helpers\StringHelper;

class MyProductSelect extends Widget
{
    const PRODUCT_NO_PICTURE = '/upload/image/placeholder.png';
    public $model;
    public $name;
    private $modelClass;
    public $attribute;
    public $exclude_current = false;
    public $selected = [];
    public $ids = [];

    public function run() {

        $this->id = str_replace(['-', ' '], '_', $this->id);
        if ($this->model) {
          $this->modelClass = StringHelper::basename(get_class($this->model));
          $inputText = "<input type=\"hidden\" name=\"' + modelClass_" . $this->id . " + '[' + attributeName_" . $this->id . " + '][]' + '\" value=\"' + record.id + '\" />";
          $id_attr = $this->attribute;
          $product_ids = $this->model->$id_attr;
        } else {
          $inputText = "<input type=\"hidden\" name=\"" . $this->name . "[]" . "\" value=\"' + record.id + '\" />";
          $product_ids = $this->selected;
          if (!$product_ids) $product_ids = [];
        }
        if ($product_ids) {
            $this->ids = $product_ids;
            $products_list = Product::find()
                ->alias('p')
                ->select([
                  'p.id', 'p.image', 'pd.name'
                ])
                ->leftJoin('oc_product_description pd', '(pd.product_id = p.id AND pd.language_id = "' . \Yii::$app->language . '")')
                ->where(['p.id' => $this->ids])
                ->all();
        } else {
            $products_list = [];
            $this->ids = [];
        }

        if ($this->exclude_current && $this->model) {
          $cur_product_id = $this->model->id;
        } else {
          $cur_product_id = 0;
        }

        $this->getView()->registerJs("
            var no_pic_" . $this->id . " = '".self::PRODUCT_NO_PICTURE."';
            var modelClass_" . $this->id . " = '".$this->modelClass."';
            var attributeName_" . $this->id . " = '".$this->attribute."';
            var productIds_" . $this->id . " = ".json_encode($this->ids).";
            function setGridSelections_" . $this->id . "() {
              $('#" . $this->id . "-selection-grid tbody tr').each(function() {
                const id_str = $(this).attr('data-key')
                const id = parseInt($(this).attr('data-key'))
                if (productIds_" . $this->id . ".includes(id) || productIds_" . $this->id . ".includes(id_str)) {
                  $(this).find('input[name=\"selection[]\"]').prop('checked', true)
                } else {
                  $(this).find('input[name=\"selection[]\"]').prop('checked', false)
                }
              })
            }

            $(document).delegate('#".$this->id." .clear-person-btn', 'click', function() {
                $('#".$this->id." table.selections').html('');
                productIds_" . $this->id . " = []

                setGridSelections_" . $this->id . "()
            });
            $(document).delegate('#".$this->id." .delete', 'click', function() {
              const id = parseInt($(this).attr('data-id'))
              const index = productIds_" . $this->id . ".indexOf(id);
              if (index !== -1) {
                productIds_" . $this->id . ".splice(index, 1);
              }

              $(this).closest('tr').remove();
              $('#".$this->id." div.selections').html('<div><span class=\"customer-name\">' + (productIds_" . $this->id . ".length > 0 ? 'Выбрано товаров: ' + productIds_" . $this->id . ".length : 'Выберите товары') + '</span></div>');

              setGridSelections_" . $this->id . "()
            });
            $(document).delegate('#".$this->id."-form .select-chosen-products-btn', 'click', function() {
                $('#".$this->id." table.selections').html('');
                productIds_" . $this->id . " = []

                let allRows = $('#" . $this->id . "-selection-grid input[name=\"selection[]\"]');
                allRows.each(function() {
                  const record = JSON.parse($(this).val())
                  if ($(this).prop('checked')) {
                    $('<tr data-key=\"' + record.id + '\">\
                        <td>".$inputText."</td>\
                        <td class=\"delete\" data-id=\"' + record.id + '\" title=\"Удалить\"><i class=\"fa fa-minus-square\" aria-hidden=\"true\"></i></td>\
                        <td class=\"product-image\">' + record.image + '</td>\
                        <td class=\"product-name\">' + record.name + '</td>\
                      </tr>').appendTo($('#".$this->id." table.selections'));

                    productIds_" . $this->id . ".push(record.id);  
                  }
                })
                $('#".$this->id." div.selections').html('<div><span class=\"customer-name\">' + (productIds_" . $this->id . ".length > 0 ? 'Выбрано товаров: ' + productIds_" . $this->id . ".length : 'Выберите товары') + '</span></div>');

                setGridSelections_" . $this->id . "()

              $.fancybox.close();
            });
            $(document).on('pjax:complete', function() {
              console.log('pjax completed')
              setGridSelections_" . $this->id . "();
            })

            setGridSelections_" . $this->id . "();
        ");
        $searchModel = new ProductChoose();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $cur_product_id);
        $dataProvider->pagination = false; ?>
        <div class="product-select-widget" id="<?= $this->id; ?>">
            <div style="display: flex; align-items: flex-start; border: 1px solid #ddd">
                <div class="selections">
                    <div><span class="customer-name"><?= count($product_ids) > 0 ? 'Выбрано товаров: ' . count($product_ids): 'Выберите товары' ?></span></div>
                </div>
                <div>
                    <?= Html::a('<i class="fa fa-hand-pointer-o" aria-hidden="true"></i>', '#'.$this->id.'-form', ['title' => 'Выбрать', 'class' => 'btn btn-success', ' data-options' => '{"touch" : false}', 'data-fancybox' => '']) ?>
                    <?= Html::button('<i class="fa fa-eraser" aria-hidden="true"></i>', ['title' => 'Очистить', 'class' => 'btn btn-warning clear-person-btn']) ?>
                </div>
            </div>
            <br />
            <div class="form-group well-group">
              <div class="well well-sm" style="min-height: 150px;max-height: 500px;overflow: auto;">
                <table class="selections">
                  <tbody>
                  <?php foreach ($products_list as $product) { ?>
                    <tr data-key="<?= $product->id ?>">
                      <td>
                        <?php if ($this->model) { ?>
                        <?= Html::hiddenInput(StringHelper::basename(get_class($this->model)) . '[' . $this->attribute . '][]', $product->id) ?>
                        <?php } else { ?>
                        <?= Html::hiddenInput($this->name . '[]', $product->id) ?>
                        <?php } ?>
                      </td>  
                      <td class="delete" data-id="<?= $product->id ?>" title="Удалить">  
                        <i class="fa fa-minus-square" aria-hidden="true"></i>
                      </td>
                      <td class="product-image">
                          <?= EasyThumbnailImage::thumbnailImg(
                              '@root' . $product->image,
                              40,
                              40,
                              EasyThumbnailImage::THUMBNAIL_INSET,
                              ['class' => 'img-thumbnail', 'style' => ['max-width' => '34px', 'border-radius' => '5px', 'margin-right' => '10px']]
                          );?>
                      </td>
                      <td class="product-name">
                        <?= $product->name ?>
                      </td>
                    </tr>
                  <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
            <div style="display:none" class="fancybox-hidden">
                <div class="callback-form fancybox-popup" id="<?= $this->id; ?>-form">

                    <?php Pjax::begin(['enablePushState' => false]); ?>
                    <h3 class="text-center">Выберите товары
                      <button style="float: right" class="btn btn-success select-chosen-products-btn" type="button">Выбрать</button>
                    </h3>
                    <?= GridView::widget([
                        'id' => $this->id . '-selection-grid',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'label' => 'Картинка',
                                'headerOptions' => ['class' => 'text-left'],
                                'contentOptions' => ['class' => 'text-left'],
                                'value' => function ($data) {
                                    if ($data->image) {
                                        return EasyThumbnailImage::thumbnailImg(
                                            '@root' . $data->image,
                                            40,
                                            40,
                                            EasyThumbnailImage::THUMBNAIL_INSET,
                                            ['class' => 'img-thumbnail', 'style' => ['max-width' => '34px', 'padding' => '0', 'border-radius' => '5px', 'margin-right' => '10px']]
                                        );
                                    } else {
                                        return Html::img(self::PRODUCT_NO_PICTURE, ['class' => 'img-thumbnail', 'style' => ['max-width' => '34px', 'padding' => '0', 'border-radius' => '5px', 'margin-right' => '10px']]);
                                    }
                                },
                                'format' => 'raw',
                            ],
                            'name',
                            [
                              'attribute' => 'category_id',
                              'filter' => $this->model->categoriesTree,
                              'value' => function ($data) {
                                return $data->main_cat_name;
                              },
                            ],
                            'model',
                            'price',
                            'quantity',
                            [
                                'class' => CheckboxColumn::className(),
                                'checkboxOptions' => function ($model, $key, $index, $column) {
                                  return [
                                    'value' => json_encode([
                                      'id' => $model->id, 
                                      'name' => $model->name, 
                                      'image' => $model->image ? EasyThumbnailImage::thumbnailImg(
                                          '@root' . $model->image,
                                          40,
                                          40,
                                          EasyThumbnailImage::THUMBNAIL_INSET,
                                          ['class' => 'img-thumbnail', 'style' => ['max-width' => '34px', 'border-radius' => '5px', 'margin-right' => '10px']]
                                      ) : Html::img(self::PRODUCT_NO_PICTURE, ['class' => 'img-thumbnail', 'style' => ['max-width' => '34px', 'border-radius' => '5px', 'margin-right' => '10px']]),
                                    ])
                                  ];
                                }
                            ],
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div><?
    }
}
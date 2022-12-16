<?php

namespace frontend\models;

use Yii;
use common\models\ProductOptionValue as ModelsProductOptionValue;

/**
 * This is the model class for table "oc_product_option_value".
 */
class ProductOptionValue extends ModelsProductOptionValue
{
  
  public $optionValueName;
  public $optionValueColor;
  public $optionValueAlias;

}

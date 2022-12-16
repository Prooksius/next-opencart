<?php
  
namespace frontend\components;

use frontend\models\Cart;
use frontend\models\ModuleGroup;
use frontend\models\Product;
use frontend\models\ProductDiscount;
use frontend\models\ProductOption;
use frontend\models\ProductOptionValue;
use frontend\models\ProductSpecial;
use frontend\models\Translation;
use Yii;
use yii\base\Component;
use himiklab\thumbnail\EasyThumbnailImage;
use yii\helpers\Json;

class AppCart extends Component {

  private $_customer = [];
  private $_address = [];


  public function getCustomer()
  {
    return $this->_customer;
  }

  public function setCustomer($customer)
  {
    $this->_customer = $customer ? $customer : [];
  }

  public function getAddress()
  {
    return $this->_address;
  }

  public function setAddress($address)
  {
    $this->_address = $address ? $address : [];
  }

  public function Init()
  {

    $period = (int)Yii::$app->shopConfig->getParam('cart_preserve');

    if (!$period) {
      $period = 1;
    }

    Cart::deleteAll('customer_id = 0 AND FROM_UNIXTIME(created_at) < DATE_SUB(NOW(), INTERVAL ' . (int)$period . ' HOUR)');

    if (Yii::$app->user->id) {
      Cart::updateAll(['session_id' => Yii::$app->shopConfig->getParam('session_id')], 'customer_id = ' . (int)Yii::$app->user->id);
      
      $my_guest_cart_items = Cart::find()
        ->where(['customer_id' => 0, 'session_id' => Yii::$app->shopConfig->getParam('session_id')])
        ->all();

      foreach ($my_guest_cart_items as $cart) {
        $product_id = $cart->product_id;
        $quantity = $cart->quantity;
        $option = json_decode($cart->option);

        $cart->delete();

        $this->_add($product_id, $quantity, $option);
      }
    }
  }

  private function _getProducts()
  {
    $product_data = [];

    $cart_products = Cart::find()
      ->where([
        'customer_id' => (int)Yii::$app->user->id, 
        'session_id' => Yii::$app->shopConfig->getParam('session_id'),
      ])
      ->all();

    foreach ($cart_products as $cart) {
      $product = Product::find()
        ->alias('p')
        ->select([
          'p.*',
          'pd.name AS name'
        ])
        ->leftJoin('oc_product_description pd', 'pd.product_id = p.id AND pd.language_id = "' . \Yii::$app->language . '"')
        ->where([
          'p.id' => $cart->product_id,
          'p.status' => 1,
        ])
        ->andWhere('p.date_available <= ' . time())
        ->one();

      if (($product instanceof Product) && ((int)$cart->quantity > 0)) {

				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = [];

        foreach (json_decode($cart->option) as $product_option_id => $value) {

          $product_option = ProductOption::find()
            ->alias('po')
            ->select([
              'po.id',
              'po.option_id',
              'od.name AS optionName',
              'o.type AS optionType',
            ])
            ->leftJoin('oc_option o', 'o.id = po.option_id')
            ->leftJoin('oc_option_description od', 'od.option_id = o.id')
            ->where([
              'po.id' => (int)$product_option_id,
              'po.product_id' => (int)$cart->product_id,
              'od.language_id' => Yii::$app->language
            ])
            ->one();
          
          if ($product_option instanceof ProductOption) {

            if ($product_option->optionType == 'select' || $product_option->optionType == 'radio') {

              $product_option_value = ProductOptionValue::find()
                ->alias('pov')
                ->select([
                  'pov.option_value_id', 
                  'ovd.name AS optionValueName', 
                  'pov.quantity', 
                  'pov.subtract', 
                  'pov.price', 
                  'pov.price_prefix', 
                  'pov.points', 
                  'pov.points_prefix', 
                  'pov.weight', 
                  'pov.weight_prefix'
                ])
                ->leftJoin('oc_option_value ov', 'ov.id = pov.option_value_id')
                ->leftJoin('oc_option_value_description ovd', 'ovd.option_value_id = ov.id')
                ->where([
                  'pov.id' => (int)$value,
                  'pov.product_option_id' => (int)$product_option_id,
                  'ovd.language_id' => Yii::$app->language,
                ])
                ->one();

              if ($product_option_value instanceof ProductOptionValue) {
                
                if ($product_option_value->price_prefix == '+') {
                  $option_price += (float)$product_option_value->price;
                } elseif ($product_option_value->price_prefix == '-') {
                  $option_price -= (float)$product_option_value->price;
                }

                if ($product_option_value->points_prefix == '+') {
                  $option_points += (float)$product_option_value->points;
                } elseif ($product_option_value->points_prefix == '-') {
                  $option_points -= (float)$product_option_value->points;
                }

                if ($product_option_value->weight_prefix == '+') {
                  $option_weight += (float)$product_option_value->weight;
                } elseif ($product_option_value->weight_prefix == '-') {
                  $option_weight -= (float)$product_option_value->weight;
                }

                if ((int)$product_option_value->subtract && (!(int)$product_option_value->quantity || ((int)$product_option_value->quantity < (int)$cart->quantity))) {
                  $stock = false;
                }

                $option_data[] = [
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $product_option->id,
									'option_value_id'         => $product_option_value->option_value_id,
									'name'                    => $product_option->optionName,
									'value'                   => $product_option_value->optionValueName,
									'type'                    => $product_option->optionType,
									'quantity'                => $product_option_value->quantity,
									'subtract'                => $product_option_value->subtract,
									'price'                   => $product_option_value->price,
									'price_prefix'            => $product_option_value->price_prefix,
									'points'                  => $product_option_value->points,
									'points_prefix'           => $product_option_value->points_prefix,
									'weight'                  => $product_option_value->weight,
									'weight_prefix'           => $product_option_value->weight_prefix
                ];
              }
            
            } elseif ($product_option->optionType == 'checkbox' && is_array($value)) {

              foreach ($value as $product_option_value_id) {

                $product_option_value = ProductOptionValue::find()
                  ->alias('pov')
                  ->select([
                    'pov.option_value_id', 
                    'ovd.name AS optionValueName', 
                    'pov.quantity', 
                    'pov.subtract', 
                    'pov.price', 
                    'pov.price_prefix', 
                    'pov.points', 
                    'pov.points_prefix', 
                    'pov.weight', 
                    'pov.weight_prefix'
                  ])
                  ->leftJoin('oc_option_value_description ovd', 'ovd.option_value_id = pov.option_value_id')
                  ->where([
                    'pov.id' => (int)$product_option_value_id,
                    'pov.product_option_id' => (int)$product_option_id,
                    'ovd.language_id' => Yii::$app->language,
                  ])
                  ->one();

                if ($product_option_value instanceof ProductOptionValue) {
                  
                  if ($product_option_value->price_prefix == '+') {
                    $option_price += (float)$product_option_value->price;
                  } elseif ($product_option_value->price_prefix == '-') {
                    $option_price -= (float)$product_option_value->price;
                  }

                  if ($product_option_value->points_prefix == '+') {
                    $option_points += (float)$product_option_value->points;
                  } elseif ($product_option_value->points_prefix == '-') {
                    $option_points -= (float)$product_option_value->points;
                  }

                  if ($product_option_value->weight_prefix == '+') {
                    $option_weight += (float)$product_option_value->weight;
                  } elseif ($product_option_value->weight_prefix == '-') {
                    $option_weight -= (float)$product_option_value->weight;
                  }

                  if ((int)$product_option_value->subtract && (!(int)$product_option_value->quantity || ((int)$product_option_value->quantity < (int)$cart->quantity))) {
                    $stock = false;
                  }

                  $option_data[] = [
                    'product_option_id'       => $product_option_id,
                    'product_option_value_id' => $product_option_value_id,
                    'option_id'               => $product_option->id,
                    'option_value_id'         => $product_option_value->option_value_id,
                    'name'                    => $product_option->optionName,
                    'value'                   => $product_option_value->optionValueName,
                    'type'                    => $product_option->optionType,
                    'quantity'                => $product_option_value->quantity,
                    'subtract'                => $product_option_value->subtract,
                    'price'                   => $product_option_value->price,
                    'price_prefix'            => $product_option_value->price_prefix,
                    'points'                  => $product_option_value->points,
                    'points_prefix'           => $product_option_value->points_prefix,
                    'weight'                  => $product_option_value->weight,
                    'weight_prefix'           => $product_option_value->weight_prefix
                  ];
                }
              }

            } else {

              $option_data[] = [
                'product_option_id'       => $product_option_id,
                'product_option_value_id' => '',
                'option_id'               => $product_option->id,
                'option_value_id'         => '',
                'name'                    => $product_option->optionName,
                'value'                   => $value,
                'type'                    => $product_option->optionType,
                'quantity'                => '',
                'subtract'                => '',
                'price'                   => '',
                'price_prefix'            => '',
                'points'                  => '',
                'points_prefix'           => '',
                'weight'                  => '',
                'weight_prefix'           => '',
              ];
            }
          }
        }

        $price = (float)$product->price;

        // Product Discounts
        $discount_quantity = 0;
        
        foreach ($cart_products as $cart_2) {
          if ($cart_2->product_id == $cart->product_id) {
            $discount_quantity += (int)$cart_2->quantity;
          }
        }

        $discount = ProductDiscount::find()
          ->select('price')
          ->where([
            'product_id' => (int)$cart->product_id,
            'customer_group_id' => (int)Yii::$app->shopConfig->getParam('customer_group_id'),
          ])
          ->andWhere('quantity <= ' . (int)$discount_quantity)
          ->andWhere('(date_start = 0 OR date_start < ' . time() . ') AND (date_end  = 0 OR date_end > ' . time() . ')')
          ->orderBy([
            'quantity' => SORT_DESC,
            'priority' => SORT_ASC,
            'price' => SORT_ASC,
          ])
          ->limit(1)
          ->one();
        
        if ($discount instanceof ProductDiscount) {
          $price = (float)$discount->price;
        }

        // Product Specials
        $special = ProductSpecial::find()
          ->select('price')
          ->where([
            'product_id' => (int)$cart->product_id,
            'customer_group_id' => (int)Yii::$app->shopConfig->getParam('customer_group_id'),
          ])
          ->andWhere('(date_start = 0 OR date_start < ' . time() . ') AND (date_end  = 0 OR date_end > ' . time() . ')')
          ->orderBy([
            'priority' => SORT_ASC,
            'price' => SORT_ASC,
          ])
          ->limit(1)
          ->one();

        if ($special instanceof ProductSpecial) {
          $price = (float)$special->price;
        }

        // Stock
        if (!(int)$product->quantity || ((int)$product->quantity < (int)$cart->quantity)) {
          $stock = false;
        }

        $product_data[] = [
          'cart_id'         => $cart->id,
          'product_id'      => $product->id,
          'alias'           => $product->alias,
          'name'            => $product->name,
          'short_name'      => $product->short_name,
          'model'           => $product->model,
          'shipping'        => $product->shipping,
          'image'           => $product->image,
          'option'          => $option_data,
          'quantity'        => $cart->quantity,
          'minimum'         => $product->minimum,
          'subtract'        => $product->subtract,
          'stock'           => $stock,
          'price'           => ($price + $option_price),
          'total'           => ($price + $option_price) * $cart->quantity,
          'points'          => ($product->points ? ($product->points + $option_points) * $cart->quantity : 0),
          'weight'          => ($product->weight + $option_weight) * $cart->quantity,
          'weight_class_id' => $product->weight_class_id,
          'length'          => $product->length,
          'width'           => $product->width,
          'height'          => $product->height,
          'length_class_id' => $product->length_class_id,
        ];


      } else {
        $this->remove($cart->id);
      }
    }

    return $product_data;
  }

  private function _add($product_id, $quantity, $option = [])
  {
    $cart_id = 0;

    $totals = Cart::find()
      ->where([
        'customer_id' => (int)Yii::$app->user->id, 
        'session_id' => Yii::$app->shopConfig->getParam('session_id'),
        'product_id' => (int)$product_id,
        'option' => json_encode($option)
      ])
      ->count();
    
    if (!(int)$totals) {
      $new_item = new Cart();
      $new_item->customer_id = (int)Yii::$app->user->id;
      $new_item->session_id = Yii::$app->shopConfig->getParam('session_id');
      $new_item->product_id = (int)$product_id;
      $new_item->quantity = (int)$quantity;
      $new_item->option = json_encode($option);
      $new_item->save();
      $new_item->refresh();

      if ($new_item->errors) {
//        var_dump($new_item->errors);
      }

      $cart_id = $new_item->id;

    } else {
      $present = Cart::find()
        ->where([
          'customer_id' => (int)Yii::$app->user->id, 
          'session_id' => Yii::$app->shopConfig->getParam('session_id'),
          'product_id' => (int)$product_id,
          'option' => json_encode($option)
        ])
        ->limit(1)
        ->one();

      $present->quantity = (int)$present->quantity + (int)$quantity;
      $present->save();

      $cart_id = $present->id;
    }

    return $cart_id;
  }

  private function _update($cart_id, $quantity)
  {
    $present = Cart::findOne(['id' => (int)$cart_id]);
    if ($present instanceof Cart) {
      $present->quantity = (int)$quantity;
      $present->save();
    }
  }

  private function _remove($cart_id)
  {
    $present = Cart::findOne(['id' => (int)$cart_id]);
    if ($present instanceof Cart) {
      $present->delete();
    }
  }

  private function _clear()
  {
    Cart::deleteAll('customer_id = ' . (int)Yii::$app->user->id . ' AND session_id = ' . Yii::$app->db->quoteValue(Yii::$app->shopConfig->getParam('session_id')));
  }

	public function getProducts() 
  {
	
    $products = [];
    $total = 0;
    $count = 0;
    $weight = 0;
    $stock = true;
    $shipping = false;

    if (Yii::$app->shopConfig->getParam('session_id')) {

      foreach ($this->_getProducts() as $product) {
        if ($product['image']) {
          $thumb = EasyThumbnailImage::thumbnailFileUrl(
            '@root' . ($product['image'] ? $product['image'] : '/upload/image/placeholder.png'),
            (int)Yii::$app->shopConfig->getParam('thumbs_cart_width'),
            (int)Yii::$app->shopConfig->getParam('thumbs_cart_height'),
            EasyThumbnailImage::THUMBNAIL_INSET
          );
        }
        $option_data = [];

        foreach ($product['option'] as $option) {
          $option_data[] = [
            'name'  => $option['name'],
            'value' => $option['value'],
            'type'  => $option['type']
          ];
        }

        $count += (int)$product['quantity'];
        $total += (float)$product['total'];

        if ($product['shipping']) {
          $weight += Yii::$app->weight->convert($product['weight'], $product['weight_class_id'], (int)Yii::$app->shopConfig->getParam('weight_class_id'));
        }

        if (!$product['stock']) {
          $stock = false;
        }
        if ($product['shipping']) {
          $shipping = true;
        }

        $products[] = [
          'cart_id'     => $product['cart_id'],
          'product_id'  => $product['product_id'],
          'thumb'       => $thumb,
          'name'        => $product['name'],
          'model'       => $product['model'],
          'option'      => $option_data,
          'quantity'    => (int)$product['quantity'],
          'price'       => (float)$product['price'],
          'price_str'   => Yii::$app->currency->format((float)$product['price'], Yii::$app->currency->getCurrent()),
          'total'       => (float)$product['total'],
          'total_str'   => Yii::$app->currency->format((float)$product['total'], Yii::$app->currency->getCurrent()),
          'alias'       => $product['alias'],
        ];
      }
		}
    
		return [
      'products'    => $products,
      'total'       => $total,
      'total_str'   => Yii::$app->currency->format((float)$total, Yii::$app->currency->getCurrent()),
      'count'       => $count,
      'weight'      => $weight,
      'weight_str'  => Yii::$app->weight->format($weight, (int)Yii::$app->shopConfig->getParam('weight_class_id')),
      'stock'       => $stock,
      'shipping'    => $shipping,
    ];
	}

  public function add($product_id, $quantity, $option)
  {
    $result = [
      'result' => 'success',
    ];
  
    if (!Yii::$app->shopConfig->getParam('session_id')) {
      $result['result'] = 'error';
      $result['error']['product'] = Yii::t('app', 'session_id not specified');
      return $result;
    }

    if (!$product_id) {
      $result['result'] = 'error';
      $result['error']['product'] = Translation::getTranslation('CartProductNotFound');
      return $result;
    }

    $product = Product::getProduct($product_id);
    if (!$product) {
      $result['result'] = 'error';
      $result['error']['product'] = Translation::getTranslation('CartProductNotFound');
      return $result;
    }
    if ((int)$quantity && (int)$quantity >= $product['minimum']) {
      $quantity = (int)$quantity;
    } else {
      $quantity = $product['minimum'] ? $product['minimum'] : 1;
    }

    if ($option) {
      $option = array_filter($option);
    } else {
      $option = [];
    }

    $product_options = ProductOption::getProductOptions($product_id);

    foreach ($product_options as $product_option) {
      if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
        $result['result'] = 'error';
        $result['error']['option'][$product_option['product_option_id']] = sprintf(Translation::getTranslation('CartOptionIsRequired'), $product_option['name']);
      }
    }
    if ($result['result'] == 'error') {
      $result['message'] = sprintf(Translation::getTranslation('CartProductAddingError'), $product['name']);
      return $result;
    }

    $this->_add($product_id, $quantity, $option);

    $result['cart'] = $this->getProducts();
    $result['message'] = sprintf(Translation::getTranslation('CartProductAddedToCart'), $product['name']);
    return $result;
  }

	public function edit($cart_id, $quantity)
  {
    $result = [
      'result' => 'success',
    ];

    if (!Yii::$app->shopConfig->getParam('session_id')) {
      $result['result'] = 'error';
      $result['error']['product'] = Yii::t('app', 'session_id not specified');
      return $result;
    }

    if ($cart_id) {
      if ((int)$quantity) {
        $this->_update($cart_id, $quantity);
      } else {
        $this->_remove($cart_id);
      }

      $result['cart'] = $this->getProducts();
      $result['message'] = Translation::getTranslation('CartChangedSuccessfully');
      return $result;
    }
  
    $result['result'] = 'error';
    $result['error']['product'] = Translation::getTranslation('CartProductNotFound');
    return $result;
  }

	public function remove($cart_id)
  {
    $result = [
      'result' => 'success',
    ];

    if (!Yii::$app->shopConfig->getParam('session_id')) {
      $result['result'] = 'error';
      $result['error'] = Yii::t('app', 'session_id not specified');
      return $result;
    }

    if ($cart_id) {
      $this->_remove($cart_id);

      $result['cart'] = $this->getProducts();      
      $result['message'] = Translation::getTranslation('CartChangedSuccessfully');
      return $result;
    }
  
    $result['result'] = 'error';
    $result['error']['product'] = Translation::getTranslation('CartProductNotFound');
    return $result;
  }

	public function clear()
  {
    if (!Yii::$app->shopConfig->getParam('session_id')) {
      return [
        'result' => 'error',  
        'error' => Yii::t('app', 'session_id not specified'),  
      ];
    }

    $this->_clear();

    return [
      'result' => 'success',
      'cart' => $this->getProducts(),
    ];
  }
}
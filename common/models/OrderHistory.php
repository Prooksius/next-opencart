<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "oc_order_history".
 */
class OrderHistory extends \yii\db\ActiveRecord
{

  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'oc_order_history';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['order_status_id', 'order_id'], 'required'],
      [['order_id', 'order_status_id', 'notify', 'created_at'], 'integer'],
      [['comment'], 'string'],
    ];
  }

  public static function addHistory(array $order_data, int $order_status_id, string $comment, bool $notify = false)
  {
    if ($order_data) {

      $order = Order::findOne($order_data['order_id']);
      if ($order instanceof Order) {

        // если новый статус - ненулевой, а старый - нулевой
        if (!(int)$order_data['order_status_id'] && $order_status_id) {

          // вычитаем количество со склада
          foreach ($order_data['products'] as $order_product) {
            $product = Product::findOne(['id' => $order_product['product_id'], 'subtract' => 1]);
            if ($product instanceof Product) {
              $product->quantity -= (int)$order_product['quantity'];
              $product->save();

              // вычитаем количество опций со склада
              foreach ($order_product['options'] as $order_product_option) {
                $product_option_value = ProductOptionValue::findOne(['id' => $order_product_option['product_option_value_id'], 'subtract' => 1]);
                if ($product_option_value instanceof ProductOptionValue) {
                  $product_option_value->quantity -= (int)$order_product['quantity'];
                  $product_option_value->save();
                }
              }
            }
          }
        }

        // сохраняем новый статус в заказе
        $order->order_status_id = $order_status_id;
        $order->save();

        // добавляем событие в историю заказа
        $new_history = new self();
        $new_history->created_at = time();
        $new_history->order_id = $order_data['order_id'];
        $new_history->order_status_id = $order_status_id;
        $new_history->notify = (int)$notify;
        $new_history->comment = $comment;
        $new_history->save();

        //var_dump($new_history->errors);

        // если новый статус - нулевой, а старый - ненулевой
        if ((int)$order_data['order_status_id'] && !$order_status_id) {
 
          // восстанавливаем количество на склад
          foreach ($order_data['products'] as $order_product) {
            $product = Product::findOne(['id' => $order_product['product_id'], 'subtract' => 1]);
            if ($product instanceof Product) {
              $product->quantity += (int)$order_product['quantity'];
              $product->save();

              // восстанавливаем количество опций на склад
              foreach ($order_product['options'] as $order_product_option) {
                $product_option_value = ProductOptionValue::findOne(['id' => $order_product_option['product_option_value_id'], 'subtract' => 1]);
                if ($product_option_value instanceof ProductOptionValue) {
                  $product_option_value->quantity += (int)$order_product['quantity'];
                  $product_option_value->save();
                }
              }
            }
          }          
        }

        // If order status is 0 then becomes greater than 0 send main html email
        if (!(int)$order_data['order_status_id'] && $order_status_id) {
          // здесь будм посылать все мыла о новом заказе
        }
        // If order status is not 0 then send update text email
        if ((int)$order_data['order_status_id'] && $order_status_id && $notify) {
          // здесь будм посылать все мыла об изменении в заказе
        }        

      }

    }
  }
}

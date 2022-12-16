<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{

  private $_datefrom;
  private $_dateto;
  private $_totalfrom;
  private $_totalto;
  public $order_status_id;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['order_status_id', 'customer_id', 'datefrom', 'dateto', 'totalfrom', 'totalto'], 'safe'],
    ];
  }

  public function getDatefrom()
  {
    if ($this->_datefrom) {
      return date('d.m.Y H:i', $this->_datefrom);
    } else {
      return '';
    }
  }

  public function setDatefrom($value)
  {
    if ($value) {
      $date_field = date_create_from_format('d.m.Y H:i', $value);
      $this->_datefrom = date_timestamp_get($date_field);
    } else {
      $this->_datefrom = '';
    }
  }

  public function getDateto()
  {
    if ($this->_dateto) {
      return date('d.m.Y H:i', $this->_dateto);
    } else {
      return '';
    }
  }

  public function setDateto($value)
  {
    if ($value) {
      $date_field = date_create_from_format('d.m.Y H:i', $value);
      $this->_dateto = date_timestamp_get($date_field);
    } else {
      $this->_dateto = '';
    }
  }

  public function getTotalfrom()
  {
    return $this->_totalfrom;
  }

  public function setTotalfrom($value)
  {
    if ($value) {
      $this->_totalfrom = $value;
    } else {
      $this->_totalfrom = '';
    }
  }

  public function getTotalto()
  {
    return $this->_totalto;
  }

  public function setTotalto($value)
  {
    if ($value) {
      $this->_totalto = $value;
    } else {
      $this->_totalto = '';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function scenarios()
  {
    // bypass scenarios() implementation in the parent class
    return Model::scenarios();
  }

  /**
   * Creates data provider instance with search query applied
   *
   * @param array $params
   *
   * @return ActiveDataProvider
   */
  public function search($params)
  {
    $query = Order::find()
      ->alias('o')
      ->select([
        'o.*', 
        'os.color as status_color',
        'SUM(op.quantity) as products',
        'osd.name as status',
        'c.email as customer',
      ])
      ->leftJoin('oc_order_status os', '(os.id = o.order_status_id)')
      ->leftJoin('oc_order_status_description osd', '(osd.order_status_id = os.id AND osd.language_id = "' . \Yii::$app->language . '")')
      ->leftJoin('oc_customer c', 'c.id = o.customer_id')
      ->leftJoin('oc_order_product op', 'op.order_id = o.id')
      ->groupBy('o.id');

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'id',
          'created_at',
          'delivery_method',
          'payment_method',
          'customer',
          'products',
          'total',
          'status',
        ],
        'defaultOrder'=> [
          'created_at' => SORT_DESC
        ],
      ],
    ]);

    $this->load($params);

    if (!$this->validate()) {
      // uncomment the following line if you do not want to return any records when validation fails
      // $query->where('0=1');
      return $dataProvider;
    }
    // grid filtering conditions
    $query->andFilterWhere([
      'o.id' => $this->id,
      'o.order_status_id' => $this->order_status_id,
      'o.customer_id' => $this->customer_id,
    ]);

    if ($this->_totalfrom && $this->_totalto) {
      $query->andFilterHaving(['between', 'o.total', $this->_totalfrom, $this->_totalto]);
    } elseif ($this->_totalfrom) {
      $query->andFilterHaving(['>', 'o.total', $this->_totalfrom]);
    } elseif ($this->_totalto) {
      $query->andFilterHaving(['<', 'o.total', $this->_totalto]);
    }

    if ($this->_datefrom && $this->_dateto) {
      $query->andFilterWhere(['between', 'o.created_at', $this->_datefrom, $this->_dateto]);
    } elseif ($this->_datefrom) {
      $query->andFilterWhere(['>', 'o.created_at', $this->_datefrom]);
    } elseif ($this->_dateto) {
      $query->andFilterWhere(['<', 'o.created_at', $this->_dateto]);
    }


    return $dataProvider;
  }
}

<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductSearch represents the model behind the search form of `app\models\Product`.
 */
class ProductSearch extends Product
{

  public $category_id;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['status', 'quantity'], 'integer'],
      [['price'], 'number'],
      [['name', 'model', 'category_id'], 'safe'],
    ];
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
    $query = Product::find()
      ->alias('p')
      ->select([
        'p.*', 
        'pd.name',
        '(SELECT COUNT(prd.id) FROM oc_product_discount prd WHERE prd.product_id = p.id) AS discount_count',
        '(SELECT COUNT(ps.id) FROM oc_product_special ps WHERE ps.product_id = p.id) AS special_count',
        '(SELECT COUNT(pi.id) FROM oc_product_image pi WHERE pi.product_id = p.id) AS image_count',
        '(SELECT COUNT(po.id) FROM oc_product_option po WHERE po.product_id = p.id) AS option_count',
        '(SELECT COUNT(pf.product_id) FROM oc_product_filter pf WHERE pf.product_id = p.id) AS filter_count',
        '(SELECT COUNT(pa.product_id) FROM oc_product_attribute pa WHERE pa.product_id = p.id AND pa.language_id = "' . \Yii::$app->language . '") AS attribute_count'
      ])
      ->leftJoin('oc_product_description pd', '(pd.product_id = p.id AND pd.language_id = "' . \Yii::$app->language . '")')
      ->leftJoin('oc_product_to_category p2c', 'p2c.product_id = p.id')
      ->groupBy('p.id');

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'id',
          'name',
          'model',
          'price',
          'quantity',
          'status',
          'sort_order',
        ],
        'defaultOrder'=>[
          'sort_order'=>SORT_ASC
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
      'p.id' => $this->id,
      'p.price' => $this->price,
      'p.status' => $this->status,
      'p.quantity' => $this->quantity,
      'p.model' => $this->model,
      'p2c.category_id' => $this->category_id,
    ]);

    $query->andFilterWhere(['like', 'LOWER(pd.name)', mb_strtolower($this->name, 'UTF-8')]);

    return $dataProvider;
  }
}

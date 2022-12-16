<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ProductChoose represents the model behind the search form of `app\models\Product`.
 */
class ProductChoose extends Product
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
  public function search($params, $product_id = 0)
  {
    $query = Product::find()
      ->alias('p')
      ->select([
        'p.*', 
        'pd.name',
        '(SELECT cd.name 
          FROM oc_category_description cd 
          WHERE 
            cd.language_id = "' . \Yii::$app->language . '" AND
            cd.category_id = (SELECT p2c2.category_id FROM oc_product_to_category p2c2 WHERE p2c2.product_id = p.id AND p2c2.main_category = 1)
        ) AS main_cat_name'
      ])
      ->leftJoin('oc_product_description pd', '(pd.product_id = p.id AND pd.language_id = "' . \Yii::$app->language . '")')
      ->leftJoin('oc_product_to_category p2c', 'p2c.product_id = p.id')
      ->where('p.id != ' . (int)$product_id)
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

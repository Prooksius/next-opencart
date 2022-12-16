<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BlogArticleSearch represents the model behind the search form of `app\models\BlogArticle`.
 */
class BlogArticleSearch extends BlogArticle
{

  public $category_id;

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['status'], 'integer'],
      [['name', 'category_id'], 'safe'],
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
    $query = BlogArticle::find()
      ->alias('ba')
      ->select([
        'ba.*', 
        'bad.name',
        '(SELECT COUNT(bai.id) FROM oc_newsblog_article_image bai WHERE bai.article_id = ba.id) AS image_count',

      ])
      ->leftJoin('oc_newsblog_article_description bad', '(bad.article_id = ba.id AND bad.language_id = "' . \Yii::$app->language . '")')
      ->leftJoin('oc_newsblog_article_to_category ba2bc', 'ba2bc.article_id = ba.id')
      ->groupBy('ba.id');

    // add conditions that should always apply here

    $dataProvider = new ActiveDataProvider([
      'query' => $query,
      'sort' => [
        'attributes' => [
          'id',
          'name',
          'featured',
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
      'ba.id' => $this->id,
      'ba.status' => $this->status,
      'ba2bc.category_id' => $this->category_id,
    ]);

    $query->andFilterWhere(['like', 'LOWER(bad.name)', mb_strtolower($this->name, 'UTF-8')]);

    return $dataProvider;
  }
}

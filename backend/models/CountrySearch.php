<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PaymentSearch represents the model behind the search form of `app\models\Deposit`.
 */
class CountrySearch extends Country
{

    public $popularity_sign;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'popularity', 'popularity_sign', 'status'], 'safe'],
        ];
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
        $query = Country::find()
            ->alias('c')
            ->select('c.*, cd.*')
            ->leftJoin('oc_country_desc cd', '(cd.country_id = c.id AND cd.language_id = "' . \Yii::$app->language . '")');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'name',
                    'phonemask',
                    'status',
                    'popularity',
                ],
                'defaultOrder' => [
                    'popularity' => SORT_DESC,
                    'name' => SORT_ASC,
                ],
            ],
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'cd.name', $this->name]);
        $query->andFilterWhere(['c.status' => $this->status]);
        if ($this->popularity !== null && $this->popularity !== '') {
            $query->andWhere('c.popularity ' . $this->popularity_sign . ' ' . $this->popularity);
        }

        return $dataProvider;
    }
}
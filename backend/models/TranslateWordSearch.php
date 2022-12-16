<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PaymentSearch represents the model behind the search form of `app\models\Deposit`.
 */
class TranslateWordSearch extends TranslateWord
{
    public $translate;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['translate_group_id', 'integer'],
            [['phrase', 'translate'], 'safe'],
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
        $query = TranslateWord::find()
            ->alias('tw')
            ->select([
              'tw.*',
              'twr.*', 
              'tg.name AS group'
            ])
            ->leftJoin('translate_group tg ON (tg.id = tw.translate_group_id)')
            ->leftJoin('translate_result twr ON (twr.translate_word_id = tw.id AND twr.language_id = "' . \Yii::$app->language . '")');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'id',
                    'group',
                    'phrase',
                    'name',
                ],
                'defaultOrder'=>[
                    'phrase'=>SORT_ASC
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

        // grid filtering conditions
        $query->andFilterWhere([
            'tw.translate_group_id' => $this->translate_group_id,
        ]);

        $query->andFilterWhere(['like', 'tw.phrase', $this->phrase]);
        $query->andFilterWhere(['like', 'twr.name', $this->translate]);

        return $dataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'phrase' => \Yii::t('app', 'Phrase'),
            'translate' => \Yii::t('app', 'Translation'),
            'translate_group_id' => \YII::t('localisation', 'Translation group'),
        ];
    }
}
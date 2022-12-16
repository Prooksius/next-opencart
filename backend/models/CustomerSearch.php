<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Customer;

/**
 * CustomerSearch represents the model behind the search form of `app\models\Customer`.
 */
class CustomerSearch extends Customer
{
    private $_datefrom;
    private $_dateto;
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'email', 'first_name', 'last_name', 'phone', 'telegram', 'ref_link', 'datefrom', 'dateto'], 'safe'],
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

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Customer::find()
          ->alias('c')
          ->select([
            'c.*',
          ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
              'attributes' => [
                'id',
                'username',
                'created_at',
                'email',
                'phone',
                'first_name',
                'status',
                'email_confirmed',
              ],
              'defaultOrder'=>[
                'created_at'=>SORT_DESC
              ],
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'updated_at' => $this->updated_at,
        ]);

        if ($this->_datefrom && $this->_dateto) {
            $query->andFilterWhere(['between', 'created_at', $this->_datefrom, $this->_dateto]);
        } elseif ($this->_datefrom) {
            $query->andFilterWhere(['>', 'created_at', $this->_datefrom]);
        } elseif ($this->_dateto) {
            $query->andFilterWhere(['<', 'created_at', $this->_dateto]);
        }

        $query
          ->andFilterWhere(['like', 'username', $this->username])
          ->andFilterWhere(['like', 'email', $this->email])
          ->andFilterWhere(['like', 'first_name', $this->first_name])
          ->andFilterWhere(['like', 'last_name', $this->last_name])
          ->andFilterWhere(['like', 'phone', $this->phone])
          ->andFilterWhere(['like', 'telegram', $this->telegram])
          ->andFilterWhere(['like', 'ref_link', $this->ref_link]);

        return $dataProvider;
    }
}

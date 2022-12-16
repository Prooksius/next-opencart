<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 09.05.2020
 * Time: 23:49
 */

namespace backend\components;

use yii\data\BaseDataProvider;

class MoySkladDataProvider extends BaseDataProvider
{
    private $msklad_arr;
    /**
    * @var MoySklad
    */
    public $moy_sklad;

    /**
     * @var string имя столбца с ключом
     */
    public $key;

    /**
     * {@inheritdoc}
     */
    protected function prepareModels()
    {
        $models = [];

        $pagination = $this->getPagination();

        if ($pagination === false) {
            // в случае отсутствия разбивки на страницы - прочитать все строки
            $models = $this->moy_sklad->get_all();
        } else {
            // в случае, если разбивка на страницы есть - прочитать только одну страницу
            $pagination->totalCount = $this->getTotalCount();
            $offset = $pagination->getOffset();
            $limit = $pagination->getLimit();
            if (($sort = $this->getSort()) !== false) {
                $this->moy_sklad->set_sort($sort->getOrders());
            }

            $models = $this->moy_sklad->get($limit, $offset);
        }

        return $models;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareKeys($models)
    {
        if ($this->key !== null) {
            $keys = [];

            foreach ($models as $model) {
                if (is_string($this->key)) {
                    $keys[] = $model[$this->key];
                } else {
                    $keys[] = call_user_func($this->key, $model);
                }
            }

            return $keys;
        } else {
            return array_keys($models);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTotalCount()
    {
        $this->msklad_arr = $this->moy_sklad->get(1);
        $count = $this->moy_sklad->get_record_count();

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($value)
    {
        parent::setSort($value);
        if (($sort = $this->getSort()) !== false && $this->moy_sklad instanceof MoySklad) {
            if (empty($sort->attributes)) {
                foreach ($this->msklad_arr as $attribute => $value) {
                    $sort->attributes[$attribute] = [
                        'asc' => [$attribute => SORT_ASC],
                        'desc' => [$attribute => SORT_DESC],
                        'label' => $attribute,
                    ];
                }
            } else {
                foreach ($sort->attributes as $attribute => $config) {
                    if (!isset($config['label'])) {
                        $sort->attributes[$attribute]['label'] = $attribute;
                    }
                }
            }
        }
    }
}
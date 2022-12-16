<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 05.06.2019
 * Time: 9:36
 */

namespace frontend\components;

use yii\bootstrap\Nav;
use yii\helpers\Html;
use frontend\models\MenuPrices;

class MyPricesNav extends Nav
{
    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $query = MenuPrices::find()
            ->alias('mp')
            ->select('mp.*, mpd.*')
            ->leftJoin('menu_prices_desc mpd ON (mpd.menu_prices_id = mp.id AND mpd.language_id = "' . \Yii::$app->language . '")')
            ->where(['mp.status' => 1, 'mp.top_status' => 1])
            ->orderBy('mp.sort_order ASC');

        $items = [];

        foreach ($query->all() as $menu_item) {

            $item = [
                'label' => $menu_item->name,
                'url'   => $menu_item->link
            ];
            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

}
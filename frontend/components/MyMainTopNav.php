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
use frontend\models\MenuMain;

class MyMainTopNav extends Nav
{
    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $query = MenuMain::find()
            ->alias('ma')
            ->select('ma.*')
            ->where(['ma.status' => 1, 'ma.top_status' => 1])
            ->orderBy('ma.sort_order ASC');

        $items = [];

        foreach ($query->all() as $menu_item) {

            $item = [
                'label' => $menu_item->title,
                'url'   => $menu_item->link,
                'linkOptions' => ['data-target' => 'anchor']
            ];
            $items[] = $this->renderItem($item);
        }

        return Html::tag('menu', implode("\n", $items), $this->options);
    }

}
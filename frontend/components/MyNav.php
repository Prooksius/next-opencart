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
use frontend\models\Menu;

class MyNav extends Nav
{
    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $query = Menu::find()->where(['depth' => '0']);

        $items = [];

        foreach ($query->all() as $menu_all) {
            foreach ($menu_all->children(1)->all() as $menu_item) {
    //        foreach ($this->items as $i => $item) {

                if (!$menu_item->status) {
                    continue;
                }
                $item = [
                    'label' => '<span class="icon"><img src="' . $menu_item->icon . '"></span><span class="text">' . $menu_item->name . '</span>',
                    'url'   => $menu_item->link,
                    'encode' => false,
                    'linkOptions' => ['data-pjax' => '0']
                ];
                $items[] = $this->renderItem($item);
            }
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 05.06.2019
 * Time: 9:36
 */

namespace frontend\components;

use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use frontend\models\WorkCategory;

class MyCategoryNav extends Nav
{
    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $query = WorkCategory::find()
            ->alias('wc')
            ->select('wc.*, wcd.*')
            ->leftJoin('work_category_desc wcd ON (wcd.work_category_id = wc.id AND wcd.language_id = "' . \Yii::$app->language  . '")')
            ->orderBy(['wc.sort_order' => SORT_ASC])
            ->where(['wc.status' => 1]);

        $items = [];
//        $route_arr = explode('/', $this->route);
        $items[] = $this->renderItem([
            'label' => 'Все',
//            'url'   => [$route_arr[0] . '/index'],
            'url'   => ['/portfolio/index'],
        ]);

        foreach ($query->all() as $menu_item) {
            $linkOptions = [];
            if ($menu_item->name == '···') {
                $linkOptions['style'] = 'font-size: 14px; line-height: 13px;';
                $linkOptions['title'] = 'Другое';
            }
            $item = [
                'label' => $menu_item->name,
                'linkOptions' => $linkOptions,
//                'url'   => [$route_arr[0] . '/category', 'category_id' => $menu_item->id],
                'url'   => ['/portfolio/category', 'category_id' => $menu_item->id],
            ];
            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

}
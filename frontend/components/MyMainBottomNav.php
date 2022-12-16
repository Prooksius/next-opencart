<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 05.06.2019
 * Time: 9:36
 */

namespace frontend\components;

use Yii;
use yii\bootstrap\Nav;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\MenuMain;

class MyMainBottomNav extends Nav
{
    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $query = MenuMain::find()
            ->alias('ma')
            ->select('ma.*')
            ->where(['ma.status' => 1, 'ma.bottom_status' => 1])
            ->orderBy('ma.sort_order ASC');

        $items = [];

        $cur_url = Yii::$app->request->url;
        $home_url = Yii::$app->homeUrl;
        $is_home = $home_url == $cur_url;

        foreach ($query->all() as $menu_item) {

            $item = [
                'label' => $menu_item->title,
                'url'   => ($is_home ? '' : '/') . $menu_item->link,
                'linkOptions' => ['data-target' => 'anchor']
            ];
            $items[] = $this->renderItem($item);
        }

        return Html::tag('menu', implode("\n", $items), $this->options);
    }

}
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
use common\modules\blog\models\BlogCategory;
use common\modules\blog\traits\IActiveStatus;

class MyBlogNav extends Nav
{
    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $query = BlogCategory::find()
            ->where(['status' => IActiveStatus::STATUS_ACTIVE, 'is_nav' => BlogCategory::IS_NAV_YES])
            ->orderBy(['sort_order' => SORT_ASC]);

        $items = [];
        $items[] = $this->renderItem([
            'label' => 'Все',
            'url'   => ['/blog/default/index'],
        ]);

        foreach ($query->all() as $menu_item) {
            $linkOptions = [];
            $item = [
                'label' => $menu_item->title,
                'linkOptions' => $linkOptions,
                'url'   => ['/blog/default/category', 'category_id' => $menu_item->id, 'slug' => $menu_item->slug],
            ];
            $items[] = $this->renderItem($item);
        }

        return Html::tag('ul', implode("\n", $items), $this->options);
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 05.06.2019
 * Time: 19:39
 */

namespace frontend\components;

use yii\bootstrap\Nav;
use yii\helpers\Html;
use frontend\models\Settings;


class MySocialLinks extends Nav
{
    public $encodeLabels = false;

    /**
     * Initializes the widget.
     */
    public function init()
    {
        parent::init();
        if ($this->route === null && Yii::$app->controller !== null) {
            $this->route = Yii::$app->controller->getRoute();
        }
        if ($this->params === null) {
            $this->params = Yii::$app->request->getQueryParams();
        }
        if ($this->dropDownCaret === null) {
            $this->dropDownCaret = '<span class="caret"></span>';
        }
        Html::removeCssClass($this->options, ['widget' => 'nav']);
    }

    /**
     * Renders widget items.
     */
    public function renderItems()
    {
        $query = Settings::readAllSettings('social');

        $items = [];

        $items = [
            $this->renderItem([
                'label' => '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.5 15C7.20833 15 5.99479 14.6875 4.85938 14.0625L0 16L1.9375 11.1406C1.3125 10.0052 1 8.79167 1 7.5C1 6.47917 1.19792 5.50781 1.59375 4.58594C1.98958 3.66406 2.52344 2.86719 3.19531 2.19531C3.86719 1.52344 4.66406 0.989583 5.58594 0.59375C6.50781 0.197917 7.47917 0 8.5 0C9.52083 0 10.4922 0.197917 11.4141 0.59375C12.3359 0.989583 13.1328 1.52344 13.8047 2.19531C14.4766 2.86719 15.0104 3.66406 15.4062 4.58594C15.8021 5.50781 16 6.47917 16 7.5C16 8.52083 15.8021 9.49219 15.4062 10.4141C15.0104 11.3359 14.4766 12.1328 13.8047 12.8047C13.1328 13.4766 12.3359 14.0104 11.4141 14.4062C10.4922 14.8021 9.52083 15 8.5 15ZM11 9H10L9.4375 9.5C8.96875 9.375 8.39323 8.97135 7.71094 8.28906C7.02865 7.60677 6.625 7.03125 6.5 6.5625L7 6V5C7 4.82292 6.9375 4.64583 6.8125 4.46875C6.6875 4.29167 6.54948 4.16406 6.39844 4.08594C6.2474 4.00781 6.14062 4 6.07812 4.0625L5.34375 4.79688C4.9375 5.20312 4.8776 5.83594 5.16406 6.69531C5.45052 7.55469 5.9974 8.38802 6.80469 9.19531C7.61198 10.0026 8.44531 10.5495 9.30469 10.8359C10.1641 11.1224 10.7969 11.0625 11.2031 10.6562L11.9375 9.92188C12 9.85938 11.9922 9.7526 11.9141 9.60156C11.8359 9.45052 11.7083 9.3125 11.5312 9.1875C11.3542 9.0625 11.1771 9 11 9Z" fill="black"/></svg>',
                'url'   => $query['wa'],
				'linkOptions' => ['data-goal' => 'clickWA', 'id' => 'clickWA'],
            ]),
            $this->renderItem([
                'label' => '<svg width="14" height="12" viewBox="0 0 14 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.9595 1.08166L11.847 11.0442C11.6875 11.7472 11.272 11.9222 10.6815 11.5912L7.46247 9.21916L5.90947 10.7132C5.73747 10.8852 5.59397 11.0287 5.26247 11.0287L5.49397 7.75066L11.4595 2.36016C11.719 2.12916 11.403 2.00066 11.0565 2.23216L3.68147 6.87616L0.506473 5.88216C-0.184027 5.66666 -0.196527 5.19166 0.650473 4.86016L13.069 0.0756646C13.644 -0.139835 14.147 0.203665 13.9595 1.08216V1.08166Z" fill="black"/></svg>',
                'url'   => $query['tg'],
				'linkOptions' => ['data-goal' => 'clickTG', 'id' => 'clickTG'],
            ]),
        ];

        return Html::tag('ul', implode("\n", $items), $this->options);
    }
}
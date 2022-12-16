<?php

use yii\helpers\Html;
use backend\components\MyHtml;
use yii\bootstrap\Tabs;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $settings array */

?>
<div class="pages-form">

    <?= Tabs::widget([
        'navType' => 'nav-pills',
        'items' => [
            [
                'label' => 'Стр "Партнерство"',
                'content' => $this->render('partner', ['partner' => $partner, 'languages' => $languages]),
            ],
        ],
        'options' => [
            'id' => 'pages-update-tabs',
        ]
    ]);?>

</div>
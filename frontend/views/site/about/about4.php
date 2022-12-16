<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;

$items = [];
$active = 1;
foreach ($cat_services as $service_cat) {

    $cols = 3;
    $rubrics_count = count($service_cat['rubrics']);
    if ($rubrics_count > 4) {
        $cols = 2;
    }
    $html = Html::beginTag('div', ['class' => 'row']);
    foreach ($service_cat['rubrics'] as $rubric_name => $services) {
        $html .= Html::beginTag('div', ['class' => 'col-sm-' . $cols]);
        $html .= Html::tag('h4', $rubric_name, ['class' => 'about-service-h3']);
        $html .= Html::beginTag('ul', ['class' => 'about-service-list']);
        foreach ($services as $service) {
            $html .= Html::beginTag('li');
            $html .= Html::a($service['name'], Url::to(['service/service', 'service_id' => $service['id']]), ['target' => '_blank']);
            $html .= Html::endTag('li');
        }
        $html .= Html::endTag('ul');
        $html .= Html::endTag('div');
    }
    $html .= Html::endTag('div');

    $items[] = [
        'label' => $service_cat['name'],
        'content' => $html,
        'active' => $active,
    ];
    if ($active) {
        $active = 0;
    }
}

?>
<div class="site-about site-page page4<? echo ($active ? ' open' : ''); ?>" data-background="light-bg">

    <div class="body-content about-services">
        <div class="hidden-xs">
            <?= Tabs::widget([
                'items' => $items,
                'options' => [
                    'class' => 'our-tabs',
                ]
            ]);?>
        </div>
        <div class="visible-xs">
            <? foreach ($items as $item) { ?>
                <h2><?= $item['label'] ?></h2>
                <div class="cat-content">
                    <?= $item['content'] ?>
                </div>
            <? } ?>
        </div>
    </div>
</div>

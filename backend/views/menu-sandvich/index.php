<?php

use yii\helpers\Html;
use yii\grid\GridView;
use klisl\nestable\MyNestable;
use yii\helpers\Url;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сэндвич-меню сайта';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-index">

    <p><?= Html::a('Добавить пункт меню', ['create'], ['class' => 'btn btn-success']) ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <span id="nestable-menu">
            <button class="btn btn-primary" type="button" data-action="expand-all"><i class="fa fa-expand" aria-hidden="true"></i> Раскрыть все узлы</button>
            <button class="btn btn-primary" type="button" data-action="collapse-all"><i class="fa fa-compress" aria-hidden="true"></i> Закрыть все узлы</button>
        </span>
    </p>

    <?= MyNestable::widget([
        'type' => MyNestable::TYPE_LIST,
        'query' => $query,
        'modelOptions' => [
            'name' => function ($data) { //поле из БД с названием элемента (отображается в дереве)
                $html  = html::beginForm(Url::to(['menu-sandvich/update', 'id' => $data->id]), 'POST');
                $html .= html::activeTextInput($data, 'name', ['class' => 'form-control grid-editable', 'label' => false]);
                $html .= html::endForm();
                return $html;
            },
            'dops' => [
                [
                    'caption' => 'Показывать на сайте',
                    'data' => function ($data) {
                        $html  = '<span class="label label-' . ((bool)$data->status ? 'success' : 'danger') . '">' . ((bool)$data->status ? 'Да' : 'Нет') . '</span>';
                        $html .= html::beginForm(Url::to(['menu-sandvich/update', 'id' => $data->id]), 'POST');
                        $html .= html::activeCheckbox($data, 'status', ['class' => 'grid-editable', 'label' => false]);
                        $html .= html::endForm();
                        return $html;
                    }
                ],
                [
                    'caption' => 'Ссылка',
                    'data' => function ($data) {
                        $html  = html::beginForm(Url::to(['menu-sandvich/update', 'id' => $data->id]), 'POST');
                        $html .= html::activeTextInput($data, 'link', ['class' => 'form-control grid-editable', 'label' => false]);
                        $html .= html::endForm();
                        return $html;
                    }
                ]
            ],
        ],
        'pluginEvents' => [
            'change' => 'function(e) {}', //js событие при выборе элемента
        ],
        'pluginOptions' => [
            'maxDepth' => 3, //максимальное кол-во уровней вложенности
        ],
        'update' => Url::to(['menu-sandvich/update']), //действие по обновлению
        'delete' => Url::to(['menu-sandvich/delete']), //действие по удалению
        'viewItem' => Url::to(['menu-sandvich/view']), //действие по просмотру
    ]);

    $this->registerJs("

           // $('#nestable-menu button[data-action = \"collapse-all\"]').trigger('click');

        ", View::POS_READY);

    ?>
</div>

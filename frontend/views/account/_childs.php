<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $query  yii\db\query*/
/* @var $id integer
/* @var $level integer
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="childs" data-parentid="<?= $id ?>">
    <div class="childs-cont">
        <? foreach ($query->all() as $item) { ?>
        <div class="data-line<?= ($item->level_percent ? '' : ' greyed') ?>"<?= ($item->level_percent ? '' : ' title="Этот клиент не попадает в ваши рефералы по поколению"') ?> data-id="<?= $item->id ?>" data-level="<?= $level + 1 ?>">
            <div class="plus">
                <? if ($item->childs_present) { ?>
                    <i class="fa fa-plus-square-o open-childs" aria-hidden="true"></i>
                <? } ?>
            </div>
            <div class="name">Уровень&nbsp;<?= $level + 1 ?></div>
            <div class="email">-</div>
            <div class="phone">-</div>
            <div class="agreement"><?= $item->username ?></div>
			<div class="first-depo">
				<?= str_replace(' ', '&nbsp;', date('d.m.Y г.', $item->created_at)) ?>
			</div>
            <div class="history"><? if ($item->level_percent) { ?><?= html::button('История', ['class' => 'btn2 small-btn form-popup', 'data-size' => 'big', 'data-target' => '/account/client-deposit?id=' . $item->id]) ?><? } ?></div>
        </div>
        <? } ?>
    </div>
</div>

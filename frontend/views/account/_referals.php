<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use \yii\web\View;

/* @var $this yii\web\View */
/* @var $query  yii\db\query*/
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Партнерская программа';
$this->params['breadcrumbs'][] = $this->title;

$deposit_sum = 0;

?>
<section class="privateOffice<?= ($is_partner ? ' privateOffice1' : '') ?>">
    <div class="container">
        <?= $this->render('account-menu'); ?>
		<br />
		<br />
        <div class="accruals">
			<div class="panel referals-info">
				<table class="table-3 smalltable-3">
					<thead>
						<tr>
							<th align="left">Уровень</th>
							<th align="left">Процент вознаграждения</th>
							<th align="left" class="text-right">Партнеров в этом месяце</th>
							<th align="left" class="text-right">Партнеров всего</th>
						</tr>
					</thead>
					<tbody>
						<?php $totalCl = 0 ?>
						<?php $totalMCl = 0 ?>
						<?php foreach ($referalTotals as $level => $count) { ?>
							<?php $deposit_sum += (float)$count['amount'] ?>
							<tr>
								<td data-label="Уровень"><?= $level ?></td>
								<td data-label="Процент вознаграждения" style="color: green;font-weight: bold"><?= $count['percent'] ?> %</td>
								<td data-label="Партнеров в этом месяце" class="text-right"><?= $count['month'] ?></td>
								<td data-label="Партнеров всего" class="text-right"><?= $count['all'] ?></td>
							</tr>
							<?php $totalMCl += (int)$count['month']; ?>
							<?php $totalCl += (int)$count['all']; ?>
						<?php } ?>
							<tr class="last-row">
								<td>Всего партнеров:</td>
								<td></td>
								<td data-label="Партнеров в этом месяце" class="text-right"><b style="color: red"><?= $totalMCl ?></b></td>
								<td data-label="Партнеров всего" class="text-right"><b style="color: red"><?= $totalCl ?></b></td>
							</tr>
					</tbody>
				</table>
			</div>
		</div>

        <div class="accruals">
            <div class="accruals-container">
				<div class="referals-container" data-id="<?= $id ?>">
					<div class="client-area">
						<div class="header-line">
							<div class="plus"></div>
							<div class="name">Уровень</div>
							<div class="email">Email</div>
							<div class="phone">Телефон</div>
							<div class="agreement">Логин</div>
							<div class="first-depo">Регистрация</div>
							<?php if ($is_manager) { ?>
							<div class="history">Портфель и история</div>
							<?php } else { ?>
							<div class="history">История</div>
							<?php } ?>
						</div>
						<?php $items = $query->all()?>
						<?php if (count($items)) { ?>
							<?php foreach ($items as $item) { ?>
								<div class="data-line<?= ($item->level_percent ? '' : ' greyed') ?>" data-id="<?= $item->id ?>" data-level="<?= $startLevel ?>">
									<div class="plus">
										<?php if ($item->childs_present && !$is_manager) { ?>
											<i class="fa fa-plus-square-o open-childs" aria-hidden="true"></i>
										<?php } ?>
									</div>
									<div class="name">Уровень&nbsp;<?= $startLevel ?></div>
									<div class="email"><?= html::a($item->email, 'mailto:' . $item->email) ?></div>
									<div class="phone"><?= $item->phone ?></div>
									<div class="agreement"><?= $item->username ?></div>
									<div class="first-depo">
										<?= str_replace(' ', '&nbsp;', date('d.m.Y г.', $item->created_at)) ?>
									</div>
									<div class="history"><?= html::button('История', ['class' => 'btn2 small-btn form-popup', 'data-size' => 'big', 'data-target' => '/account/client-deposit?id=' . $item->id]) ?></div>
								</div>
							<?php } ?>
						<?php } else { ?>
							<div class="data-line">
								<div class="name">У вас нет клиентов</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
        </div>
    </div>
</div>
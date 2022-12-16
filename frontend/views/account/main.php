<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 27.04.2020
 * Time: 14:38
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\Json;
use yii\web\View;

$this->title = 'Промокоды | Личный кабинет | ' . $seo['meta_title'];

$user_boughts_arr = $user_boughts->all();

$funcs_aval = (is_array($user_boughts_arr) && count($user_boughts_arr) > 0) || $customer->lk_enabled || $is_partner;

$this->registerJs("
let bots = ".Json::encode($bots).";
let cur_discount = 0;
", View::POS_HEAD);

?>

<section class="privateOffice<?= ($is_partner ? ' privateOffice1' : '') ?>">
    <div class="container">
        <?= $this->render('account-menu'); ?>
        <div class="privateOfficePromotionalCodeSetting">
            <div class="all-botslist">
                <div class="bought-bots">
                    <h4>Ваш тариф</h4>
                    <table>
                        <thead>
                        <tr>
                            <th>Дата покупки</th>
                            <th>Счет</th>
                        </tr>
                        </thead>
                        <tbody>
                        <? if (count($user_boughts_arr)) { ?>
                        <? foreach ($user_boughts_arr as $bot) { ?>
                            <tr>
                                <td><?= date('d.m.Y', $bot->created_at); ?></td>
                                <td><?= $bot->botname ?></td>
                            </tr>
                        <? } ?>
                        <? } else { ?>
                            <tr>
                                <td colspan="2" style="text-align: left">Покупок еще нет</td>
                            </tr>
                        <? } ?>
                        </tbody>
                    </table>
                </div>
                <div class="available-bots">
                    <h4>Купить тариф</h4>
                    <table class="table-3 smalltable-3">
                        <thead>
                        <tr>
                            <th>Счет</th>
                            <th>Цена</th>
                            <th>Новая цена</th>
                            <th>Дата дедлайн</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($bots as $bot) { ?>
                            <tr>
                                <td data-label="Счет" class="tooltip-text"><span class="tooltip" data-tooltip-content="#tooltip_content<?= $bot->id ?>"><?= $bot->name ?></span></td>
								<?php if ($bot->date_deadline && $bot->date_deadline > time() && $bot->new_price) { ?>
                                <td data-label="Цена" class="line-through">$<?= number_format($bot->price, 0, '', ' '); ?></td>
                                <td data-label="Новая цена"><span class="blinking">$<?= number_format($bot->new_price, 0, '', ' '); ?></span></td>
								<?php } else { ?>
                                <td data-label="Цена">$<?= number_format($bot->price, 0, '', ' '); ?></td>
                                <td data-label="Новая цена"></td>
								<?php } ?>
								<?php if ($bot->date_deadline && $bot->date_deadline > time()) { ?>
                                <td data-label="До"><span data-timer="<?= date('Y, m, d, H,i,s', $bot->date_deadline) ?>"></span></td>
								<?php } else { ?>
								<td data-label="До"></td>
								<?php }?>
                                <td data-label="Купить"><?= Html::button('Купить', ['class' => 'btn2 form-popup', 'data-target' => '/site/paymentpopup?bot_id='.$bot->id.'&init=1'])?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
					<div style="display:none">
						<?php foreach ($bots as $bot) { ?>
						<span id="tooltip_content<?= $bot->id ?>"><?= nl2br($bot->description) ?></span>
						<?php } ?>
					</div>
                </div>
            </div>
            <div class="privateOfficePromotionalCodeSetting-wrap1<?= ($funcs_aval ? '' : ' disabled-funcs'); ?>">
                <div class="privateOfficePromotionalCodeSetting-prize">
					<p><b>% c продаж</b></p><br />
					<?php foreach ($fees as $fee) { ?>
					<div class="prize-cont">
						<span><?= $fee->level ?> уровень</span>
						<b><?= $fee->percent; ?>%</b>
						<i>
							<img src="/img/question-icon.svg" class="tooltip" title="Процент с продаж" alt="">
						</i>
					</div>
					<?php } ?>
                </div>
                <a href="<?= $general[($is_partner ? 'codesvideopopuppartner' : 'codesvideopopupusual')] ?>" class="privateOfficePromotionalCodeSetting-video" data-fancybox>
                    <div class="privateOfficePromotionalCodeSetting-video-icon">
                        <img src="/img/play-icon.svg" alt="">
                    </div>
                    <div class="privateOfficePromotionalCodeSetting-video-text">Смотреть видео <br> “Инструкция пользования ЛК”</div>
                </a>
            </div>
        </div>
    </div>
</section>
<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 28.04.2020
 * Time: 17:10
 */

/* @var $this yii\web\View */
use yii\helpers\Url;
use yii\helpers\Html;
use yii\web\View;

$terminal = $merchant['terminal'];
$password = $merchant['password'];

$order = $payment_id;

$desc  = "Оплата бота " . $bot->name;
$sum  = round($price, 2);

$this->registerJs("$(\"input[type='tel']\").mask(\"+7(999)999-99-99\",{placeholder:\"+7(   )   -  -  \"});");

?>

<div class="popup2-bg">
    <img src="img/popup2-bg.png" alt="">
</div>
<? $err = 0; ?>
<?= Html::beginForm(Url::to(['/site/paymentpopup']), 'GET', ['class' => 'popup2-form payment-params-form']); ?>
    <div class="payment-popup">
        <?= Html::hiddenInput('bot_id', $bot->id) ?>
        <h2>Покупка копи-счета</h2>
        <h3>Информация о покупателе</h3>
        <div class="form-group">
            <?= Html::textInput('fio', $fio, ['class' => 'input', 'placeholder' => 'Имя*']) ?>
            <? if (!$init && !$fio) { ?>
                <? $err = 1; ?>
                <p class="help-block help-block-error">Необходимо заполнить «Имя».</p>
            <? } ?>
        </div>
        <div class="form-group">
            <?= Html::textInput('phone', $phone, ['type' => 'tel', 'class' => 'input', 'placeholder' => '+7(   )   -  -  ']) ?>
            <? if (!$init && !$phone) { ?>
                <? $err = 1; ?>
                <p class="help-block help-block-error">Необходимо заполнить «Телефон».</p>
            <? } ?>
        </div>
        <div class="form-group">
            <?= Html::textInput('email', $email, ['type' => 'email', 'class' => 'input', 'placeholder' => 'Email']) ?>
            <? if (!$init && !$email) { ?>
                <? $err = 1; ?>
                <p class="help-block help-block-error">Необходимо заполнить «Email».</p>
            <? } ?>
        </div>
        <div class="form-group" style="display: none;"><?= Html::textInput('broker_acc', $broker_acc, ['type' => 'text', 'class' => 'input', 'placeholder' => 'Счет брокера']) ?></div>
        <div class="popup2-info">
            <h4>Информация о покупке</h4>
            <ul class="popup2-list">
                <li>
                    <span>Продукт</span>
                    <span><?= $bot->name ?></span>
                </li>
                <? if ($discount) { ?>
                    <li>
                        <span>Розн. цена</span>
                        <span style="text-decoration: line-through">$<?= number_format((float)$bot->price, 0, '', ' '); ?></span>
                    </li>
                    <li>
                        <span>Ваша цена (-<?= round(100-((float)$price/(float)$bot->price * 100), 0) ?> %)</span>
                        <span style="color: green">$<?= number_format((float)$price, 0, '', ' ') ?></span>
                    </li>
                <? } else { ?>
                <li>
                    <span>Цена</span>
                    <span>$<?= number_format((float)$bot->price, 0, '', ' '); ?></span>
                </li>
                <? } ?>
            </ul>
			<h4><?= sprintf(nl2br($cryptotext), '$'.number_format((float)$price, 0, '', ' ')) ?></h4>
			<textarea rows="2" style="font-size: 12px;" type="text" readonly class="input"><?= $cryptowallet ?></textarea>
        </div>
        <div class="popup2-link-wrap">
            <?//= Html::button('Применить', ['class' => 'popup2-' . (((!$init || !$is_guest) && !$err) ? 'link ' : 'btn ') . 'apply-promocode', 'data-target' => '/site/paymentpopup?bot_id='.$bot->id]) ?>
            <?= Html::button('ОК', ['class' => 'btn2', 'onClick' => '$.fancybox.close()']) ?>
        </div>
    </div>
<?= Html::endForm(); ?>
<? if ( false && (!$init || !$is_guest) && !$err) { ?>
<script src="https://securepay.tinkoff.ru/html/payForm/js/tinkoff_v2.js"></script>
<form name="TinkoffPayForm" class="payment-submit-form popup2-form" onsubmit="ym(66152347,'reachGoal','submitBuyBot'); pay(this); return false;">
	<input class="tinkoffPayRow" type="hidden" name="terminalkey" value="<?= $terminal ?>">
	<input class="tinkoffPayRow" type="hidden" name="frame" value="false">
	<input class="tinkoffPayRow" type="hidden" name="language" value="ru">
    <input class="tinkoffPayRow" type="hidden" name="amount" value="<?= $sum ?>">
    <input class="tinkoffPayRow" type="hidden" name="order" value="<?= $order ?>">
    <input class="tinkoffPayRow" type="hidden" name="description" value="<?= $desc ?>">
    <input class="tinkoffPayRow" type="hidden" name="name" value="<?= $fio ?>">
    <input class="tinkoffPayRow" type="hidden" name="email" value="<?= $email ?>">
    <input class="tinkoffPayRow" type="hidden" name="phone" value="<?= $phone ?>">
	<input class="tinkoffPayRow" type="hidden" name="receipt" value='{"EmailCompany": "<?= $company_email ?>","Taxation": "usn_income","Items": [{"Name": "<?= $bot->name ?>","Price": <?= $sum*100 ?>,"Quantity": 1.00, "Amount": <?= $sum*100 ?>,"Tax": "none"}]}'>
    <input class="tinkoffPayRow btn2" id="submitBuyBot" type="submit" value="Оплатить">
</form>
<div id="tinkoffWidgetContainer"></div>
<script type="text/javascript">
	const terminalkey = document.forms.TinkoffPayForm.terminalkey; 
	const widgetParameters = {
		container: 'tinkoffWidgetContainer',
		terminalKey: terminalkey.value, 
		paymentSystems: {
			ApplePay: {
				buttonOptions: {
					color: 'black',
				},
				paymentInfo: function(){
					return {
						infoEmail: document.forms.TinkoffPayForm.email.value, 
						paymentData: document.forms.TinkoffPayForm
					}
				}
			},
		},
	};
	setTimeout(function () {
		initPayments(widgetParameters);
	}, 1000);
</script>
<? } ?>
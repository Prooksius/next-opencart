<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

?>
<div class="site-about site-page simform-screen hidden-page page4-1<? echo ($active ? ' open' : ''); ?>" style="background-image: url(/img/book40.png);" data-background="light-bg">
    <div class="bg-shadow"></div>
    <div class="body-content simform-area about-bonus-form">
        <h2>Для вас бонус!</h2>
        <p>Получите бесплатно книгу, которая поможет вам избежать большинства ошибок</p>
        <form id="theForm" class="simform" autocomplete="off" data-redirect="/thanks-about" data-book="1">
            <input type="hidden" name="<?=Yii::$app->request->csrfParam; ?>" value="<?=Yii::$app->request->getCsrfToken(); ?>" />
            <input type="hidden" name="page" value="/about" />
            <div class="simform-inner">
                <ol class="questions">
                    <li>
                        <input type="email" id="q1" name="email" placeholder="Введите email" />
                    </li>
                    <li>
                        <input id="q2" name="name" placeholder="Ваше имя" type="text" />
                    </li>
                    <li>
                        <input id="q3" name="phone" placeholder="Ваш телефон" class="phone" type="text" />
                    </li>
                </ol><!-- /questions -->
                <button class="submit" type="submit">Отправить</button>
                <div class="controls">
                    <button class="next">
                        <svg width="28" height="24" viewBox="0 0 28 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M27.7601 11.4468L16.5499 0.240644C16.3894 0.0804391 16.2048 0 15.9965 0C15.788 0 15.6035 0.0804391 15.4431 0.240644L14.2406 1.4431C14.08 1.60339 14 1.78752 14 1.99624C14 2.20496 14.08 2.38908 14.2406 2.54937L23.6945 12.0003L14.2406 21.4513C14.08 21.6116 14 21.7961 14 22.0042C14 22.2128 14.08 22.3973 14.2406 22.5576L15.4432 23.7596C15.6036 23.9201 15.7881 24 15.9965 24C16.2049 24 16.3894 23.9199 16.5499 23.7596L27.7595 12.5534C27.9198 12.3932 28 12.2086 28 12.0003C28 11.7919 27.9204 11.6074 27.7601 11.4468Z" fill="#C4C4C4"/><rect y="10" width="25" height="4" rx="2" fill="#C4C4C4"/></svg>
                    </button>
                    <div class="progress"></div>
                    <span class="number">
								<span class="number-current">1</span>
								<span class="number-total">3</span>
							</span>
                    <span class="error-message"></span>
                </div>
            </div>
            <span class="final-message"></span>
        </form>
    </div>
</div>

<? $this->registerJsFile( 'js/simform.js', ['depends'=>'yii\web\YiiAsset']); ?>
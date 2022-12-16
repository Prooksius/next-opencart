<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 01.05.2020
 * Time: 19:57
 */

use yii\helpers\Html;

?>
<section class="payment">
    <div class="container">
        <div class="payment-container">
            <div class="payment-wrap1">
                <div class="payment-title">
                    <h2>Оцените возможности робота</h2>
                    <h3>У робота есть набор заданных алгоритмов по основным направлениям</h3>
                </div>
                <div class="payment-list">
                    <div class="payment-item">
                        <div class="payment-item-wrap">
                            <div class="payment-item-dotted">
                                <i></i>
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                            </div>
                            <p>Твой первый шаг в мир трейдинга</p>
                        </div>
                    </div>
                    <div class="payment-item">
                        <div class="payment-item-wrap">
                            <div class="payment-item-dotted">
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                                <i></i>
                            </div>
                            <p>Робот, которому не страшен любой кризис</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="payment-wrap2">
                <div class="payment-inlet">
                    <div class="payment-inlet-wrap">
                        <div class="payment-inlet-subtitle">
                            <b>Сумма счёта:</b>
                            <span>
                                $ <input type="text" id="amount" readonly>
                            </span>
                        </div>
                        <div class="payment-inlet-slider">
                            <div id="slider-range-max"></div>
                        </div>
                    </div>
                    <div class="payment-inlet-wrap">
                        <div class="payment-inlet-subtitle">
                            <b>Процент:</b>
                            <span>
                                <input type="text" id="amount1" readonly> %
                            </span>
                        </div>
                        <div class="payment-inlet-slider">
                            <div id="slider-range-max1"></div>
                        </div>
                    </div>
                </div>
                <div class="payment-result">
                    <ul class="payment-list1" id="payment-list1-res">
                        <li>
                            <span>Результат в месяц</span>
                            <b>$ <i>50</i></b>
                        </li>
                        <li>
                            <span>Результат в год</span>
                            <b>$ <i>1450</i></b>
                        </li>
                    </ul>
                    <?= Html::button('Хочу так', ['class' => 'btn2 form-popup', 'data-target' => '/site/income-request-popup', 'data-goal' => 'buttonClickTry'])?>
                </div>
            </div>
        </div>
    </div>
</section>
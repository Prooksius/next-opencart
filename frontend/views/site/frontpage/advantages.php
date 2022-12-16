<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 01.05.2020
 * Time: 19:57
 */

use yii\helpers\Html;

?>
<section class="advantages">
    <div class="advantages-bg"></div>
    <div class="container">
        <h2>Преимущества робота</h2>
        <div class="advantages-container1">
            <div class="advantages-wrap1">
                <h3>
                    <i>
                        <img src="/img/advantages-icon1.svg" alt="">
                    </i>
                    Самостоятельно
                </h3>
                <div class="advantages-list1">
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                                <i style="opacity: 0;"></i>
                            </div>
                            <p>Метод проб и ошибок, вам не раз придется потерять весь депозит</p>
                        </div>
                    </div>
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i style="opacity: 0;"></i>
                            </div>
                            <p>Почти невозможно эффективно совмещать с другой деятельностью</p>
                        </div>
                    </div>
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i style="opacity: 0;"></i>
                                <i style="opacity: 0;"></i>
                            </div>
                            <p>Самостоятельно вести аналитику, контроль рисков, совершать сделки</p>
                        </div>
                    </div>
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                                <i></i>
                            </div>
                            <p>Дополнительные ошибки от эмоций и желания отыграться</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="advantages-wrap1">
                <h3>
                    <i>
                        <img src="/img/advantages-icon2.svg" alt="">
                    </i>
                    Робот
                </h3>
                <div class="advantages-list1">
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                            </div>
                            <p>Полное отсутствие эмоций при использовании алгоритмической торговли</p>
                        </div>
                    </div>
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                                <i style="opacity: 0;"></i>
                            </div>
                            <p>Не боится трендов и сам анализирует волатильность торгуемого инструмента</p>
                        </div>
                    </div>
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                            </div>
                            <p>Использования нескольких совместно работающих алгоритмов</p>
                        </div>
                    </div>
                    <div class="advantages-item1">
                        <div class="advantages-item1-wrap">
                            <div class="advantages-item1-dotted">
                                <i></i>
                                <i style="opacity: 0;"></i>
                                <i></i>
                                <i></i>
                            </div>
                            <p>Робот работает сам, а вы получаете постоянный пассивный доход</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="advantages-container2">
            <ul class="advantages-list2">
                <li>Вы можете дальше класть деньги в банк под мизерный процент и с опасностью их потерять</li>
                <li>Вы можете дальше откладывать желание войти в сферу трейдинга</li>
            </ul>
            <div class="advantages-feedback">
                <i>или</i>
                <p>Начать стабильно увеличивать ваши средства <br> и застраховать свою жизнь от любых потрясений</p>
                <div class="advantages-feedback-wrap">
                    <b>Бесплатный тестовый период в 14 дней</b>
                    <?= Html::button('Попробовать без риска', ['class' => 'btn2 form-popup', 'data-target' => '/site/test-request-popup', 'data-goal' => 'buttonClickTry', 'data-goal' => 'buttonClickTry'])?>
                </div>
            </div>
        </div>
    </div>
</section>


<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 01.05.2020
 * Time: 20:01
 */
?>
<section class="info scroll" id="info">
    <div class="container">
        <div class="info-title">
            <h2>Как это работает?</h2>
            <h3>У робота есть набор заданных алгоритмов по основным направлениям</h3>
        </div>
        <ul class="tab_list">
            <? $i2 = 1; ?>
            <? foreach ($query->all() as $step) { ?>
            <li>
                <a class="<?= ($i2 == 1 ? 'active' : '') ?>" href="#info-tab<?= $i2 ?>"><?= $step->title ?></a>
            </li>
            <? $i2++; ?>
            <? } ?>
        </ul>
        <div class="info-tabs">
            <? $i2 = 1; ?>
            <? foreach ($query->all() as $step) { ?>
            <div <?= ($i2 > 1 ? 'style="display: none;"' : '')?> class="info-tab block_content" id="info-tab<?= $i2 ?>">
                <div class="info-tab-wrap">
                    <div class="info-item">
                        <b>
                            <i>A</i>
                        </b>
                        <div class="info-item-dotts">
                            <i></i>
                            <i></i>
                            <i style="opacity: 0;"></i>
                            <i></i>
                        </div>
                        <div class="info-item-text">
                            <p><?= str_replace("\r\n", '</p><p>', $step->texta) ?></p>
                        </div>
                    </div>
                    <div class="info-item">
                        <b>
                            <i>B</i>
                        </b>
                        <div class="info-item-dotts">
                            <i style="opacity: 0;"></i>
                            <i></i>
                            <i></i>
                            <i></i>
                        </div>
                        <div class="info-item-text">
                            <p><?= str_replace("\r\n", '</p><p>', $step->textb) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <? $i2++; ?>
            <? } ?>
        </div>
    </div>
</section>


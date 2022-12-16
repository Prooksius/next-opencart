<?php
/**
 * Created by PhpStorm.
 * User: prook
 * Date: 26.04.2020
 * Time: 21:28
 */
?>
<section class="faq scroll" id="faq">
    <div class="container">
        <h2>FAQ</h2>
        <div class="faq-list">
            <? foreach ($query->all() as $faq) { ?>
            <div class="faq-item">
                <div class="open_toggle"><?= $faq->name; ?></div>
                <div style="display: none;" class="block_toggle"><?= $faq->text; ?></div>
            </div>
            <? } ?>
        </div>
    </div>
</section>
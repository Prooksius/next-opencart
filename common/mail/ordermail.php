<?

use himiklab\thumbnail\EasyThumbnailImage;

$attributes = $model->attributeLabels(); ?>
<h1>Заполнение брифа с сайта</h1>
<p>Посетитель заполнил бриф на сайте kanagency.ru.</p>
<p>&nbsp;</p>
<h3>Общая информация</h3>
<table width="100%">
    <? foreach ($attributes as $attribute => $label) { ?>
        <tr>
            <td><?= $label?>:</td>
            <td><?
                if ($attribute == 'auditory_gender') {
                    if ($model->auditory_gender == 0) {
                        echo 'не указано';
                    } else if ($model->auditory_gender == 1) {
                        echo 'мужской';
                    } else if ($model->auditory_gender == 2) {
                        echo 'женский';
                    }
                } else if ($attribute == 'deadline') {
                    if ($model->deadline !== '0000-00-00 00:00:00') {
                        echo Yii::$app->formatter->asDatetime($model->deadline,'php:d.m.Y H:i');
                    } else {
                        echo '';
                    }
                } else if ($attribute == 'budjet') {
                    if ($model->budjet == 0) {
                        echo '';
                    } else {
                        echo $model->budjet;
                    }
                } else if ($attribute == 'no_primers') {
                    if ($model->no_primers == 0) {
                        echo '<span style="color: #fff; background-color: red; padding: 1px 5px;">Нет</span>';
                    } else {
                        echo '<span style="color: #fff; background-color: #00a65a; padding: 1px 5px;">Да</span>';
                    }
                } else {
                    echo $model->{$attribute};
                }
                ?>
            </td>
        </tr>
    <? } ?>
</table>
<hr>
<?
$function_sections = $model->loadFunctions(1); // 1 - основной функционал
$add_function_sections = $model->loadFunctions(2); // 2 - доп. функционал
?>
<? foreach ($function_sections as $function_section) { ?>
    <h4><?= $function_section['name'] ?></h4>
    <div class="functions-list"><?
        $item_html = [];
        if (!empty($function_section['items'])) {
            foreach ($function_section['items'] as $item) {
                $item_html[] = '<span style="display: inline-block; margin-bottom: 15px; white-space: nowrap; padding: 3px 10px 4px 10px; border-radius: 15px; color: #fff; background-color:#00a65a">' . $item['name'] . '</span>';
            }
        } else {
            $item_html[] = '-';
        }
        echo implode(' &nbsp;', $item_html); ?>
    </div>
<? } ?>
<? foreach ($add_function_sections as $function_section) { ?>
    <h4><?= $function_section['name'] ?></h4>
    <div class="functions-list"><?
        $item_html = [];
        if (!empty($function_section['items'])) {
            foreach ($function_section['items'] as $item) {
                $item_html[] = '<span style="display: inline-block; margin-bottom: 15px; white-space: nowrap; padding: 3px 10px 4px 10px; border-radius: 15px; color: #fff; background-color:#00a65a">' . $item['name'] . '</span>';
            }
        } else {
            $item_html[] = '-';
        }
        echo implode(' &nbsp;', $item_html); ?>
    </div>
<? } ?>
<hr>
<?
$client_needs = $model->loadClientNeeds(1);
$client_needs_solve = $model->loadClientNeeds(2);
$client_needs_notsolve = $model->loadClientNeeds(3);
?>
<h3>Существующие потребности клиента</h3>
<ol>
    <? foreach ($client_needs as $client_need) { ?>
        <li><?= $client_need; ?></li>
    <? } ?>
</ol>
<h4>Как решаются потребности</h4>
<ol>
    <? foreach ($client_needs_solve as $client_need) { ?>
        <li><?= $client_need; ?></li>
    <? } ?>
</ol>
<h4>Смежные потребности</h4>
<ol>
    <? foreach ($client_needs_notsolve as $client_need) { ?>
        <li><?= $client_need; ?></li>
    <? } ?>
</ol>
<hr>
<?
$concurents = $model->loadConcurents(1);
$advantages = $model->loadConcurents(2);
?>
<h4>Конкуренты</h4>
<ol>
    <? foreach ($concurents as $item) { ?>
        <li><?= $item; ?></li>
    <? } ?>
</ol>
<h4>Преимущества</h4>
<ol>
    <? foreach ($advantages as $item) { ?>
        <li><?= $item; ?></li>
    <? } ?>
</ol>
<hr>
<? $pages = $model->loadPages(); ?>
<h3>Постраничная структура сайта</h3>
<table width="100%">
    <thead>
    <tr>
        <th>Страница</th>
        <th>Описание</th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($pages as $page) { ?>
        <tr>
            <td><?= $page['name']; ?></td>
            <td><?= $page['description']; ?></td>
        </tr>
    <? } ?>
    </tbody>
</table>
<hr>
<? $primers = $model->loadPrimers(); ?>
<h3>Примеры</h3>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Пример</th>
        <th>Что нравится</th>
        <th>Что не нравится</th>
    </tr>
    </thead>
    <tbody>
    <? foreach ($primers as $primer) { ?>
        <tr>
            <? if ($primer['link']) { ?>
                <td><a href="<?= $primer['link']; ?>" target="_blank"><?= $primer['link']; ?></a></td>
            <? } elseif ($primer['file_link']) { ?>
                <td>
                    <? $image = EasyThumbnailImage::thumbnailImg(
                        '@root' . $primer['file_link'],
                        150,
                        100,
                        EasyThumbnailImage::THUMBNAIL_INSET,
                        ['class' => 'img-responsive']
                    );
                    $image_arr = explode('src="', $image);
                    $image = $image_arr[0] . 'src="http://' . $_SERVER['HTTP_HOST'] . $image_arr[1];
                    echo $image;
                    ?>
                </td>
            <? } ?>
            <td><?= $primer['likeit']; ?></td>
            <td><?= $primer['dislikeit']; ?></td>
        </tr>
    <? } ?>
    </tbody>
</table>
<p>Всего доброго, администрация сайта kanagency.ru</p>
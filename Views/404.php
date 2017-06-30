<?php
use Core\Widgets;
use Core\HTML;
?>
<!DOCTYPE html>
<html lang="ru-ru" dir="ltr">
<!-- (c) студия Wezom | www.wezom.com.ua -->
<head>
    <?php echo Widgets::get('Head', $_seo); ?>
    <?php foreach ($_seo['scripts']['head'] as $script): ?>
        <?php echo $script; ?>
    <?php endforeach ?>
    <?php echo $GLOBAL_MESSAGE; ?>
</head>
<body>
<?php foreach ($_seo['scripts']['body'] as $script): ?>
    <?php echo $script; ?>
<?php endforeach ?>
<div class="wWrapper">
    <div class="page_404">
        <p>404</p>
        <p>Страница не найдена. Начните с <a href="<?php echo HTML::link(); ?>">Главной страницы</a>.</p>
        <a href="<?php echo HTML::link('Sitemap'); ?>">Карта сайта</a>
    </div>
</div>
</body>
</html>
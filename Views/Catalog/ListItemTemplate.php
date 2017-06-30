<?php
use Core\HTML;
use Core\Support;

?>
<li>
    <a href="<?php echo HTML::link($obj->alias . '/p' . $obj->id); ?>" class="img_tovar">
        <?php if (is_file(HOST . HTML::media('images/catalog/medium/' . $obj->image))): ?>
            <img src="<?php echo HTML::media('images/catalog/medium/' . $obj->image); ?>"
                 alt="<?php echo $obj->name; ?>">
        <?php else: ?>
            <img src="<?php echo HTML::media('pic/no-photo.png'); ?>" alt="">
        <?php endif ?>
        <?php echo Support::addItemTag($obj); ?>
    </a>
    <a href="<?php echo HTML::link($obj->alias . '/p' . $obj->id); ?>"
       class="tovar_name"><span><?php echo $obj->name; ?></span></a>
    <?php if ($obj->sale): ?>
        <div class="old_price"><span><?php echo $obj->cost_old; ?></span> грн</div>
    <?php endif; ?>
    <div class="tovar_price"><span><?php echo $obj->cost; ?></span> грн</div>
    <a href="<?php echo HTML::link($obj->alias . '/p' . $obj->id); ?>" class="buy_but"><span>КУПИТЬ</span></a>
    <a href="#enterReg5" class="enterReg5 buy_for_click"
       data-id="<?php echo $obj->id; ?>"><span>КУПИТЬ В ОДИН КЛИК</span></a>
</li>
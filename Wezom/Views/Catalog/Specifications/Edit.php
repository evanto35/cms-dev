<?php echo \Forms\Builder::open(); ?>
    <div class="form-actions" style="display: none;">
        <?php echo \Forms\Form::submit(['name' => 'name', 'value' => __('Отправить'), 'class' => 'submit btn btn-primary pull-right']); ?>
    </div>
    <div class="col-md-12">
        <div class="widget box">
            <div class="widgetContent">
                <div class="form-vertical row-border">
                    <div class="form-group">
                        <?php echo \Forms\Builder::bool($obj->status); ?>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::input([
                            'name' => 'FORM[name]',
                            'value' => $obj->name,
                            'class' => ['valid', 'translitSource'],
                        ], __('Название')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::alias([
                            'name' => 'FORM[alias]',
                            'value' => $obj->alias,
                            'class' => 'valid',
                        ], [
                            'text' => __('Алиас'),
                            'tooltip' => __('<b>Алиас (англ. alias - псевдоним)</b><br>Алиасы используются для короткого именования страниц. <br>Предположим, имеется страница с псевдонимом «<b>about</b>». Тогда для вывода этой страницы можно использовать или полную форму: <br><b>http://domain/?go=frontend&page=about</b><br>или сокращенную: <br><b>http://domain/about.html</b>'),
                        ]); ?>
                    </div>
                    <?php if($obj->type_id != 1): ?>
                        <div class="form-group">
                            <?php echo \Forms\Builder::select(\Core\Support::selectData($types, 'id', 'name', ''),
                                $obj->type_id, [
                                    'name' => 'FORM[type_id]',
                                    'class' => 'valid',
                                ], __('Тип')); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php echo \Forms\Form::close(); ?>

<?php if ($obj->type_id == 1): ?>
    <?php echo Core\View::tpl(['list' => $list], 'Catalog/Specifications/ValuesColor'); ?>
<?php else: ?>
    <?php echo Core\View::tpl(['list' => $list], 'Catalog/Specifications/ValuesSimple'); ?>
<?php endif ?>

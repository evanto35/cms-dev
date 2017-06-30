<?php echo \Forms\Builder::open(); ?>
    <div class="form-actions" style="display: none;">
        <?php echo \Forms\Form::submit(['name' => 'name', 'value' => __('Отправить'), 'class' => 'submit btn btn-primary pull-right']); ?>
    </div>
    <div class="col-md-7">
        <div class="widget box">
            <div class="widgetHeader">
                <div class="widgetTitle">
                    <i class="fa fa-reorder"></i>
                    <?php echo __('Основные данные'); ?>
                </div>
            </div>
            <div class="widgetContent">
                <div class="form-vertical row-border">
                    <div class="form-group">
                        <?php echo \Forms\Builder::input([
                            'name' => 'FORM[h1]',
                            'value' => $obj->h1,
                        ], [
                            'text' => 'H1',
                            'tooltip' => __('Рекомендуется, чтобы тег h1 содержал ключевую фразу, которая частично или полностью совпадает с title'),
                        ]); ?>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::input([
                            'name' => 'FORM[title]',
                            'value' => $obj->title,
                        ], [
                            'text' => 'Title',
                            'tooltip' => __('<p>Значимая для продвижения часть заголовка должна быть не более 12 слов</p><p>Самые популярные ключевые слова должны идти в самом начале заголовка и уместиться в первых 50 символов, чтобы сохранить привлекательный вид в поисковой выдаче.</p><p>Старайтесь не использовать в заголовке следующие знаки препинания – . ! ? – </p>'),
                        ]); ?>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::textarea([
                            'name' => 'FORM[keywords]',
                            'rows' => 5,
                            'value' => $obj->keywords,
                        ], [
                            'text' => 'Keywords',
                        ]); ?>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::textarea([
                            'name' => 'FORM[description]',
                            'value' => $obj->description,
                            'rows' => 5,
                        ], [
                            'text' => 'Description',
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-5">

        <div class="widget">
            <div class="widgetHeader">
                <div class="widgetTitle">
                    <i class="fa fa-reorder"></i>
                    <?php echo __('Переменные'); ?>
                </div>
            </div>
            <div class="pageInfo alert alert-info">
                <?php if ( $obj->id == 1 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Название группы'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Текст со страницы группы'); ?></strong></div>
                        <div class="col-md-6">{{content}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Текст со страницы группы (N первых символов включая пробелы, но с вырезанными HTML тегами)'); ?></strong></div>
                        <div class="col-md-6">{{content:N}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 2 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Наименование товара'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Наименование группы товара'); ?></strong></div>
                        <div class="col-md-6">{{group}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Наименование бренда товара'); ?></strong></div>
                        <div class="col-md-6">{{brand}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Наименование модели товара'); ?></strong></div>
                        <div class="col-md-6">{{model}}</div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php echo \Forms\Form::close(); ?>
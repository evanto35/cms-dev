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
                        <label class="control-label"><?php echo __('Наименование шаблона'); ?></label>
                        <div class="red" style="font-weight: bold;"><?php echo $obj->name; ?></div>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::bool($obj->status); ?>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::input([
                            'name' => 'FORM[subject]',
                            'value' => $obj->subject,
                            'class' => 'valid',
                        ], __('Тема')); ?>
                    </div>
                    <div class="form-group">
                        <?php echo \Forms\Builder::tiny([
                            'name' => 'FORM[text]',
                            'value' => $obj->text,
                        ], __('Шаблон')); ?>
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
                <div class="rowSection">
                    <div class="col-md-6"><strong><?php echo __('Доменное имя сайта'); ?></strong></div>
                    <div class="col-md-6">{{site}}</div>
                </div>
                <?php if( $obj->id != 15 && $obj->id != 20 && $obj->id != 21 && $obj->id != 26 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong>IP</strong></div>
                        <div class="col-md-6">{{ip}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Текущая дата в формате dd.mm.YYYY'); ?></strong></div>
                        <div class="col-md-6">{{date}}</div>
                    </div>
                <?php endif; ?>
                <?php if ( $obj->id == 1 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('E-Mail'); ?></strong></div>
                        <div class="col-md-6">{{email}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Текст сообщения'); ?></strong></div>
                        <div class="col-md-6">{{text}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 2 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка для отмены рассылки'); ?></strong></div>
                        <div class="col-md-6">{{link}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('E-Mail'); ?></strong></div>
                        <div class="col-md-6">{{email}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 3 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Номер телефона'); ?></strong></div>
                        <div class="col-md-6">{{phone}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 4 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка для подтверждения'); ?></strong></div>
                        <div class="col-md-6">{{link}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 5 OR $obj->id == 6 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Новый пароль для входа'); ?></strong></div>
                        <div class="col-md-6">{{password}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 7 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Город'); ?></strong></div>
                        <div class="col-md-6">{{city}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Текст коментария'); ?></strong></div>
                        <div class="col-md-6">{{text}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Наименование товара'); ?></strong></div>
                        <div class="col-md-6">{{item_name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на товар на сайте'); ?></strong></div>
                        <div class="col-md-6">{{link}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на товар в админ-панели'); ?></strong></div>
                        <div class="col-md-6">{{link_admin}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 8 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Указанный номер телефона'); ?></strong></div>
                        <div class="col-md-6">{{phone}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Наименование товара'); ?></strong></div>
                        <div class="col-md-6">{{item_name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на товар на сайте'); ?></strong></div>
                        <div class="col-md-6">{{link}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на товар в админ-панели'); ?></strong></div>
                        <div class="col-md-6">{{link_admin}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 9 OR $obj->id == 10 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('E-Mail'); ?></strong></div>
                        <div class="col-md-6">{{email}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Вопрос'); ?></strong></div>
                        <div class="col-md-6">{{question}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Наименование товара'); ?></strong></div>
                        <div class="col-md-6">{{item_name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на товар на сайте'); ?></strong></div>
                        <div class="col-md-6">{{link}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на товар в админ-панели'); ?></strong></div>
                        <div class="col-md-6">{{link_admin}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 11 OR $obj->id == 12 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Телефон'); ?></strong></div>
                        <div class="col-md-6">{{phone}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Способ оплаты'); ?></strong></div>
                        <div class="col-md-6">{{payment}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Способ доставки (с номером склада, если нужно)'); ?></strong></div>
                        <div class="col-md-6">{{delivery}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Список заказаных товаров'); ?></strong></div>
                        <div class="col-md-6">{{items}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на заказ в кабинете пользователя'); ?></strong></div>
                        <div class="col-md-6">{{link_user}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на заказ в админ-панели'); ?></strong></div>
                        <div class="col-md-6">{{link_admin}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 15 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Номер заказа'); ?></strong></div>
                        <div class="col-md-6">{{id}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Фамилия'); ?></strong></div>
                        <div class="col-md-6">{{last_name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Отчество'); ?></strong></div>
                        <div class="col-md-6">{{middle_name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Список заказаных товаров'); ?></strong></div>
                        <div class="col-md-6">{{items}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Стоимость товаров'); ?></strong></div>
                        <div class="col-md-6">{{amount}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Скидка'); ?></strong></div>
                        <div class="col-md-6">{{discount}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Стоимость заказа с учетом скидки'); ?></strong></div>
                        <div class="col-md-6">{{real_amount}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 20 || $obj->id == 21 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя, указанное при заказе'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Фамилия, указанное при заказе'); ?></strong></div>
                        <div class="col-md-6">{{last_name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Отчество, указанное при заказе'); ?></strong></div>
                        <div class="col-md-6">{{middle_name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Номер заказа'); ?></strong></div>
                        <div class="col-md-6">{{id}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Сумма заказа'); ?></strong></div>
                        <div class="col-md-6">{{amount}}</div>
                    </div>
                <?php endif ?>
                <?php if ( $obj->id == 26 ): ?>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('E-Mail'); ?></strong></div>
                        <div class="col-md-6">{{email}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Пароль'); ?></strong></div>
                        <div class="col-md-6">{{password}}</div>
                    </div>
                <?php endif ?>
				<?php if ( $obj->id == 27 ): ?>
					<div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Имя'); ?></strong></div>
                        <div class="col-md-6">{{name}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('E-Mail'); ?></strong></div>
                        <div class="col-md-6">{{email}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Отзыв'); ?></strong></div>
                        <div class="col-md-6">{{text}}</div>
                    </div>
                    <div class="rowSection">
                        <div class="col-md-6"><strong><?php echo __('Ссылка на отзыв в админ-панели'); ?></strong></div>
                        <div class="col-md-6">{{link_admin}}</div>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php echo \Forms\Form::close(); ?>
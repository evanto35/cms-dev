<?php echo \Forms\Builder::open(); ?>
    <div class="form-actions" style="display: none;">
        <?php echo \Forms\Form::submit(['name' => 'name', 'value' => __('Отправить'), 'class' => 'submit btn btn-primary pull-right']); ?>
    </div>
	 <div class="col-md-12">
		<div class="widget">
			<div class="widgetContent">
				<?php foreach ($result as $obj): ?>
					<?php echo \Core\View::tpl(['obj' => $obj], 'Mobile/Row'); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

<?php echo \Forms\Form::close(); ?>

<script>
    $(function(){
        var input;
        $('input[type="password"]').closest('div').addClass('input-group');
        $('.showPassword').on('click', function(){
            input = $(this).closest('div.input-group').find('input');
            if(input.attr('type') == 'password') {
                input.attr('type', 'text');
                $(this).text('Скрыть');
            } else {
                input.attr('type', 'password');
                $(this).text('Показать');
            }
        });;

    });
</script>
<div class="form-group">
    <?php $attributes = [
        'name' => $obj->key,
        'rows' => 5,
        'value' => $obj->value,
    ]; ?>
    <?php if($obj->valid): ?>
        <?php $attributes['class'] = 'valid'; ?>
    <?php endif; ?>

    <?php if($obj->type == 'textarea'): ?>
        <?php $attributes['rows'] = 5; ?>
        <?php echo \Forms\Builder::textarea($attributes, __($obj->name)); ?>
    <?php elseif($obj->type == 'tiny'): ?>
        <?php echo \Forms\Builder::tiny($attributes, __($obj->name)); ?>
    <?php elseif($obj->type == 'select'): ?>
        <?php $values = json_decode($obj->values, true); ?>
        <?php echo \Forms\Builder::select(\Core\Support::selectData($values, 'value', 'key'), $obj->value, $attributes, __($obj->name)); ?>
    <?php elseif($obj->type == 'radio'): ?>
        <?php echo \Forms\Form::label(__($obj->name)); ?>
        <div class="clear"></div>
        <?php $values = json_decode($obj->values, true); ?>
        <?php foreach($values AS $v): ?>
            <?php $attr = $attributes; ?>
            <label class="checkerWrap-inline radioWrap col-md-4" style="margin-right: 0;">
                <?php $attr['value'] = $v['value']; ?>
                <?php echo \Forms\Builder::radio($obj->value == $v['value'] ? true : false, $attr); ?>
                <?php echo $v['key']; ?>
            </label>
        <?php endforeach; ?>
    <?php elseif($obj->type == 'password'): ?>
        <?php echo \Forms\Form::label($obj->name, ['for' => 'field_'.$obj->id]); ?>
        <div class="clear"></div>
        <?php $attributes['id'] = 'field_'.$obj->id; ?>
        <?php $attributes['autocomplete'] = 'off'; ?>
        <?php echo \Forms\Builder::password($attributes); ?>
        <span class="input-group-btn" style="vertical-align: bottom;">
            <?php echo \Forms\Form::button(__('Показать'), [
                'type' => 'button',
                'class' => 'btn showPassword',
            ]); ?>
        </span>
	<?php elseif ($obj->type == 'image'): ?>
		<label class="control-label" for="f_date"><?php echo __($obj->name); ?></label>
		<div class="">
			<?php if (is_file( HOST . Core\HTML::media('images/'.$obj->key.'/original/'.$obj->value) )): ?>
				<a href="<?php echo Core\HTML::media('images/'.$obj->key.'/original/'.$obj->value); ?>" rel="lightbox">
					<img src="<?php echo Core\HTML::media('images/'.$obj->key.'/small/'.$obj->value); ?>" style="max-height: 100px;" />
				</a>
				<br />
				<a href="/wezom/<?php echo Core\Route::controller(); ?>/delete_image/<?php echo $obj->id; ?>"><?php echo __('Удалить изображение'); ?></a>
				<br />
				<a href="<?php echo \Core\General::crop($obj->key, 'small', $obj->value); ?>"><?php echo __('Редактировать'); ?></a>
			<?php else: ?>
				<input type="file" name="<?php echo $obj->key; ?>" />
			<?php endif ?>
		</div>
    <?php else: ?>
        <?php echo \Forms\Builder::input($attributes, __($obj->name)); ?>
    <?php endif; ?>
</div>
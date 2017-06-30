<?php
use Core\HTML;
use Core\Text;

?>
<?php foreach ($result as $obj): ?>
    <div class="news clearFix">
		<p><?php echo $obj->name; ?> <?php echo date('d.m.Y', $obj->date); ?></p>
		<p><?php echo $obj->text; ?></p>
		<?php if ($obj->answer) :?>
			<p>Админитратор <?php echo date('d.m.Y', $obj->date_answer); ?></p>
			<p><?php echo $obj->answer; ?></p>
		<?php endif; ?>
    </div>
<?php endforeach; ?>
<?php echo $pager; ?>

<div class="leave_otziv_block">
	<div form="true" class="wForm" data-ajax="review">
		<div class="title">оставь свой отзыв</div>
		<div class="wFormRow">
			<input type="text" data-name="name" name="name" placeholder="Имя" data-rule-bykvu="true"
				   data-rule-minlength="2" required="">
			<label>Имя</label>
		</div>
		<div class="wFormRow">
			<input type="email" data-name="email" name="email" data-rule-email="true" placeholder="E-mail"
				   data-rule-minlength="2" data-rule-required="true">
			<label>E-mail</label>
		</div>
		<div class="wFormRow">
			<textarea name="text" data-name="text" placeholder="Ваш отзыв" required=""></textarea>
			<label>Ваш отзыв</label>
		</div>
		<?php if (array_key_exists('token', $_SESSION)): ?>
			<input type="hidden" data-name="token" value="<?php echo $_SESSION['token']; ?>"/>
		<?php endif; ?>
		<div class="tal">
			<button class="wSubmit enterReg_btn">Отправить</button>
		</div>
	</div>
</div>
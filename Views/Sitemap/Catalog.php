<?php 
	use Core\HTML;
	use Core\Arr;
	use Core\View;
?>
<li><a href="<?php echo HTML::link('products'); ?>"><?php echo $obj->name; ?></a>
	<ul>
	<?php foreach ($result[$obj->id] as $item): ?>
		<?php if ($item->alias == 'catalog_groups' and sizeof(Arr::get($links,'catalog_groups'))): ?>
			<?php echo View::tpl(['result' => $links['catalog_groups'], 'cur' => 0, 'add' => 'products', 'items' => Arr::get($links,'catalog_items')], 'Sitemap/Recursive'); ?>
		<?php elseif ($item->alias == 'catalog_brands'): ?>
			<li><a href="<?php echo HTML::link('bransd'); ?>"><?php echo $item->name; ?></a>
				<?php if (isset($links['brands_list']) and sizeof($links['brands_list'])): ?>
				<ul>
					<?php foreach ($links['brands_list'] as $brand): ?>
					<li><a href="<?php echo HTML::link('brands/'.$brand->alias); ?>"><?php echo $brand->name; ?></a></li>
					<?php endforeach; ?>
				</ul>
				<?php endif; ?>
			</li>
		<?php elseif($item->alias == 'catalog_sale'): ?>
			<li><a href="<?php echo HTML::link('sale'); ?>"><?php echo $item->name; ?></a></li>
		<?php elseif($item->alias == 'catalog_new'): ?>
			<li><a href="<?php echo HTML::link('new'); ?>"><?php echo $item->name; ?></a></li>
		<?php elseif($item->alias == 'catalog_popular'): ?>
			<li><a href="<?php echo HTML::link('popular'); ?>"><?php echo $item->name; ?></a></li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
</li>
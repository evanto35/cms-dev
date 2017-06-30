<div class="rowSection clearFix">
    <div class="col-md-12">
        <div class="widget">
            <div class="widgetHeader" style="padding-bottom: 10px;">
				  <?php echo \Forms\Form::open(['class' => 'widgetContent filterForm', 'action' => '/wezom/'.Core\Route::controller().'/devices']); ?>

                    <div class="col-md-2">
                        <?php echo \Forms\Builder::input([
                            'name' => 'date_s',
                            'value' => Core\Arr::get($_GET, 'date_s', NULL),
                            'class' => 'fPicker',
                        ], __('Дата от')); ?>
                    </div>
                    <div class="col-md-2">
                        <?php echo \Forms\Builder::input([
                            'name' => 'date_po',
                            'value' => Core\Arr::get($_GET, 'date_po', NULL),
                            'class' => 'fPicker',
                        ], __('Дата до')); ?>
                    </div>
                                      <div class="col-md-1">
                        <label class="control-label" style="height:16px;">&nbsp </label>
                        <?php echo \Forms\Form::submit([
                            'class' => 'btn btn-primary',
                            'value' => __('Подобрать'),
                        ]); ?>
                    </div>
                    <div class="col-md-1">
                        <label class="control-label" style="height:22px;"></label>
                        <div class="">
                            <div class="controls">
                                <a href="/wezom/<?php echo Core\Route::controller(); ?>/devices">
                                    <i class="fa fa-refresh"></i>
                                    <span class="hidden-xx"><?php echo __('Сбросить'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php echo \Forms\Form::close(); ?>
            </div>
		</div>
		<div class="widget">
            <div class="widgetContent">
                <div class="visitChartSize" id="visitChart"></div>
            </div>
            <div class="divider"></div>
            <div class="widgetContent">
                <ul class="stats" id="stats">
					<?php foreach ($data['hits'] as $key=>$val): ?>
					<li class="test"><strong><?php echo $val; ?></strong> <small><?php echo __('Переходов '.$key); ?></small> </li>
					<?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        if( $('#visitChart').length){
			$('#visitChart').highcharts({
				title: { text: null },
				subtitle: { text: 'Источник: weZom CMS' },
				tooltip: { shared: true, crosshairs: true },
				plotOptions: { series: { cursor: 'pointer', marker: { lineWidth: 1 } } },
				series: [
							<?php foreach ($data['visitors'] as $key=>$values): ?>
							{ name: '<?php echo __('Уникальные посетители '.$key); ?>', data: [<?php echo implode(', ', $values); ?>]},
							<?php endforeach; ?>
							<?php $i=0; ?>
							<?php foreach ($data['visits'] as $key=>$values): ?>
							<?php $i++; ?>
							{ name: '<?php echo __('Всего переходов '.$key); ?>', data: [<?php echo implode(', ', $values); ?>] }<?php if ($i<count($data['visits'])) echo ','; ?> 
							<?php endforeach; ?>
						],
				yAxis: { title: { text: 'Количество' }, allowDecimals: false, floor: 0 },
				xAxis: { type: 'date', categories: ["<?php echo implode('", "', $data['dates']); ?>"], offset: 0, tickmarkPlacement: 'on' }
			});
        }
    });
</script>
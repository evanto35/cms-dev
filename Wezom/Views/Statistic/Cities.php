<div class="rowSection clearFix">
    <div class="col-md-12">
        <div class="widget">
            <div class="widgetHeader" style="padding-bottom: 10px;">
				  <?php echo \Forms\Form::open(['class' => 'widgetContent filterForm', 'action' => '/wezom/'.Core\Route::controller().'/cities']); ?>

                    <div class="col-md-2">
                        <?php echo \Forms\Builder::input([
                            'name' => 'date',
                            'value' => Core\Arr::get($_GET, 'date', NULL),
                            'class' => 'fPicker',
                        ], __('Дата')); ?>
                    </div>
					<div class="col-md-1">
                        <label class="control-label" style="height:16px;">&nbsp</label>
                        <?php echo \Forms\Form::submit([
                            'class' => 'btn btn-primary',
                            'value' => __('Подобрать'),
                        ]); ?>
                    </div>
                    <div class="col-md-1">
                        <label class="control-label" style="height:22px;"></label>
                        <div class="">
                            <div class="controls">
                                <a href="/wezom/<?php echo Core\Route::controller(); ?>/cities">
                                    <i class="fa fa-refresh"></i>
                                    <span class="hidden-xx"><?php echo __('Сбросить'); ?></span>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php echo \Forms\Form::close(); ?>
            </div>
		</div>
		<div class="widget col-md-6" >
            <div class="widgetContent">
                <div class="visitChartSize" id="visitChart"></div>
            </div>
        </div>
		<div class="widget col-md-6" >
            <div class="widgetContent">
                <div class="visitChartSize" id="allVisitsChart"></div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function(){
        if( $('#visitChart').length){
			$('#visitChart').highcharts({
				title: { text: '<?php echo __('Уникальных посетителей'); ?>' },
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				subtitle: { text: 'Источник: weZom CMS' },
				tooltip: { shared: true, crosshairs: true },
				plotOptions: { series: { cursor: 'pointer', marker: { lineWidth: 1 } } },
				series: [{
					name: '<?php echo __('Города'); ?>',
					colorByPoint: true,
					data: [
					<?php $i=0; ?>
					<?php foreach ($result as $obj): ?>
					<?php $i++; ?>
					{
						name: '<?php echo $obj->country.' '.$obj->region.' '.$obj->city; ?>',
						y: <?php echo $obj->unique_visitors; ?>
					}<?php if ($i<count($result)) echo ','; ?>
					<?php endforeach; ?>
					]
				}]
			});
        }
		
		if( $('#allVisitsChart').length){
			$('#allVisitsChart').highcharts({
				title: { text: '<?php echo __('Всего переходов'); ?>' },
				chart: {
					plotBackgroundColor: null,
					plotBorderWidth: null,
					plotShadow: false,
					type: 'pie'
				},
				subtitle: { text: 'Источник: weZom CMS' },
				tooltip: { shared: true, crosshairs: true },
				plotOptions: { series: { cursor: 'pointer', marker: { lineWidth: 1 } } },
				series: [{
					name: '<?php echo __('Города'); ?>',
					colorByPoint: true,
					data: [
					<?php $i=0; ?>
					<?php foreach ($result as $obj): ?>
					<?php $i++; ?>
					{
						name: '<?php echo $obj->country.' '.$obj->region.' '.$obj->city; ?>',
						y: <?php echo $obj->all_enters; ?>
					}<?php if ($i<count($result)) echo ','; ?>
					<?php endforeach; ?>
					]
				}]
			});
        }
    });
</script>
<?php if(\Core\User::god() || \Core\User::caccess() == 'edit'): ?>
    <div class="widget box">
        <div class="widgetHeader"><div class="widgetTitle"><i class="fa fa-reorder"></i><?php echo __('Добавить значение'); ?></div></div>
        <div class="widgetContent">
            <table class="table table-striped table-bordered table-hover checkbox-wrap">
                <thead>
                    <tr>
                        <th class="align-center"><?php echo __('Название'); ?></th>
                        <th class="align-center"><?php echo __('Цвет'); ?></th>
                        <th class="align-center"><?php echo __('Алиас'); ?></th>
                        <th class="align-center"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="align-center">
                            <?php echo \Forms\Builder::input([
                                'id' => 'sName',
                                'data-trans' => 2,
                                'class' => 'translitSource',
                            ]); ?>
                        </td>
                        <td class="align-center">
                            <?php echo \Forms\Builder::input([
                                'id' => 'sColor',
                                'class' => 'hue',
                                'value' => '#ffffff',
                            ]); ?>
                        </td>
                        <td class="align-center input-group">
                            <?php echo \Forms\Builder::input([
                                'id' => 'sAlias',
                                'data-trans' => 2,
                                'class' => 'translitConteiner',
                            ]); ?>
                            <span class="input-group-btn">
                                <?php echo \Forms\Form::button(__('Заполнить автоматически'), [
                                    'type' => 'button',
                                    'class' => 'btn translitAction',
                                    'data-trans' => 2,
                                ]); ?>
                            </span>
                        </td>
                        <td class="align-center">
                            <a title="Сохранить" id="sSave" href="#" class="btn btn-xs bs-tooltip liTipLink" style="white-space: nowrap; margin-top: 7px;">
                                <i class="fa fa-fixed-width">&#xf0c7;</i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
<div class="widget box">
    <div class="widgetHeader"><div class="widgetTitle"><i class="fa fa-reorder"></i><?php echo __('Список значений'); ?></div></div>
    <div class="widgetContent" id="sValuesList">
        <table class="table table-striped table-bordered table-hover checkbox-wrap" data-specification="<?php echo Core\Route::param('id'); ?>">
            <thead>
                <tr>
                    <th class="align-center"><?php echo __('Название'); ?></th>
                    <th class="align-center"><?php echo __('Цвет'); ?></th>
                    <th class="align-center"><?php echo __('Алиас'); ?></th>
                    <th class="align-center"><?php echo __('Статус'); ?></th>
                    <?php if(\Core\User::god() || \Core\User::caccess() == 'edit'): ?>
                        <th class="align-center"></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="sortableSimple" data-params="<?php echo \Core\HTML::chars(json_encode(['table' => 'specifications_values'])); ?>">
                <?php foreach ( $list as $obj ): ?>
                    <tr id="sort_<?php echo $obj->id; ?>" data-id="<?php echo $obj->id; ?>">
                        <td class="align-center">
                            <?php $attributes = [
                                'value' => $obj->name,
                                'data-trans' => 'val-'.$obj->id,
                                'class' => ['translitSource', 'sName'],
                            ]; ?>
                            <?php if(!\Core\User::god() && \Core\User::caccess() != 'edit'): ?>
                                <?php $attributes['disabled'] = 'disabled'; ?>
                            <?php endif; ?>
                            <?php echo \Forms\Builder::input($attributes); ?>
                        </td>
                        <td class="align-center">
                            <?php $attributes = [
                                'value' => $obj->color,
                                'class' => ['hue', 'sColor'],
                            ]; ?>
                            <?php if(!\Core\User::god() && \Core\User::caccess() != 'edit'): ?>
                                <?php $attributes['disabled'] = 'disabled'; ?>
                            <?php endif; ?>
                            <?php echo \Forms\Builder::input($attributes); ?>
                        </td>
                        <td class="align-center  <?php echo !(\Core\User::god() || \Core\User::caccess() == 'edit') ?: 'input-group' ?>">
                            <?php $attributes = [
                                'value' => $obj->alias,
                                'data-trans' => 'val-'.$obj->id,
                                'class' => ['translitConteiner', 'sAlias'],
                            ]; ?>
                            <?php if(!\Core\User::god() && \Core\User::caccess() != 'edit'): ?>
                                <?php $attributes['disabled'] = 'disabled'; ?>
                            <?php endif; ?>
                            <?php echo \Forms\Builder::input($attributes); ?>
                            <?php if(\Core\User::god() || \Core\User::caccess() == 'edit'): ?>
                                <span class="input-group-btn">
                                    <?php echo \Forms\Form::button(__('Заполнить автоматически'), [
                                        'type' => 'button',
                                        'class' => 'btn translitAction',
                                        'data-trans' => 'val-'.$obj->id,
                                    ]); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="align-center">
                            <label class="ckbxWrap" style="top: 8px; left: 6px;">
                                <?php $attributes = [
                                    'class' => 'sStatus',
                                    'value' => 1,
                                ]; ?>
                                <?php if(!\Core\User::god() && \Core\User::caccess() != 'edit'): ?>
                                    <?php $attributes['disabled'] = 'disabled'; ?>
                                <?php endif; ?>
                                <?php echo \Forms\Builder::checkbox($obj->status, $attributes); ?>
                            </label>
                        </td>
                        <?php if(\Core\User::god() || \Core\User::caccess() == 'edit'): ?>
                            <td class="align-center" style="width: 80px;">
                                <a title="<?php echo __('Сохранить изменения'); ?>" href="#" class="sSave btn btn-xs bs-tooltip liTipLink" style="white-space: nowrap; margin-top: 7px;"><i class="fa fa-fixed-width">&#xf0c7;</i></a>
                                <a title="<?php echo __('Удалить'); ?>" href="#" class="sDelete btn btn-xs bs-tooltip liTipLink" style="white-space: nowrap; margin-top: 7px;"><i class="fa fa-trash-o"></i></a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="sParametersColor" data-id="<?php echo Core\Route::param('id'); ?>"></div>


<!-- SCRIPT ZONE -->
<script>
    $(function(){
        if( $('#sParametersColor').length ) {
            // Start colorpicker
            var setMinicolor = function(){
                $('.hue').each( function() {
                    $(this).minicolors({
                        control: 'hue',
                        defaultValue: $(this).val(),
                        position: 'bottom right',
                        change: function(hex, opacity) {
                            if( !hex ) return;
                            if( opacity ) hex += ', ' + opacity;
                        },
                        theme: 'bootstrap'
                    });
                });
            };
            setMinicolor();
            // Specification id
            var sID = $('#sParametersColor').data('id');
            // Message with error for admin
            var generate_warning = function( message ) {
                $(document).alert2({
                    message: message,
                    headerCOntent: false,
                    footerContent: false,
                    typeMessage: 'warning' //false or 'warning','success','info','danger'
                });
            };;
            // Set checkbox
            var checkboxStart = function( el ) {
                var parent = el.parent();
                if(parent.is('label')){
                    if(el.prop('checked')){
                        parent.addClass('checked');
                    } else {
                        parent.removeClass('checked');
                    }
                }
            };;
            // Generate a row from object
            var generateTR = function( obj ) {
                var html = '';
                html  = '<tr id="sort_'+obj.id+'" data-id="'+obj.id+'">';
                html += '<td class="align-center">';
                html += '<input type="text" class="form-control sName translitSource" data-trans="val-'+obj.id+'" value="'+obj.name+'" />';
                html += '</td>';
                html += '<td class="align-center">';
                html += '<input type="text" class="form-control sColor hue" value="'+obj.color+'" />';
                html += '</td>';
                html += '<td class="align-center input-group">';
                html += '<input class="form-control sAlias translitConteiner" data-trans="val-'+obj.id+'" type="text" value="'+obj.alias+'" />';
                html += '<span class="input-group-btn">' +
                        '<button class="btn translitAction" data-trans="val-'+obj.id+'" type="button"><?php echo __('Заполнить автоматически'); ?></button>' +
                        '</span>';
                html += '</td>';
                html += '<td class="align-center"><label class="ckbxWrap" style="top: 8px; left: 6px;">';
                if ( parseInt( obj.status ) == 1 ) {
                    html += '<input class="sStatus" type="checkbox" value="1" checked />';
                } else {
                    html += '<input class="sStatus" type="checkbox" value="1" />';
                }
                html += '</label></td>';
                html += '<td class="align-center">';
                html += '<a title="<?php echo __('Сохранить изменения'); ?>" href="#" class="sSave btn btn-xs bs-tooltip liTipLink" style="white-space: nowrap; margin-top: 7px;"><i class="fa fa-fixed-width">&#xf0c7;</i></a>';
                html += '<a title="<?php echo __('Удалить'); ?>" href="#" class="sDelete btn btn-xs bs-tooltip liTipLink" style="white-space: nowrap; margin-top: 7px;"><i class="fa fa-trash-o"></i></a>';
                html += '</td>';
                html += '</tr>';
                return html;
            };;
            // Add a row
            $('#sSave').on('click', function(e){
                e.preventDefault();
                loader($('#sValuesList'), 1);
                var name = $('#sName').val();
                var color = $('#sColor').val();
                var alias = $('#sAlias').val();
                $.ajax({
                    url: '/wezom/ajax/catalog/addColorSpecificationValue',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        specification_id: sID,
                        name: name,
                        color: color,
                        alias: alias
                    },
                    success: function(data){
                        if( data.success ) {
                            var html = '';
                            if( data.result.length ) {
                                for( var i = 0; i < data.result.length; i++ ) {
                                    html += generateTR(data.result[i]);
                                }
                            }
                            $('#sValuesList tbody').html(html);
                            $('#sValuesList input[type=checkbox]').each(function(){ checkboxStart($(this)); });
                            $('#sValuesList input[type=checkbox]').on('change',function(){ checkboxStart($(this)); });
                            $('#sName').val('');
                            $('#sAlias').val('');
                            $('#sColor').val('#ffffff');
                            setMinicolor();
                        } else {
                            generate_warning(data.error);
                        }
                        loader($('#sValuesList'), 0);
                    }
                });
            });
            // Save a row
            $('#sValuesList').on('click', '.sSave', function(e){
                e.preventDefault();
                loader($('#sValuesList'), 1);
                var tr = $(this).closest('tr');
                var id = tr.data('id');
                var name = tr.find('.sName').val();
                var color = tr.find('.sColor').val();
                var alias = tr.find('.sAlias').val();
                var status = 0;
                if( tr.find('.sStatus').parent().hasClass('checked') ) {
                    status = 1;
                }
                $.ajax({
                    url: '/wezom/ajax/catalog/editColorSpecificationValue',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        specification_id: sID,
                        name: name,
                        status: status,
                        id: id,
                        color: color,
                        alias: alias
                    },
                    success: function(data){
                        if( data.success ) {
                            var html = '';
                            if( data.result.length ) {
                                for( var i = 0; i < data.result.length; i++ ) {
                                    html += generateTR(data.result[i]);
                                }
                            }
                            $('#sValuesList tbody').html(html);
                            $('#sValuesList input[type=checkbox]').each(function(){ checkboxStart($(this)); });
                            $('#sValuesList input[type=checkbox]').on('change',function(){ checkboxStart($(this)); });
                            setMinicolor();
                        } else {
                            generate_warning(data.error);
                        }
                        loader($('#sValuesList'), 0);
                    }
                });
            });
            // Delete a row
            $('#sValuesList').on('click', '.sDelete', function(e){
                e.preventDefault();
                loader($('#sValuesList'), 1);
                var tr = $(this).closest('tr');
                var id = tr.data('id');
                $.ajax({
                    url: '/wezom/ajax/catalog/deleteSpecificationValue',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        id: id
                    },
                    success: function(data){
                        if( data.success ) {
                            tr.remove();
                        } else {
                            generate_warning(data.error);
                        }
                        loader($('#sValuesList'), 0);
                    }
                });
            });
        }
    });
</script>
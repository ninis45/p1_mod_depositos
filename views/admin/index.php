<div class="content" ng-controller="IndexCtrl">
    <div class="lead text-success"><?=lang('depositos:title')?></div>
<?php if($this->current_user->group!='director'):?>
    <?php echo form_open('admin/depositos/'.$anio, 'class="form-inline" method="get" ') ?>
    		
    			
                <div class="form-group col-md-5">
    				<label for="f_concepto">Centro: </label>
                    
                    <?=form_dropdown('f_centro',array(''=>' [ Todos ] ')+$centros,false,'class="form-control"')?>
    			</div>
    		
    
    			<button class="md-raised btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                <?php if($_GET):?>
                <a href="<?=base_url('admin/depositos/'.$anio)?>" class="md-raised btn btn-success"><i class="fa fa-refresh"></i> Mostrar todos</a>
                <?php endif;?>
                
                
                <a href="<?=base_url('admin/depositos/export/'.$anio.'/?'.$_SERVER["QUERY_STRING"])?>" class="md-raised btn btn-primary" tooltip-placement="left" uib-tooltip="Descargar XLS">Descargar <?=$anio?></a>
    			<a href="#" ng-click="open_import()" class="md-raised btn btn-primary" tooltip-placement="left" uib-tooltip="Importar XLS"><i class="fa fa-upload"></i> Importar</a>
    			
    		
    	<?php echo form_close() ?>
        <hr />
<?php else:?>
<div class="alert alert-info"><?=lang('depositos:welcome');?></div>
<?php endif;?>
    <?php if($items):?>
    <div class="ui-tab-container ui-tab-vertical">
    
        <uib-tabset class="ui-tab">
            <?php foreach($items as $mes => $data){?>
            <uib-tab heading="<?=month_long($mes)?>">
                    <h4 class="text-success"><?=month_long($mes).' '.$anio?> <a href="<?=base_url('admin/depositos/export/'.$anio.'/'.$mes.'?'.$_SERVER["QUERY_STRING"])?>" ui-wave class="btn-icon btn-icon-sm btn-primary pull-right" tooltip-placement="left" uib-tooltip="Descargar  <?=month_long($mes).' '.$anio?>"><i class="fa fa-download"></i></a></h4>
                    <hr />
                    <?php if($this->current_user->group!='director'):?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="5%"></th>
                                <th>Centro</th>
                                <th>Director o responsable</th>
                                <th width="14%">Total</th>
                                
                                <th width="15%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($data as $fondo){?>
                            <tr>
                                <td><a ng-click="isCollapsed_<?=$fondo['id_director']?> = !isCollapsed_<?=$fondo['id_director']?>" ui-wave class="btn-icon  btn-icon-sm btn-tumblr" href="#" title="Mostrar <?=count($fondo['depositos'])?> depósitos"><i class="fa fa-plus"></i></a></td>
                                <td><?=$fondo['nombre_centro']?></td>
                                <td><?=$fondo['nombre_director']?></td>
                                <td class="text-right"><?=number_format($fondo['importe'],2);?></td>
                                
                                <td>
                                    
                                    
                                    
                                    <!--a href="<?=base_url('admin/depositos/create/'.$anio.'/'.$mes.'/'.$fondo['id_director']);?>" ui-wave class="btn-icon  btn-icon-sm btn-success" title="Registrar depósito"><i class="fa fa-credit-card"></i></a-->
                                    <?php if(group_has_role('depositos','delete')):?>
                                    <a href="<?=base_url('admin/depositos/delete/'.$anio.'/'.$mes.'/'.$fondo['id_director'])?>" ui-wave class="btn-icon  btn-icon-sm btn-danger" title="Eliminar todos los registros" confirm-action ><i class="fa fa-trash"></i></a>
                                    <?php endif;?>
                                </td>
                            </tr>
                            <tr uib-collapse="!isCollapsed_<?=$fondo['id_director']?>">
                                <td colspan="7" >
                                
                                    <table class="" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="14%">
                                                    Fecha
                                                </th>
                                                
                                                
                                                <th width="20%">Tipo</th>
                                                <th>Concepto</th>
                                                <th width="10%">
                                                    Importe
                                                </th>
                                                <th width="12%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach($fondo['depositos'] as $deposito):?>
                                            <tr>
                                                <td><?=format_date_calendar($deposito['fecha_deposito'])?></td>
                                                
                                                
                                                <td><?=$deposito['tipo_pago']=='fondo'?'Fondo Revolvente':'Apoyo';?></td>
                                                <td><?=$deposito['concepto'];?></td>
                                                <td class="text-right"><?=number_format($deposito['importe'],2)?></td>
                                                <td class="text-center  text-green">
                                                    <?php if(group_has_role('depositos','edit')):?>
                                                    <a href="<?=base_url('admin/depositos/edit/'.$deposito['id_deposito'])?>" title="Modificar registro"><i class="fa fa-edit"></i></a>
                                                    <?php endif;?>
                                                    <a href="<?=base_url('admin/depositos/details/'.$deposito['id_deposito'])?>"  class="" title="Detalles registro"><i class="fa fa-search"></i></a>
                                                    <?php if(group_has_role('depositos','delete')):?>
                                                    <a href="<?=base_url('admin/depositos/delete/'.$anio.'/'.$mes.'/'.$fondo['id_director'].'/'.$deposito['id_deposito'])?>" confirm-action><i class="fa fa-times"></i></a>
                                                    <?php endif;?>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <?php }?>
                        </tbody>
                   </table>
                   <div class="row">
                                        <div class="invoice-inner callout">
                                                    <div class="col-xs-6">
                                                        
                                                       
                                                    </div>
                                                    <div class="col-xs-6 text-right">
                                                        
                                                            <p><strong>Resumen:</strong></p>
                                                            <strong>Depósitos en el mes:</strong> <?=$total_rows[$mes]?> <br/>
                                                            <strong>Fondo revolvente:</strong> <?=isset($importe[$mes]['fondo'])?number_format($importe[$mes]['fondo'],2):'0.00'?><br/>
                                                            <strong>Apoyo:</strong> <?=isset($importe[$mes]['apoyo'])?number_format($importe[$mes]['apoyo'],2):'0.00'?><br/>
                                                            <strong>Total:</strong> <?=number_format($importe[$mes]['total'],2)?><br/>
                                                        
                                                        
                                                    </div>  
                                        </div>
                   </div> 
                   <?php else:?>
                         <table class="table" width="100%">
                                        <thead>
                                            <tr>
                                                <th width="14%">
                                                    Fecha
                                                </th>
                                                
                                                
                                                <th width="20%">Tipo</th>
                                                <th>Concepto</th>
                                                <th width="10%">
                                                    Importe
                                                </th>
                                                <th width="20%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                         <?php foreach($data as $fondo):?>
                                            <?php foreach($fondo['depositos'] as $deposito):?>
                                                <tr>
                                                    <td><?=format_date_calendar($deposito['fecha_deposito'])?></td>
                                                    
                                                    
                                                    <td><?=$deposito['tipo_pago']=='fondo'?'Fondo Revolvente':'Apoyo';?></td>
                                                    <td><?=$deposito['concepto'];?></td>
                                                    <td class="text-right"><?=number_format($deposito['importe'],2)?></td>
                                                    <td class="text-center  text-green box-icon">
                                                        <?php if(group_has_role('depositos','edit')):?>
                                                        <a href="<?=base_url('admin/depositos/edit/'.$deposito['id_deposito'])?>"  ui-wave class="btn-icon  btn-icon-sm btn-success" title="Modicar registro"><i class="fa fa-edit"></i></a>
                                                         <?php endif;?>
                                                        <a href="<?=base_url('admin/depositos/details/'.$deposito['id_deposito'])?>" ui-wave class="btn-icon  btn-icon-sm btn-primary" title="Detalles registro"><i class="fa fa-search"></i></a>
                                                        
                                                        <?php if(group_has_role('depositos','delete')):?>
                                                        <a href="<?=base_url('admin/depositos/delete/'.$anio.'/'.$mes.'/'.$fondo['id_director'].'/'.$deposito['id_deposito'])?>" ui-wave class="btn-icon  btn-icon-sm btn-danger" confirm-action><i class="fa fa-times"></i></a>
                                                        <?php endif;?>
                                                    </td>
                                                </tr>
                                            <?php endforeach;?>
                                        <?php endforeach;?>
                                        </tbody>
                                    </table>
                                    
                   <?php endif; ?>
                           
            </uib-tab>
            <?php }?>                            
        </uib-tabset>
    
    </div>   
     
    <?php else:?>
    <div class="alert alert-info text-center">
        <?=lang('depositos:not_found')?>
    </div>
    <?php endif;?>
</div>

<script type="text/ng-template" id="importModal.html">
    <?php echo form_open(); ?>
                            <div class="modal-header">
                                <h3>Importar archivo excel</h3>
                            </div>
                            <div class="modal-body" >
                            
                               
                                 <div class="form-group">
                                    <label>Tipo</label>
                                    
                                        <?=form_dropdown('tipo',array(''=>' [ Elegir ]','fondo'=>'Fondo Revolvente','apoyo'=>'Apoyo'),null,'class="form-control" ')?>
                                   
                                </div>
                                <div class="form-group">
                                    <label>Fecha de depósito</label>
                                  
                                    <div class="input-group ui-datepicker">
                                         <?=form_input('fecha_deposito',NULL,'class="form-control" uib-datepicker-popup="yyyy-MM-dd" 
                                                               ng-model="fecha_deposito"
                                                               is-open="status.fecha_deposito" 
                                                               datepicker-options="dateOptions" 
                                                               date-disabled="disabled(date, mode)" 
                                                               ')?>
                                         
                                         <span class="input-group-addon" ng-click="status.fecha_deposito=true;"><i class="glyphicon glyphicon-calendar"></i></span>
                                         
                                    </div>
                    
                                </div>
                                <div class="form-group">
                                    <label>Archivo xlsx</label>
                                    <?=form_upload('file_xls',false,'ng-model="file_xsl" ngf-select="upload_file(file_xsl)"')?>
                                                     
                                </div>
                               
                            </div>
                            <div class="modal-footer">
                                <button type="button" ui-wave class="btn btn-flat btn-default" ng-click="cancel()">Cancelar</button>
                                <button type="button" ui-wave class="btn btn-flat btn-primary" ng-click="save()">Aceptar</button>
                            </div>
     <?php echo form_close(); ?>
</script>
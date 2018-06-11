<div ng-controller="InputCtrl" class="panel-body">
    <div class="lead text-success">
        
            <?=lang('depositos:'.$this->method)?>
        
    </div>
    <?php if($datos && $this->method=='create'):?>
    <div class="alert alert-info">
        <h4>Atención</h4>
        <p><?=sprintf(lang('depositos:info'),month_long($mes),$datos->nombre)?></p>
    </div>
    <?php endif;?>
<?php echo form_open($this->uri->uri_string().'?tab='.$tab,'class=""  id="form_deposito"    ');?>
    <input type="hidden" name="tab" ng-model="tab" />
    
    <?php if($tab =='single'): ?>
    
                       <?php if(!$datos && $this->method=='create'):?>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>* Centro</label>
                                                    
                                    <?=form_dropdown('id_centro',array(''=>' [ Elegir ] ')+$centros,NULL,'class="form-control"  ng-init="f_centro.selected=\''.$deposito->id_centro.'\'" ng-model="f_centro.selected" '.($this->method=='details'?'disabled':''));?>
                                </div>
                                                
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                                        <label>* Director</label>
                                                        <select name="id_director" ng-options="f_director.nombre+' - '+f_director.vigencia for f_director in f_directores track by f_director.id" ng-disabled="!f_directores"  class="form-control" ng-init="f_director.selected={id:'<?=$deposito->id_director;?>'}" ng-model="f_director.selected">
                                                            <option value=""> [ Elegir ] </option>
                                                            
                                                        </select>
                                </div>
                            </div>
                        </div>
                    <?php else:?>
                    <input type="hidden" name="id_centro" value="<?=$datos->id_centro?>" />
                    <input type="hidden" name="id_director" value="<?=$datos->id?>"/>
                    <?php endif;?>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>* Banco:</label>
                                
                                    <?=form_input('banco',$deposito->banco,'class="form-control"  ng-model="f_banco" ng-init="f_banco=\''.$deposito->banco.'\'"  '.($this->method=='details'?'disabled':''))?>
                                    
                                
                            </div>
                            <div class="form-group">
                                <label >* Tarjeta:</label>
                                 
                                    <?=form_input('no_tarjeta',$deposito->no_tarjeta,'class="form-control" ng-model="f_tarjeta" ng-init="f_tarjeta=\''.$deposito->no_tarjeta.'\'" '.($this->method=='details'?'disabled':''))?>
                                    
                                
                            </div>
                            <div class="form-group" ng-init="importe='<?=$deposito->importe?number_format(str_replace(',','',$deposito->importe),2,'.',','):''?>'">
                                <label>* Importe</label>
                                
                                <?=form_input('importe',$deposito->importe,'class="form-control" ng-model="importe"  formato-moneda     ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" '.($this->method=='details'?'disabled':''))?>
                               
                            </div>
                        </div>
                        <div class="col-lg-6">
                            
                            <div class="form-group">
                                <label>* Tipo</label>
                                
                                    <?=form_dropdown('tipo',array(''=>' [ Elegir ]','fondo'=>'Fondo Revolvente','apoyo'=>'Apoyo'),$deposito->tipo,'class="form-control" '.($this->method=='details'?'disabled':''))?>
                               
                            </div>
                            <div class="form-group">
                                <label>* Fecha de depósito</label>
                                <div class="input-group ui-datepicker">
                                     <?=form_input('fecha_deposito',NULL,'class="form-control" uib-datepicker-popup="yyyy-MM-dd" 
                                                           ng-init="fecha_deposito=\''.$deposito->fecha_deposito.'\'"
                                                           ng-model="fecha_deposito"
                                                           is-open="status.fecha_deposito" 
                                                           
                                                           datepicker-options="dateOptions" 
                                                           date-disabled="disabled(date, mode)" 
                                                           
                                                           close-text="Cerrar" '.($this->method=='details'?'disabled':''))?>
                                     <?php if($this->method!='details'):?>
                                     <span class="input-group-addon" ng-click="status.fecha_deposito=true;"><i class="glyphicon glyphicon-calendar"></i></span>
                                     <?php endif; ?>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <hr />
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>* Concepto</label>
                                 <div class="input-group">	
                		                 <select class="form-control"  name="concepto"   
                                         ng-init="concepto='<?=$deposito->concepto?>' " ng-model="concepto" >
                                          <option value=""> [ Elegir ] </option>
                                          <option ng-repeat="concepto in conceptos" value="{{concepto.concepto}}" >{{concepto.concepto}}</option>
                                        </select>
                                        <?php if($this->method!='details'):?>
                        				          <span class="input-group-btn">
                        				            <button class=" md-raised btn btn-secondary" type="button" ng-click="showModal()"  >
                        				                  <span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Nuevo
                        				            </button>
                        				          </span>		
                                        <?php endif; ?>	
                                 </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            
                        </div>
                    </div>
       <?php else:?>
                
                               <div class="form-group">
                                    <label>* Tipo</label>
                                    
                                        <?=form_dropdown('tipo',array(''=>' [ Elegir ]','fondo'=>'Fondo Revolvente','apoyo'=>'Apoyo'),$deposito->tipo,'class="form-control" ')?>
                                   
                                </div>
                                <div class="form-group">
                                    <label>* Fecha de depósito</label>
                                  
                                    <div class="input-group ui-datepicker" ng-init="fecha_deposito='<?=$deposito->fecha_deposito?>'">
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
                                    <?=form_upload('file_xls',false,'ng-model="file_xsl" ngf-select="upload_file(file_xsl)" accept=".xls"')?>
                                    <p class="help-block">Antes de subir el archivo verificar que las columnas esten correctas y esten ordenadas de la siguiente manera: método, cuenta, nombre, tipo, importe y referencia</p>                 
                                </div>
                                <p class="text-right">Total registros: {{depositos.length}}</p>
                                <div ng-if="dispose"> 
                                    <h4>Trabajando, espere por favor...</h4>
                                
                                </div>
                               
                                 <table class="table" ng-if="!dispose">
                                    <thead>
                                        <tr>
                                            <th width="30%">Titular</th>
                                            <th  width="20%">Cuenta</th>
                                            <th>Concepto</th>
                                            <th width="10%">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr ng-repeat="deposito in depositos">
                                            <td >
                                                <span class="text-danger" ng-if="!deposito.nombre" tooltip-placement="top" uib-tooltip="Verificar si se encuentra habilitado en el panel de directores o corregir el nombre en el XLS">El director no existe en la BD.</span>
                                                <span>{{deposito.nombre}}</span>  <br />  
                                                <span class="text-muted">{{deposito.centro}}</span> 
                                                
                                                <input type="hidden" name="depositos[{{$index}}][nombre]" value="{{deposito.nombre}}" />
                                                <input type="hidden" name="depositos[{{$index}}][centro]" value="{{deposito.centro}}" />
                                                
                                                <input type="hidden" name="depositos[{{$index}}][cuenta]" value="{{deposito.cuenta}}" />
                                                <input type="hidden" name="depositos[{{$index}}][metodo]" value="{{deposito.metodo}}" />
                                                <input type="hidden" name="depositos[{{$index}}][importe]" value="{{deposito.importe}}" />
                                                <input type="hidden" name="depositos[{{$index}}][referencia]" value="{{deposito.referencia}}" />
                                            </td>
                                            
                                            <td>
                                                <span>{{deposito.cuenta}}</span><br />
                                                <span class="text-muted">{{deposito.metodo}}</span>
                                            </td>
                                            <td>{{deposito.referencia}}<span class="text-danger" ng-if="!deposito.referencia">Falta el concepto o referencia</span></td>
                                            <td class="text-right">{{deposito.importe|number:2}} <span class="text-warning" ng-if="!deposito.importe" title="Falta el importe">0.00</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                
    <?php endif;?>
   
    
    
  
    <p>Nota: Los campos marcados con (*) son obligatorios.</p>
    <div class="divider"></div>
    <input type="hidden" value="<?=$mes?>" name="mes" readonly="" />
       <div class="buttons">
       <?php if($this->method != 'details'):?>
    	   <?php $this->load->view('admin/partials/buttons', array('buttons' => array('save'))) ?>
        
            <?php 	echo anchor('admin/depositos/'.$anio, lang('buttons:cancel'), 'class="btn btn-default  btn-w-md ui-wave"');?>
       <?php else:?>
            <?php 	echo anchor('admin/depositos/'.$anio, lang('buttons:exit'), 'class="btn btn-primary  btn-w-md ui-wave"');?>
       <?php endif;?>
       </div>

<?php echo form_close();?>
</div>
<script type="text/ng-template" id="add-miembros.html">
                            <div class="modal-header">
                                <h3>Nuevo concepto</h3>
                            </div>
                            <div class="modal-body" >
                            
                               
                                 <div class="form-group">
                                    <label>Concepto</label>
                                    <input type="text" name="" ng-model="form.concepto"  class="form-control" autofocus required >

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button ui-wave class="btn btn-flat btn-default" ng-click="cancel()">Cancelar</button>
                                <button ui-wave class="btn btn-flat btn-primary" ng-click="add()">Aceptar</button>
                            </div>
</script>
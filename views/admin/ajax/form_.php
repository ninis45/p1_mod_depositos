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
<?php echo form_open($this->uri->uri_string(),'class=""  id="form_deposito"    ');?>
    <?php if(!$datos && $this->method=='create'):?>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Centro</label>
                                    
                    <?=form_dropdown('id_centro',array(''=>' [ Elegir ] ')+$centros,NULL,'class="form-control"  ng-init="f_centro.selected=\''.$deposito->id_centro.'\'" ng-model="f_centro.selected" '.($this->method=='details'?'disabled':''));?>
                </div>
                                
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                                        <label>Director</label>
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
                <label>Banco:</label>
                
                    <?=form_input('banco',$deposito->banco,'class="form-control"  ng-model="f_banco" ng-init="f_banco=\''.$deposito->banco.'\'"  '.($this->method=='details'?'disabled':''))?>
                    
                
            </div>
            <div class="form-group">
                <label >Tarjeta:</label>
                 
                    <?=form_input('no_tarjeta',$deposito->no_tarjeta,'class="form-control" ng-model="f_tarjeta" ng-init="f_tarjeta=\''.$deposito->no_tarjeta.'\'" '.($this->method=='details'?'disabled':''))?>
                    
                
            </div>
            <div class="form-group" >
                <label>Importe</label>
                
                    <?=form_input('importe',$deposito->importe,'class="form-control"     ng-pattern="/^[0-9]+(\.[0-9]{1,2})?$/" '.($this->method=='details'?'disabled':''))?>
                  
            </div>
        </div>
        <div class="col-lg-6">
            
            <div class="form-group">
                <label>Tipo</label>
                
                    <?=form_dropdown('tipo',array(''=>' [ Elegir ]','fondo'=>'Fondo Revolvente','apoyo'=>'Apoyo'),$deposito->tipo,'class="form-control" '.($this->method=='details'?'disabled':''))?>
               
            </div>
            <div class="form-group">
                <label>Fecha de depósito</label>
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
            <!--div class="form-group">
                <label >Concepto</label>
                
                    <?=form_textarea('concepto',$deposito->concepto,'class="form-control" '.($this->method=='details'?'disabled':''))?>
                
            </div-->
            <div class="form-group">
            
                 <div class="input-group">	
		                 <select class="form-control"  name="concepto" required  
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
<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Pages Module
 *
 * @author PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Pages
 */
class Module_Depositos extends Module
{
	public $version = '1.0';

	public function info()
	{
		$info=array(
			'name' => array(
				'en' => 'Deposits',
			
				'es' => 'Depósitos',
				
			),
			'description' => array(
				'en' => 'Deposits to bank accounts for expenses and Support Revolving Fund',
				
				'es' => 'Depositos a cuentas bancarias para gastos de Fondo Revolvente y Apoyo',
				
			),
			'frontend' => true,
			'backend'  => true,
			'skip_xss' => true,
			'menu'	  => 'admin',

			'roles' => array(
				'create', 'edit', 'delete'
			),
            
            'uri' => 'admin/depositos/{{ anio }}', 
            'shortcuts' => array(
			       array(
        					'name'  => 'depositos:create',
        					'uri'   => 'admin/depositos/create/{{ anio }}',
        					'class' => 'btn btn-success',
                            'ng-if' => '!hide_b',
                            'open-modal'  => '',
                            'modal-title' => 'Seleccionar forma de entrada'
      				),      
            ),
            /*'sections' => array(
                'eventos'=>array(
                    'name'=>'eventos:list_title',
                    'uri' => 'admin/eventos',                    
                    'shortcuts' => array(
			             array(
  						    'name' => 'eventos:create_title',
  						    'uri' => 'admin/eventos/create',
  						    'class' => 'add'
			             )
    		        ),
               )
                
            ),*/
			
		);
        
        
        
        return $info;
	}

	public function install()
	{
		
		$this->dbforge->drop_table('depositos');

		$tables = array(
			
			'depositos'=>array(
				'id'          => array('type' => 'INT', 'constraint' => 11, 'auto_increment' => true, 'primary' => true,),
                'id_director' => array('type' => 'INT', 'constraint' => 11,),
                'id_centro' => array('type' => 'INT', 'constraint' => 11,),
				'descripcion' => array('type' => 'VARCHAR', 'constraint' => 255,'null'=>true),
                'tipo'        => array('type' => 'ENUM', 'constraint' => array('fondo','apoyo'),'null'=>true),
                'banco'       => array('type' => 'VARCHAR', 'constraint' => 255,'null'=>true),
                'no_tarjeta'  => array('type' => 'VARCHAR', 'constraint' => 255,'null'=>true),
                'concepto'  => array('type' => 'TEXT','null'=>true),
                'fecha_deposito'  => array('type' => 'DATE','null'=>true),
                'importe'     => array('type' => 'DECIMAL', 'constraint' => array(10,2),'null'=>true),
				
            )
			
		);
        
        
        
	    if ( ! $this->install_tables($tables))
		{
			return false;
		}
        return true;

		
	}

	public function uninstall()
	{
		 $this->dbforge->drop_table('depositos');
		return true;
	}

	public function upgrade($old_version)
	{
		return true;
	}
    public function help(){
        return "Ayuda para este modulo";
    }
}
?>
<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Search Plugin
 *
 * Use the search plugin to display search forms and content
 *
 * @author  PyroCMS Dev Team
 * @package PyroCMS\Core\Modules\Search\Plugins
 */
class Plugin_Depositos extends Plugin
{
	public $version = '1.0.0';

	public $name = array(
		'en' => 'Search',
        'fa' => 'جستجو',
	);

	public $description = array(
		'en' => 'Create a search form and display search results.',
        'fa' => 'ایجاد فرم جستجو و نمایش نتایج',
	);
    public function __construct()
	{
		$this->load->model(array(
			'depositos/deposito_m',
            'directores/director_m'
			
		));
        
        $this->lang->load(array(
            'depositos',
            'calendar'
        ));
	}
    function listing()
    {
        
        $anio = $this->attribute('anio',date('Y'));
        
        $depositos  = array();
        
        $base_where = array(
            'user_id' => $this->current_user->id
        );
        
        if($anio)
        {
            $base_where['YEAR(fecha_deposito)'] = $anio;
        }
        
        $result    = $this->deposito_m->join('directores','directores.id=depositos.id_director')
                                    ->order_by('fecha_deposito')
                                    ->where($base_where)
                                    ->get_all();
        $css = 'active';
        foreach($result as &$row)
        {
            list($year,$month,$day)   = explode('-',$row->fecha_deposito);
            
            $row->fecha_deposito = format_date_calendar($row->fecha_deposito);
            if(!isset($depositos[$month]))
            {
                
                $depositos[$month] = array(
                    'value' => $month,
                    'label' => month_long($month),
                    'items' => array(),
                    'total' => 0,
                    'css'   => $css
                );
                
                if($css)
                {
                    $css = '';
                }
            }
            $depositos[$month]['total']   +=  $row->importe;
            $depositos[$month]['items'][]  =  $row;
           
        }
       
        return $depositos;
    }
    function resumen()
    {
        $tipo = $this->attribute('tipo','');
        $anio = $this->attribute('anio',date('Y'));
        $base_where = array(
        
            'user_id' => $this->current_user->id
        );
        
        
        if($tipo)
        {
            $base_where['tipo'] = $tipo;
        }
        if($anio)
        {
            $base_where['YEAR(fecha_deposito)'] = $anio;
        }
        $result    = $this->deposito_m->join('directores','directores.id=depositos.id_director')
                                    ->select('SUM(importe) AS importe')
                                    ->get_by($base_where);
                                   
                                    
        return $result?number_format($result->importe,2):'0.00';
    }
    function nombre_director()
    {
        if($this->current_user == FALSE) {
            
            return false;
        }
        
        $result = $this->director_m
                    ->join('users','users.id=directores.user_id')->get_by('users.id',$this->current_user->id);
          
        if($result)          
            return $result->nombre;
        else
            return false;
    }
    function status()
    {
        if($this->current_user == FALSE) {
            
            return false;
        }
        
        $result = $this->director_m
                    ->join('users','users.id=directores.user_id')->get_by('users.id',$this->current_user->id);
                    
                    
        return (bool)$result;
    }
 
}
?>
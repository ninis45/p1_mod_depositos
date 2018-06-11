<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Depositos extends Public_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(array(
			'depositos/deposito_m',
            'directores/director_m'
			
		));
        
        $this->lang->load(array(
            'depositos',
            'calendar'
        ));
    }
    function descargar($anio='',$mes='')
    {
        ini_set('max_execution_time', 300);
        $this->load->library(array('pdf'));
        
        $html2pdf = new HTML2PDF('P', 'A4', 'fr');
        
        if($this->current_user == FALSE) {
            $this->session->set_userdata('redirect_to', current_url());
            $this->session->set_flashdata('error',lang('depositos:error_access'));
            redirect('users/login');
        }
        
        $centro =  $this->db->select('centros.nombre,centros.tipo')
                ->where('user_id',$this->current_user->id)
                ->join('centros','centros.id=directores.id_centro')
                ->get('directores')->row() OR show_404();;
        
        $depositos   = $this->deposito_m->join('directores','directores.id=depositos.id_director')
                                    ->order_by('fecha_deposito')
                                    ->where(array(
                                            'user_id'=>$this->current_user->id,
                                            'MONTH(fecha_deposito)' => $mes,
                                            'YEAR(fecha_deposito)' => $anio
                                    ))
                                    ->get_all();
        $total = 0;                        
        foreach($depositos as $deposito)
        {
            $total += $deposito->importe;
        }
                                    
        $output=$this->template->set_layout(false)
          //              ->title('Reporte ')
                        ->enable_parser(true)
						->build('templates/pdf_mes',array('total'=> $total ,'centro'=>$centro->nombre,'depositos'=>$depositos,'anio'=>$anio,'mes'=>strtoupper(month_long($mes))),true);
          
         //echo $output;                          
        $html2pdf->writeHTML($output);
        $html2pdf->Output('depositos_'.now().'.pdf','D');
    }
}
?>
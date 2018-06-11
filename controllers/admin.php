<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin extends Admin_Controller {
	protected $section='depositos';

	public function __construct()
	{
		parent::__construct();
        $this->lang->load(array('depositos','calendar'));
        $this->load->model(array('deposito_m','conceptos/conceptos_m'));
        $this->load->helper('fondo/fondo');
        $this->template->enable_parser(true);
        $this->base_director = array();
           $this->config->load('files/files');
         $this->_path = FCPATH.rtrim($this->config->item('files:path'), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
        
        if($this->current_user->group=='director')
        {
            
            
            $member = $this->db->select('*,directores.id AS id_director')
                            ->join('directores','directores.user_id=users.id')
                            ->where('users.id',$this->current_user->id)
                            ->get('users')->row();
            
            if(!$member)
            {
                $this->session->set_flashdata('error',lang('depositos:error_assoc'));
            
                redirect('admin');
            }
            
            $this->base_director['directores.id'] = $member->id_director;
            
            $this->template->append_metadata('<script type="text/javascript">$(document).ready(function(){$("#shortcuts").remove();});</script>');
        }
        
        
        $this->validation_rules = array(
            'single' => array(
    			array(
    				'field' => 'id_director',
    				'label' => 'Director',
    				'rules' => 'trim|required'
    			),
      	         array(
    				'field' => 'id_centro',
    				'label' => 'Centro',
    				'rules' => 'trim|required'
    			),
    			array(
    				'field' => 'importe',
    				'label' => 'Importe',
    				'rules' => 'trim|required'
    			),
                array(
    				'field' => 'concepto',
    				'label' => 'Concepto',
    				'rules' => 'trim|required'
    			),
                array(
    				'field' => 'banco',
    				'label' => 'Banco',
    				'rules' => 'trim|required'
    			),
                array(
    				'field' => 'no_tarjeta',
    				'label' => 'Tarjeta',
    				'rules' => 'trim|required'
    			),
                array(
    				'field' => 'tipo',
    				'label' => 'Tipo',
    				'rules' => 'trim|required'
    			),
      	         array(
    				'field' => 'fecha_deposito',
    				'label' => 'Fecha depósito',
    				'rules' => 'trim|required|callback__validar_mes'
    			),
            ),
            'multi' => array(
            
                 array(
    				'field' => 'tipo',
    				'label' => 'Tipo',
    				'rules' => 'trim|required'
    			),
      	         array(
    				'field' => 'fecha_deposito',
    				'label' => 'Fecha depósito',
    				'rules' => 'trim|required|callback__validar_mes'
    			),
                array(
    				'field' => 'depositos',
    				'label' => 'Depositos',
    				'rules' => 'callback__validar_depositos'
    			),
            )
        );
    }
    function index()
    {
        
        
        ///$periodos = $this->conceptos_m->group_by('anio')->get_all();
        
        $min_fecha = $this->db->select_min('fecha_deposito')->get('depositos')->result();
        
       
        list($year,$month,$day) = explode('-',$min_fecha[0]->fecha_deposito);
        
         
         
        $this->template->title($this->module_details['name'])
            ->set('min_year',$year)
            //->set('periodos',$year)
            ->append_js('module::depositos.controller.js')
			->build('admin/init');
        
    }
    function _validar_depositos($input)
    {
        $status = true;
        if($input){
           foreach($input as $row)
           {
                if($row['cuenta'] == '' || $row['nombre'] == '' || $row['metodo'] =='' || $row['referencia'] ==''  || $row['importe'] == '' )
                {
                    $status = false;
                    break;
                }
           }
       }
       else
       {
            $status = false;
       }
       if($status == false)
       {
            $this->form_validation->set_message('_validar_depositos',lang('depositos:error_list'));
       }
       
       return $status;
    }
    function _validar_mes($input)
    {
        $mes = $this->input->post('mes');
        
    }
    function load($anio='')
    {
        $base_where = array('YEAR(fecha_deposito)'=>$anio);
        $f_centro = $this->input->get('f_centro');
        
        $f_centro AND $base_where['centros.id'] = $f_centro;
        
        $base_where = array_merge($this->base_director,$base_where);
        
        
        
        
        
        $result = $this->db->select('*,depositos.id AS id_deposito,depositos.tipo AS tipo_pago,depositos.banco AS banco, depositos.no_tarjeta AS no_tarjeta,directores.id AS id_director,centros.nombre AS nombre_centro,directores.nombre AS nombre_director')
                    //->join('cat_conceptos','cat_conceptos.id=fondo.id_concepto')
                    
                    ->join('directores','directores.id=depositos.id_director')
                    ->join('centros','centros.id=directores.id_centro')
                    ->where($base_where)
                    //->group_by('nombre_centro,id_director')
                    ->order_by('fecha_deposito,centros.nombre')
                    ->get('depositos')->result();
        
        $items = array();
        
        $dif = '';
        $importe    = array('total'=>0,'fondo'=>0,'apoyo'=>0);
        $total_rows = array();
        $tmp_director = '';
        $tmp_month       = '';
        
        foreach($result as $row)
        {
            list($year,$month,$day) = explode('-',$row->fecha_deposito);
            
            !$tmp_director AND $tmp_director = $row->id_director;
            !$tmp_month AND $tmp_month       = $month;
            
            
            if(!isset($items[$month][$row->id_director]['id_director']))
            {
                $items[$month][$row->id_director]['id_director']     = $row->id_director;
                $items[$month][$row->id_director]['nombre_director'] = $row->nombre_director;
                $items[$month][$row->id_director]['nombre_centro']   = $row->nombre_centro;
                $items[$month][$row->id_director]['importe']         = 0;
            }
            
            if(!isset($importe[$month]['total'])) $importe[$month]['total'] = 0;
            
            $importe[$month]['total']                            += $row->importe;
            
            if(!isset($importe[$month][$row->tipo_pago])) $importe[$month][$row->tipo_pago] = 0;
            
            $importe[$month][$row->tipo_pago]                    += $row->importe; 
           
            $items[$month][$row->id_director]['importe'] += $row->importe;
            
            /*if($tmp_director == $row->id_director)
            {
                $importe += $row->importe;
            }
            else
            {
                
                echo $tmp_director;
                $items[$tmp_month][$tmp_director]['importe'] = $importe;
                
                $importe          = $row->importe;                
                $tmp_director     = $row->id_director;
                $tmp_month        = $month;
            }*/
            
            $items[$month][$row->id_director]['depositos'][] =(array) $row;
            
            if(!isset($total_rows[$month])) $total_rows[$month] = 0;
            $total_rows[$month]++;
        }
         //echo '<pre>';
         //print_r($importe);       
        //echo '</pre>';
        //exit();
        
        $centros = $this->db->get('centros')->result();
        
        
        $this->template->title($this->module_details['name'])
			->set('items',$items)
            ->set('anio',$anio)
            ->set('importe',$importe)
            ->set('total_rows',$total_rows)
            ->set('centros',array_for_select($centros,'id','nombre'))
			->append_js('module::depositos.controller.js')
			->build('admin/index');
    }
    function edit($id=0)
    {
        role_or_die('depositos','edit');
        if(!$deposito = $this->deposito_m->get($id))
        {
            
            $this->session->set_flashdata('error',lang('global:not_found_edit'));
            
            redirect('admin/depositos');
        }
        
        $datos = $this->db->where('id',$deposito->id_director)->get('directores')->row();
        
        list($anio,$mes,$dia) = explode('-',$deposito->fecha_deposito);
        
        $this->form_validation->set_rules($this->validation_rules);
        
        if ($this->form_validation->run())
		{
		     unset($_POST['btnAction']);
            
            //$_POST['anio'] = $anio;
            //$_POST['id_director'] = $id_director;
            
            if($this->deposito_m->edit($id,$this->input->post()))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('depositos:save_success'),$this->input->post('concepto')));
				
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
				
			}
			redirect('admin/depositos/edit/'.$id);
        }
        if($_POST)
        {
            $deposito = (Object)$_POST;
        }
        
        $this->db->distinct();   
        $concepto = $this->db->select('concepto')->order_by('concepto','ASC')->get('depositos')->result();
          $this->template->append_metadata('<script type="text/javascript">var depositos=[], conceptos='.json_encode($concepto).';</script>');
          
        $this->template->title($this->module_details['name'])
           ->enable_parser(false)
			->set('datos',$datos)
            ->set('deposito',$deposito)
            ->set('mes',$mes)
            ->set('anio',$anio)
            ->set('tab','single')
            ->set('deposito',$deposito)
			->append_js('module::depositos.controller.js')
			->build('admin/form');
    }
    
    function create($anio='',$mes='',$id_director='')
    {
        
        role_or_die('depositos','create');
        
        $anio = $anio?$anio:date('Y');
        if($this->input->is_ajax_request())
        {
            echo '<p>Selecciona la forma de entrada.</p>';
            echo '<a href="'.base_url('admin/depositos/create/'.$anio.'?tab=single').'" class="btn btn-primary btn-lg">Simple</a>';
            echo ' | ';
            echo '<a href="'.base_url('admin/depositos/create/'.$anio.'?tab=multi').'" class="btn btn-primary btn-lg">Multiple</a>';
            exit();
        }
        
        
        $deposito = new StdClass();
        $tab      = $this->input->get('tab')?$this->input->get('tab'):'single';
        
        $anio OR show_404();
        
        if(!$datos = $this->db->where('id',$id_director)
                    ->get('directores')->row())
        {
            $centros = $this->db->get('centros')->result();
            
            $this->template->set('centros',array_for_select($centros,'id','nombre')); 
        }
        
        
        
        
        
        $this->form_validation->set_rules($this->validation_rules[$tab]);
         
        if ($this->form_validation->run())
		{
		     unset($_POST['btnAction']);
            $depositos = array();
            $input     = $this->input->post();
            $adds      = 0;
            if($tab == 'single')
            {
                if($this->deposito_m->create($input))
                { 
                    $adds++;
                    
                }
            }
            else
            {
                foreach($this->input->post('depositos') as $deposito)
                {
                    $director = $this->db->select('*,directores.id AS id_director')
                                    ->where(array(
                                        'directores.nombre' => $deposito['nombre'],
                                        'directores.activo' => 1
                                    ))
                                    ->join('centros','centros.id=directores.id_centro')
                                    ->get('directores')->row();
                    if($director){
                        $deposito = array(
                            'id_centro'   => $director->id_centro,
                            'id_director' => $director->id_director,
                            'concepto'    => $deposito['referencia'],
                            'importe'     => str_replace(',','',$deposito['importe']),
                            'tipo'        => $input['tipo'],
                            'no_tarjeta'  => str_replace(' ','',$deposito['cuenta']),
                            'banco'       => $deposito['metodo'],
                            'fecha_deposito'    => $input['fecha_deposito']
                        
                        
                        );
                    }
                   
                    if($director && $this->deposito_m->create($deposito)) $adds++;
                }
            }
            
            
            if($adds == count($this->input->post('depositos')) || ($tab=='single' && $adds==1))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('depositos:save_success'),$adds));
				
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
				
			}
			redirect('admin/depositos/'.$anio);
        }
        
        foreach ($this->validation_rules[$tab] as $key => $field)
		{
    				$deposito->$field['field'] = $this->input->post($field['field']);
		}
        
        if(!$_POST && $datos)
        {
            $deposito->banco      = $datos->banco;
            $deposito->no_tarjeta = $datos->no_tarjeta;
        }
       
       
       $this->db->distinct();   
        $concepto = $this->db->select('concepto')->order_by('concepto','ASC')->get('depositos')->result();
          $this->template->append_metadata('<script type="text/javascript">var conceptos='.json_encode($concepto).',depositos='.(isset($deposito->depositos) && is_array($deposito->depositos)?json_encode($deposito->depositos):'[]').';</script>');
          
       
        $this->template->title($this->module_details['name'])
           ->enable_parser(false)
			->set('datos',$datos)
            ->set('deposito',$deposito)
            ->set('mes',$mes)
            ->set('anio',$anio)
            ->set('tab',$tab)
			->append_js('module::depositos.controller.js')
			->build('admin/form');
        
    }
    
    function details($id=0)
    {
        
        $base_where = array('depositos.id'=>$id);
        
        
        $base_where = array_merge($this->base_director,$base_where);
        
        if(!$deposito = $this->deposito_m->select('*,depositos.no_tarjeta AS no_tarjeta,depositos.banco AS banco')
                    ->join('directores','directores.id=depositos.id_director')
                    ->get_by($base_where))
        {
            
            $this->session->set_flashdata('error',lang('global:not_found_edit'));
            
            redirect('admin/depositos');
        }
        
        $datos = $this->db->where('id',$deposito->id_director)->get('directores')->row();
        
        list($anio,$mes,$dia) = explode('-',$deposito->fecha_deposito);
        
        $this->form_validation->set_rules($this->validation_rules);
        
        if ($this->form_validation->run())
		{
		     unset($_POST['btnAction']);
            
            //$_POST['anio'] = $anio;
            //$_POST['id_director'] = $id_director;
            
            if($this->deposito_m->edit($id,$this->input->post()))
            {
				
				$this->session->set_flashdata('success',sprintf(lang('depositos:save_success'),$this->input->post('concepto')));
				
			}
            else
            {
				$this->session->set_flashdata('error',lang('global:save_error'));
				
			}
			redirect('admin/depositos/edit/'.$id);
        }
        if($_POST)
        {
            $deposito = (Object)$_POST;
        }
        $this->template->title($this->module_details['name'])
           
			->set('datos',$datos)
            ->set('deposito',$deposito)
            ->set('mes',$mes)
            ->set('anio',$anio)
            ->set('deposito',$deposito)
			->append_js('module::depositos.controller.js')
			->build('admin/ajax/form');
    }
    function delete($anio='',$mes='',$id_director='',$id='')
    {
        
        role_or_die('depositos','delete');
        $base_where = array();
        
        $base_where['YEAR(fecha_deposito)'] = $anio;
        $base_where['MONTH(fecha_deposito)'] = $mes;
        
        $base_where['id_director'] = $id_director;
        
        if($id)
        {
            $base_where['id'] = $id;
        }
        
        $items = $this->db->where($base_where)->delete('depositos');
        
        
        redirect('admin/depositos/'.$anio);        
        
    }
    function upload()
    {
          
        //ini_set('max_execution_time', 0); 
         $this->load->library('excel');
         
        $this->load->model(array(
            'files/file_folders_m'
        ));
        $this->load->library('files/files');
        
        $result = array(
            'status' => false,
            'data'   => array()
        );
        
        $folder  = $this->file_folders_m->get_by_path('otros') or show_error('No hay carpeta creada');
        $file    = Files::upload($folder->id,false,'file',false,false,false,'xls|xlsx');
         
        if($file['status'])
        {
            $file_path = $this->_path.'/'.$file['data']['filename'];
            
            $objReader = PHPExcel_IOFactory::createReader($file['data']['extension']=='.xls'?'Excel5':'Excel2007');
       
            $objPHPExcel = $objReader->load($file_path);
            
            $heading = array('metodo','cuenta','nombre','centro','tipo','importe','referencia');
            $position = 0;
            $columns = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
           
            
            $rows = array();
            $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
            
           
            foreach($rowIterator AS $i=>$row)
            {
                 $cells = array();
                 
                 
                 
                
                 $cellIterator = $row->getCellIterator();
                 $cellIterator->setIterateOnlyExistingCells(FALSE);
                    
                 
                     foreach($cellIterator as $inc=>$cell)
                     {
                        
                        
                        if(count($heading)== $inc) break;
                        
                        if(strtolower($heading[$inc]) == 'nombre')
                        {
                            $nombre =  trim($cell->getValue());//str_replace(',',' ',trim($cell->getValue()));
                            $nombre = str_replace(',',' ',$nombre);
                            $nombre = str_replace('/',' ',$nombre);
                            
                            $director = $this->db->select('*,directores.nombre AS nombre_director,centros.nombre AS centro')
                                            ->where('directores.nombre',$nombre)
                                            ->where('directores.activo',1)
                                        ->join('centros','centros.id=directores.id_centro')
                                        ->get('directores')->row();
                            
                            $cells[] = $director? $director->nombre_director:'';
                            $cells[] = $director? $director->centro:'';
                        }
                        else{
                       
                              $cells[] =  $cell->getValue();
                        }
                          
                     }
                     
                     
                     
                     $rows[] = array_combine($heading,$cells);
                 
            }
            
            if(count($rows)>0)
            {
                $result['status'] = true;
            }
            
            unset($rows[0]);
            $result['data'] =  $rows;//$rows;//$objPHPExcel->getActiveSheet()->getCell('A1')->getValue();
            
          
          
            /*$objreader =    $this->excel->createReader('Excel5');
            
            $excel     = $objreader->load($file_path);
            
           
            print_r($excel);
            */
            Files::delete_file($file['data']->id);
            
            
            
        }
        else
        {
            $result = $file;
        }
        return $this->template->build_json($result);
         
        // $this->excel->load();
    }
    function export($anio='',$mes='')
    {
        $base_where = array('YEAR(fecha_deposito)'=>$anio);
        $id_centro  = $this->input->get('f_centro');
        $file_name  = $anio;
        
        if($mes)
        {
            $base_where['MONTH(fecha_deposito)']     = $mes_f = $mes;
            $file_name.='_'.month_short($mes);
        } 
        if($id_centro)
        {
            $base_where['depositos.id_centro'] = $id_centro;
            
        }
        
        
        $base_where = array_merge($this->base_director,$base_where);
        
        $result = $this->deposito_m->get_reporte($base_where) OR redirect('admin/depositos');
        
        error_reporting(E_ALL);
        ini_set('display_errors', TRUE);
        ini_set('display_startup_errors', TRUE);
        
        define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
        
        date_default_timezone_set('Europe/London');
        
      
        
        
        $this->load->library('Factory');
        
        $this->excel = factory::getTemplate('depositos.xlsx');
        
        $extra = 3;
       
        $array = array(); 
        $worksheet = array();
        
        $position = 0;
        
        foreach($result as $row)
        {
            list($year,$month,$day) = explode('-',$row->fecha_deposito);
            $array[$month][] = (array)$row;
           
        }
        
        
        
        
        foreach($array as $mes => $data)
        {
            $inc   = 0;
            if($worksheet)
            {
                $worksheet[$mes] = clone  $cloned; 
                $worksheet[$mes]->setTitle(month_long($mes));
                $this->excel->addSheet($worksheet[$mes]);
                $this->excel->setActiveSheetIndex($position);
            }
            else
            {
                $cloned = clone $this->excel->getActiveSheet();
                $this->excel->setActiveSheetIndex(0);
                $this->excel->getActiveSheet()->setTitle(month_long($mes));
                $worksheet[$mes] = 1;
            }
            
            foreach($data as $row)
            {
                $this->excel->getActiveSheet()->insertNewRowBefore($inc+$extra,1);
                $this->excel->getActiveSheet()->setCellValue('A'.($inc+$extra),($row['tipo']=='Plantel'?'P':'CE').pref_centro($row['clave'],''));
                $this->excel->getActiveSheet()->setCellValue('B'.($inc+$extra), $row['nombre_centro']);
                $this->excel->getActiveSheet()->setCellValue('C'.($inc+$extra), $row['nombre_director']);
                $this->excel->getActiveSheet()->setCellValue('D'.($inc+$extra), format_date_calendar($row['fecha_deposito']));
                $this->excel->getActiveSheet()->setCellValue('E'.($inc+$extra), $row['banco']);
                $this->excel->getActiveSheet()->setCellValue('F'.($inc+$extra), ' '.$row['no_tarjeta']);
                $this->excel->getActiveSheet()->setCellValue('G'.($inc+$extra), number_format($row['importe'],2,'.',''));
                $this->excel->getActiveSheet()->setCellValue('H'.($inc+$extra), $row['tipo']=='fondo'?'Fondo Revolvente':'Apoyo');
                $this->excel->getActiveSheet()->setCellValue('I'.($inc+$extra), $row['concepto']);
                
                $inc++;
            }
            
            $this->excel->getActiveSheet()->setCellValue('B'.($inc+$extra),'DEPOSITOS REALIZADOS EN '.strtoupper(month_long($mes)).'/'.$anio);
           
            $this->excel->getActiveSheet()->setCellValue('G'.($inc+$extra),'=SUM(G3:G'.($extra+$inc-1).')');
            $this->excel->getActiveSheet()->removeRow(2,1);
            
            $position++;
            
        }
        
        //print_r($array);
        //exit();
        
        //$this->excel->getActiveSheet()->removeRow(3,1);
         /*******Imprimo contenido del Excel*********/
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Depositos_'.$file_name.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
        
    }
    function list_directores()
    {
        
        $id_centro = $this->input->post('id_centro');
        
        $result = $this->db->select('id,nombre,fecha_fin,fecha_ini,banco,no_tarjeta')
                        ->order_by('fecha_ini','DESC')
                        ->where('id_centro',$id_centro)
                        ->where('activo',1)
                        ->get('directores')
                        ->result();
        
        foreach($result as &$row)
        {
            
            $row->vigencia = format_date_calendar($row->fecha_ini,'short').' al '.format_date_calendar($row->fecha_fin,'short');
            
        }
        
        if($result)echo json_encode($result);
    }
    
    function prueba()
    {
        $this->load->library('drive');
    }
}
?>
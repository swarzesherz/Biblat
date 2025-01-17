<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);
require 'vendor/autoload.php';
require 'vendor/google-api/vendor/autoload.php';

class Main extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->output->enable_profiler($this->config->item('enable_profiler'));
		$this->template->set_partial('biblat_js', 'javascript/biblat', array(), TRUE, FALSE);
		$this->template->set_partial('submenu', 'layouts/submenu');
		$this->template->set_partial('search', 'layouts/search');
		$this->template->set_breadcrumb(_('Inicio'), site_url('/'));
		$this->template->set('class_method', $this->router->fetch_class().$this->router->fetch_method());
	}
	public function index(){
		$data = array();
		$data['header']['title'] = _("Biblat");
		$data['header']['description'] = _('Biblat ofrece: referencias bibliográficas de documentos publicados en revistas científicas y académicas latinoamericanas indizadas en CLASE y PERIÓDICA, acceso al texto completo de revistas en acceso abierto, indicadores bibliométricos e información sobre los políticas de acceso de las revistas.');
		/*Consultas*/
		$this->load->database();
		
		/*Vistas*/
		//$this->template->set_partial('view_js', 'main/header', array(), TRUE, FALSE);
		$this->template->set_partial('frecuencias_accordion', 'frecuencias/index', array(), TRUE);
		$this->template->title(_('Biblat - Bibliografía latinoamericana'));
		$this->template->js('js/d3.js');
		$this->template->js('js/d3.layout.cloud.js');
		$this->template->js('assets/js/highcharts/phantomjs/highcharts8.js');
		$this->template->js('assets/js/highcharts/phantomjs/map8.js');
		$this->template->js('assets/js/highcharts/phantomjs/sunburst.js');
		$this->template->js('assets/js/highcharts/mapdata/latinoamerica.js');    
		$this->template->js('assets/js/highcharts/phantomjs/treemap8.js');
		$this->template->js('assets/js/datatables/datatables.min.js');
		$this->template->js('assets/js/datatables/input.js');
		$this->template->js('assets/js/utils/utils.js');
		$this->template->set_partial('main_js','main/mapa.js', array(), TRUE, FALSE);																			 
		$this->template->set_breadcrumb(_('Sobre Biblat'));
		$this->template->set_meta('description', _('Bibliografía latinoamericana'));
		$this->template->build('main/index', $data['index']);
	}

	public function creditos(){
		$data = array();
		$data['page_title'] = _('Créditos');
		$this->template->title(_('Créditos'));
		$this->template->set_meta('description', _('Créditos'));
		$this->template->build('main/creditos', $data);
	}

	public function bibliografia(){
		$data = array();
		$data['page_title'] = _('Bibliografía');
		$this->template->title(_('Bibliografía'));
		$this->template->set_breadcrumb(_('Documentos'));
		$this->template->set_meta('description', _('Bibliografía'));
		$this->template->build('main/bibliografia', $data);
	}

		public function presentaciones(){
		$data = array();
		$data['page_title'] = _('Presentaciones PPT');
		$this->template->title(_('Presentaciones PPT'));
		$this->template->set_breadcrumb(_('Documentos'));
		$this->template->set_meta('description', _('Presentaciones PPT'));
		$this->template->build('main/presentaciones', $data);
	}

	public function multimedia(){
		$data = array();
		$data['page_title'] = _('Multimedia');
		$this->template->title(_('Multimedia'));
		$this->template->set_breadcrumb(_('Documentos'));
		$this->template->set_meta('description', _('Multimedia'));
		$this->template->build('main/multimedia', $data);
	}
	
	public function guias(){
		$data = array();
		$data['page_title'] = _('Guías');
		$this->template->title(_('Guías'));
		$this->template->set_breadcrumb(_('Documentos'));
		$this->template->set_meta('description', _('Guías'));
		$this->template->build('main/guias', $data);
	}

	public function sobreBiblat(){
		$data = array();
		$data['page_title'] = _('¿Qué es Biblat?');
		$this->template->title(_('¿Qué es Biblat?'));
		$this->template->set_breadcrumb(_('Sobre Biblat'));
		$this->template->set_meta('description', _('¿Qué es Biblat?'));
		$this->template->build('main/info_biblat', $data);
	}

	public function clasePeriodica(){
		$data = array();
		$this->load->database();
		$query = "SELECT * FROM \"mvDisciplinasBase\" ORDER BY base, \"disciplinaRevista\"";
		$query = $this->db->query($query);
		$data[ 'disciplina' ] = array();
		foreach ($query->result_array() as $row):
				$disciplina = array();
				$disciplina['disciplina'] = $row['disciplinaRevista'];
				$disciplina['slug'] = $row['disciplinaSlug'];
   				$data[ 'disciplina' ][ $row[ 'base' ] ][] = $disciplina;	
		endforeach;
		$data['page_title'] = _('CLASE y PERIÓDICA');
		$this->template->title(_('CLASE y PERIÓDICA'));
		$this->template->set_breadcrumb(_('Sobre Biblat'));
		$this->template->set_meta('description', _('CLASE y PERIÓDICA'));
		$this->template->build('main/info_clase_periodica', $data);
	}

	public function scielo(){
		$data = array();
		$data['page_title'] = _('Sobre SciELO');
		$this->template->title(_('Sobre SciELO'));
		$this->template->set_breadcrumb(_('Sobre Biblat'));
		$this->template->set_meta('description', _('Sobre SciELO'));
		$this->template->build('main/info_scielo', $data);
	}

	public function manualIndizacion(){
		$data = array();
		$data['page_title'] = _('Manual de indización');
		$this->template->set_partial('view_js', 'main/header_metodologia', array(), TRUE);
		$this->template->title(_('Manual de indización'));
		$this->template->css('assets/css/colorbox.css');
		$this->template->js('assets/js/colorbox.js');
		$this->template->set_breadcrumb(_('Sobre Biblat'));
		$this->template->set_meta('description', _('Manual de indización'));
		$this->template->build('main/manual_indizacion', $data);
	}

	public function materialesDifusion(){
		$data = array();
		$data['page_title'] = _('Materiales de difusión');
		$this->template->title(_('Materiales de difusión'));
		$this->template->set_breadcrumb(_('Sobre Biblat'));
		$this->template->set_meta('description', _('Materiales de difusión'));
		$this->template->build('main/materiales_difusion', $data);
	}

	public function descripcionBiblat(){
		$data = array();
		$data['page_title'] = _('Descripción');
		$this->template->title(_('Descripción'));
		$this->template->set_breadcrumb(_('Bibliometría'));
		$this->template->set_meta('description', _('Descripción'));
		$this->template->build('main/descripcion_biblat', $data);
	}

	public function metodologiaBiblat(){
		$data = array();
		$data['page_title'] = _('Metodología');
		$this->template->set_partial('view_js', 'main/header_metodologia', array(), TRUE);
		$this->template->title(_('Metodología'));
		$this->template->css('assets/css/colorbox.css');
		$this->template->js('assets/js/colorbox.js');
		$this->template->set_breadcrumb(_('Bibliometría'));
		$this->template->set_meta('description', _('Metodología'));
		$this->template->build('main/metodologia_biblat', $data);
	}

	public function indicadoresScielo(){
		$data = array();
		$data['header']['title'] = _("Indicadores por revista");
		$this->load->view('header', $data['header']);
		$this->load->view('menu');
		$this->load->view('main/indicadores_scielo');
		$this->load->view('footer');
	}

	public function indicadoresRevista($alpha="a"){
		$this->load->library('pagination');
		$config['base_url'] = site_url('bibliometria/indicadores-por-revista');
		$config['uri_segment'] = 4;
		$config['first_link'] = "&laquo;";
		$config['last_link'] = "&raquo;";
		$config['next_link'] = "&rsaquo;";
		$config['prev_link'] = "&lsaquo;";
		$config['cur_tag_open'] = '<li class="active text-uppercase"><a href="javascript:;">';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li class="text-uppercase">';
		$config['num_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['full_tag_open'] = '<ul class="pagination hidden-md hidden-lg">';
		$config['full_tag_close'] = '</ul>';
		$this->pagination->initialize($config);

		$this->load->database();
		$query = ' SELECT '. 
                            ' v.revista, v."revistaSlug", v.articulos, "revistaISSN","revistaSiglum","disciplinaSlug",coautoriapricezakutina,subramayan,tasalawani,pratt,exogena '.
                            ' from '.
                            ' (SELECT max(v.revista) revista, v."revistaSlug", count(v.revista) AS articulos FROM "mvSearch" v GROUP BY v."revistaSlug") v '.
                            ' LEFT JOIN (SELECT distinct "revistaSlug", "revistaISSN","revistaSiglum","disciplinaSlug",coautoriapricezakutina,subramayan,tasalawani,pratt,exogena '.
                            ' FROM "vIndicadoresRevistaGeneral" WHERE substr("revistaSlug", 1 , 1)=\''.$alpha.'\')a '.
                            ' ON a."revistaSlug" = v."revistaSlug" '.
                            ' WHERE SUBSTRING(LOWER(v.revista), 1, 1)=\''.$alpha.'\'' .
                            ' ORDER BY v.revista; ';							 
		
		$query = $this->db->query($query);
		$this->db->close();

		$data['registrosTotalArticulos'] = 0;											 
		$data = array();
		foreach ($query->result_array() as $row):
			$row['agedocjournalcitation'] = json_decode($row['agedocjournalcitation'], TRUE);
			$row['doctypejournalcitation'] = json_decode($row['doctypejournalcitation'], TRUE);
			$data['revistas'][] = $row;
		$data['registrosTotalArticulos'] += $row['articulos'];																	  
		endforeach;
		$data['alpha_links'] = $this->pagination->create_alpha_links();
		$data['alpha'] = strtoupper($alpha);
		
		$data['page_title'] = sprintf('Revistas por orden alfabético con sus indicadores: %s', strtoupper($alpha));																									  
		$this->template->css('assets/css/colorbox.css');
		$this->template->js('assets/js/colorbox.js');
		$this->template->set_partial('view_js', 'main/indicadores_por_revista_js', array(), TRUE);
		$this->template->title(_('Indicadores por revista'));
		$this->template->set_breadcrumb(_('Bibliometría'));
		$this->template->set_meta('description', _('Metodología'));
		$this->template->build('main/indicadores_por_revista', $data);
	}

	public function criteriosSeleccion(){
		$data = array();
		$data['page_title'] = _('Criterios de selección');               
		$this->template->title(_('Criterios de selección'));
		$this->template->set_breadcrumb(_('Postular una revista'));
		$this->template->set_meta('description', _('Criterios de selección'));
		$this->template->build('main/criterios_seleccion', $data);
	}
        
        function multi_attach_mail($to, $subject, $message, $senderMail, $senderName, $files, $usuario){

            //$from = $senderName." <".$senderMail.">"; 
            //$headers = "From: $from\n";
			$headers = "From: BIBLAT<biblat_comite@dgb.unam.mx>\nReply-To: biblat_comite@dgb.unam.mx";

            // boundary 
            $semi_rand = md5(time()); 
            $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

            // headers for attachment 
            $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\""; 

            // multipart boundary 
            $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" .
            "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n"; 
			
			if($usuario){
				// preparing attachments
				if(count($files) > 0){
					for($i=0;$i<count($files);$i++){
						if(is_file($files[$i])){
							$message .= "--{$mime_boundary}\n";
							$fp =    @fopen($files[$i],"rb");
							$data =  @fread($fp,filesize($files[$i]));

							@fclose($fp);
							$data = chunk_split(base64_encode($data));
							$message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n" . 
							"Content-Description: ".basename($files[$i])."\n" .
							"Content-Disposition: attachment;\n" . " filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n" . 
							"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
						}
					}
				}
			}

            $message .= "--{$mime_boundary}--";
            $returnpath = "-f" . $senderMail;

            //send email
            $mail = @mail($to, $subject, $message, $headers, $returnpath); 

            //function return true, if email sent, otherwise return fasle
            if($mail){ return TRUE; } else { return FALSE; }

        }
        
        public function preevaluacion(){
            $data = array();
            $pos = strpos(uri_string(), 'simulador');
            $pos2 = strpos(uri_string(), 'postular');
			$pos3 = strpos(uri_string(), 'revista');
            if($pos == false){
                $data['simulador'] = false;
            }else{
                $data['simulador'] = true;
            }
            if($pos2 == false){
                $data['postularSegunda'] = false;
            }else{
                $data['postularSegunda'] = true;
            }
			if($pos3 == false){
                $data['sinOJS'] = false;
            }else{
                $data['sinOJS'] = true;
            }
			if( isset($_POST['url']) ){
                $data['url'] = $_POST['url'];
            }						   
            $this->template->set_layout('default_sel');
            $data['page_title'] = _('Preevaluación editorial');
            $data['page_subtitle'] = _('Módulo de autoevaluación de revistas editoriales');										 
            $this->template->js('assets/js/highcharts/phantomjs/highcharts8.js');
            $this->template->js('assets/js/highcharts/phantomjs/highcharts-more8.js');
            $this->template->js('assets/js/highcharts/phantomjs/solid-gauge8.js');
            $this->template->js('assets/js/apigoogle/api.js');
            $this->template->js('assets/js/apigoogle/getaccesstokenfromserviceaccount.js');
			$this->template->js('assets/js/utils/utils.js');												
            $this->template->set_partial('main_js','main/preevaluacion.js', array(), TRUE, FALSE);
            $this->template->title(_('Preevaluación'));
            $this->template->set_breadcrumb(_('Postular una revista'));
            $this->template->set_meta('description', _('Preevaluación'));
            $this->template->build('main/preevaluacion', $data);
        }
        
        public function createPlantilla(){
            $spreadsheet = PHPExcel_IOFactory::load("archivos/Plantilla.xlsx");
           
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            if(!filter_var($_POST['completo'], FILTER_VALIDATE_BOOLEAN)){
                $spreadsheet->removeSheetByIndex(6);
                $spreadsheet->removeSheetByIndex(5);
                $spreadsheet->removeSheetByIndex(4);
                $spreadsheet->removeSheetByIndex(3);
                $spreadsheet->removeSheetByIndex(2);
                $spreadsheet->removeSheetByIndex(1);
                $spreadsheet->removeSheetByIndex(3);
                $spreadsheet->removeSheetByIndex(2);
                $spreadsheet->removeSheetByIndex(1);
                $spreadsheet->removeSheetByIndex(1);
            }
            if ( $_POST ) {
                    foreach ( $_POST['criterio'] as $key2 => $value2 ) {
                        if(filter_var($value2['cumplo'], FILTER_VALIDATE_BOOLEAN))
                            $sheet->setCellValue($value2['celda'], 1);
                        else
                            $sheet->setCellValue($value2['celda'], 0);
                    }
					
					$spreadsheet->setActiveSheetIndex(1);
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setCellValue("C4", $_POST['nombre_revista']);
                    $sheet->setCellValue("C5", $_POST['issn']);
                    $sheet->setCellValue("C6", $_POST['issne']);
                    $sheet->setCellValue("C7", $_POST['pais']);
                    $sheet->setCellValue("C8", $_POST['organizacion']);
                    $sheet->setCellValue("C9", $_POST['periodicidad']);
                    $sheet->setCellValue("C10", $_POST['correo']);
                    $sheet->setCellValue("C11", $_POST['url']);
                    $sheet->setCellValue("C12", $_POST['nombre']);
                    
                    $sheet->setCellValue("C14", $_POST['calle']);
                    $sheet->setCellValue("C15", $_POST['ciudad']);
                    $sheet->setCellValue("C16", $_POST['estado']);
                    $sheet->setCellValue("C17", $_POST['telefono']);
                    $sheet->setCellValue("C18", $_POST['correo_ed']);
                    $sheet->setCellValue("C19", $_POST['cp']);
                    $sheet->setCellValue("C20", $_POST['ap']);
                    
                    $sheet->setCellValue("C22", $_POST['periodicidad']);
                    $sheet->setCellValue("C23", $_POST['periodicidad_anterior']);
                    
                    for($i=1;$i<=intval($_POST['num_otra_institucion']);$i++){
                        $sheet->setCellValue("C".(25+$i), $_POST['otra_institucion_'.$i]);
                        $sheet->setCellValue("D".(25+$i), $_POST['otra_institucion_pais_'.$i]);
                    }
                    
                    if($_POST['tipo_arbitraje']!='')
                        $sheet->setCellValue("C".(32+intval($_POST['tipo_arbitraje'])),'X');
                    
                    if($_POST['licencia']!='')
                        $sheet->setCellValue("C".(39+intval($_POST['licencia'])),'X');
                    
                    if($_POST['acceso']!='')
                        $sheet->setCellValue("C".(50+intval($_POST['acceso'])),'X');
                    
                    $sheet->setCellValue("C58", $_POST['latindex_impresa']);
                    $sheet->setCellValue("C59", $_POST['latindex_e']);
                    $sheet->setCellValue("C60", $_POST['doaj']);
                    $sheet->setCellValue("C61", $_POST['scielo']);
                    $sheet->setCellValue("C62", $_POST['redalyc']);
                    $sheet->setCellValue("C63", $_POST['dialnet']);
                    $sheet->setCellValue("C64", $_POST['redib']);
                    
                    for($i=1;$i<=intval($_POST['num_otra_liga']);$i++){
                        $sheet->setCellValue("C".(64+$i), $_POST['otro_sistema_'.$i]);
                    }
                    
                    for($i=1;$i<=intval($_POST['num_otro_miembro']);$i++){
                        $sheet->setCellValue("B".(93+$i), $_POST['otro_miembro_nombre_'.$i]);
                        $sheet->setCellValue("C".(93+$i), $_POST['otro_miembro_institucion_'.$i]);
                        $sheet->setCellValue("D".(93+$i), $_POST['otro_miembro_institucion_pais_'.$i]);
                        $sheet->setCellValue("E".(93+$i), $_POST['otro_miembro_orcid_'.$i]);
                    }
            } 
            
            $writer = PHPExcel_IOFactory::createWriter($spreadsheet, 'Excel2007');
            if(filter_var($_POST['completo'], FILTER_VALIDATE_BOOLEAN))
                $writer->save('Preevaluacion_'.$_POST['issn'].'.xlsx');
            else
                $writer->save('Preevaluacion_'.$_POST['correo'].'.xlsx');
            
            $name = basename('CartaDePostulacion', '.php');
            $source = "archivos/{$name}.docx";
            
            if(filter_var($_POST['completo'], FILTER_VALIDATE_BOOLEAN)){
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($source);
                $sections = $phpWord->getSections();
                $documento = new \PhpOffice\PhpWord\PhpWord();

                $paragraphStyleName = 'pStyle';
                $documento->addParagraphStyle($paragraphStyleName, array(
                                                                            //'spacing'=> 480,
                                                                            'lineHeight'=>1.5,
                                                                            'alignment'=>'both'
                                                                        ));
                $meses = array('','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
                $dia = intval(date("j"));
                $mes = intval(date("n"));
                $anio = intval(date("Y"));
                $mes = $meses[$mes];

                foreach ($sections as $section) {
                    $seccionw = $documento->addSection();
                    $elements = $section->getElements();
                    foreach ($elements as $element) {                                    
                        if (get_class($element) === 'PhpOffice\PhpWord\Element\Text') {
                            $uploadedText .= $element->getText();
                            $uploadedText .= ' ';
                        } else if (get_class($element) === 'PhpOffice\PhpWord\Element\TextRun') {
                            $textRunElements = $element->getElements();
                            $textRun = $seccionw->addTextRun($paragraphStyleName);
                            foreach ($textRunElements as $textRunElement) {
                                $uploadedText = $textRunElement->getText();
                                $uploadedText = str_replace("NOMBRE DE LA REVISTA", mb_strtoupper($_POST['nombre_revista']), $uploadedText);
                                $uploadedText = str_replace("xxxx-xxxx", mb_strtoupper($_POST['issn']), $uploadedText);
                                $uploadedText = str_replace("PAÍS", mb_strtoupper($_POST['pais']), $uploadedText);
                                $uploadedText = str_replace("NOMBRE DE LA ORGANIZACIÓN QUE EDITA", mb_strtoupper($_POST['organizacion']), $uploadedText);
                                $uploadedText = str_replace("PERIODICIDAD", mb_strtoupper($_POST['periodicidad']), $uploadedText);
                                if(!filter_var($_POST['autorizo'], FILTER_VALIDATE_BOOLEAN))
                                    $uploadedText = str_replace("AUTORIZO", "NO AUTORIZO", $uploadedText);
                                $uploadedText = str_replace("NOMBRE", mb_strtoupper($_POST['nombre']), $uploadedText);
                                $uploadedText = str_replace("CIUDAD", mb_strtoupper($_POST['ciudad']), $uploadedText);
                                $uploadedText = str_replace("FECHA", $dia . " DE " . mb_strtoupper($mes) . " DE " . $anio , $uploadedText);

                                $textRun->addText($uploadedText,$textRunElement->getFontStyle());
                            }
                        } else if (get_class($element) === 'PhpOffice\PhpWord\Element\TextBreak') {
                            $uploadedText .= ' ';
                        } else {
                            throw new Exception('Unknown class type ' . get_class($e));
                        }
                    }
                }
                $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($documento, "Word2007");
                $objWriter->save("CartaDePostulacion_".$_POST['issn'].".docx");
            }
            
            if(filter_var($_POST['completo'], FILTER_VALIDATE_BOOLEAN)){
                $mensaje = "Estimado(a) editor(a):<br><br>".
                "De acuerdo con la autoevaluación que realizó el día de hoy en nuestro sitio web biblat.unam.mx, le informamos que su revista cumple con los criterios obligatorios para ingresar a CLASE, PERIÓDICA, portal BIBLAT y catálogo SERIUNAM.<br><br>".
                "Adjunto a este mensaje, usted encontrará su carta de postulación y un archivo con los resultados de su postulación.".
                "<br><br>".
				"Le recordamos que su autoevaluación será analizada por nuestro Comité de Evaluación, y que este, podrá emitir recomendaciones previa indización de su revista.".
                "Quedamos atentos a sus comentarios y agradecemos su colaboración.<br><br>".
                "Saludos.<br>".
                "Comité de Evaluación de Publicaciones Periódicas para CLASE, PERIÓDICA y Catálogo SERIUNAM<br>".
                "biblat_comite@dgb.unam.mx<br><br>".
                "<i>Este correo es generado automáticamente a nombre del Comité, su revista ya se encuentra registrada en nuestra agenda y en breve le comunicaremos el resultado de postulación. Si usted requiere información adicional, favor de responder este mismo mensaje.</i>";
            }else{
                $mensaje = "Estimado(a) editor(a):<br><br>".
                "De acuerdo con la autoevaluación que realizó el día de hoy en nuestro sitio web biblat.unam.mx, lamentamos informarle que su revista no cumple con los criterios obligatorios para ingresar a CLASE, PERIÓDICA, portal BIBLAT y catálogo SERIUNAM.<br><br>".
                "Adjunto a este mensaje, usted encontrará el resultado de su autoevaluación.".
                " Los criterios marcados con el número cero corresponden a aquellos que su revista no cumple, mismos que le recomendamos perfeccionar.<br><br>".
                "Quedamos atentos a sus comentarios y esperamos contar con su postulación en un momento posterior.<br><br>".
                "Saludos.<br>".
                "Comité de Evaluación de Publicaciones Periódicas para CLASE, PERIÓDICA y Catálogo SERIUNAM<br>".
                "biblat_comite@dgb.unam.mx<br><br>".
				"<i>Este correo es generado automáticamente a nombre del Comité. Si usted requiere información adicional, favor de responder este mismo mensaje.</i>";
            }

            $mensaje = wordwrap($mensaje, 70, "\r\n");
            
            if(filter_var($_POST['completo'], FILTER_VALIDATE_BOOLEAN)){
                $correos = $_POST['correo']/*.",biblat_comite@dgb.unam.mx"*/;
                $arraydocs = array(
                    "CartaDePostulacion_".$_POST['issn'].".docx",'Preevaluacion_'.$_POST['issn'].'.xlsx'
                );
            }else{
                $correos = $_POST['correo'];
                $arraydocs = array(
                    'Preevaluacion_'.$_POST['correo'].'.xlsx'
                );
            }
            
            $this->multi_attach_mail($correos,
                    "Postulación de Revista",
                    $mensaje,
                    "biblat_comite@dgb.unam.mx",
                    "Comité de Evaluación de Publicaciones Periódicas para CLASE, PERIÓDICA y Catálogo SERIUNAM",
                    $arraydocs,
					true
                    );
			$this->multi_attach_mail('biblat_comite@dgb.unam.mx',
                    "Postulación de Revista",
                    $mensaje,
                    "biblat_comite@dgb.unam.mx",
                    "Comité de Evaluación de Publicaciones Periódicas para CLASE, PERIÓDICA y Catálogo SERIUNAM",
                    $arraydocs,
					false
                    );
            
            if(filter_var($_POST['completo'], FILTER_VALIDATE_BOOLEAN)){
                $result = $this->setDocumentsDrive("CartaDePostulacion_".$_POST['issn'].".docx", 'Preevaluacion_'.$_POST['issn'].'.xlsx');
                unlink("CartaDePostulacion_".$_POST['issn'].".docx");
                unlink('Preevaluacion_'.$_POST['issn'].'.xlsx');
                echo('{"docx":"'.$result[0].'", "xlsx":"'.$result[1].'"}');
            }
            else
                unlink('Preevaluacion_'.$_POST['correo'].'.xlsx');

        }

	public function sitemap(){
		$data = array();
		$data['header']['title'] = _("Mapa del sitio");
		$this->load->view('header', $data['header']);
		$this->load->view('menu');
		$this->load->view('main/sitemap');
		$this->load->view('footer');
	}

	public function contacto(){
		$this->load->library('recaptcha');
		$data['main']['recaptcha_html'] = $this->recaptcha->recaptcha_get_html();
		$this->load->view('header', $data['header']);
		$this->load->view('menu', $data['header']);
		$this->load->view('main/contacto',$data['main']);
		$this->load->view('footer');
	}

	public function contactoSubmit(){
		$this->load->library('recaptcha');
		$this->recaptcha->recaptcha_check_answer();
		var_dump($this->recaptcha->getIsValid());
	}

	public function lang_notification(){
		$browserLang = browser_lang_array();
		$data['message'] = _sprintf('Deacuerdo al idioma de su navegador le sugerimos cambiar el idioma de la página a %s', $browserLang['title']);
		$data['button'] = '<button class="btn btn-warning translate">'._('Traducir').'</button>';
		echo json_encode($data, TRUE); exit(0);
	}
	
	public function dashboardmu(){
            $data = array();
            $data['page_title'] = _('Red de Biliotecas de América Latina y el Caribe');
            $this->template->css('css/dashboardmu.css');
            $this->template->title(_('Red de Biliotecas de América Latina y el Caribe'));
            $this->template->set_meta('description', _('Red de Biliotecas de América Latina y el Caribe'));
            $this->template->build('main/dashboardmu', $data);
	}
        
        public function getClientGoogle()
        {
            //Realiza la conexión hacia Googl Drive, utiliza las credenciales creadas en clientes OAuth 2.0
            $client = new Google_Client();
            $client->setApplicationName('Biblat');
            $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY, Google_Service_Drive::DRIVE]);
            $client->setAuthConfig('credentials.json');
            $client->setAccessType('offline');
            $client->setPrompt('select_account consent');

            // Load previously authorized token from a file, if it exists.
            // The file token.json stores the user's access and refresh tokens, and is
            // created automatically when the authorization flow completes for the first
            // time.
            $tokenPath = 'token.json';
            if (file_exists($tokenPath)) {
                $accessToken = json_decode(file_get_contents($tokenPath), true);
                $client->setAccessToken($accessToken);
            }

            // If there is no previous token or it's expired.
            if ($client->isAccessTokenExpired()) {
                // Refresh the token if possible, else fetch a new one.
                if ($client->getRefreshToken()) {
                    $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
                } else {
                    // Request authorization from the user.
                    $authUrl = $client->createAuthUrl();
                    
                

                    // Exchange authorization code for an access token.
                    $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
                    $client->setAccessToken($accessToken);

                    // Check to see if there was an error.
                    if (array_key_exists('error', $accessToken)) {
                        throw new Exception(join(', ', $accessToken));
                    }
                }
                // Save the token to a file.
                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            }
            return $client;
        }
        
        public function setDocumentsDrive($docx, $xlsx){
            //$client = $this->getClientGoogle();
            $client = $this->getClientGoogle2();
            $service = new Google_Service_Drive($client);
            $folderIddocx = "";
            $folderIdxlsx = "";
            
            
            if (file_exists($docx)) {
                $file = new Google_Service_Drive_DriveFile();
                $file->setName($docx);
                $file->setParents(array($folderIddocx));
                $file->setMimeType('application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        
                $result = $service->files->create(
                    $file,
                    array(
                        'data' => file_get_contents($docx),
                        'mimeType' => 'application/octet-stream',
                        'uploadType' => 'media'
                    )
                );
                
                $fileDocId = $result->getId();

            }
            
            if (file_exists($xlsx)) {
                $file = new Google_Service_Drive_DriveFile();
                $file->setName($xlsx);
                $file->setParents(array($folderIdxlsx));
                $file->setMimeType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                        
                $result = $service->files->create(
                    $file,
                    array(
                        'data' => file_get_contents($xlsx),
                        'mimeType' => 'application/octet-stream',
                        'uploadType' => 'media'
                    )
                );
                
                $fileXlsxId = $result->getId();

            }
            
            return [$fileDocId, $fileXlsxId];
        }
        
        public function getClientGoogle2()
        {
            //Realiza la conexión hacia Googl Drive, utiliza las credenciales creadas mediante service account
             putenv("GOOGLE_APPLICATION_CREDENTIALS=credentials2.json");
            $client = new Google_Client();
            $client->setApplicationName('Biblat');
            $client->setScopes([Google_Service_Sheets::SPREADSHEETS_READONLY, Google_Service_Drive::DRIVE]);
            $client->useApplicationDefaultCredentials();
            return $client;
        }
	
}

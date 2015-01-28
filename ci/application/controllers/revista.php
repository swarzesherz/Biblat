<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Revista extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->output->enable_profiler($this->config->item('enable_profiler'));
	    $this->template->set_partial('biblat_js', 'javascript/biblat', array(), TRUE);
		$this->template->set_partial('submenu', 'layouts/submenu');
		$this->template->set_partial('search', 'layouts/search');
		$this->template->set_breadcrumb(_('Inicio'), site_url('/'));
		$this->template->set_breadcrumb(_('Revista'));
		$this->template->set('class_method', $this->router->fetch_class().$this->router->fetch_method());
	}

	private $database = array(
				'CLA01' => 'CLASE',
				'PER01' => 'PERIÓDICA'
			);

	public function index($revistaSlug){
		$data = array();
		/*Obteniendo articulos de la revista*/
		$queryFields="SELECT 
					DISTINCT sistema, 
					articulo, 
					\"articuloSlug\", 
					revista, 
					\"revistaSlug\",  
					\"paisRevista\", 
					\"anioRevista\", 
					volumen, 
					numero, 
					periodo, 
					paginacion, 
					url->>0 AS url, 
					\"autoresSecJSON\",
					\"autoresSecInstitucionJSON\",
					\"autoresJSON\",
					\"institucionesSecJSON\",
					\"institucionesJSON\"";
		$queryFrom = "FROM \"vSearchFull\" WHERE \"revistaSlug\"='{$revistaSlug}'";
		$query = "{$queryFields} 
				{$queryFrom} 
				ORDER BY \"anioRevista\" DESC, volumen DESC, numero DESC, \"articuloSlug\"";
		
		$queryCount = "SELECT count (DISTINCT sistema) as total {$queryFrom}";
		
		/*Paginación y resultados*/
		$paginationURL = site_url("/revista/{$revistaSlug}");
		$perPage = 20;
		$articulosResultado = articulosResultado($query, $queryCount, $paginationURL, $perPage);
		/*Resultados de la página*/
		$data['main']['links'] = $articulosResultado['links'];
		$data['main']['search']['total'] = $articulosResultado['totalRows'];
		$data['main']['resultados'] = $articulosResultado['articulos'];
		$data['main']['revista'] = current($articulosResultado['articulos']);
		$data['main']['revista'] = $data['main']['revista']['revista'];
		/*Vistas*/
		$data['header']['title'] = _sprintf('Biblat - Revista: %s', $data['main']['revista']);
		$breadcrumb = sprintf('%s (%%d documentos)', $data['main']['revista']);
		$data['main']['page_title'] = sprintf($breadcrumb, $articulosResultado['totalRows']);

		$this->template->set_partial('view_js', 'buscar/header', $data['header'], TRUE);
		$this->template->title($data['header']['title']);
		$this->template->css('assets/css/colorbox.css');
		$this->template->css('assets/css/colorboxIndices.css');
		$this->template->js('assets/js/colorbox.js');
		$this->template->js('assets/js/jquery.highlight.js');
		if(ENVIRONMENT === "production"):
			$this->template->js('//s7.addthis.com/js/300/addthis_widget.js#pubid=herz');
		endif;
		$this->template->set_meta('description', $data['main']['page_title']);
		$this->template->build('revista/index', $data['main']);
	}

	public function articulo($revista='', $articulo='', $mail=''){
		$uriVar = $this->uri->ruri_to_assoc();
		if($mail == 'true'):
			$uriVar['revista'] = $revista;
			$uriVar['articulo'] = $articulo;
			$uriVar['mail'] = $mail;
		endif;

		/*Consultas*/
		$this->load->database();
		$query = "SELECT 
				s.sistema, 
				s.articulo, 
				s.\"articuloSlug\",
				s.revista, 
				s.\"revistaSlug\", 
				s.issn, 
				s.\"anioRevista\", 
				s.volumen, 
				s.numero, 
				s.periodo, 
				s.paginacion, 
				s.\"paisRevista\", 
				s.idioma, 
				s.\"tipoDocumento\", 
				s.\"enfoqueDocumento\", 
				s.\"autoresSecJSON\", 
				s.\"autoresSecInstitucionJSON\", 
				s.\"autoresJSON\", 
				s.\"institucionesSecJSON\", 
				s.\"institucionesJSON\", 
				s.\"disciplinas\", 
				s.\"palabraClave\",
				s.\"keyword\",
				s.\"resumen\",
				s.url
			FROM \"vSearchFull\" s
			WHERE \"revistaSlug\"='{$uriVar['revista']}' AND \"articuloSlug\"='{$uriVar['articulo']}'";
		$query = $this->db->query($query);
		$articulo = $query->row_array();
		$query->free_result();
		$this->db->close();
		/*Ordenando los datos del articulo*/
		/*Generando arreglo de autores*/
		if($articulo['autoresSecJSON'] != NULL && $articulo['autoresJSON'] != NULL):
			$articulo['autores'] = array_combine(json_decode($articulo['autoresSecJSON']), json_decode($articulo['autoresJSON']));
		endif;
		/*Generando arreglo institucion de autores*/
		if($articulo['autoresSecJSON'] != NULL && $articulo['autoresSecInstitucionJSON'] != NULL):
			$articulo['autoresInstitucionSec'] = array_combine(json_decode($articulo['autoresSecJSON']), json_decode($articulo['autoresSecInstitucionJSON']));
		endif;
		unset($articulo['autoresSecJSON'], $articulo['autoresJSON'], $articulo['autoresSecInstitucionJSON']);
		/*Generando arreglo de instituciones*/
		if($articulo['institucionesSecJSON'] != NULL && $articulo['institucionesJSON'] != NULL):
			$articulo['instituciones'] = array_combine(json_decode($articulo['institucionesSecJSON']), json_decode($articulo['institucionesJSON']));
		endif;
		unset($articulo['institucionesSecJSON'], $articulo['institucionesJSON']);
		/*Generando disciplinas*/
		$articulo['disciplinas'] = json_decode($articulo['disciplinas']);
		/*Generando palabras clave*/
		if($articulo['palabraClave'] != NULL):
			$articulo['palabraClave'] = json_decode($articulo['palabraClave']);
		endif;
		/*Generando keyword*/
		if($articulo['keyword'] != NULL):
			$articulo['keyword'] = json_decode($articulo['keyword']);
		endif;
		/*Generando resumen*/
		if($articulo['resumen'] != NULL):
			$articulo['resumen'] = json_decode($articulo['resumen']);
		endif;
		/*Generando ulr*/
		if($articulo['url'] != NULL):
			$articulo['url'] = json_decode($articulo['url']);
		endif;

		/*Limpiando caracteres html*/
		$articulo = htmlspecialchars_deep($articulo);
		/*Creando lista de autores en html*/
		$articulo['autoresHTML'] = "";
		if(isset($articulo['autores'])):
			$totalAutores = count($articulo['autores']);
			$indexAutor = 1;
			foreach ($articulo['autores'] as $key => $autor):
				$articulo['autoresHTML'] .= "{$autor}";
				if ( isset($articulo['instituciones'][$articulo['autoresInstitucionSec'][$key]]) ):
					$articulo['autoresHTML'] .= "<sup>{$articulo['autoresInstitucionSec'][$key]}</sup>";
				endif;
				if($indexAutor < $totalAutores):
					$articulo['autoresHTML'] .= "<br/>";
				endif;
				$indexAutor++;
			endforeach;
		endif;
		/*Creando lista de instituciones html*/
		$articulo['institucionesHTML'] = "";
		if(isset($articulo['instituciones'])):
			$totalInstituciones = count($articulo['instituciones']);
			$indexInstitucion = 1;
			foreach ($articulo['instituciones'] as $key => $institucion):
				$articulo['institucionesHTML'] .= "<sup>{$key}</sup>{$institucion}";
				if($indexInstitucion < $totalInstituciones):
					$articulo['institucionesHTML'] .= "<br/>";
				endif;
				$indexInstitucion++;
			endforeach;
		endif;

		/*Creando disciplinas HTML*/
		$articulo['disciplinasHTML'] = "";
		if(isset($articulo['disciplinas'])):
			$totalDisciplinas = count($articulo['disciplinas']);
			$indexDisciplina = 1;
			foreach ($articulo['disciplinas'] as $key => $disciplina):
				$articulo['disciplinasHTML'] .= "{$disciplina}";
				if($indexDisciplina < $totalDisciplinas):
					$articulo['disciplinasHTML'] .= ",<br/>";
				endif;
				$indexDisciplina++;
			endforeach;
		endif;

		/*Creando palabras clave HTML*/
		$articulo['palabraClaveHTML'] = "";
		if(isset($articulo['palabraClave'])):
			$totalPalabrasClave = count($articulo['palabraClave']);
			$indexPalabraClave = 1;
			foreach ($articulo['palabraClave'] as $key => $palabraClave):
				$articulo['palabrasClaveHTML'] .= "{$palabraClave}";
				if($indexPalabraClave < $totalPalabrasClave):
					$articulo['palabrasClaveHTML'] .= ",<br/>";
				endif;
				$indexPalabraClave++;
			endforeach;
		endif;

		/*Creando keyword HTML*/
		$articulo['keywordHTML'] = "";
		if(isset($articulo['keyword'])):
			$totalKeyword = count($articulo['keyword']);
			$indexKeyword = 1;
			foreach ($articulo['keyword'] as $key => $keyword):
				$articulo['keywordHTML'] .= "{$keyword}";
				if($indexKeyword < $totalKeyword):
					$articulo['keywordHTML'] .= ",<br/>";
				endif;
				$indexKeyword++;
			endforeach;
		endif;
		/*Creando resumen HTML*/
		$articulo['resumenHTML'] = array();
		if(isset($articulo['resumen'])):
			foreach ($articulo['resumen'] as $key => $resumen):
				switch ($key):
					case 'a':
						$resumenHTML['title'] = _('Resumen en español');
						break;
					case 'p':
						$resumenHTML['title'] = _('Resumen en portugués');
						break;
					case 'i':
						$resumenHTML['title'] = _('Resumen en inglés')	;
						break;
					case 'o':
						$resumenHTML['title'] = _('Otro resumen');
						break;
				endswitch;
				$resumenHTML['body'] = $resumen;
				$articulo['resumenHTML'][] = $resumenHTML;
			endforeach;
		endif;

		if (isset($articulo['paginacion'])):
			$articulo['paginacionFirst'] = preg_replace("/[-\s]+/", "", preg_replace('/(^\s*\d+\s*?-|^\s*\d+?\s*$).*/m', '\1', $articulo['paginacion']));
			$articulo['paginacionLast'] = preg_replace("/[-\s]+/", "", preg_replace('/.*(-\s*\d+\s*?$|^\s*\d+?\s*$).*/m', '\1', $articulo['paginacion']));
		endif;

		/*Generando database*/
		$articulo['database'] = $this->database[substr($articulo['sistema'], 0, 5) ];
		/*Limpiando número de sistema*/
		$articulo['sistema'] = substr($articulo['sistema'], 5, 9);

		$articulo = remove_empty($articulo);

		$data['main']['articulo'] = $articulo;
		$data['header']['articulo'] = $data['main']['articulo'];
		$data['header']['title'] = _sprintf('Biblat - Revista: %s - Artículo: %s', $articulo['revista'], $articulo['articulo']);
		$data['main']['page_title'] = $articulo['articulo'];
		$data['main']['mail'] = FALSE;
		/*Vistas*/
		if(isset($_POST['ajax'])):
			$this->output->enable_profiler(FALSE);
			$data['main']['ajax'] = TRUE;
			$this->parser->parse('revista/articulo', $data['main']);
			return;
		endif;
		if($uriVar['mail'] == "true"):
			$this->output->enable_profiler(FALSE);
			$data['main']['mail'] = TRUE;
			return $this->parser->parse('revista/articulo', $data['main'], TRUE);
		endif;

		$this->template->set_partial('view_js', 'revista/articulo_header', array(), TRUE);
		$this->template->title($data['header']['title']);
		$this->template->set_breadcrumb(_('Artículo'));
		if(ENVIRONMENT === "production"):
			$this->template->js('//s7.addthis.com/js/300/addthis_widget.js#pubid=herz');
		endif;
		$this->template->set_meta('description', $data['main']['page_title']);
		/*Article meta*/
		if(isset($articulo)):
			$this->template->set_meta('citation_title', $articulo['articulo']);
			$this->template->set_meta('eprints.title', $articulo['articulo']);
			$this->template->set_meta('citation_journal_title', $articulo['revista']);
			$this->template->set_meta('citation_issn', $articulo['issn']);
			$this->template->set_meta('eprints.type', "article");
			$this->template->set_meta('eprints.ispublished', "pub");
			$this->template->set_meta('eprints.date_type', "published");
			$this->template->set_meta('eprints.publication', $articulo['revista']);
			$this->template->set_meta('prism.publicationName', $articulo['revista']);
			$this->template->set_meta('prism.issn', $articulo['issn']);
			$this->template->set_meta('dc.title', $articulo['articulo']);
			if(isset($articulo['numero'])):
				$this->template->set_meta('citation_issue', $articulo['numero']);
				$this->template->set_meta('prism.number', $articulo['numero']);
			endif;
			if(isset($articulo['volumen'])):
				$this->template->set_meta('citation_volume', $articulo['volumen']);
				$this->template->set_meta('eprints.volume', $articulo['volumen']);
			endif;
			if(isset($articulo['paginacion'])):
				$this->template->set_meta('citation_firstpage', $articulo['paginacionFirst']);
				$this->template->set_meta('citation_lastpage', $articulo['paginacionLast']);
				$this->template->set_meta('eprints.pagerange', $articulo['paginacion']);
				$this->template->set_meta('prism.startingPage', $articulo['paginacionFirst']);
				$this->template->set_meta('prism.endingPage', $articulo['paginacionLast']);
			endif;
			if(isset($articulo['anioRevista'])):
				$this->template->set_meta('citation_date', $articulo['anioRevista']);
				$this->template->set_meta('eprints.date', $articulo['anioRevista']);
				$this->template->set_meta('prism.publicationDate', $articulo['anioRevista']);
				$this->template->set_meta('dc.date', $articulo['anioRevista']);
			endif;
			if(isset($articulo['autores'])):
				$autoresTotal = count($articulo['autores']);
				$autorIndex = 1;
				$citation_authors = "";
				foreach ($articulo['autores'] as $autor):
					$citation_authors .= "{$autor}";
					if($autorIndex < $autoresTotal):
						$citation_authors .= "; ";
					endif;
					$autorIndex++;
					$this->template->set_meta('eprints.creators_name', $autor);
					$this->template->set_meta('dc.creator', $autor);
				endforeach;
				$this->template->set_meta('citation_authors', $citation_authors);
			endif;
		endif;
		/*Article meta*/
		$this->template->build('revista/articulo', $data['main']);
	}

	public function solicitudDocumento(){
		$this->output->enable_profiler(false);
		if(!empty($_POST['email']) && !empty($_POST['from']) && !empty($_POST['revista']) && !empty($_POST['articulo'])):
			$biblatDB = $this->load->database('biblat', TRUE);
			$config['mailtype'] = 'html';
			$this->load->library('email');
			$this->email->initialize($config);
			$this->email->from('solicitud@biblat.unam.mx', 'Solicitud Biblat');
			$this->email->to('sinfo@dgb.unam.mx');
			//$this->email->to('achwazer@gmail.com');
			//$this->email->cc('anoguez@dgb.unam.mx');
			$this->email->subject('Solicitud de documento Biblat');
			$data = $_POST;
			$data['fichaDocumento'] = $this->articulo($data['revista'], $data['articulo'], 'true');
			$body = $this->parser->parse('revista/mail_solicitud', $data, TRUE);
			$this->email->message($body);
			$this->email->send();
			$this->email->clear();

			$this->email->from('anoguez@dgb.unam.mx', 'Dra. Araceli Noguez O.');
			$this->email->to($_POST['email']);
			$this->email->subject('Solicitud de documento Biblat');
			$body = $this->parser->parse('revista/mail_solicitud_usuario', $data, TRUE);
			$this->email->message($body);
			$this->email->send();
			/*Almacenando registro en la bitácora*/
			$database = ($data['database'] == "CLASE") ? 0 : 1;
			$ip = (isset($_SERVER['GEOIP_ADDR'])) ? $_SERVER['GEOIP_ADDR'] : $_SERVER['REMOTE_ADDR'];
			$pais = (isset($_SERVER['GEOIP_COUNTRY_NAME'])) ? "'{$_SERVER['GEOIP_COUNTRY_NAME']}'" : "NULL";
			$ciudad = (isset($_SERVER['GEOIP_REGION_NAME'])) ? "'{$_SERVER['GEOIP_REGION_NAME']}'" : "NULL";
			$session_id = $this->session->userdata('session_id');
			$query = "INSERT INTO \"logSolicitudDocumento\"(database, sistema, nombre, email, instituto, telefono, ip, pais, ciudad, session_id)
				VALUES ({$database}, '{$data['sistema']}', '{$data['from']}', '{$data['email']}', '{$data['instituto']}', '{$data['telefono']}', '{$ip}', {$pais}, {$ciudad}, '{$session_id}');";
			$biblatDB->query($query);
			$result = array(
					'type' => 'success',
					'title' => _('La solicitud ha sido enviada')
				);
		else:
			$result = array(
					'type' => 'error',
					'title' => _('No se pudo enviar la solicitud')
				);
		endif;
		echo json_encode($result);
	}
}
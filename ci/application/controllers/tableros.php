<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
error_reporting(0);

class Tableros extends CI_Controller {
    
    public function __construct(){
            parent::__construct();
            $this->output->enable_profiler($this->config->item('enable_profiler'));
            $this->template->set_partial('biblat_js', 'javascript/biblat', array(), TRUE, FALSE);
            $this->template->set_partial('submenu', 'layouts/submenu');
            $this->template->set_partial('search', 'layouts/search');
            $this->template->set_breadcrumb(_('Inicio'), site_url('/'));
            $this->template->set('class_method', $this->router->fetch_class().$this->router->fetch_method());
            $this->template->js('assets/js/highcharts/phantomjs/highcharts8.js');
            $this->template->js('assets/js/highcharts/phantomjs/highcharts-more8.js');
            $this->template->js('assets/js/highcharts/phantomjs/drilldown8.js');
            $this->template->js('assets/js/highcharts/phantomjs/treemap8.js');
            $this->template->js('assets/js/apigoogle/api.js');
            $this->template->js('assets/js/apigoogle/getaccesstokenfromserviceaccount.js');
            $this->template->js('assets/js/flip/flip.js');
            $this->template->js('assets/js/utils/utils.js');
            $this->template->css('css/jquery.slider.min.css');
            $this->template->css('css/colorbox.css');
            $this->template->js('js/jquery.slider.min.js');
            $this->template->js('js/jquery.serializeJSON.min.js');
            $this->template->js('js/colorbox.js');
            $this->template->js('js/env.js');
            $this->template->js('assets/js/datatables/datatables.min.js');
            $this->template->js('assets/js/datatables/input.js');
    }
    
    public function metametrics(){
        $data = array();
        $data['page_title'] = _('Estadísticas de uso');
        $this->template->set_layout('default_sel');
        $this->template->title(_('Tablero Metametrics'));
        $data['page_subtitle'] = _('Ranking MetaMetrics');
        $this->template->set_meta('description', _('MetaMetrics'));
        $this->template->set_partial('main_js', 'tableros/metametrics.js', array(), TRUE, FALSE);
        $this->template->build('tableros/metametrics', $data);
    }
    
    public function nucleorevistas(){
        $data = array();
        $data['page_title'] = _('Núcleo central de revistas');
        $this->template->set_layout('default_sel');
        $this->template->title(_('Núcleo central de revistas'));
        //$data['page_subtitle'] = _('Ranking MetaMetrics');
        $this->template->set_meta('description', _('Núcleo central de revistas'));
        $this->template->set_partial('main_js', 'tableros/nucleo_revistas.js', array(), TRUE, FALSE);
        $this->template->build('tableros/nucleo_revistas', $data);
    }
    
    public function get_nucleo(){
        $this->load->database();
        $query = '
        with conteo as (

	with revistas as(
	select 
	case when 
	slug(a.revista) in (select slug from revistas_scielo) or 
	a.issn in (select issn from revistas_scielo) or
        a.issn in (select issn2 from revistas_scielo)
        then
	\'Scielo-BIBLAT\'
	else
	\'BIBLAT\'
	end as coleccion,
	case when
	substring(a.sistema,1,3) = \'CLA\' then
	\'CLASE\'
	else
	\'PERIÓDICA\'
	end as base, 
	a.issn, 
	a.revista,
	slug(a.revista) slug, 
	a."paisRevista", 
	a."anioRevista", 
	a."disciplinaRevista"
	from article a
	where slug(a.revista) in (
				select rev from (
					select rev, count(1) num from(
						select distinct slug(revista) rev, "anioRevista" from article
						where "anioRevista" in (
							(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-5)::text,
							(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-4)::text,
							(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-3)::text,
							(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-2)::text,
							(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-1)::text
						)
					)a group by 1
				)b where num >= 5
			)
	and
	a."anioRevista" in (
		(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-5)::text,
		(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-4)::text,
		(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-3)::text,
		(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-2)::text,
		(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-1)::text
		)
	)
	select
		(select max(revista) from revistas where slug = r.slug) revista,
		(select max(issn) from revistas where slug = r.slug) issn,
		"anioRevista" anio,
		coleccion,
		base,
		slug,
		(select max("paisRevista") from revistas where slug = r.slug) pais,
		(select max("disciplinaRevista") from revistas where slug = r.slug) disciplina,
		count(1) docs
	from revistas r
	group by coleccion, base, slug, "anioRevista"
        )

        select  
                revista,
                issn,
                coleccion,
                base,
                slug,
                pais,
                disciplina,
                (select max(docs) from conteo where anio =(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-5)::text and slug = c.slug) as anio1,
                (select max(docs) from conteo where anio =(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-4)::text and slug = c.slug) as anio2,
                (select max(docs) from conteo where anio =(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-3)::text and slug = c.slug) as anio3,
                (select max(docs) from conteo where anio =(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-2)::text and slug = c.slug) as anio4,
                (select max(docs) from conteo where anio =(SELECT EXTRACT(\'Year\' FROM CURRENT_DATE)-1)::text and slug = c.slug) as anio5
        from conteo c
        group by
        revista,
                issn,
                coleccion,
                base,
                slug,
                pais,
                disciplina
        order by 1';
        $query = $this->db->query($query);
        
        $myarray = array();
        foreach ($query->result_array() as $row)
        {
            $myarray[] = $row;
        }

        echo json_encode($myarray);
    }
}
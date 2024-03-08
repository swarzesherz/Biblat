<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.'libraries/REST_Controller.php');

class Datos extends REST_Controller {
	public function __construct()
	{
		parent::__construct();
	}

	public function datosPais_get(){
		$data = array();
		$this->load->database();
		$query = "select \"paisRevistaSlug\" pais, count(distinct slug(\"revista\")) revistas, sum(art) articulos from(" .
                            "select ve.\"paisRevistaSlug\", ve.\"revista\", count(1) art from \"mvPaisRevistaArticulo\" ve group by 1,2".
                            ") a group by \"paisRevistaSlug\"";
		
		$query = $this->db->query($query);
		
		$this->response($query->result_array(), 200);
	}
        
        public function datosRevistas_get(){
            $data = array();
            $this->load->database();
            $query = "select ve.\"revista\", ve.\"revistaSlug\", ve.\"paisRevistaSlug\" pais, count(1) art from \"mvPaisRevistaArticulo\" ve group by 1,2,3";

            $query = $this->db->query($query);

            $this->response($query->result_array(), 200);
        }
        
        public function disciplinaPais_get(){
            $data = array();
            $this->load->database();
            $query = "select ve.\"paisRevistaSlug\" pais, ve.\"disciplinaRevista\" disciplina, count(1) art from \"mvPaisRevistaArticulo\" ve group by 1,2";

            $query = $this->db->query($query);

            $this->response($query->result_array(), 200);
        }
        
        public function anioPais_get(){
            $data = array();
            $this->load->database();
            $query = "select ve.\"paisRevistaSlug\" pais, ve.\"anioRevista\" anio, count(1) art from \"mvPaisRevistaArticulo\" ve group by 1,2";

            $query = $this->db->query($query);

            $this->response($query->result_array(), 200);
        }
        
        public function frec_institucion_get($institucion='',$limit=''){
            $data = array();
            $this->load->database();
            $query = "SELECT * FROM \"mvFrecuenciaInstitucionDARP\"";
            if($institucion != '' && $institucion != 'sin')
                $query .= ' where "institucionSlug"=\''.$institucion.'\'';
            if($institucion == 'sin' && $limit != '')
                $query .= ' order by documentos desc limit ' . $limit;
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function frec_institucion_pais_get($institucion=''){
            $data = array();
            $this->load->database();
            $query = 'SELECT * FROM "mvFrecuenciaInstitucionPais"';
            if($institucion !== '')
                $query .= ' where "institucionSlug"=\''.$institucion.'\'';
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function frec_institucion_disc_get($institucion=''){
            $data = array();
            $this->load->database();
            $query = 'SELECT * FROM "mvFrecuenciaInstitucionDisciplina"';
            if($institucion !== '')
                $query .= ' where "institucionSlug"=\''.$institucion.'\'';
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function frec_institucion_autor_get($institucion='',$limit=''){
            $data = array();
            $this->load->database();
            $query = 'SELECT * FROM "mvFrecuenciaInstitucionAutor"';
            if($institucion != '' && $institucion != 'sin')
                $query .= ' where "institucionSlug"=\''.$institucion.'\'';
            if($limit != '')
                $query .= ' order by documentos desc limit ' . $limit;
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function frec_institucion_coautoria_get($institucion='',$limit=''){
            $data = array();
            $this->load->database();
            $query = 'SELECT * FROM "mvFrecuenciaInstitucionCoautoria"';
            if($institucion != '' && $institucion != 'sin')
                $query .= ' where "institucionSlug"=\''.$institucion.'\'';
            if($limit != '')
                $query .= ' order by documentos desc limit ' . $limit;
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function frec_ipdr_get($institucion){
            $data = array();
            $this->load->database();
            $query = 'SELECT t."institucionSlug", t."revistaSlug", t."paisRevistaSlug", t."disciplinaSlug", (array_agg(t."disciplinaRevista"))[1] AS "disciplinaRevista", (array_agg(t."paisRevista"))[1] AS "paisRevista", (array_agg(t.revista))[1] AS revista, (array_agg(t.institucion))[1] AS institucion, sum(t.documentos) AS documentos
                FROM ( SELECT i.slug AS "institucionSlug", (array_agg(i.institucion))[1] AS institucion, s."paisRevistaSlug", s."paisRevista", s."disciplinaRevista", s."disciplinaSlug", s."revistaSlug", s.revista, count(DISTINCT s.sistema) AS documentos
                        FROM institution i
                        JOIN "vSearchFull" s ON i.sistema::text = s.sistema::text
                        GROUP BY i.slug, s."revistaSlug", s.revista, s."paisRevistaSlug", s."paisRevista", s."disciplinaSlug", s."disciplinaRevista"
                        ORDER BY i.slug, s."revistaSlug", (count(DISTINCT s.sistema)) DESC) t
                where t."institucionSlug"=\'' . $institucion .'\'
                GROUP BY t."institucionSlug", t."revistaSlug", t."paisRevistaSlug", t."disciplinaSlug", t."disciplinaRevista"
                order by "paisRevista" asc';
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function tabla_get($tabla){
            if($tabla){
		$data = array();
		$biblatDB = $this->load->database('biblat', TRUE);
                $query = $biblatDB->get($tabla); 
		$this->response($query->result_array(), 200);
            }
            
            $this->response(NULL, 200);
	}
        
        public function revista_num_get($revista='',$anio=''){
            $data = array();
            $this->load->database();
            $query = 'WITH numeros AS (
                        SELECT max(article.revista::text) AS revista,
                        slug(article.revista) AS "revistaSlug",
                        article."anioRevista",
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'a\'::text) IS NULL THEN \'s/v\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'a\'::text) = \'\'::text THEN \'s/v\'::text
                            ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'a\'::text), \'V\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END AS volumen,
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'b\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'b\'::text) = \'\'::text THEN \'\'::text
                        ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'b\'::text), \'N\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END AS numero,
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'d\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'d\'::text) = \'\'::text THEN \'\'::text
                            WHEN upper(article."descripcionBibliografica" ->> \'d\'::text) ~ \'P.*-\'::text THEN \'\'::text
                        ELSE replace(replace(article."descripcionBibliografica" ->> \'d\'::text, \'"\'::text, \'\'::text), \' \'::text, \'\'::text)
                        END AS parte
                        FROM article
                        WHERE article."anioRevista" IS NOT NULL and slug(article.revista) = \''.$revista.'\' and article."anioRevista" = \''.$anio.'\'
                        GROUP BY (slug(article.revista)), article."anioRevista", (
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'a\'::text) IS NULL THEN \'s/v\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'a\'::text) = \'\'::text THEN \'s/v\'::text
                            ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'a\'::text), \'V\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END), (
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'b\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'b\'::text) = \'\'::text THEN \'\'::text
                            ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'b\'::text), \'N\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END), (
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'d\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'d\'::text) = \'\'::text THEN \'\'::text
                            WHEN upper(article."descripcionBibliografica" ->> \'d\'::text) ~ \'P.*-\'::text THEN \'\'::text
                            ELSE replace(replace(article."descripcionBibliografica" ->> \'d\'::text, \'"\'::text, \'\'::text), \' \'::text, \'\'::text)
                        END)
                    ORDER BY (slug(article.revista)), article."anioRevista", (
                          CASE
                              WHEN (article."descripcionBibliografica" ->> \'a\'::text) IS NULL THEN \'s/v\'::text
                              WHEN btrim(article."descripcionBibliografica" ->> \'a\'::text) = \'\'::text THEN \'s/v\'::text
                              ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'a\'::text), \'V\'::text, \'\'::text), \'"\'::text, \'\'::text)
                          END), (NULLIF(regexp_replace(
                          CASE
                              WHEN (article."descripcionBibliografica" ->> \'b\'::text) IS NULL THEN \'\'::text
                              WHEN btrim(article."descripcionBibliografica" ->> \'b\'::text) = \'\'::text THEN \'\'::text
                              ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'b\'::text), \'N\'::text, \'\'::text), \'"\'::text, \'\'::text)
                          END, \'\D\'::text, \'\'::text, \'g\'::text), \'\'::text)::numeric), (
                          CASE
                              WHEN (article."descripcionBibliografica" ->> \'d\'::text) IS NULL THEN \'\'::text
                              WHEN btrim(article."descripcionBibliografica" ->> \'d\'::text) = \'\'::text THEN \'\'::text
                              WHEN upper(article."descripcionBibliografica" ->> \'d\'::text) ~ \'P.*-\'::text THEN \'\'::text
                              ELSE replace(replace(article."descripcionBibliografica" ->> \'d\'::text, \'"\'::text, \'\'::text), \' \'::text, \'\'::text)
                          END)
                            )
                     SELECT 
                        numeros."anioRevista",
                        ARRAY_AGG(
                            CASE WHEN numeros.parte <> \'\' THEN
                                \'V\' || numeros.volumen || \'N\' || numeros.numero || \' \' || numeros.parte
                            ELSE
                                \'V\' || numeros.volumen || \'N\' || numeros.numero || numeros.parte
                            END
                        ) as numero
                       FROM numeros
                       group by numeros."anioRevista"';
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
		public function revista_estatus_get(){
            $data = array();
            $this->load->database('prueba');
            
            $query = 'SELECT max(article.revista::text) AS revista, max(substr(article.sistema,1,3)) as base, max(asignado) as asignado, max("fechaIngreso") as fecha, max("fechaAsignado") as fecha_asignado,
                        slug(article.revista) AS "revistaSlug",
                        article."anioRevista",
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'a\'::text) IS NULL THEN \'s/v\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'a\'::text) = \'\'::text THEN \'s/v\'::text
                            ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'a\'::text), \'V\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END AS volumen,
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'b\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'b\'::text) = \'\'::text THEN \'\'::text
                        ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'b\'::text), \'N\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END AS numero,
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'d\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'d\'::text) = \'\'::text THEN \'\'::text
                            WHEN upper(article."descripcionBibliografica" ->> \'d\'::text) ~ \'P.*-\'::text THEN \'\'::text
                        ELSE replace(replace(article."descripcionBibliografica" ->> \'d\'::text, \'"\'::text, \'\'::text), \' \'::text, \'\'::text)
                        END AS parte, count(1) articulos
                        FROM article
                        WHERE article."anioRevista" IS NOT NULL and sistema ~ \'^(CLA|PER)99.*\' and (estatus is null or (estatus <> \'C\' and estatus <> \'B\'))
						
                        GROUP BY (slug(article.revista)), article."anioRevista", (
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'a\'::text) IS NULL THEN \'s/v\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'a\'::text) = \'\'::text THEN \'s/v\'::text
                            ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'a\'::text), \'V\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END), (
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'b\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'b\'::text) = \'\'::text THEN \'\'::text
                            ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'b\'::text), \'N\'::text, \'\'::text), \'"\'::text, \'\'::text)
                        END), (
                        CASE
                            WHEN (article."descripcionBibliografica" ->> \'d\'::text) IS NULL THEN \'\'::text
                            WHEN btrim(article."descripcionBibliografica" ->> \'d\'::text) = \'\'::text THEN \'\'::text
                            WHEN upper(article."descripcionBibliografica" ->> \'d\'::text) ~ \'P.*-\'::text THEN \'\'::text
                            ELSE replace(replace(article."descripcionBibliografica" ->> \'d\'::text, \'"\'::text, \'\'::text), \' \'::text, \'\'::text)
                        END)
                    ORDER BY (slug(article.revista)), article."anioRevista", (
                          CASE
                              WHEN (article."descripcionBibliografica" ->> \'a\'::text) IS NULL THEN \'s/v\'::text
                              WHEN btrim(article."descripcionBibliografica" ->> \'a\'::text) = \'\'::text THEN \'s/v\'::text
                              ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'a\'::text), \'V\'::text, \'\'::text), \'"\'::text, \'\'::text)
                          END), (NULLIF(regexp_replace(
                          CASE
                              WHEN (article."descripcionBibliografica" ->> \'b\'::text) IS NULL THEN \'\'::text
                              WHEN btrim(article."descripcionBibliografica" ->> \'b\'::text) = \'\'::text THEN \'\'::text
                              ELSE replace(replace(upper(article."descripcionBibliografica" ->> \'b\'::text), \'N\'::text, \'\'::text), \'"\'::text, \'\'::text)
                          END, \'\D\'::text, \'\'::text, \'g\'::text), \'\'::text)::numeric), (
                          CASE
                              WHEN (article."descripcionBibliografica" ->> \'d\'::text) IS NULL THEN \'\'::text
                              WHEN btrim(article."descripcionBibliografica" ->> \'d\'::text) = \'\'::text THEN \'\'::text
                              WHEN upper(article."descripcionBibliografica" ->> \'d\'::text) ~ \'P.*-\'::text THEN \'\'::text
                              ELSE replace(replace(article."descripcionBibliografica" ->> \'d\'::text, \'"\'::text, \'\'::text), \' \'::text, \'\'::text)
                          END)';
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function avance_get(){
            $data = array();
            $this->load->database('prueba');
            
            $query = "
                    with registros as (
                        select 
                        asignado, estatus
                        from article a
                        inner join
                        catalogador c
                        on a.sistema = c.sistema
                        where 
                        (
                            estatus in ('A', 'R')
                            and 
                            c.nombre in ('OJS', 'SciELO')
                        )
                        or
                        (
                            estatus in ('C', 'B')
                            and
                            c.nombre <> 'OJS' and c.nombre <> 'SciELO'
                            and
                            extract(year from c.fecha) = extract(year from CURRENT_DATE)
                        )
                    )
                    select 
                        r.asignado analista, 
                        (select count(1) from registros where asignado = r.asignado) total,
                        (select count(1) from registros where asignado = r.asignado and estatus ='R') revision,
                        (select count(1) from registros where asignado = r.asignado and estatus ='C') completados,
                        (select count(1) from registros where asignado = r.asignado and estatus ='B') borrados
                    from registros r
                    group by r.asignado
                    ";
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function articulos_get(){
            $data = array();
            $this->load->database('prueba');
            $usuario = $this->session->userdata('usu_base');
            $query = '
                        select
                            a.sistema,
                            revista,
                            "anioRevista" 
                            || coalesce("descripcionBibliografica"->>\'a\', \'\') 
                            || coalesce("descripcionBibliografica"->>\'b\', \'\')
                            || coalesce(\' - \' || ("descripcionBibliografica"->>\'c\')::text, \'\') numero,
                            issn,
                            articulo,
                            url->0->>\'u\' url1,
                            url->1->>\'u\' url2,
                            estatus
                        from article a
                        inner join
                        catalogador c
                        on a.sistema = c.sistema
                        where
                        (
                        (
                            estatus in (\'A\', \'R\')
                            and 
                            c.nombre in (\'OJS\', \'SciELO\')
                        )
                        or
                        (
                            estatus in (\'C\', \'B\')
                            and
                            c.nombre <> \'OJS\' and c.nombre <> \'SciELO\'
                            and
                            extract(year from c.fecha) = extract(year from CURRENT_DATE)
                        )
                        )
                        and asignado = \''.$usuario.'\' 
                        order by 1
                    ';
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function autores_get($sistema){
            $data = array();
            $this->load->database('prueba');
            
            $query = "
                        select
                            sistema,
                            id,
                            nombre,
                            orcid,
                            \"institucionId\",
                            email
                        from author
                        where sistema = '".$sistema."'
                        order by 1
                    ";
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function instituciones_get($sistema){
            $data = array();
            $this->load->database('prueba');
            
            $query = "
                        select
                            sistema,
                            id,
                            institucion,
                            dependencia,
                            ciudad,
                            pais,
                            0 as corporativo
                        from institution
                        where sistema = '".$sistema."'
                        union
                        select
                            sistema,
                            id,
                            institucion,
                            dependencia,
                            '' as ciudad,
                            pais,
                            1 as corporativo
                        from author_coorp
                        where sistema = '".$sistema."'
                        order by 1
                    ";
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function documento_get($sistema){
            $data = array();
            $this->load->database('prueba');
        
            $query = '
                        select 
                            articulo,
                            doi,
                            idioma,
                            documento->>\'a\' tipo_documento,
                            case when "articuloIdiomas"->>0 is not null then
                                    case when "articuloIdiomas"->0->>\'y\' = \'spa\' then
                                            \'Español\'
                                    when "articuloIdiomas"->0->>\'y\' = \'eng\' then
                                            \'Inglés\'
                                    when "articuloIdiomas"->0->>\'y\' = \'por\' then
                                            \'Portugués\'
                                    else
                                            \'Otro\'
                                    end
                            else
                                    null
                            end idioma2,
                            case when "articuloIdiomas"->>0 is not null then
                                    "articuloIdiomas"->0->>\'a\'
                            else
                                    null
                            end titulo2,
                            case when "articuloIdiomas"->>1 is not null then
                                    case when "articuloIdiomas"->1->>\'y\' = \'spa\' then
                                            \'Español\'
                                    when "articuloIdiomas"->1->>\'y\' = \'eng\' then
                                            \'Inglés\'
                                    when "articuloIdiomas"->1->>\'y\' = \'por\' then
                                            \'Portugués\'
                                    else
                                            \'Otro\'
                                    end
                            else
                                    null
                            end idioma3,
                            case when "articuloIdiomas"->>1 is not null then
                                    "articuloIdiomas"->1->>\'a\'
                            else
                                    null
                            end titulo3,
                            case when resumen->>\'a\' is not null then
                                    resumen->>\'a\'
                            else
                                    null
                            end "Resumen español",
                            case when resumen->>\'i\' is not null then
                                    resumen->>\'i\'
                            else
                                    null
                            end "Resumen inglés",
                            case when resumen->>\'p\' is not null then
                                    resumen->>\'p\'
                            else
                                    null
                            end "Resumen portugués",

                            case when disciplinas->>0 is not null then
                                    disciplinas->>0
                            else
                                    null
                            end disciplina1,
                            case when disciplinas->>1 is not null then
                                    disciplinas->>1
                            else
                                    null
                            end disciplina2,
                            case when disciplinas->>2 is not null then
                                    disciplinas->>2
                            else
                                    null
                            end disciplina3,

                            case when "subdisciplinas"->>0 is not null then
                                    "subdisciplinas"->>0
                            else
                                    null
                            end subdisciplina1,
                            case when "subdisciplinas"->>1 is not null then
                                    "subdisciplinas"->>1
                            else
                                    null
                            end subdisciplina2,
                            case when "subdisciplinas"->>2 is not null then
                                    "subdisciplinas"->>2
                            else
                                    null
                            end subdisciplina3,
                            "palabraClave",
                            "keyword",
                            case when url->0 is not null then
                                    case when url->0->>\'y\' like \'%PDF%\' then
                                            \'pdf\'
                                    else
                                            \'html\'
                                    end
                            else
                                    null
                            end tipourl1,

                            case when url->0 is not null then
                                    url->0->>\'u\'
                            else
                                    null
                            end url1,

                            case when url->1 is not null then
                                    case when url->1->>\'y\' like \'%PDF%\' then
                                            \'pdf\'
                                    else
                                            \'html\'
                                    end
                            else
                                    null
                            end tipourl2,

                            case when url->1 is not null then
                                    url->1->>\'u\'
                            else
                                    null
                            end url2

                            from article where sistema = \''.$sistema.'\'
                        ';
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function ciudad_by_pais_get($pais){
            $data = array();
            $this->load->database('prueba');
            
            /*
            $query = "
                    select 
                        slug(ciudad) slug,
                        max(ciudad) ciudad
                    from institution 
                    where 
                        \"paisInstitucionSlug\" = slug('".$pais."') and ciudad is not null group by 1 order by 1
            ";*/
            
            $query = "
                    select 
                        distinct ciudad
                    from institution 
                    where 
                        \"paisInstitucionSlug\" = slug('".urldecode($pais)."') and ciudad is not null order by 1
            ";
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function ciudad_by_institucion_get($institucion){
            $data = array();
            $this->load->database('prueba');
            $query = "
                    select 
                        distinct ciudad 
                    from institution 
                    where 
                        slug = slug('".urldecode($institucion)."') and ciudad is not null order by 1
            ";
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function institucion_by_pais_get($pais, $corporativo){
            $data = array();
            $this->load->database('prueba');

            if($corporativo == 0){
                $query = "
                        select 
                            distinct institucion
                        from institution 
                        where 
                            \"paisInstitucionSlug\" = slug('".urldecode($pais)."') and institucion is not null order by 1
                ";
            }else{
                $query = "
                        select 
                            distinct institucion
                        from author_coorp 
                        where 
                            \"paisSlug\" = slug('".urldecode($pais)."') and institucion is not null order by 1
                ";
            }
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function dependencia_by_institucion_get($institucion, $corporativo){
            $data = array();
            $this->load->database('prueba');
            
            if($corporativo == 0){
                $query = "
                        select 
                            distinct dependencia
                        from institution 
                        where 
                            slug = slug('".urldecode($institucion)."') and dependencia is not null order by 1
                ";
            }else{
                $query = "
                        select 
                            distinct dependencia
                        from author_coorp 
                        where 
                            slug = slug('".urldecode($institucion)."') and dependencia is not null order by 1
                ";
            }
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function autor_by_nombre_get($nombre, $sistema){
            $data = array();
            $this->load->database('prueba');
            
            $query = "
                        select 
                        distinct
                        a.nombre,
                        coalesce(a.orcid, 'Sin ORCID') orcid,
                        coalesce(i.institucion, '') || coalesce( ' - ' || i.dependencia, '') || ': ' || coalesce(i.pais || '; ', '') || coalesce(i.ciudad, '') institucion
                        from author a 
                        inner join 
                        institution i
                        on a.sistema = i.sistema and a.\"institucionId\" = i.id
                        where 
                        a.sistema <> '".$sistema."' 
                        and a.slug like replace(slug('".urldecode($nombre)."'),'-','%')
            ";
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }									  
		public function allrevistas_get(){
            $data = array();
            $this->load->database('prueba');
            
            //select max(revista) revista from article where revista is not null group by slug(revista) order by 1
            
            $query = "          
                        select max(revista) revista from \"mvNumerosRevista\" where revista is not null group by slug(revista) order by 1
            ";
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);
        }
        
        public function revistas_articulo_by_nombre_get($nombre=null){
            $data = array();
            $this->load->database('prueba');
            
            if(!isset($nombre)){
                $nombre = $this->session->userdata('usu_base');
            }
            
            $query = "
                with asignadas as(					
                    select * from article where asignado is not null
                )
                select 
                    json_agg(distinct revista) revistas 
                from 
                    asignadas 
                where
                    asignado = '".$nombre."'
            ";
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);  
        }
        
        public function revistas_by_nombre_get($nombre=null){
            $data = array();
            $this->load->database('prueba');
            
            if(!isset($nombre)){
                $nombre = $this->session->userdata('usu_base');
            }
            
            //array_to_json(revistas) revistas
            $query = "
                select 
                    revistas
                from 
                    usuario_revista
                where 
                    usuario = '".$nombre."'
            ";
            
            $query = $this->db->query($query);
            $this->response($query->result_array(), 200);  
        }
}
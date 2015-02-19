google.load("visualization", "1", {packages:["corechart", "table"], 'language': 'en'});
var chart = {normal: null, bradford:null, group1:null, group2:null, pratt:null, data:null};
chart.data = {normal: null, bradford:null, group1:null, group2:null, pratt:null, prattJ:null};
var tables = {normal: null, bradford:null, group1:null, group2:null, group3:null, pratt:null};
var brfLim = null;
var popState = {indicador:false, disciplina:false, revista:false, paisRevista:false, paisAutor:false, periodo:false};
var rangoPeriodo="0-0";
var dataPeriodo="0-0";
var paisRevistaURL="";
var asyncAjax=false;
var soloDisciplina = ['indice-concentracion', 'modelo-bradford-revista', 'modelo-bradford-institucion', 'productividad-exogena'];
var soloPaisAutor = ['indice-coautoria', 'tasa-documentos-coautorados', 'indice-colaboracion'];
$(document).ready(function(){
	$('.carousel').carousel({
	  interval: false
	})
	$("#indicador").select2({
		allowClear: true
	});

	$("#indicador").on("change", function(e){
		value = $(this).val();
		$("#paisRevistaDiv, #periodos, #tabs, #chartContainer, #bradfodContainer, #prattContainer").hide("slow");
		$("#disciplina").select2("val", "");
		$("#sliderPeriodo").prop('disabled', true);
		if (value == "") {
			$("#disciplina, #revista, #paisRevista, #paisAutor").select2("enable", false);
			$("#revista, #paisRevista, #paisAutor").empty().append('<option></option>');
			$("#revista, #paisRevista, #paisAutor").select2("destroy");
		}else if($.inArray(value, soloDisciplina) > -1){
			$("#revista, #paisRevista, #paisAutor").select2("enable", false);
			$("#revista, #paisRevista, #paisAutor").empty().append('<option></option>');
			$("#revista, #paisRevista, #paisAutor").select2("destroy");
			$("#disciplina").select2("enable", true);
			updateInfo(value);
		}else{
			$("#revista, #paisRevista, #paisAutor").select2({allowClear: true, closeOnSelect: true});
			$("#disciplina").select2("enable", true);
			updateInfo(value);
		}

		if(typeof history.pushState === "function" && !popState.indicador){
			history.pushState($("#generarIndicador").serializeJSON(), null, '<?=site_url('indicadores')."/"?>' + value);
		}
		popState.indicador=false;
		console.log(e);
	});

	$(window).bind('popstate',  function(event) {
		console.log('pop:');
		updateData(event.originalEvent.state)
		
	});

	$("#disciplina").select2({
		allowClear: true
	});

	$("#disciplina").on("change", function(e){
		value = $(this).val();
		indicadorValue = $("#indicador").val();
		$("#sliderPeriodo").prop('disabled', true);
		if (value == "") {
			$("#paisRevistaDiv, #periodos, #tabs, #chartContainer, #bradfodContainer, #prattContainer").hide("slow");
			$("#revista, #paisRevista, #paisAutor").empty().append('<option></option>');
			$("#revista, #paisRevista, #paisAutor").select2("destroy");
			$("#revista, #paisRevista, #paisAutor").select2({allowClear: true, closeOnSelect: true});
			$("#revista, #paisRevista, #paisAutor").select2("enable", false);
		} else if ($.inArray(indicadorValue, soloDisciplina) > -1) {
			$("#generarIndicador").submit();
		} else {
			if(!loading.status && !popState.disciplina){
				loading.start();
			}
			$("#orPaisRevistaColumn").show();
			$("#paisRevistaDiv").removeClass("hidden").slideDown("slow");
			$("#periodos, #tabs, #chartContainer, #bradfodContainer, #prattContainer").hide("slow");
			$.ajax({
				url: '<?=site_url("indicadores/getRevistasPaises");?>',
				type: 'POST',
				dataType: 'json',
				data: $("#generarIndicador").serialize(),
				async: asyncAjax,
				success: function(data) {
					console.log(data);
					controlsTotal = 0;
					$("#revista, #paisRevista, #paisAutor").empty().append('<option></option>');
					$("#revista").select2("destroy");
					$("#paisRevista").select2("destroy");
					$("#paisAutor").select2("destroy");
					$("#revista, #paisRevista, #paisAutor").hide();
					if(typeof data.revistas !== "undefined"){
						$("#revista").show().select2({allowClear: true, closeOnSelect: true}).select2("enable", false);
						$.each(data.revistas, function(key, val) {
							$("#revista").append('<option value="' + val.val +'">' + val.text + '</option>');
						});
						$("#revista").select2("enable", true);
						controlsTotal++;
					}
					if(typeof data.paisesRevistas !== "undefined" && indicadorValue != "indice-densidad-documentos"){
						$("#paisRevista").show().select2({allowClear: true, closeOnSelect: true}).select2("enable", false);
						$.each(data.paisesRevistas, function(key, val) {
							$("#paisRevista").append('<option value="' + val.val +'">' + val.text + '</option>');
						});
						$("#paisRevista").select2("enable", true);
						controlsTotal++;
					}
					if(typeof data.paisesAutores !== "undefined" && $.inArray(indicadorValue, soloPaisAutor) > -1){
						$("#paisAutor").show().select2({allowClear: true, closeOnSelect: true}).select2("enable", false);
						$.each(data.paisesAutores, function(key, val) {
							$("#paisAutor").append('<option value="' + val.val +'">' + val.text + '</option>');
						});
						$("#paisAutor").select2("enable", true);
						controlsTotal++;
					}
					if(controlsTotal < 2){
						$("#orPaisRevistaColumn").hide();
					}
					if(controlsTotal == 0){
						$.pnotify({
							title: '<?php _e('No se encontraron datos para la disciplina seleccionada');?>',
							icon: true,
							type: 'error',
							addclass: 'errorNotification',
							sticker: false
						});
					}
				},
				complete: function(){
					loading.end();
				}
			});
		}
		if(typeof history.pushState === "function" && !popState.disciplina){
			disciplina="";
			if(value != "" && value != null){
				disciplina='/disciplina/' + value;
			}
			history.pushState($("#generarIndicador").serializeJSON(), null, '<?=site_url('indicadores')."/"?>' + indicadorValue + disciplina);
		}
		popState.disciplina=false;
		console.log(e);
	});

	$("#revista").select2({
		allowClear: true,
		closeOnSelect: true
	});

	$("#revista").on("change", function(e){
		value = $(this).val();
		indicadorValue = $("#indicador").val();
		disciplinaValue = $("#disciplina").val();
		$("#sliderPeriodo").prop("disabled", true);
		if (value != "" && value != null) {
			$("#paisRevista").select2("enable", false);
			$("#paisAutor").select2("enable", false);
			setPeridos();
		}else{
			$("#periodos, #tabs, #chartContainer").hide("slow");
			$("#paisRevista").select2("enable", true);
			$("#paisAutor").select2("enable", true);
		}

		if(typeof history.pushState === "function" && !popState.revista){
			paisRevistaURL="";
			if(value != "" && value != null){
				paisRevistaURL='/revista/' + value.join('/');
			}
			history.pushState($("#generarIndicador").serializeJSON(), null, '<?=site_url('indicadores')."/"?>' + indicadorValue + '/disciplina/' + disciplinaValue + paisRevistaURL);
		}
		popState.revista=false;
		console.log(e);
	});

	$("#paisRevista").select2({
		allowClear: true,
		closeOnSelect: true
	});

	$("#paisRevista").on("change", function(e){
		value = $(this).val();
		indicadorValue = $("#indicador").val();
		disciplinaValue = $("#disciplina").val();
		$("#sliderPeriodo").prop("disabled", true);
		if (value != "" && value != null) {
			$("#revista").select2("enable", false);
			$("#paisAutor").select2("enable", false);
			setPeridos();
		}else{
			$("#periodos, #tabs, #chartContainer").hide("slow");
			$("#revista").select2("enable", true);
			$("#paisAutor").select2("enable", true);
		}
		if(typeof history.pushState === "function" && !popState.paisRevista){
			paisRevistaURL="";
			if(value != "" && value != null){
				paisRevistaURL='/pais-revista/' + value.join('/');
			}
			console.log('pushState');
			console.log(paisRevistaURL);
			history.pushState($("#generarIndicador").serializeJSON(), null, '<?=site_url('indicadores')."/"?>' + indicadorValue + '/disciplina/' + disciplinaValue + paisRevistaURL);
		}
		popState.paisRevista=false;
		console.log(e);
	});
	
	$("#paisAutor").select2({
		allowClear: true,
		closeOnSelect: true
	});

	$("#paisAutor").on("change", function(e){
		value = $(this).val();
		indicadorValue = $("#indicador").val();
		disciplinaValue = $("#disciplina").val();
		$("#sliderPeriodo").prop("disabled", true);
		if (value != "" && value != null) {
			$("#revista").select2("enable", false);
			$("#paisRevista").select2("enable", false);
			setPeridos();
		}else{
			$("#periodos, #tabs, #chartContainer").hide("slow");
			$("#revista").select2("enable", true);
			$("#paisRevista").select2("enable", true);
		}
		if(typeof history.pushState === "function" && !popState.paisAutor){
			paisRevistaURL="";
			if(value != "" && value != null){
				paisRevistaURL='/pais-autor/' + value.join('/');
			}
			history.pushState($("#generarIndicador").serializeJSON(), null, '<?=site_url('indicadores')."/"?>' + indicadorValue + '/disciplina/' + disciplinaValue + paisRevistaURL);
		}
		popState.paisAutor=false;
		console.log(e);
	});

	$("#sliderPeriodo").jslider();

	$("#tabs").tabs({ 
		show: { effect: "fade", duration: 800 },
		activate: function(){
			if($("#tabs").tabs("option", "active") != 1){
				if($("#indicador").val() == "modelo-bradford-revista" || $("#indicador").val() == "modelo-bradford-institucion"){
					$("#gridContainer").accordion("option", "active", false);
				}
			}
			$('html, body').animate({
				scrollTop: $("#tabs").offset().top
			}, 700);
		}
	});

	$("#gridContainer").accordion({
		heightStyle: "content",
		collapsible: true,
		active: false,
		activate: function( event, ui ) {
			$('html, body').animate({
				scrollTop: $("#tabs").offset().top
			}, 700);
		}
	});
	
	$("#generarIndicador").on("submit", function(e){
		console.log(e);
		if(!loading.status){
			loading.start();
		}
		indicadorValue = $("#indicador").val();
		urlRequest = '<?=site_url("indicadores/getChartData");?>';
		switch(indicadorValue){
			case "modelo-bradford-revista":
			case "modelo-bradford-institucion":
				urlRequest = '<?=site_url("indicadores/getChartDataBradford");?>';
				break;
			case "indice-concentracion":
			case "productividad-exogena":
				urlRequest = '<?=site_url("indicadores/getChartDataPrattExogena");?>';
				break;
		}
		$.ajax({
		  url: urlRequest,
		  type: 'POST',
		  dataType: 'json',
		  data: $(this).serialize(),
		  success: function(data) {
		  	console.log(data);
		  	$("#tabs").tabs("option", "active", 0);
			switch(indicadorValue){
				case "modelo-bradford-revista":
				case "modelo-bradford-institucion":
					//$("#gridContainer").accordion("destroy");
					$("#tabs, #bradfodContainer").slideDown("slow");
					brfLim = data.grupos;
					chart.data.bradford = new google.visualization.DataTable(data.chart.bradford);
					if(chart.bradford == null){
						chart.bradford = new google.visualization.ComboChart(document.getElementById('chartBradford'));
					}
					chart.bradford.draw(chart.data.bradford, data.options.bradford);
					google.visualization.events.addListener(chart.bradford, 'select', chooseZone);

					$("#bradfordTitle").html(data.title.bradford);

					chart.data.group1 = new google.visualization.DataTable(data.chart.group1);
					if(chart.group1 == null){
						chart.group1 = new google.visualization.ColumnChart(document.getElementById('chartGroup1'));
					}
					chart.group1.draw(chart.data.group1, data.options.groups);
					google.visualization.events.addListener(chart.group1, 'select', function(){bradfordArticles('group1')});
					$("#group1Title").html(data.title.group1);

					chart.data.group2 = new google.visualization.DataTable(data.chart.group2);
					if(chart.group2 == null){
						chart.group2 = new google.visualization.ColumnChart(document.getElementById('chartGroup2'));
					}
					chart.group2.draw(chart.data.group2, data.options.groups);
					google.visualization.events.addListener(chart.group2, 'select', function(){bradfordArticles('group2')});
					$("#group2Title").html(data.title.group2);
					var tableData = new google.visualization.DataTable(data.table.bradford);
					$("#gridContainer").empty();
					$("#gridContainer").append(data.table.title.bradford);
					$("#gridContainer").append('<div id="table0"></div>');
					tables.bradford = new google.visualization.Table(document.getElementById('table0'));
					tables.bradford.draw(tableData, data.tableOptions);
					google.visualization.events.addListener(tables.bradford , 'sort', changeTableClass);


					var tableData = new google.visualization.DataTable(data.table.group1);
					$("#gridContainer").append(data.table.title.group1);
					$("#gridContainer").append('<div class="groupTable" id="table1"></div>');
					tables.group1 = new google.visualization.Table(document.getElementById('table1'));
					tables.group1.draw(tableData, data.tblGrpOpt);
					google.visualization.events.addListener(tables.group1 , 'sort', changeTableClass);

					var tableData = new google.visualization.DataTable(data.table.group2);
					$("#gridContainer").append(data.table.title.group2);
					$("#gridContainer").append('<div class="groupTable" id="table2"></div>');
					tables.group2 = new google.visualization.Table(document.getElementById('table2'));
					tables.group2.draw(tableData, data.tblGrpOpt);
					google.visualization.events.addListener(tables.group2 , 'sort', changeTableClass);

					var tableData = new google.visualization.DataTable(data.table.group3);
					$("#gridContainer").append(data.table.title.group3);
					$("#gridContainer").append('<div class="groupTable" id="table3"></div>');
					tables.group3 = new google.visualization.Table(document.getElementById('table3'));
					tables.group3.draw(tableData, data.tblGrpOpt);
					changeTableClass();
					google.visualization.events.addListener(tables.group3 , 'sort', changeTableClass);

					$("#gridContainer").accordion( "refresh" );
					break;
				case "indice-concentracion":
				case "productividad-exogena":
					$("#tabs, #prattContainer").slideDown("slow");
					$("#prattSlide").empty();
					chart.pratt = new Array();
					chart.data.pratt = new Array();
					chart.data.prattJ = data.journal; 
					$.each(data.chart, function(key, grupo) {
						active='';
						if(key == 0){
							active='active';
						}
						$("#carousel-pratt .carousel-indicators").append('<li data-target="#carousel-pratt" data-slide-to="' + key + '" class="' + active + '"></li>');
						$("#carousel-pratt .carousel-inner").append('<div class="item ' + active + '">' + data.chartTitle + ' <div id="chartPratt' + key +'" class="chart_data"></div></div>');
						chart.data.pratt[key] = new google.visualization.DataTable(grupo);
						chart.pratt[key] = new google.visualization.ColumnChart(document.getElementById('chartPratt' + key));
						chart.pratt[key].draw(chart.data.pratt[key], data.options);
						google.visualization.events.addListener(chart.pratt[key], 'select', function(){getFrecuencias(key)});
					});

					var tableData = new google.visualization.DataTable(data.table);
					$("#gridContainer").empty();
					$("#gridContainer").append(data.tableTitle);
					$("#gridContainer").append('<div id="table0"></div>');
					tables.pratt = new google.visualization.Table(document.getElementById('table0'));
					tables.pratt.draw(tableData, data.tableOptions);
					changeTableClass();
					google.visualization.events.addListener(tables.pratt , 'sort', changeTableClass);
					break;
				default:
					$("#tabs, #chartContainer").show("slow");
					chart.data.normal = new google.visualization.DataTable(data.data);
					if(chart.normal == null){
						chart.normal = new google.visualization.LineChart(document.getElementById('chart'));
					}
					chart.normal.draw(chart.data.normal, data.options);
					google.visualization.events.addListener(chart.normal, 'select', choosePoint);
					$("#chartTitle").html(data.chartTitle);

					var tableData = new google.visualization.DataTable(data.dataTable);
					$("#gridContainer").empty();
					$("#gridContainer").append(data.tableTitle);
					$("#gridContainer").append('<div id="table0"></div>');
					tables.normal = new google.visualization.Table(document.getElementById('table0'));
					tables.normal.draw(tableData, data.tableOptions);
					changeTableClass();
					google.visualization.events.addListener(tables.normal , 'sort', changeTableClass);
					break;
			}
		  },
		  complete: function(){
		  	loading.end();
		  }
		});
		console.log(chart);	
		return false;
	});
<?php if (preg_match('%indicadores/(...+?)%', uri_string())):?>
	urlData = {
<?php 	if (preg_match('%indicadores/(.+?)(/.*|$)%', uri_string())):?>
		indicador:"<?=preg_replace('%.+?/indicadores/(.+?)(/.*|$)%', '\1', uri_string());?>",
<?php 	endif;?>
<?php 	if (preg_match('%.*?/disciplina/(.+?)(/.*|$)%', uri_string())):?>
		disciplina:"<?=preg_replace('%.*?/disciplina/(.+?)(/.*|$)%', '\1', uri_string());?>",
<?php 	endif;?>
<?php 	if (preg_match('%.*?/revista/(.+?)($|/[0-9]{4}-[0-9]{4})%', uri_string())):?>
		revista:"<?=preg_replace('%.*?/revista/(.+?)($|/[0-9]{4}-[0-9]{4})%', '\1', uri_string());?>".split('/'),
<?php 	endif;?>
<?php 	if (preg_match('%.*?/pais-revista/(.+?)($|/[0-9]{4}-[0-9]{4})%', uri_string())):?>
		paisRevista:"<?=preg_replace('%.*?/pais-revista/(.+?)($|/[0-9]{4}-[0-9]{4})%', '\1', uri_string());?>".split('/'),
<?php 	endif;?>
<?php 	if (preg_match('%.*?/pais-autor/(.+?)($|/[0-9]{4}-[0-9]{4})%', uri_string())):?>
		paisAutor:"<?=preg_replace('%.*?/pais-autor/(.+?)($|/[0-9]{4}-[0-9]{4})%', '\1', uri_string());?>".split('/'),
<?php 	endif;?>
<?php 	if (preg_match('%.*?/([0-9]{4})-([0-9]{4})%', uri_string())):?>
		periodo:"<?=preg_replace('%.*?/([0-9]{4})-([0-9]{4})%', '\1;\2', uri_string());?>"
<?php 	endif;?>
	}
<?php 	if (preg_match('%.*?/revista/(.+?)($|/[0-9]{4}-[0-9]{4})%', uri_string())):?>
	paisRevistaURL="/revista/<?=preg_replace('%.*?/revista/(.+?)($|/[0-9]{4}-[0-9]{4})%', '\1', uri_string());?>";
<?php 	endif;?>
<?php 	if (preg_match('%.*?/pais/(.+?)($|/[0-9]{4}-[0-9]{4})%', uri_string())):?>
	paisRevistaURL="/pais/<?=preg_replace('%.*?/pais/(.+?)($|/[0-9]{4}-[0-9]{4})%', '\1', uri_string());?>";
<?php 	endif;?>
	if(typeof urlData.indicador !== "undefined"){
		updateData(urlData);
	}
	delete urlData;
<?php endif;?>
	if(typeof history.replaceState === "function"){
		history.replaceState($("#generarIndicador").serializeJSON(), null);
	}
});

setPeridos = function(){
	if(!loading.status){
		loading.start();
	}
	$("#periodos").removeClass("hidden").slideDown("slow");
	$.ajax({
		url: '<?=site_url("indicadores/getPeriodos");?>',
		type: 'POST',
		dataType: 'json',
		data: $("#generarIndicador").serialize(),
		async: asyncAjax,
		success: function(data) {
			console.log(data);
			console.log($.parseJSON(data.scale));
			console.log($.parseJSON(data.heterogeneity));
			if(data.result){
				$("#sliderPeriodo").jslider().destroy();
				$("#sliderPeriodo").prop('disabled', false);
				$("#generate").prop('disabled', false);
				rangoPeriodo=data.anioBase + ";" + data.anioFinal;
				console.log(data)
				$("#sliderPeriodo").val(rangoPeriodo);
				$("#sliderPeriodo").data('pre', $("#sliderPeriodo").val());
				$("#sliderPeriodo").jslider({
					from: data.anioBase, 
					to: data.anioFinal, 
					heterogeneity: $.parseJSON(data.heterogeneity), 
					scale: $.parseJSON(data.scale),
					format: { format: '####', locale: 'us' }, 
					limits: false, 
					step: 1, 
					callback: function(value){
						console.log(value);
						if($("#sliderPeriodo").data('pre') != value){
							$("#sliderPeriodo").data('pre', value);
							$("#sliderPeriodo").val(value);
							rango=value.replace(';', '-');
							if(typeof history.pushState === "function"){
								history.pushState($("#generarIndicador").serializeJSON(), null, '<?=site_url('indicadores')."/"?>' + indicadorValue + '/disciplina/' + disciplinaValue + paisRevistaURL + '/' + rango);
							}
							$("#revista, #paisRevista").select2("close");
							$("#generarIndicador").submit();
						}
					}
				});
				$("#sliderPeriodo").jslider("value", data.anioBase, data.anioFinal);
				if(!popState.periodo){
					$("#generarIndicador").submit();
				}
				popState.periodo=false;
			}else{
				$("#sliderPeriodo").prop('disabled', true);
				$("#generate").prop('disabled', true);
				console.log(data.error);
			}
		},
		complete: function(){
			loading.end();
		}
	});
};

updateInfo = function(indicador){
	$("#info").children(".infoBox").hide();
	$("#info-" + indicador).show();
}

updateData = function(data){
	console.log(data);
	asyncAjax=false;
	actualForm = $("#generarIndicador").serializeJSON();
	if(typeof data.periodo !== "undefined"){
		popState.periodo = true;
	}
	if(typeof data.indicador !== "undefined"){
		updateInfo(data.indicador);
	}
	if(typeof data.indicador !== "undefined"){
		popState.indicador=true;
		$("#indicador").val(data.indicador).trigger("change");
		actualForm = $("#generarIndicador").serializeJSON();
	}
	if(typeof data.disciplina !== "undefined"){
		popState.disciplina=true;
		$("#disciplina").val(data.disciplina).trigger("change");
		actualForm = $("#generarIndicador").serializeJSON();
	}

	if(!actualForm.revista){
		actualForm.revista = ["revista"];
	}
	if(data.revista === "" || typeof data.revista === "undefined" && typeof data.pais === "undefined"){
		$("#periodos, #tabs, #chartContainer, #bradfodContainer, #prattContainer").hide("slow");
		$("#revista").select2("val", null);
		$('#revista option').first().prop('selected', false);
		$("#revista").select2("destroy");
		$("#revista").select2({allowClear: true, closeOnSelect: true});
		$("#paisRevista").select2("enable", true);
	}
	
	if(data.revista !== "" && typeof data.revista !== "undefined" && data.revista.join('/') != actualForm.revista.join('/')){
		popState.revista=true;
		$("#revista").val(data.revista).trigger("change");
		actualForm = $("#generarIndicador").serializeJSON();
	}

	if(!actualForm.paisRevista){
		actualForm.paisRevista = ["pais"];
	}

	if(data.paisRevista !== "" &&  typeof data.paisRevista !== "undefined" && data.paisRevista.join('/') != actualForm.paisRevista.join('/')){
		popState.paisRevista=true;
		$("#paisRevista").val(data.paisRevista).trigger("change");
		actualForm = $("#generarIndicador").serializeJSON();
	}

	if(!actualForm.paisAutor){
		actualForm.paisAutor = ["pais"];
	}

	if(data.paisAutor !== "" &&  typeof data.paisAutor !== "undefined" && data.paisAutor.join('/') != actualForm.paisAutor.join('/')){
		popState.paisAutor=true;
		$("#paisAutor").val(data.paisAutor).trigger("change");
		actualForm = $("#generarIndicador").serializeJSON();
	}
	if(typeof data.periodo !== "undefined"){
		$("#sliderPeriodo").prop("disabled", false);
		$("#sliderPeriodo").jslider("value", data.periodo.substring(0, 4), data.periodo.substring(5));
		$("#sliderPeriodo").val(data.periodo);
		$("#generarIndicador").submit();
	}
	asyncAjax=true;
};

chooseZone = function () {
	var selection = chart.bradford.getSelection();
	console.log(selection);
	if (selection[0] != null && selection[0].row != null){
		var value = chart.data.bradford.getFormattedValue(selection[0].row, 0);
		if (value <= brfLim[1].lim.x){
			$("#carousel-bradford").carousel(1);
		}
		else if (value > brfLim[1].lim.x && value <= brfLim[2].lim.x) {
			$("#carousel-bradford").carousel(2);
		}else{
			$("#tabs").tabs("option", "active", 1);
			$("#gridContainer").accordion("option", "active", 3);
		}
	}else if  (selection[0] != null && selection[0].column != null){
		if(selection[0].column == 2){
			$("#carousel-bradford").carousel(1);
		}else if (selection[0].column == 3){
			$("#carousel-bradford").carousel(2);
		}else{
			$("#tabs").tabs("option", "active", 1);
			$("#gridContainer").accordion("option", "active", 3);
		}
	}
}

choosePoint = function () {
	var selection = chart.normal.getSelection()[0];
	indicadorValue = $("#indicador").val();
	if (selection && indicadorValue == "modelo-elitismo"){
		var revistaPais = chart.data.normal.getColumnId(selection.column);
		var anio = chart.data.normal.getFormattedValue(selection.row, 0);
		console.log(anio);
		$.ajax({
			url: '<?=site_url("indicadores/getAutoresPrice");?>/'+ revistaPais + '/' + anio,
			type: 'POST',
			dataType: 'json',
			data: $("#generarIndicador").serialize(),
			success: function(data){
				console.log(data);
				var tableData = new google.visualization.DataTable(data.table);
				var table = new google.visualization.Table(document.getElementById('floatTable'));
				table.draw(tableData, data.tableOptions);
				changeTableClass();
				google.visualization.events.addListener(table , 'sort', changeTableClass);
				$.colorbox({inline: true, href: $('#floatTable'), height:"90%",});
			}
		});
	}
}

bradfordArticles = function (group) {
	var selection = chart[group].getSelection()[0];
	indicadorValue = $("#indicador").val();
	if (selection && indicadorValue == "modelo-bradford-revista"){
		var revista = chart.data[group].getColumnId(selection.column);
		var disciplina=$('#disciplina').val();
		location.href = "<?=site_url("indicadores/modelo-bradford-revista/disciplina");?>/"+ disciplina + "/revista/"+ revista + "/documentos"
	}
}

getFrecuencias = function (key) {
	var selection = chart.pratt[key].getSelection();
	if (selection[0] != null && selection[0].column != null){
		disciplina=$('#disciplina').val();
		revista=chart.data.prattJ[key][(selection[0].column+1)/2 -1];
		$.ajax({
			url: '<?=site_url("indicadores/getFrecuencias");?>/'+ revista,
			type: 'POST',
			dataType: 'json',
			data: $("#generarIndicador").serialize(),
			success: function(data){
				console.log(data);
				var tableData = new google.visualization.DataTable(data.table);
				var table = new google.visualization.Table(document.getElementById('floatTable'));
				table.draw(tableData, data.tableOptions);
				changeTableClass();
				google.visualization.events.addListener(table , 'sort', changeTableClass);
				$.colorbox({inline: true, href: $('#floatTable'), height:"90%",});
			}
		});
		
		console.log(revista);
	}
}
changeTableClass = function (argument) {
	$('.google-visualization-table-table')
	.removeClass('google-visualization-table-table')
	.addClass('table table-bordered table-condensed table-striped')
	.parent().addClass('table-responsive');
}

$('.download-chart').on('click', function(e){
	e.preventDefault();
	var indicador = $("#indicador").val();
	var imgData = '';
	var fName = '';
	switch(indicador){
		case "modelo-bradford-revista":
		case "modelo-bradford-institucion":
			var current_chart = $('#carousel-bradford').find('.item.active .chart_data').attr('id').replace('chart', '').toLowerCase();
			imgData = chart[current_chart].getImageURI();	
			fName = indicador+'-'+current_chart+'.png';
			break;
		case "indice-concentracion":
		case "productividad-exogena":
			var current_chart = $('#carousel-pratt').find('.item.active .chart_data').attr('id').replace('chartPratt', '');
			imgData = chart.pratt[current_chart].getImageURI();	
			fName = indicador+'-group'+current_chart+'.png';
			break;
		default:
			imgData = chart.normal.getImageURI();
			fName = indicador+'.png';
			break;
	}
	tmp=$('<a></a>').attr('href', imgData).attr('download', fName);
	$('body').append(tmp);
	tmp.get(0).click();
	tmp.remove()
});

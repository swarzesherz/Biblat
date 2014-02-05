<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if(isset($description)):?>
	<meta name="description" content="<?php echo $description;?>" /> 
<?php endif;?>
	<title><?php echo $title;?></title>
	<link rel="icon" href="<?php echo base_url();?>img/favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" href="<?php echo base_url();?>css/estiloBiblat.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url();?>css/font-awesome.min.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url();?>css/default.css" type="text/css" />
	<link rel="stylesheet" href="<?php echo base_url();?>css/default_ajax.css" type="text/css" />
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>js/jquery.autosize.min.js"></script>
	<script type="text/javascript">
		var addthis_config = addthis_config||{};
		addthis_config.data_track_addressbar = false;
		addthis_config.data_track_clickback = false;
		addthis_config.ui_language = "<?php echo lang_iso_code();?>";
	</script>
	<script type="text/javascript" src="http://s7.addthis.com/js/300/addthis_widget.js#pubid=herz"></script>
	<script type="text/javascript" language="javascript">
		jQuery(document).ready(function()
		{
			jQuery(document).bind("contextmenu",function(e){
				return false;
			});
			jQuery("#options").click(function(e) {
				jQuery(".optionsMenu").toggle();
				return false;
			});
			jQuery(".optionsMenu li").click(function(e) {
				var button = jQuery(this).attr('rel');
				jQuery('#options').attr('class', 'icon-'+button);
				jQuery('#filtro').val(button);
				jQuery(".optionsMenu").toggle();
				console.log(button);
			});
			jQuery('.searchform #slug').keypress(function(e) {
				if(e.which == 13) {
					jQuery('.searchform').submit();
					return false;
				}
			});
			jQuery('.searchform').submit(function(e) {
				var data = jQuery(this).serializeArray();
				data.push({name: "ajax", value: true});
				jQuery.ajax({
					url: '<?php echo site_url('buscar');?>',
					type: 'POST',
					data: jQuery.param(data),
					success: function(data) {
						window.location = data;
						return false;
					}
				});
				return false;
			});
			jQuery('textarea').autosize();
			jQuery('body').click(function(e) {
				jQuery(".optionsMenu").hide();
			});
		});
	</script>
<?php if(ENVIRONMENT === "production"):?>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-33940112-1']);
		_gaq.push(['_trackPageview']);

		(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
<?php endif;?>
<?php 
if(isset($content)):
	echo $content;
endif;
?>
</head>
<body>
	<div class="wrapper">
<div class="row">
    {if $rol == "Administrador"}
        <div class="col-xs-4">
            <a href="<?=site_url("adminb/cosecha");?>" class="thumbnail" style="height: 200px; background-color: #cacaca">
                <br><br>
                <center>
                    <i class="fa fa-cloud-download" style="font-size:100px"></i><br><span style="font-weight: bold">Cosechar números</span>
                </center>
            </a>
        </div>
        <div class="col-xs-4">
            <a href="<?=site_url("adminb/asigna");?>" class="thumbnail" style="height: 200px; background-color: #cacaca">
                <br><br>
                <center>
                    <i class="glyphicon glyphicon-list-alt" style="font-size:95px"></i><br><span style="font-weight: bold">Asignar registros</span>
                </center>
            </a>
        </div>
        <div class="col-xs-4">
            <a href="<?=site_url("adminb/avance");?>" class="thumbnail" style="height: 200px; background-color: #cacaca">
                <br><br>
                <center>
                    <i class="glyphicon glyphicon-tasks" style="font-size:95px"></i><br><span style="font-weight: bold">Avance</span>
                </center>
            </a>
        </div>
    {/if}
    <div class="col-xs-4">
        <a href="<?=site_url("adminb/analisis");?>" class="thumbnail" style="height: 200px; background-color: #cacaca">
            <br><br>
            <center>
                <i class="glyphicon glyphicon-edit" style="font-size:95px"></i><br><span style="font-weight: bold">Análisis de registros</span>
            </center>
        </a>
    </div>
						  
															
							 
			
		  
</div>


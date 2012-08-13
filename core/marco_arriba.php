<html>
<head>
	<title>
		<?php echo $NombreRAD; ?> <?php echo $VersionRAD; ?>
  	</title>
  	
	<script type="text/javascript" src="js/tooltips.js"></script>
	<script type="text/javascript" src="js/validaform.js"></script>
	<script type="text/javascript" src="js/popup.js"></script>
	<script type="text/javascript" src="js/calendario.js"></script>
	<script type="text/javascript" src="js/tecladovirtual.js"></script>

	<link rel="stylesheet" type="text/css" href="skin/<?php echo $PlantillaActiva; ?>/general.css">
	<link rel="stylesheet" type="text/css" href="skin/<?php echo $PlantillaActiva; ?>/calendario.css">
	<link rel="stylesheet" type="text/css" href="skin/<?php echo $PlantillaActiva; ?>/tecladovirtual.css">
	<link rel="stylesheet" type="text/css" href="skin/<?php echo $PlantillaActiva; ?>/acordeon.css">
</head>
<body leftmargin="0"  margin="0" topmargin="0" oncontextmenu="return false;">

<form action="<?php echo $ArchivoCORE; ?>" method="POST" style="display:inline; height: 0px; border-width: 0px; width: 0px; padding: 0; margin: 0;" name="core_ver_menu">
	<input type="Hidden" name="accion" value="Ver_menu">
</form>



<!-- INICIA LA TABLA PRINCIPAL -->
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" align="left">

	<!-- INICIO DEL ENCABEZADO -->
	<tr><td>
		<table width="100%" cellspacing="0" cellpadding="0" border=0 class="MarcoSuperior"><tr>
			<td valign="bottom" width="20%">
				<img src="<?php echo 'skin/'.$PlantillaActiva.'/img/logo.png'; ?>" border="0">
			</td>
			<td align="center" valign="middle" width="60%">
				<b>
				<?php 
					if ($Sesion_abierta)
						echo '<font color="#d4dce4">'.$Nombre_Empresa_Corto.'</font> - '.$Nombre_Aplicacion.' </b> <i> v'.$Version_Aplicacion.'</i>';
					else
						echo '<font color="#d4dce4">Generador de Aplicaciones WEB</font> Libre y multiplataforma';
				?>
			</td>
			<td align="right"  width="20%" valign="bottom">
				<?php 
					if ($Sesion_abierta) {
				?>
							<?php echo $Nombre_usuario;?>
							(<font color="#ffff00"><?php 
								for ($i=1;$i<=$Nivel_usuario;$i++)
								echo "&#9733;";
							?></font>)&nbsp;
							<br>
							<img src="<?php echo 'skin/'.$PlantillaActiva.'/img/cerrar.gif'; ?>" border="0" OnClick="cerrar_sesion.submit();" style="cursor:pointer;">&nbsp;
				<?php 
					}
				?>
			</td>
		</tr></table>
		<!-- FIN DEL ENCABEZADO -->

		<table width="100%" cellspacing="0" cellpadding="0" border=0 class="MenuSuperior"><tr>
			<td valign="top">
				<?php 
					
					if ($Sesion_abierta && $accion=="Ver_menu") {
						echo '&nbsp;<b>@'.@$Login_usuario.'</b>>&nbsp;&nbsp;&nbsp;';
						// Carga las opciones del menu superior

						// Si el usuario es el administrador muestra todas las opciones
						if ($Login_usuario=="admin")
							$resultado=ejecutar_sql("SELECT * FROM ".$TablasCore."menu WHERE posible_arriba");
						//else
						//	$consulta = "SELECT menu.* FROM menu,usuario_menu WHERE  posible_arriba='S' AND usuario_menu.menu=menu.id AND usuario_menu.usuario='$Id_usuario' AND nivel>0";
					
						while($registro = $resultado->fetch())
							{
								// Imprime la imagen asociada si esta definida
								if ($registro["imagen"]!="") echo '<img src="img/'.$registro["imagen"].'" border=0 alt="" valign="absmiddle" align="absmiddle" width="14" height="13" >&nbsp;';
								
								// Verifica si se trata de un comando interno y crea formulario y enlace correspondiente
								if ($registro["tipo_comando"]=="Interno")
									{
										echo '<form action="'.$ArchivoCORE.'" method="post" name="a_'.$registro["id"].'" id="a_'.$registro["id"].'" style="display:inline; height: 0px; border-width: 0px; width: 0px; padding: 0; margin: 0;"><input type="hidden" name="accion" value="'.$registro["comando"].'"></form>';
										echo '<a href="javascript:document.a_'.$registro["id"].'.submit();">'.$registro["texto"].'</a>';
									}

								// Agrega un separador de menu
								echo '<img src="skin/'.$PlantillaActiva.'/img/sep1.gif" border=0 alt="" valign="absmiddle" align="absmiddle" >';
							}

				?>
				<?php 
					}
					else
						echo '<br>';
				?>
			</td>
		</tr></table>
		<form method="POST" name="cerrar_sesion" style="display:inline; height: 0px; border-width: 0px; width: 0px; padding: 0; margin: 0;">
			<input type="Hidden" name="accion" value="Terminar_sesion">
		</form>
	</td></tr>
		
	<!-- INICIO  DE CONTENIDOS DE APLICACION -->
	
	
	<!-- INICIO DEL CONTENIDO CENTRAL -->
	<tr><td height="100%" valign="<?php if ($accion=="Ver_menu") echo 'TOP'; else echo 'MIDDLE'; ?>" align="center">
	<!-- INICIO DEL CONTENIDO CENTRAL -->

		<table width="100%" border=0>
			<tr><td valign=top>
				<?php 
					if ($Sesion_abierta && $accion=="Ver_menu") {
						// Carga las opciones del escritorio

						// Si el usuario es el administrador muestra todas las opciones
						if ($Login_usuario=="admin")
							$resultado=ejecutar_sql("SELECT * FROM ".$TablasCore."menu WHERE posible_escritorio");
						//else
						//	$consulta = "SELECT menu.* FROM menu,usuario_menu WHERE posible_escritorio='S' AND usuario_menu.menu=menu.id AND usuario_menu.usuario='$Id_usuario' AND nivel>0";
						// Imprime los formularios
						while($registro = $resultado->fetch())
							{
								// Verifica si se trata de un comando interno y crea formulario y enlace correspondiente
								if ($registro["tipo_comando"]=="Interno")
									{
										// Imprime la imagen asociada si esta definida
										if ($registro["imagen"]!="") 
											{
												echo '<form action="'.$ArchivoCORE.'" method="post" name="b_'.$registro["id"].'" id="b_'.$registro["id"].'" style="display:inline; height: 0px; border-width: 0px; width: 0px; padding: 0; margin: 0;"><input type="hidden" name="accion" value="'.$registro["comando"].'"></form>';
												echo '<a title="'.$registro["texto"].'" name="" href="javascript:document.b_'.$registro["id"].'.submit();">';
												echo '<img src="img/'.$registro["imagen"].'" alt="'.$registro["texto"].'" class="IconosEscritorio" valign="absmiddle" align="absmiddle">';
												echo '</a>';
											}
									}
							}

						} //Finsi

				?>
			</td>
			<td align=right valign=top>
				<?php 
					if ($Sesion_abierta && $accion=="Ver_menu") {
						//Imprime una imagen en el escritorio - en desuso
						//echo '<img src="img/lagartin.png" alt="" valign="top">';
					}
				?>
			</td>
		</tr>
	</table>
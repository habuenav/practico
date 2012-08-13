<?php
			/*
				Title: Modulo usuarios
				Ubicacion *[/core/usuarios.php]*.  Archivo de funciones relacionadas con la administracion de usuarios y permisos del sistema.
			*/
?>

<?php
			/*
				Section: Administracion de permisos
				Funciones asociadas a la gestion de permisos, roles y demas posibilidades de acceso que puedan tener los usuarios en el aplicativo.
			*/
?>

<script language="Javascript">
	function buscar_texto_en_plantilla(texto,plantilla){
	   for(i=0; i<texto.length; i++){
		  if (plantilla.indexOf(texto.charAt(i),0)!=-1){
			 return 1;
		  }
	   }
	   return 0;
	} 

	function tiene_simbolos(texto){
		return buscar_texto_en_plantilla(texto,"!#$%&*");
	} 

	function tiene_numeros(texto){
		return buscar_texto_en_plantilla(texto,"0123456789");
	} 

	function tiene_letras(texto){
		return buscar_texto_en_plantilla(texto,"abcdefghyjklmnñopqrstuvwxyz");
	} 

	function tiene_minusculas(texto){
		return buscar_texto_en_plantilla(texto,"abcdefghyjklmnñopqrstuvwxyz");
	} 

	function tiene_mayusculas(texto){
		return buscar_texto_en_plantilla(texto,"ABCDEFGHYJKLMNÑOPQRSTUVWXYZ");
	} 

	function seguridad_clave(clave){
		var seguridad = 0;
		if (clave.length!=0)
			{
				if (tiene_numeros(clave)) seguridad += 10;
				if (tiene_minusculas (clave)) seguridad += 20;
				if (tiene_mayusculas(clave)) seguridad += 20;
				if (tiene_simbolos(clave)) seguridad += 20;
				if (tiene_minusculas(clave) && tiene_mayusculas(clave)) seguridad += 30;
				if (tiene_simbolos(clave) && (tiene_mayusculas(clave) || tiene_minusculas (clave))) seguridad += 10;
				if (clave.length <= 7) seguridad -= 40;
				if (clave.length >= 8) seguridad += 10;
			}
		if (seguridad>100) seguridad=100;
		if (seguridad<0) seguridad=0;
		return seguridad;
	}

	function muestra_seguridad_clave(clave,formulario){
		seguridad=seguridad_clave(clave);
		formulario.seguridad.value=seguridad;
	}
</script>








<?php
/* ################################################################## */
/*
	Function: eliminar_permiso
	Elimina un permiso a un usuario determinado.

	Variables de entrada:

		usuario - UID/Login de usuario al que se desea eliminar el permiso
		menu - ID del menu que se desea eliminar del perfil del usuario

		(start code)
			DELETE FROM usuario_menu WHERE menu=$menu AND usuario='$usuario'
		(end)

	Salida:
		Tabla de permisos actualizada al eliminar el registro correspondiente
*/
if ($accion=="eliminar_permiso")
	{
		// Elimina los datos de la opcion
		ejecutar_sql_unaria("DELETE FROM ".$TablasCore."usuario_menu WHERE menu=$menu AND usuario='$usuario'");
		// Lleva a auditoria
		ejecutar_sql_unaria("INSERT INTO ".$TablasCore."auditoria VALUES (0,'$Id_usuario','Elimina permiso $menu a $usuario','$fecha_operacion','$hora_operacion')");
		echo '<form name="cancelar" action="'.$ArchivoCORE.'" method="POST"><input type="Hidden" name="accion" value="permisos_usuario"><input type="Hidden" name="usuario" value="'.$usuario.'"></form>
				<script type="" language="JavaScript"> document.cancelar.submit();  </script>';
	}
?>
<!--  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<?php
	if ($accion=="agregar_permiso")
		{
			$mensaje_error="";
			// Busca si existe ese permiso para el usuario
			$resultado=ejecutar_sql("SELECT * FROM ".$TablasCore."usuario_menu WHERE usuario='$usuario' AND menu='$menu'");
			$registro_menu = $resultado->fetch();
			if($registro_menu["menu"]!="")
				$mensaje_error="El usuario ya posee el permiso seleccionado.";

			if ($mensaje_error=="")
				{
					// Guarda el permiso para el usuario
					ejecutar_sql_unaria("INSERT INTO ".$TablasCore."usuario_menu VALUES (0,'$usuario','$menu')");
					// Lleva a auditoria
					ejecutar_sql_unaria("INSERT INTO ".$TablasCore."auditoria VALUES (0,'$Login_usuario','Agrega permiso $menu al usuario $usuario','$fecha_operacion','$hora_operacion')");
					echo '<form name="cancelar" action="'.$ArchivoCORE.'" method="POST">
							<input type="Hidden" name="accion" value="permisos_usuario">
							<input type="Hidden" name="usuario" value="'.$usuario.'">
							</form>
							<script type="" language="JavaScript"> document.cancelar.submit();  </script>';
				}
			else
				{
					echo '<form name="cancelar" action="'.$ArchivoCORE.'" method="POST"><input type="Hidden" name="accion" value="permisos_usuario"><input type="Hidden" name="usuario" value="'.$usuario.'"></form>
							<script type="" language="JavaScript"> 
							window.alert("El usuario ya posee el permiso seleccionado.");
							document.cancelar.submit();  </script>';
				}
		}
?>
<!--  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
		<?php if ($accion=="permisos_usuario")
				{
						echo '<div align="center"><br>';
						abrir_ventana('Administraci&oacute;n de permisos del usuario','','90%');
		?>

		<div align="center">
		<DIV style="DISPLAY: block; OVERFLOW: auto; WIDTH: 100%; POSITION: relative; HEIGHT: 290px">
			<form name="datos" action="<?php echo $ArchivoCORE; ?>" method="POST">
			<input type="hidden" name="usuario" value="<?php echo $usuario; ?>">
			<input type="hidden" name="accion" value="agregar_permiso">
			<br><font face="" size="3" color="Navy"><b>Agregar opci&oacute;n al men&uacute; del usuario  <?php echo $usuario; ?></b></font>
			<br><br>
				<select name="menu" class="selector_01" >
						<?php
							$resultado=ejecutar_sql("SELECT ".$TablasCore."menu.* FROM ".$TablasCore."menu WHERE nivel=0");
							while($registro = $resultado->fetch())
								{
									echo '<option value="'.$registro["id"].'">'.$registro["texto"].'</option>';
									// Imprime las opciones dentro de la seccion
									$padre=$registro["id"];
									$resultado_nivel1=ejecutar_sql("SELECT ".$TablasCore."menu.* FROM ".$TablasCore."menu WHERE nivel=1 AND padre=$padre ORDER BY peso");
									while($registro_nivel1 = $resultado_nivel1->fetch())
										{
											echo '<option value="'.$registro_nivel1["id"].'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$registro_nivel1["texto"].' ('.$registro_nivel1["accion"].')</option>';
										}
								}
						?>
				</select>

			<br><br><strong><blink>Importante:</blink></strong>  recuerde que si usted agrega una opci&oacute;n del men&uacute; al usuario, tambi&eacute;n <br>deber&aacute; agregar la secci&oacute;n que contiene dicha opci&oacute;n o comando para que pueda ser<br> visualizada correctamente.

			<table width="90%" border="0" cellspacing="0" cellpadding="0"><tr>
				<td align="RIGHT" valign="TOP">
					<table border="0" cellspacing="5" cellpadding="0" align="CENTER" style="font-family: Verdana, Tahoma, Arial; font-size: 10px; margin-top: 10px; margin-right: 10px; margin-left: 10px; margin-bottom: 10px;" class="link_menu">
						<tr>
							<td align="RIGHT">
									</form>
							</td><td width="5"></td>
							<td align="RIGHT">
									<input type="Button" name="" value="Agregar" style="border-width: 1px; font-family: Verdana, Tahoma, Arial; font-size: 9px; background-color: e1e1e1; color: Teal; border-color: Gray; border-style: ridge; height: 17px; padding-top: 1px; font-weight: bold;" onClick="document.datos.submit()">
									&nbsp;&nbsp;<input type="Button" onclick="document.core_ver_menu.submit()" name="" value="Cancelar" style="border-width: 1px; font-family: Verdana, Tahoma, Arial; font-size: 9px; background-color: e1e1e1; color: Teal; border-color: Gray; border-style: ridge; height: 17px; padding-top: 1px; font-weight: bold;">
							</td>
						</tr>
					</table>
				</td>
			</tr></table>

		<font face="" size="3" color="Navy"><b>Secciones ya disponibles</b></font><br><br>
		 <?php
				echo '
				<table width="100%" border="0" cellspacing="0" align="CENTER" style="font-size: 9px; font-family: Verdana, Tahoma, Arial;">
					<tr>
						<td align="LEFT" bgcolor="#D6D6D6"><b>&nbsp;&nbsp;ID&nbsp;&nbsp;</b></td>
						<td align="LEFT" bgcolor="#D6D6D6"><b>&nbsp;&nbsp;Nivel&nbsp;&nbsp;</b></td>
						<td align="LEFT" bgcolor="#D6D6D6"><b>&nbsp;&nbsp;Ubicaci&oacute;n&nbsp;&nbsp;</b></td>
						<td align="left" bgcolor="#d6d6d6"><b>&nbsp;&nbsp;&nbsp;Texto&nbsp;&nbsp;&nbsp;</b></td>
						<td align="left" bgcolor="#d6d6d6"><b>&nbsp;&nbsp;&nbsp;Peso&nbsp;&nbsp;&nbsp;</b></td>
						<td align="left" bgcolor="#d6d6d6"></td>
						<td align="left" bgcolor="#d6d6d6"></td>
					</tr>
					<tr><td></td><td></td><td></td>	<td></td></tr>
					';

				$resultado=ejecutar_sql("SELECT ".$TablasCore."menu.* FROM ".$TablasCore."menu,".$TablasCore."usuario_menu WHERE ".$TablasCore."usuario_menu.".$TablasCore."menu=".$TablasCore."menu.id AND ".$TablasCore."usuario_menu.usuario='$usuario' AND ".$TablasCore."menu.nivel=0");
				while($registro = $resultado->fetch())
					{
						echo '<tr>
								<td >&nbsp;&nbsp;'.$registro["id"].'&nbsp;&nbsp;</td>
								<td >&nbsp;&nbsp;'.$registro["nivel"].'&nbsp;&nbsp;</td>
								<td>&nbsp;&nbsp;'.$registro["columna"].'&nbsp;&nbsp;</td>
								<td>&nbsp;&nbsp;<strong>'.$registro["texto"].'</strong>&nbsp;&nbsp;</td>
								<td>&nbsp;&nbsp;'.$registro["peso"].'&nbsp;&nbsp;</td>
								<td align="center">
										<form action="'.$ArchivoCORE.'" method="POST" name="f'.$registro["id"].'" id="f'.$registro["id"].'">
												<input type="hidden" name="accion" value="eliminar_permiso">
												<input type="hidden" name="usuario" value="'.$usuario.'">
												<input type="hidden" name="menu" value="'.$registro["id"].'">
												<input type="button" value="Eliminar" style="border-width: 1px; font-family: Verdana, Tahoma, Arial; font-size: 9px; background-color: e1e1e1; color: Teal; border-color: Gray; border-style: ridge; height: 17px; padding-top: 1px; width: 55px;" onClick="confirmar_evento(\'IMPORTANTE:  Al eliminar el registro pueden quedar sin vincular algunas opciones del sistema.\nEst&aacute; seguro que desea continuar ?\',f'.$registro["id"].');">
												&nbsp;&nbsp;
										</form>
								</td>
							</tr>';

							// Imprime las opciones dentro de la seccion
							$padre=$registro["id"];
							$resultado_nivel1=ejecutar_sql("SELECT ".$TablasCore."menu.* FROM ".$TablasCore."menu,".$TablasCore."usuario_menu WHERE ".$TablasCore."usuario_menu.usuario='$usuario' AND ".$TablasCore."usuario_menu.menu=".$TablasCore."menu.id AND ".$TablasCore."menu.padre=$padre ORDER BY peso");
							while($registro_nivel1 = $resultado_nivel1->fetch())
								{

						echo '<tr>
								<td >&nbsp;&nbsp;'.$registro_nivel1["id"].'&nbsp;&nbsp;</td>
								<td >&nbsp;&nbsp;'.$registro_nivel1["nivel"].'&nbsp;&nbsp;</td>
								<td></td>
								<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$registro_nivel1["texto"].'&nbsp;&nbsp;</td>
								<td>&nbsp;&nbsp;'.$registro_nivel1["peso"].'&nbsp;&nbsp;</td>
								<td align="center">
										<form action="'.$ArchivoCORE.'" method="POST" name="f'.$registro_nivel1["id"].'" id="f'.$registro_nivel1["id"].'">
												<input type="hidden" name="accion" value="eliminar_permiso">
												<input type="hidden" name="usuario" value="'.$usuario.'">
												<input type="hidden" name="menu" value="'.$registro_nivel1["id"].'">
												<input type="button" value="Eliminar" style="border-width: 1px; font-family: Verdana, Tahoma, Arial; font-size: 9px; background-color: e1e1e1; color: Teal; border-color: Gray; border-style: ridge; height: 17px; padding-top: 1px; width: 55px;" onClick="confirmar_evento(\'IMPORTANTE:  Al eliminar el registro pueden quedar sin vincular algunas opciones del sistema.\nEst&aacute; seguro que desea continuar ?\',f'.$registro_nivel1["id"].');">
												&nbsp;&nbsp;
										</form>
								</td>
							</tr>';
								}
					}
				echo '</table><br><br>';
		 ?>
		</DIV>
		</div>

		 <?php
		 				cerrar_ventana();
		 		}
		 ?>
		 
		 
		 
		 
<!--  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<?php
			/*
				Section: Operaciones basicas de administracion
				Funciones asociadas al mantenimiento de la informacion de usuarios: Adicion, edicio, eliminacion, cambios de estado.
			*/
?>	 
<!--  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<?php
	if ($accion=="eliminar_usuario")
		{
			/*
				Function: eliminar_usuario
				Elimina completamente un usuario del sistema.   Tambien elimina todos los permisos que puedan haber sido definidos para el.

				Variables minimas de entrada:
					uid_especifico - Login del usuario

				Proceso simplificado:
					(start code)
						if ($estado==1)
							$consulta = "UPDATE ".$TablasCore."usuario SET estado=0 WHERE login='$uid_especifico'";
						else
							$consulta = "UPDATE ".$TablasCore."usuario SET estado=1, ultimo_acceso='$fecha_operacion' WHERE login='$uid_especifico'";
					(end)

				Salida de la funcion:
					* Usuario con estado diferente (contrario) al recibido

				Ver tambien:
					<listar_usuarios> | <agregar_usuario>
			*/
			ejecutar_sql_unaria("DELETE FROM ".$TablasCore."usuario WHERE login='$uid_especifico'");
			ejecutar_sql_unaria("DELETE FROM ".$TablasCore."usuario_menu WHERE usuario='$uid_especifico'");
			// Lleva a auditoria
			ejecutar_sql_unaria("INSERT INTO ".$TablasCore."auditoria VALUES (0,'$Login_usuario','Elimina el usuario $uid_especifico','$fecha_operacion','$hora_operacion')");
			echo '<script type="" language="JavaScript"> document.core_ver_menu.submit();  </script>';
		}
?>
<!--  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<?php
	if ($accion=="cambiar_estado_usuario")
		{
			/*
				Function: cambiar_estado_usuario
				Permite inhabilitar un usuario en el sistema sin tener que eliminarlo completamente o habilitarlo cuando ya se encuentre inhabilitado previamente.   Al actualizar el estado del usuario como Habilitado tambien se actualiza la ultima fecha de ingreso como la actual para controles de login posteriores.

				Variables minimas de entrada:
					uid_especifico - Login del usuario
					estado - Estado actual del usuario: 1=Activo, 0=Inactivo

				Proceso simplificado:
					(start code)
						if ($estado==1)
							$consulta = "UPDATE ".$TablasCore."usuario SET estado=0 WHERE login='$uid_especifico'";
						else
							$consulta = "UPDATE ".$TablasCore."usuario SET estado=1, ultimo_acceso='$fecha_operacion' WHERE login='$uid_especifico'";
					(end)

				Salida de la funcion:
					* Usuario con estado diferente (contrario) al recibido

				Ver tambien:
					<listar_usuarios>
			*/
			if ($estado==1)
				ejecutar_sql_unaria("UPDATE ".$TablasCore."usuario SET estado=0 WHERE login='$uid_especifico'");
			else
				ejecutar_sql_unaria("UPDATE ".$TablasCore."usuario SET estado=1, ultimo_acceso='$fecha_operacion' WHERE login='$uid_especifico'");
			// Lleva a auditoria
			ejecutar_sql_unaria("INSERT INTO ".$TablasCore."auditoria VALUES (0,'$Login_usuario','Cambia estado del usuario $uid_especifico y actualiza ultimo acceso a $fecha_operacion','$fecha_operacion','$hora_operacion')");
			echo '<script type="" language="JavaScript"> document.core_ver_menu.submit();  </script>';
		}
?>

<!--   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<?php
	if ($accion=="guardar_usuario")
		{
			/*
				Function: guardar_usuario
				Almacena la informacion basica de un usuario en la base de datos

				Variables minimas de entrada:
					login - Nickname o login para el usuario.  Debe ser un identificador unico y no existir ya en el sistema
					nombre - Nombre completo del usuario.
					clave - Contrasena sin encriptar del usuario

				Proceso simplificado:
					(start code)
						SELECT login as uid_db FROM ".$TablasCore."usuario WHERE login='$login'
						INSERT INTO ".$TablasCore."usuario VALUES ('$login','$clavemd5','$nombre','$descripcion',$estado,'$nivel','$correo','$fecha_operacion','$pasomd5')"
					(end)

				Salida de la funcion:
					* Usuario registrado en el sistema.  El proceso agrega ademas las claves en MD5 y la llave de paso definida en el archivo de <Libreria base> 

				Ver tambien:
					<agregar_usuario> | <eliminar_usuario>
			*/
			$mensaje_error="";

			// Verifica que no existe el usuario
			$resultado_usuario=ejecutar_sql("SELECT login FROM ".$TablasCore."usuario WHERE login='$login'");
			$registro_usuario = $resultado_usuario->fetch();
			if ($registro_usuario["login"]!="")
				$mensaje_error="El usuario ingresado ya existe, por favor verifique o cambie el login ingresado para la cuenta e intente de nuevo.";

			// Verifica campos nulos
			if ($nombre=="" || $login=="" || $clave=="")
				$mensaje_error="Usted ha olvidado ingresar alguno de los datos solicitados como obligatorios: Nombre, Login o Clave.";

			// Verifica contrasenas:  longitud e igualdad de la verificacion
			if ($clave!=$clave1)
				$mensaje_error="Las contrase&ntilde;as ingresadas no coinciden.";
			if (strlen($clave)<6)
				$mensaje_error="La contrase&ntilde;a ingresada debe tener al menos seis caracteres.";

			if ($mensaje_error=="")
				{
					// Inserta datos del usuario
					$clavemd5=MD5($clave);
					$pasomd5=MD5($LlaveDePaso);
					ejecutar_sql_unaria("INSERT INTO ".$TablasCore."usuario VALUES ('$login','$clavemd5','$nombre','$descripcion',$estado,'$nivel','$correo','$fecha_operacion','$pasomd5')");
					// Lleva a auditoria
					ejecutar_sql_unaria("INSERT INTO ".$TablasCore."auditoria VALUES (0,'$Login_usuario','Agrega usuario $login para $nombre','$fecha_operacion','$hora_operacion')");
					echo '<script type="" language="JavaScript"> document.core_ver_menu.submit();  </script>';
				}
			else
				{
					echo '<form name="cancelar" action="'.$ArchivoCORE.'" method="POST">
						<input type="Hidden" name="accion" value="agregar_usuario">
						<input type="Hidden" name="error_titulo" value="Problema al agregar el usuario">
						<input type="Hidden" name="error_descripcion" value="'.$mensaje_error.'">
						</form>
						<script type="" language="JavaScript"> document.cancelar.submit();  </script>';
				}
		}
?>

<!--   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<?php if ($accion=="agregar_usuario")
	{
			/*
				Function: agregar_usuario
				Presenta el formulario base para la adicion de usuarios al sistema.

				Salida de la funcion:
					* Llamada al proceso <guardar_usuario> para almacenar la informacion correspondiente al nuevo usuario.

				Ver tambien:
					<listar_usuarios> | <permisos_usuario> | <eliminar_usuario> | <cambiar_estado_usuario>
			*/
		echo '<div align="center">';
		abrir_ventana('Adici&oacute;n de usuarios','f2f2f2','');
?>

			<table width="500" border="0" cellspacing="5" cellpadding="0" align="center" class="TextosVentana">
					<tr>
						<td valign="top"><img src="img/info_icon.png" alt="" border="0">
						</td>
						<td valign="top"><strong>Tenga presente:<br></strong>
						Las contrase&ntilde;as con condiciones m&iacute;nimas de seguridad deben tener una longitud de <b>al menos 8 caracteres</b>, n&uacute;meros, letras en may&uacute;scula y en min&uacute;scula o s&iacute;mbolos como <font color=blue>! # $ % & - *</font>.  Para que su contrase&ntilde;a sea considerada segura <b>debe cumplir al menos con un nivel de seguridad del 81%</b>.
						</td>
					</tr>
				</table>


		<!-- VALOR MD5 PARA VACIO:  d41d8cd98f00b204e9800998ecf8427e-->
					<form name="datos" action="<?php echo $ArchivoCORE; ?>" method="POST">
					<input type="hidden" name="accion" value="guardar_usuario">
					<table border="0" cellspacing="5" cellpadding="0" align="CENTER" style="font-family: Verdana, Tahoma, Arial; font-size: 10px; margin-top: 10px; margin-right: 10px; margin-left: 10px; margin-bottom: 10px;"  class="TextosVentana">
						<tr>
							<td align="RIGHT">NickName / Login</td><td width="20"></td>
							<td><input class="CampoTexto" type="Text" name="login" size="11" maxlength="20"  onkeypress="return validar_teclado(event, 'alfanumerico');" >
							<a href="#" title="Campo obligatorio" name=""><img src="img/icn_12.gif" border=0></a>
							<a href="#" title="Cuenta de usuario" name="Login &uacute;nico para identificar el usuario en el sistema. SENSIBLE A MAYUSCULAS"><img src="img/icn_10.gif" border=0></a>
							</td>
						</tr>
						<tr>
							<td align="RIGHT">Nombre completo</td><td width="20"></td>
							<td><input class="CampoTexto" type="Text" name="nombre" size="30" maxlength="200" onkeypress="return validar_teclado(event, 'alfanumerico');">
							<a href="#" title="Campo obligatorio" name=""><img src="img/icn_12.gif" border=0></a>
							</td>
						</tr>
						<tr>
							<td align="RIGHT">Descripci&oacute;n</td><td width="20"></td>
							<td><input class="CampoTexto" type="Text" name="descripcion" size="30" maxlength="200" onkeypress="return validar_teclado(event, 'alfanumerico');"></td>
						</tr>
						<tr>
							<td align="RIGHT">Contrase&ntilde;a</td><td width="20"></td>
							<td><input class="CampoTexto" type="password" name="clave" size="11" maxlength="20" onkeyup="muestra_seguridad_clave(this.value, this.form)">
							<a href="#" title="Campo obligatorio" name=""><img src="img/icn_12.gif" border=0></a>
							&nbsp;&nbsp; Nivel de seguridad: <input id="seguridad" value="0" size="3" name="seguridad" style="border: 0px; background-color:ffffff; text-decoration:italic;" class="CampoTexto" type="text" readonly onfocus="blur()">%
							</td>
						</tr>
						<tr>
							<td align="RIGHT">Contrase&ntilde;a (verificar)</td><td width="20"></td>
							<td><input class="CampoTexto" type="password" name="clave1" size="11" maxlength="20">
							<a href="#" title="Campo obligatorio" name=""><img src="img/icn_12.gif" border=0></a>
							</td>
						</tr>
						<tr>
							<td align="RIGHT" valign="TOP">Correo</td><td width="20"></td>
							<td><input class="CampoTexto" type="Text" name="correo" size="30" maxlength="200" onkeypress="return FiltrarTeclas(this, event)">
								<a href="#" title="Direcci&oacute;n para alertas y notificaciones" name="Direcci&oacute;n electr&oacute;nica de posible uso para notificaciones autom&aacute;ticas del sistema en algunos m&oacute;dulos"><img src="img/icn_10.gif" border=0></a>
							</td>
						</tr>
						<tr>
							<td align="RIGHT">Estado inicial</td><td width="20"></td>
							<td>
								<select name="estado" class="selector_01">
										<option value="1">Activo</option>
										<option value="0">Inactivo</option>
								</select>
							</td>
						</tr>
						<tr>
							<td align="RIGHT" valign="TOP"><strong>Nivel de acceso</strong></td><td width="10"></td>
							<td>
								<!-- Caracteres UNICODE para el combo
									Revisar: http://www.okelmann.com/homepage/unicode.htm -->
								<select  name="nivel" id="nivel"  style="font-family: Verdana, Tahoma, Arial; font-weight: normal; font-size: 10px; width: 100px; background-color: #FFFFFF; color: #000000;">
									<option value="1">&#9733;</option>
									<option value="2">&#9733;&#9733;</option>
									<option value="3">&#9733;&#9733;&#9733;</option>
									<option value="4">&#9733;&#9733;&#9733;&#9733;</option>
									<option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; SuperAdmin</option>
								</select>
								<a href="#" title="Perfil inicial de seguridad" name="Perfil de seguridad del usuario.  CUIDADO:  Esta opci&oacute;n es diferente a los permisos individuales de usuario definidos por el disenador para los objetos por el creados.  Este perfil solamente aplica para las operaciones internas de Pr&aacute;ctico."><img src="img/icn_10.gif" border=0></a>
							</td>
						</tr>
						<tr>
							<td align="RIGHT"></td><td width="20">
									</form>
							</td>
							<td>
									<input type="Button" onclick="document.datos.submit()" name="" value="Guardar" class="Botones">
									&nbsp;&nbsp;<input type="Button" onclick="document.core_ver_menu.submit()" name="" value="Cancelar" class="Botones">
							</td>
						</tr>
					</table>
		 <?php
		 				cerrar_ventana();
		 		}
		 ?>

<!--   @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
<?php
			/*
				Section: Informes
				Funciones que presentan informacion de los usuarios o permisos como listados.
			*/
?>
<!--  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->
		<?php if ($accion=="listar_usuarios")
				{
			/*
				Function: listar_usuarios
				Presenta el listado de usuarios (excluido el usuario admin) con posibilidad de filtrado y operaciones basicas.

				Variables minimas de entrada:
					nombre_filtro - Nombre del usuario (o parte)
					login_filtro - Parte del UID o Login

				Proceso simplificado:
					(start code)
						SELECT * FROM ".$TablasCore."usuario WHERE (login LIKE '%$login_filtro%') AND (nombre LIKE '%$nombre_filtro%' ) AND login<>'admin' ORDER BY login,nombre";
					(end)

				Salida de la funcion:
					* Listado de usuarios filtrado por algun criterio y ordenado por login y nombre

				Ver tambien:
					<agregar_usuario> | <permisos_usuario> | <eliminar_usuario> | <cambiar_estado_usuario>
			*/

				abrir_ventana('Listado de usuarios en el sistema','f2f2f2','');

				echo '<br>
		<div align="center">
			<form name="form_crear_usuario" action="'.$ArchivoCORE.'" method="POST"  style="display:inline; height: 0px; border-width: 0px; width: 0px; padding: 0; margin: 0;">
				<input type="hidden" name="accion" value="agregar_usuario">
			</form>
			<form action="'.$ArchivoCORE.'" method="POST">
					Ver s&oacute;lo los usuarios cuyo<strong> nombre</strong> contenga&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input class="CampoTexto" type="Text" value="'.$nombre_filtro.'" name="nombre_filtro" size="20">
				<input type="hidden" name="accion" value="listar_usuarios">
				<input type="submit" value="Filtrar >>>" class="Botones">
			</form>
			<form action="'.$ArchivoCORE.'" method="POST">
					Ver s&oacute;lo los usuarios cuyo <strong>login</strong> contenga&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<input class="CampoTexto" type="Text" value="'.$login_filtro.'" name="login_filtro" size="20">
				<input type="hidden" name="accion" value="listar_usuarios">
				<input type="submit" value="Filtrar >>>" class="Botones">
			</form>
		';

			if ($nombre_filtro == "" && $login_filtro=="")
			{
				echo '<br>Debido a la cantidad de usuarios registrados usted debe filtrar el resultado.<br>
				Indique el tipo de filtro deseado en la parte superior y haga clic en el bot&oacute;n correspondiente.';
			}
			else
			{
				echo '
				<table border="0" cellspacing="10" align="CENTER" class="TextosVentana">
					<tr>
						<td align="left" bgcolor="#d6d6d6"><b>Login</b></td>
						<td align="LEFT" bgcolor="#D6D6D6"><b>Nombre</b></td>
						<td align="left" bgcolor="#d6d6d6"><b>Ultimo acceso</b></td>
						<td align="left"></td>
						<td align="left"></td>
						<td align="left"></td>
						<td align="left"></td>
					</tr>
					';
				$resultado=ejecutar_sql("SELECT * FROM ".$TablasCore."usuario WHERE (login LIKE '%$login_filtro%') AND (nombre LIKE '%$nombre_filtro%' ) AND login<>'admin' ORDER BY login,nombre");
				$i=0;
				while($registro = $resultado->fetch())
					{
						echo '<tr><td align="left">'.$registro["login"].'</td>
								<td>'.$registro["nombre"].'</td>
								<td>'.$registro["ultimo_acceso"].'</td>
								<td align="center">
										<form action="'.$ArchivoCORE.'" method="POST">
												<input type="hidden" name="accion" value="cambiar_estado_usuario">
												<input type="hidden" name="uid_especifico" value="'.$registro["login"].'">
												<input type="hidden" name="estado" value="'.$registro["estado"].'">
												<input type="Submit" value="';
												if ($registro["estado"]==1) echo 'Inhabilitar';
												else echo 'Habilitar';
												echo '" class="Botones">
										</form>
								</td>
								<td align="center">
										<form action="'.$ArchivoCORE.'" method="POST"  name="f'.$i.'">
												<input type="hidden" name="accion" value="eliminar_usuario">
												<input type="hidden" name="uid_especifico" value="'.$registro["login"].'">
												<input type="Button"   onClick="confirmar_evento(\'IMPORTANTE:  Est&aacute; a punto de eliminar el usuario y perder vinculos hacia registros asociados a este, no podr&aacute;n recuperarse esta accion a menos que usted recree el usuario con las mismas credenciales posteriormente.\nEst&aacute; seguro que desea continuar ?\',f'.$i.');" name="" value="Eliminar" class="BotonesCuidado">
												<!--<input type="Submit" value="Eliminar" style="border-width: 1px; font-family: Verdana, Tahoma, Arial; font-size: 9px; background-color: e1e1e1; color: Teal; border-color: FF0000; border-style: ridge; height: 17px; padding-top: 1px; width: 55px;">-->
										</form>
								</td>
								<td align="center">
										<form action="'.$ArchivoCORE.'" method="POST">
												<input type="hidden" name="accion" value="permisos_usuario">
												<input type="hidden" name="usuario" value="'.$registro["login"].'">
												<input type="Submit" value="Permisos" class="Botones">
										</form>
								</td>
								<td align="center">
										<form action="'.$ArchivoCORE.'" method="POST">
												<input type="hidden" name="accion" value="ver_seguimiento_especifico">
												<input type="hidden" name="uid_especifico" value="'.$registro["login"].'">
												<input type="Submit" value="Auditoria (deshabilitado)" class="Botones">
										</form>
								</td>
							</tr>';
							$i++;
					}
				echo '</table>';
				
			} // Fin sino filtro
				
	echo '
		</div>';
				abrir_barra_estado();
				echo '<input type="Button" onclick="document.core_ver_menu.submit()" value="<< Regresar al escritorio" class="BotonesEstado">';
				echo '<input type="Button" onclick="document.form_crear_usuario.submit()" value="Crear un usuario" class="BotonesEstadoCuidado">';
				cerrar_barra_estado();
				cerrar_ventana();
			 }
		?>
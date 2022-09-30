<?php

// This is the email connection configuration.
return array(

	'adminEmail'=>'webmaster@example.com', //se usa para notificar al administrador sobre algun evento en el sistema
		
	// ##configuracion de servidor de correo SENDGRID##
	'mailHost'=>'smtp.sendgrid.net',
	'mailPortSsl'=>'465',
	'mailSMTPAuth'=>'ssl',
	'mailUsername'=>'apikey',
	'mailUserPassw'=>'SG.4ZcftnLDSAqMnohc4bH15Q.3fpXXXXXXXCL9A5sh3HiQwtbxHZ_Z1ZMJBQ', //clave api
	'mailEmisor'=>'xavieremv@gmail.com', //quien envia los correos, ej: noreply@facto.com
	'mailEmisorNombre'=>'Factura', //descripción de quien envia los correos, ej: Ronald Perez
	
);
<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.'assets/class/phpmailer/class.phpmailer.php');

class Cmailer extends PHPMailer{	
	public function __construct($exceptions = false) {
		parent::__construct($exceptions);
	}
	
	public function mail_send($address,$subject,$message){
		try {
			$mail = new PHPMailer();
			if(stripos($message,'<body>')!==false){
				$mail->IsSMTP(); // Using SMTP.
			}			
			$mail->CharSet = 'utf-8';						
			$mail->Host = "relay-hosting.secureserver.net"; // SMTP server host.
			$mail->Port = 25;			
			$mail->AddAddress($address); 
			$mail->From = 'info@flexbile.com';
			$mail->FromName = 'info@flexbile.com';
			$mail->Subject = $subject;			
			$mail->MsgHTML($message);
			if($mail->Send()){
				return true;
			}
		} 
		catch (phpmailerException $e) {} 
		catch (Exception $e) {}
		return false;
	}
	
}

?>
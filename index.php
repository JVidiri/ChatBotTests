<?php
define('BOT_TOKEN', 'Trocar');
define('VERIFY_TOKEN', 'Trocar');
define('API_URL', 'https://graph.facebook.com/v2.6/me/messages?access_token='.BOT_TOKEN);

$hub_verify_token = null;
//-----VEFICA O WEBHOOK-----//
if(isset($_REQUEST['hub_challenge'])) {
    $challenge = $_REQUEST['hub_challenge'];
    $hub_verify_token = $_REQUEST['hub_verify_token'];
}
if ($hub_verify_token === VERIFY_TOKEN) {
    echo $challenge;
}
//-----FIM VERIFICAÇÃO-----//

function processMessage($message) {
  // processa a mensagem recebida
  
  $sender = $message['sender']['id'];//id do emissor
  $text = $message['message']['text'];//texto recebido na mensagem
  
  if (isset($text)) {	
	  sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => 'Olá!')));
  }   
}
function sendMessage($parameters) {
  $options = array(
	  'http' => array(
	    'method'  => 'POST',
	    'content' => json_encode($parameters),
	    'header'=>  "Content-Type: application/json\r\n" .
	                "Accept: application/json\r\n"
	    )
	);
	$context  = stream_context_create( $options );
	file_get_contents(API_URL, false, $context );
}

$update_response = file_get_contents("php://input");
$update = json_decode($update_response, true);
if (isset($update['entry'][0]['messaging'][0])) {
  processMessage($update['entry'][0]['messaging'][0]);
}


?>
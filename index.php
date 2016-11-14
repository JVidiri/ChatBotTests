<?php
define('BOT_TOKEN', '<TROCAR>');
define('VERIFY_TOKEN', '<TROCAR>');
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
    $msg = array('text' => "Eu não entendi, desculpe.");
  	//TODO integrar com o Watson
  	//$data = array('input' => array('text' => $text), 
  	//        		array('alternate_intents' => true));
  	//$result = CallAPI("POST", "https://watson-api-explorer.mybluemix.net/conversation/api/v1/workspaces/1d944e7f-0e52-4200-ab4a-a79a485b0167/message?version=2016-09-20", json_encode($data));
	  if (strpos($text, 'oi') !== false){
      $msg = array('text' =>"Olá! Eu sou o Bot do Pague menos, como posso te ajudar?");
    }else if (strpos($text, 'produto') !== false){
      $msg = array('attachment' => array('type' => "template", 
                                         'payload' => array('template_type' => "generic", 
                                                            'elements' => array( array('title' => "Feijão aquele legal", 
                                                                                'item_url' => "http://www.supermercadospaguemenos.com.br/index.html",
                                                                                'image_url' => "http://www.xapuri.info/wp-content/uploads/2015/03/feij%C3%A3o-rachel.jpg",
                                                                                'subtitle' => "Promoção R$ 9,35",
                                                                                'buttons' => array( array('type' => "web_url",
                                                                                                   'url' => "http://www.supermercadospaguemenos.com.br/index.html",
                                                                                                   'title' => "Compre"))),
                                                                                array('title' => "Feijão da crise", 
                                                                                'item_url' => "http://www.supermercadospaguemenos.com.br/index.html",
                                                                                'image_url' => "http://www.xapuri.info/wp-content/uploads/2015/03/feij%C3%A3o-rachel.jpg",
                                                                                'subtitle' => "R$ 15,98",
                                                                                'buttons' => array( array('type' => "web_url",
                                                                                                   'url' => "http://www.supermercadospaguemenos.com.br/index.html",
                                                                                                   'title' => "Compre")))
                                                                                )
                                                           )));
    }else if (strpos($text, 'pedido') !== false){
      $msg = "";
    }else if (strpos($text, 'Nova Odessa') !== false){
      $msg = array('text' => "Tem sim!, aqui está o endereço: 

Avenida Ampélio Gazzeta, 1800
Santa Rosa - Nova Odessa/SP
CEP: 13460-000
Telefone: 19 3466.8100");
    }
    sendMessage( array('recipient' => array('id' => $sender), 'message' => $msg ));
//	  sendMessage(array('recipient' => array('id' => $sender), 'message' => array('text' => $text)));
  }   
}
function sendMessage($parameters) {
  echo json_encode($parameters);
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

function CallAPI($method, $url, $data = false)
{
    $curl = curl_init();

    switch ($method)
    {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);

            if ($data)
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            break;       
        default:
            if ($data)
                $url = sprintf("%s?%s", $url, http_build_query($data));
    }

    // Optional Authentication:
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, "c77f65a6-ea97-40ab-acbe-9324f68c1cef:SPfcki35tVPi");

    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $result = curl_exec($curl);

    curl_close($curl);

    return $result;
}
?>

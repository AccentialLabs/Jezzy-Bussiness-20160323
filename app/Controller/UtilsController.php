<?php

/**
 * All action about Products And Sales
 */
  require("../Vendor/pusher-http-php-master/lib/Pusher.php");
  require("../Vendor/phpmailer/PHPMailerAutoload.php");
class UtilsController extends AppController {

    public function __construct($request = null, $response = null) {
        $this->layout = '';
        $this->set('title_for_layout', '');
        parent::__construct($request, $response);
    }
	
	public function sendMobileNotification($userId, $message){
	  $options = array(
    'encrypted' => true
  );
  $pusher = new Pusher(
    '9f4cbd00132ee1e897fd',
    '3201f7c0f934526ce629',
    '189862'
  );
  

  //$data['message'] = 'hello world';
  //$pusher->trigger('test_channel', 'my_event', $data);
  
	$res = $pusher->trigger(''.$userId.'', 'my_event', array('message' => ''.$message.''));
	
		//echo "mensagem enviada";
	}
	
	public function testeNasp(){
		$this->layout = "";
	}
	
	/**
	* Função responsável por acompanhar mudança de status das transações,
	* as mudanças são enviadas pelo MoIP e processadas por nosso sistema
	* e notificadas aos respectivos usuários
	**/
	public function NASPMoip(){
	 $this->autoRender = false;
		if ($this->request->is('post')) {
		
			$transactionId = $_POST['id_transacao'];			
			$transactionState = $_POST['status_pagamento'];
			$userEmail = $_POST['email_consumidor'];
			
			//Buscando Payment State na base de dados
			    $arrayParams = array(
                'PaymentState' => array(
                    'conditions' => array(
                        'PaymentState.moip_code' => $transactionState
                    )
                )
            );
            $paymentState = $this->AccentialApi->urlRequestToGetData('payments', 'first', $arrayParams);
			
			
			//Alterando Status do pagamento na base de dados
			$updateSql = "update checkouts set payment_state_id = {$transactionState} where id = {$transactionId};";
			$updateParams = array(
                'User' => array(
                    'query' => $updateSql
                )
            );
            $statistics = $this->AccentialApi->urlRequestToGetData('users', 'query', $updateParams);
			
			//Buscando infos da compra
			$selectCheckout = "select * from checkouts inner join offers on offers.id = checkouts.offer_id inner join users on users.id = checkouts.user_id where checkouts.id = {$transactionId};";

			$selectParams = array(
                'User' => array(
                    'query' => $selectCheckout
                )
            );
            $checkout = $this->AccentialApi->urlRequestToGetData('users', 'query', $selectParams);
			
			//Email para o Usuário
			//$this->sendEmailChangeState($userEmail, $checkout[0], "{$paymentState['PaymentState']['name']}");
			
			//Notificação para o Usuário
			$this->sendMobileNotification("{$checkout[0]['users']['id']}", "Houve uma mudança de status sua compra do produto {$checkout[0]['offers']['title']}. Seu produto agora está:  {$paymentState['PaymentState']['name']}");
			
			print_r($checkout);
		}
	 
	}
	
		
	public function sendEmailChangeState($userEmail, $checkout, $newStatus){
				$mail = new PHPMailer(true);
		
		// Define os dados do servidor e tipo de conexão
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 $mail->IsSMTP(); // Define que a mensagem será SMTP
 $mail->Host = "pro.turbo-smtp.com"; // Endereço do servidor SMTP
 $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
 $mail->Username = 'contato@jezzy.com.br'; // Usuário do servidor SMTP
 $mail->Password = 'oo0MvB2Qw'; // Senha do servidor SMTP
 
 // Define o remetente
 // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 $mail->From = "contato@jezzy.com.br"; // Seu e-mail
 $mail->FromName = "Mudança de Status na compra - Jezzy"; // Seu nome
 
  $mail->AddAddress("{$userEmail}");
  
  // Define os dados técnicos da Mensagem
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
 $mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)

// Define a mensagem (Texto e Assunto)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 $mail->Subject  = "Bem-Vindo ao Jezzy Empresas"; // Assunto da mensagem
 $mail->Body = "Ola,  Jezzy gostaria de informar que houve uma mudança de status na sua compra <i>{$checkout['offers']['title']}</i> 
 <br/> Status Atual da sua compra: <strong>{$newStatus}</strong> ";
 $mail->AltBody = "";
 
 // Define os anexos (opcional)
 // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
//$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo
  // Envia o e-mail
 $enviado = $mail->Send();

// Limpa os destinatários e os anexos
 $mail->ClearAllRecipients();
$mail->ClearAttachments();
}

}

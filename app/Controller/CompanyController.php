<?php

/**
 * All actions about user login on Jezzy
 */
 require("../Vendor/phpmailer/PHPMailerAutoload.php");
class CompanyController extends AppController {

    public function __construct($request = null, $response = null) {
        $this->layout = 'default_login';
        parent::__construct($request, $response);
    }

    /**
     * Check the session every time the class is call, exepts on 'logout' 
     */
    public function beforeFilter() {
        if ($this->action !== "logout") {
           
        }
    }

    /**
     * Used just to show 'view' 
     */
    public function createDir() {
        
		$companyName = $this->request->data ['companyName'];
		
		mkdir("/../../{$companyName}", 0700);
		
    }
	
	
	
	public function createCompany(){
		
	}
	
	public function register(){
		$this->layout = "";
		
		
	}
	
	/**
	* Action responsável por cadastrar empresa
	*/
	 public function inserCompany() {
		$this->layout = "";
 
			$password = $this->geraSenha();
            // CRIAÇÃO DE FORNECEDOR
            $sql = "INSERT INTO companies(" .
                    "`corporate_name`,"
                    . "`fancy_name`,"
                    . "`description`,"
                    . "`site_url`,"
                    . "`category_id`,"
                    . "`sub_category_id`,"
                    . "`cnpj`,"
                    . "`email`,"
                    . "`password`,"
                    . "`phone`,"
                    . "`phone_2`,"
                    . "`address`,"
                    . "`complement`,"
                    . "`city`,"
                    . "`state`,"
                    . "`district`,"
                    . "`number`,"
                    . "`zip_code`,"
                    . "`responsible_name`,"
                    . "`responsible_cpf`,"
                    . "`responsible_email`,"
                    . "`responsible_phone`,"
                    . "`responsible_phone_2`,"
                    . "`responsible_cell_phone`,"
                    . "`logo`,"
                    . "`status`,"
                    . "`login_moip`,"
                    . "`register`,"
                    . "`facebook_install`,"
                    . "`date_register`"
                    . ") VALUES("
                    . "'" . $this->request->data['Company']['corporate_name'] . "',"
                    . "'" . $this->request->data['Company']['fancy_name'] . "',"
                    . "'descricao forn',"
                    . "'" . $this->request->data['Company']['site'] . "',"
                    . "15,"
                    . "15, "
                    . "'" . $this->request->data['Company']['cnpj'] . "',"
                    . "'" . $this->request->data['Company']['email'] . "',"
                    . "'".md5($password)."',"
                    . "'" . $this->request->data['Company']['phone'] . "',"
                    . "'" . $this->request->data['Company']['phone_2'] . "',"
                    . "'" . $this->request->data['Company']['address'] . "',"
                    . "'" . $this->request->data['Company']['complement'] . "',"
                    . "'" . $this->request->data['Company']['city'] . "',"
                    . "'" . $this->request->data['Company']['uf'] . "',"
                    . "'" . $this->request->data['Company']['district'] . "',"
                    . "'" . $this->request->data['Company']['number'] . "',"
                    . "'" . $this->request->data['Company']['cep'] . "',"
                    . "'" . $this->request->data['Company']['responsible_name'] . "',"
                    . "'" . $this->request->data['Company']['responsible_cpf'] . "',"
                    . "'" . $this->request->data['Company']['responsible_email'] . "',"
                    . "'" . $this->request->data['Company']['responsible_phone'] . "',"
                    . "'" . $this->request->data['Company']['responsible_phone_2'] . "',"
                    . "'" . $this->request->data['Company']['responsible_cell'] . "',"
                    . "'logo',"
                    . "'ACTIVE',"
                    . "0,"
                    . "0,"
                    . "0,"
                    . "'0000-00-00 00:00:00'"
                    . ");"; 

           $CompanysParam = array(
                'User' => array(
                    'query' => $sql
                )
            );
			
           $retorno = $this->AccentialApi->urlRequestToGetData('users', 'query', $CompanysParam); 
		
		
			$selectSql = "select * from companies where cnpj LIKE '".$this->request->data['Company']['cnpj']."';";
			$SelCompanyParam = array(
                'User' => array(
                    'query' => $selectSql
                )
            );
			
            $retornoSelect = $this->AccentialApi->urlRequestToGetData('users', 'query', $SelCompanyParam); 
			
			// CRIANDO DIRETORIOS PARA COMPANY
			$this->AccentialApi->createCompanyDir($retornoSelect[0]['companies']['id']);
			
			//	ENVIANDO EMAIL COM USUARIO E SENHA
		   $this->sendEmailNewUser($this->request->data['Company']['fancy_name'], $this->request->data['Company']['email'], $password);
		
			//	UPDATING LOGO
			$logo = $this->saveCompanyLogo($this->request->data['Company']['logo'], $retornoSelect[0]['companies']['id']);
			$LogoSql = "UPDATE companies SET logo = '".$logo."' WHERE id = ".$retornoSelect[0]['companies']['id'].";";
			$UpdCompanyParam = array(
                'User' => array(
                    'query' => $LogoSql
                )
            );
			
           $reti =  $this->AccentialApi->urlRequestToGetData('users', 'query', $UpdCompanyParam); 

			$this->redirect(array('controller' => 'login', 'action' => 'index'));
	}
	
	public function selectCompany(){
		$this->layout = "";
		
		$sql = "SELECT *  from classes inner join subclasses on  subclasses.classe_id = classes.id;";
		 $CompanysParam = array(
                'User' => array(
                    'query' => $sql
                )
            );
			
            $retorno = $this->AccentialApi->urlRequestToGetData('users', 'query', $CompanysParam); 
			
			echo print_r($retorno);
		
	}
	
	public function saveCompanyLogo($logo, $companyId){
		
		 $this->autoRender = false;
		 $url = "jezzyuploads/company-".$companyId."/config";
						 $offersExtraPhotos = $this->AccentialApi->uploadAnyPhotoCompany($url ,$logo, $companyId);
                       // $saveDatabase = $this->saveImageUrl($this->request['data']['offerId'], $offersExtraPhotos, true);
                       
        return $offersExtraPhotos;	
	}
	
	public function sendEmailNewUser($fancyName, $companyemail, $pass){
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
 $mail->FromName = "Contato - Jezzy"; // Seu nome
 
  $mail->AddAddress("{$companyemail}");
  
  // Define os dados técnicos da Mensagem
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
 $mail->CharSet = 'iso-8859-1'; // Charset da mensagem (opcional)

// Define a mensagem (Texto e Assunto)
// =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
 $mail->Subject  = "Bem-Vindo ao Jezzy Empresas"; // Assunto da mensagem
 $mail->Body = "Ola, {$fancyName} seja bem-vindo ao Jezzy Empresas, seus dados de login sao: <br/> Usuário: {$companyemail} <br/> Senha: {$pass} <br/><br/> <b>Boas Compras!</b>";
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

function geraSenha($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false)
{
// Caracteres de cada tipo
$lmin = 'abcdefghijklmnopqrstuvwxyz';
$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
$num = '1234567890';
$simb = '!@#$%*-';
// Variáveis internas
$retorno = '';
$caracteres = '';
// Agrupamos todos os caracteres que poderão ser utilizados
$caracteres .= $lmin;
if ($maiusculas) $caracteres .= $lmai;
if ($numeros) $caracteres .= $num;
if ($simbolos) $caracteres .= $simb;
// Calculamos o total de caracteres possíveis
$len = strlen($caracteres);
for ($n = 1; $n <= $tamanho; $n++) {
// Criamos um número aleatório de 1 até $len para pegar um dos caracteres
$rand = mt_rand(1, $len);
// Concatenamos um dos caracteres na variável $retorno
$retorno .= $caracteres[$rand-1];
}
return $retorno;
}

public function searchAddressByZipcode(){
 $this->autoRender = false;
	  $cURL = curl_init("http://cep.correiocontrol.com.br/{$this->request->data['cep']}.json");
      curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
      $resultado = curl_exec($cURL);
      curl_close($cURL);
      echo $resultado;
}

}

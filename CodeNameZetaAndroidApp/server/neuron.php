<?php header('Access-Control-Allow-Origin: *');?>
<?php
	require_once('config.php');
	require_once('mail.php');

	//Drop Point - Handle Requests from Synapse (Defaults to no_trigger_received & neuron)
    $userType = $_POST["usertype"] ?? 'neuron';
	$trigger = $_POST["trigger"] ?? 'no_trigger_received';
	$requestInstance = new $userType();
	$requestInstance->$trigger();

	//Neuron Class
	class neuron
	{
		private $sendresponse_url;
        private $conn;

		public function __construct()
		{
			$this->sendresponse_url = "return.php?data=";
		}

		function no_trigger_received()
		{
            echo 'no_trigger_received';
			//$this->sendResponse('no_trigger_received');
            $this->callSPRead('e_getVcode',25,'Ve','dsadsa');
		}

		function sendResponse($data)
		{
            echo 'sendResponse';
			Header("Location: $this->sendresponse_url".$data);
		}

		function vcode()
		{
			$vcodeNum = mt_rand(1000, 9999);
			return $vcodeNum;
		}

		function sendmail($email, $subject, $message)
		{
			if($email!=NULL && $subject!=NULL && $message!=NULL)
			{
				$addressx = $email;
				$subjectx = $subject;
				$bodyx = $message;
				
				$headersx = 'MIME-Version: 1.0' . "\r\n" . 
				'Content-type: text/html; charset=iso-8859-1' . "\r\n".
				'From: info@technostan.com' . "\r\n" . 
				'Reply-To: info@technostan.com' . "\r\n" . 
				'X-Mailer: PHP/' . phpversion(); 
				
				//Taking Actions.. Reverting to HTML
				if(mail($addressx, $subjectx, $bodyx, $headersx)) {
					//Throwback to HTML
					$m = "success";
				}
				else
				{
					$m = "fail mail";
				}
			}
			else
			{
				$m = "fail";
			}
			
		}

        function callSPIUD()
        {
            try
            {
				$this->conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
				if ($this->conn->connect_error)
				{
					die("Connection failed: " . $this->conn->connect_error);
				}

                $ct = func_num_args();
                $param = '';
                for ($i=1; $i<$ct; $i++)
                {
                    if($i==$ct-1)
                        $param .= "'" . func_get_arg($i) . "'";
                    else
                        $param .= "'" . func_get_arg($i) . "',";
                }
                $qr = mysqli_query($this->conn,"CALL ". func_get_arg(0) . "($param)");
                $this->conn->close();
                return $qr;
            }
            catch (MySQLException $e)
            {
                $this->logError($GLOBALS['userType'],$GLOBALS['trigger'],$e->getMessage());
            }
            catch (Exception $e)
            {
                $this->logError($GLOBALS['userType'],$GLOBALS['trigger'],$e->getMessage());
            }
        }

        function callSPRead()
        {
            try
            {
                echo 'spread';
				$this->conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
				if ($this->conn->connect_error)
				{
					die("Connection failed: " . $this->conn->connect_error);
				}

                $ct = func_num_args();
                $param = '';
                for ($i=1; $i<$ct; $i++)
                {
                    if($i==$ct-1)
                        $param .= "'" . func_get_arg($i) . "'";
                    else
                        $param .= "'" . func_get_arg($i) . "',";
                }
                $qr = $this->conn->query("CALL ". func_get_arg(0) . "($param)");
				$this->conn->close();
                return $qr;
            }
            catch (MySQLException $e)
            {
                echo 'sqlexception';
                $this->logError($GLOBALS['userType'],$GLOBALS['trigger'],$e->getMessage());
            }
            catch (Exception $e)
            {
                echo 'exception';
                $this->logError($GLOBALS['userType'],$GLOBALS['trigger'],$e->getMessage());
            }
        }

        function logError($userType,$trigger,$errorDescription)
        {
            echo 'logError';
            $result = $this->callSPIUD('sys_logError',$userType,$trigger,$errorDescription);
            $this->sendResponse('server_error');
        }
	}
/*------------------------------------END USER--------------------------------------------------*/
	class enduser extends neuron
	{
		public function __construct()
		{
            parent::__construct();
		}
		function register()
		{
			if(!empty($_POST))
			{
				$subject = "UniPortal Account Verification";
				$message="<title></title><div><span style=font-size:14px>Hello,</span></div><div> </div><div style=text-align:justify><span style=font-size:14px>This email address has been signed up to UniPortal. We would like to verify that you are the person who has signed up for the same, therefore please verify yourself by entering the following 4-digit verification code in the website or application:</span></div><div> </div><div><span style=font-size:14px>Verification Code: ".parent::vcode()."</span></div><div> </div><div><span style=font-size:14px>Regards,</span></div><div><span style=font-size:14px>UniPortal</span></div><div><span style=font-size:14px>Team HexAxle</span></div>";
				$enc_email = base64_encode(str_rot13($_POST['email']));
				$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
				$result1 = parent::callSPRead('e_emailExist',$enc_email);
				if($result1->num_rows != 0)
				{
					if ($row = $result1->fetch_object())
					{
						$emailExist = $row->user_hashemail;
						$res->response = "emailExist";
						$res->user_email = $emailExist;
						$jsonRes = json_encode($res);
						parent::sendResponse($jsonRes);
					}
				}
				else
				{
					$result = parent::callSPRead('e_registerUser',$enc_email,$password,$_POST['name'],'enduser',$_POST['loginType']);
					while ($row = $result->fetch_object())
					{
						$user_id = $row->user_id;
					}
					parent::sendmail($_POST['email'],$subject,$message);
					$res->response = "success";
					$res->user_id = $user_id;
					$jsonRes = json_encode($res);
					parent::sendResponse($jsonRes);
				}
			}
			else
			{
				$res->response = "insufficient_data";
				$jsonRes = json_encode($res);
				parent::sendResponse($jsonRes);
			}
		}
	}

	class admin extends neuron
	{
		public function __construct()
		{
            parent::__construct();
		}
	}

	class proposer extends neuron
	{
		public function __construct()
		{
		    parent::__construct();
		}
	}

	class portal extends neuron
	{
		public function __construct()
		{
			parent::__construct();
		}

        function trial()
        {
            $result = parent::callSPRead('e_selectall',24);
            while ($row = $result->fetch_object())
            {
                print_r($row->value);
            }
        }
	}
?>
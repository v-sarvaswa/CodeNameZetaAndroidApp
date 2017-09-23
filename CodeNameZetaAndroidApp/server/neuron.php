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
            $res[] = array();
            $res['response'] = "no_trigger_received";
            $this->sendResponse(json_encode($res));
		}

		function sendResponse($data)
		{
			Header("Location: $this->sendresponse_url".$data);
		}

		function vcode()
		{
			$vcodeNum = mt_rand(1000, 9999);
			return $vcodeNum;
		}

		function sendmail($email, $subject, $message)
		{
			$addressx = $email;
			$subjectx = $subject;
			$bodyx = $message;
            $attempt = 0;

			$headersx = 'MIME-Version: 1.0' . "\r\n" .
			'Content-type: text/html; charset=iso-8859-1' . "\r\n".
			'From: info@technostan.com' . "\r\n" .
			'Reply-To: info@technostan.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

            while($attempt<2)
            {
                if(!mail($addressx, $subjectx, $bodyx, $headersx))
                {
                    $attempt++;
                }
                else
                {
                    $attempt=2;
                }
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
                if(!empty($qr))
                {
                    if($qr->num_rows>0)
                    {
                        return $qr;
                    }
                    else
                    {
                        $this->logError($GLOBALS['userType'],$GLOBALS['trigger'],"Number of Rows Returned 0 by ".func_get_arg(0));
                    }
                }
                else
                {
                    $this->logError($GLOBALS['userType'],$GLOBALS['trigger'],"Empty Result Returned by ".func_get_arg(0));
                }
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

        function logError($userType,$trigger,$errorDescription)
        {
            $result = $this->callSPIUD('sys_logError',$userType,$trigger,$errorDescription);
            $res[] = array();
            $res['response'] = "server_error";
            $this->sendResponse(json_encode($res));
            exit();
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
            $res[] = array();

			if(!empty($_POST))
			{
                $enc_email = base64_encode(str_rot13(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)));
				$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

				$result = parent::callSPRead('e_registerUser',$enc_email,$password,filter_var($_POST['name'], FILTER_SANITIZE_ENCODED),$_POST['loginType']);

                while ($row = $result->fetch_object())
                {
                    $output = $row->output;
                }

                if($output == 'normalexist')
                {
                    $res['response'] = "normal_exist";
                }
                else if($output == 'ssoexist')
                {
                    $res['response'] = "sso_exist";
                }
                else
                {
                    $res['response'] = "success";
                    $res['user_id'] = $output;
                }
			}
			else
			{
				$res['response'] = "insufficient_data";
			}

            parent::sendResponse(json_encode($res));
		}

        function sendVCode()
        {
            $res[] = array();

            if(!empty($_POST))
			{
                $email = $_POST['email'];
                $user_id = $_POST['user_id'];
                $vcode = parent::vcode();

                $result = parent::callSPRead('e_setVcode',$user_id,$vcode);
				while ($row = $result->fetch_object())
				{
					$output = $row->output;
				}

                if($output=="updated")
                {
                    $subject = "UniPortal Account Verification";
                    $message="<title></title><div><span style=font-size:14px>Hello,</span></div><div> </div><div style=text-align:justify><span style=font-size:14px>This email address has been signed up to UniPortal. We would like to verify that you are the person who has signed up for the same, therefore please verify yourself by entering the following 4-digit verification code in the website or application:</span></div><div> </div><div><span style=font-size:14px>Verification Code: ".$vcode."</span></div><div> </div><div><span style=font-size:14px>Regards,</span></div><div><span style=font-size:14px>UniPortal</span></div><div><span style=font-size:14px>Team HexAxle</span></div>";
                    parent::sendmail($email,$subject,$message);
                    $res['response'] = "success";
                }
                else
                {
                    $res['response'] = "fail_re_attempt";
                }
            }
            else
            {
                $res['response'] = "insufficient_data";
            }

            parent::sendResponse(json_encode($res));
        }

        function verifyVCode()
        {
            $res[] = array();

            if(!empty($_POST))
			{
                $result = parent::callSPRead('e_getVcode',$_POST['userID']);
				while ($row = $result->fetch_object())
				{
					//$output = $row->;
				}
            }
            else
            {
                $res['response'] = "insufficient_data";
				parent::sendResponse(json_encode($res));
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
	}
?>
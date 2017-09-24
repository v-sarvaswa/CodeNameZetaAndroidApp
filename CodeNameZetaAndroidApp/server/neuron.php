<?php header('Access-Control-Allow-Origin: *');?>
<?php
	require_once('config.php');
	require_once('mail.php');

	//Drop Point - Handle Requests from Synapse (Defaults to no_trigger_received & neuron)
    $userType = $_POST["usertype"] ?? 'neuron';//neuron
	$trigger = $_POST["trigger"] ?? 'no_trigger_received';//no_trigger_received
	$requestInstance = new $userType();

	if(method_exists($requestInstance,$trigger))
    {
        $requestInstance->$trigger();
    }
    else
    {
        $errInstance = new neuron();
        $errInstance->logError($userType,'Not Applicable',"Method does not exist: ".$trigger);
    }

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

		function blank()
		{
		
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
                $email = $_POST['user_email'];
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
				$user_id = $_POST['user_id'];
				$user_passcode = $_POST['user_passcode'];

                $result = parent::callSPRead('e_getVcode',$user_id);
				while ($row = $result->fetch_object())
				{
					$output = $row->output;
				}
				if($user_passcode == $output)
				{
					$result = parent::callSPRead('e_setStage',$user_id,'2');
					while ($row = $result->fetch_object())
					{
						$output = $row->output;
					}
					if($output == "success")
					{
						$res['response'] = "success";
					}
				}
				else
				{
					$res['response'] = "failed";
				}
            }
            else
            {
                $res['response'] = "insufficient_data";
            }

			parent::sendResponse(json_encode($res));
        }

		function updateEmailPostVerification()
		{
			$res[] = array();

            if(!empty($_POST))
			{
				
			}
            else
            {
                $res['response'] = "insufficient_data";
            }

			parent::sendResponse(json_encode($res));
		}

		function updateEmailPreVerification()
		{
			$res[] = array();

            if(!empty($_POST))
			{
				$user_id = $_POST['user_id'];
				$user_email = $_POST['user_email'];
				$enc_email = base64_encode(str_rot13(filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL)));

				$result = parent::callSPRead('e_emailExist',$enc_email);
				while ($row = $result->fetch_object())
				{
					$output = $row->output;
				}
				if($output == "yes")
				{
					$res['response'] = "email_exist";
				}
				if($output == "no")
				{
					$vcode = parent::vcode();

					$result = parent::callSPRead('e_changeEmail',$user_id,$enc_email);
					while ($row = $result->fetch_object())
					{
						$output = $row->output;
					}
					if($output == "success")
					{
						$result = parent::callSPRead('e_setVcode',$user_id,$vcode);
						while ($row = $result->fetch_object())
						{
							$output = $row->output;
						}
						if($output=="updated")
						{
							$subject = "UniPortal Account Verification";
							$message="<title></title><div><span style=font-size:14px>Hello,</span></div><div> </div><div style=text-align:justify><span style=font-size:14px>This email address has been signed up to UniPortal. We would like to verify that you are the person who has signed up for the same, therefore please verify yourself by entering the following 4-digit verification code in the website or application:</span></div><div> </div><div><span style=font-size:14px>Verification Code: ".$vcode."</span></div><div> </div><div><span style=font-size:14px>Regards,</span></div><div><span style=font-size:14px>UniPortal</span></div><div><span style=font-size:14px>Team HexAxle</span></div>";
							parent::sendmail($user_email,$subject,$message);
							$res['response'] = "success";
						}
						else
						{
							$res['response'] = "fail_re_attempt";
						}
					}
				}
			}
            else
            {
                $res['response'] = "insufficient_data";
            }

			parent::sendResponse(json_encode($res));
		}

		function genderYob()
		{
			$res[] = array();

            if(!empty($_POST))
			{
				$user_id = $_POST['user_id'];
				$user_gender = $_POST['user_gender'];
				$user_yob = $_POST['user_yob'];

				$result = parent::callSPRead('e_setUserGenderYob',$user_id,$user_gender,$user_yob);
				while ($row = $result->fetch_object())
				{
					$output = $row->output;
				}
				if($output == "success")
				{
					$result = parent::callSPRead('e_setStage',$user_id,'3');
					while ($row = $result->fetch_object())
					{
						$output = $row->output;
					}
					if($output == "success")
					{
						$res['response'] = "success";
					}
				}
			}
            else
            {
                $res['response'] = "insufficient_data";
            }

			parent::sendResponse(json_encode($res));
		}

		function professionLocation()
		{
			$res[] = array();

            if(!empty($_POST))
			{
				$user_id = $_POST['user_id'];
				$user_profession = $_POST['user_profession'];
				$user_location = $_POST['user_location'];
				$loc = array();
				$loc = explode(",",$user_location);
				$user_city = $loc[0];
				$user_state = $loc[1];
				$user_country = $loc[2];
				
				$result = parent::callSPRead('e_setuserProfessionLocation',$user_id,$user_profession,$user_city,$user_state,$user_country);
				while ($row = $result->fetch_object())
				{
					$output = $row->output;
				}
				if($output == "success")
				{
					$result = parent::callSPRead('e_setStage',$user_id,'4');
					while ($row = $result->fetch_object())
					{
						$output = $row->output;
					}
					if($output == "success")
					{
						$res['response'] = "success";
					}
				}
			}
            else
            {
                $res['response'] = "insufficient_data";
            }

			parent::sendResponse(json_encode($res));
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
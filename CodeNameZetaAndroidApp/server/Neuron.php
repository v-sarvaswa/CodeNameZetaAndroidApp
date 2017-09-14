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
            $this->conn = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
            if ($this->conn->connect_error)
            {
                die("Connection failed: " . $this->conn->connect_error);
            }
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

        function callSPIUD()
        {
            try
            {
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
				$result = parent::callSPRead('e_registerUser',$_POST['email'],$_POST['password'],$_POST['name'],'enduser',$_POST['loginType']);
				//print_r($result);
				while ($row = $result->fetch_object())
				{
					$res = $row->user_id;
				}
				parent::sendResponse("success".$res);
			}
			else
			{
				parent::sendResponse('insufficient_data');
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
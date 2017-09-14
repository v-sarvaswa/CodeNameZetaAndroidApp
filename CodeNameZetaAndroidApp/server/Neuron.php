<?php header('Access-Control-Allow-Origin: *');?>
<?php
	require_once('config.php');
	require_once('mail.php');

	//Drop Point - Handle Requests from Synapse (Defaults to no_trigger_received & neuron)
    $usertype = $_POST["usertype"] ?? 'neuron';
	$trigger = $_POST["trigger"] ?? 'no_trigger_received';
	$request_instance = new $usertype();
	$request_instance->$trigger();

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
			sendResponse('no_trigger_received');
		}

		function sendResponse($data)
		{
			Header("Location: $this->sendresponse_url".$data);
		}

        function callSPIUD()
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

        function callSPRead()
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
            $qr = $this->conn->query("CALL ". func_get_arg(0) . "($param)");
            return $qr;
        }
	}

	class enduser extends neuron
	{
		public function __construct()
		{
            parent::__construct();
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
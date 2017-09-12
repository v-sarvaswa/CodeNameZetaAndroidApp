<?php header('Access-Control-Allow-Origin: *');?>
<?php 
	require_once('config.php');
	require_once('mail.php');

	//Drop Point - Handle Requests from Synapse (Defaults to no_trigger_received & neuron)
	$trigger = $_POST["trigger"] ?? 'no_trigger_received';
	$usertype = $_POST["usertype"] ?? 'neuron';
	$request_instance = new $usertype();
	$request_instance->$trigger();
	
	//Neuron Class
	class neuron
	{
		private $sendresponse_url;

		function __construct()
		{
			$sendresponse_url = "return.php?data=";
		}

		function no_trigger_received()
		{
			sendresponse('no_trigger_received');
		}
		
		function sendresponse($data)
		{
			Header("Location: $this->sendresponse_url".$data);
		}
	}
	class enduser extends neuron
	{
		function __construct()
		{
		
		}
	}
	class admin extends neuron
	{
		function __construct()
		{
		
		}
	}
	class proposer extends neuron
	{
		function __construct()
		{
		
		}
	}
	class portal extends neuron
	{
		function __construct()
		{
		
		}
	}
	
?>
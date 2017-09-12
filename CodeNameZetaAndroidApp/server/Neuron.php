<?php header('Access-Control-Allow-Origin: *');?>
<?php 
	require_once('config.php');
	require_once('mail.php');

	//Drop Point (Handle Requests from Synapse)
	$trigger = $_POST["trigger"] ?? 'no_trigger_received';
	$usertype = $_POST["usertype"] ?? 'neuron';
	$request_instance = new $usertype();
	$request_instance->$trigger();
	
	//Neuron Class
	class neuron
	{
		function __construct()
		{
		
		}

		function no_trigger_received
		{
			echo "No trigger received";
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
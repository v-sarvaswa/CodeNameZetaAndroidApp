<?php header('Access-Control-Allow-Origin: *');?>
<?php 
	require_once('config.php');
	require_once('mail.php');
	
	//Method name 
	$method = $_POST["method"];
	$method();
	/*--------------------------------Sign up---------------------------------*/
	function user_register()
	{
	
		$vresult=0;
		$user_id = "";
		$parameters = $_POST["parameters"];
		$email = $_POST["email"];

		$len = sizeof($parameters);

		echo $len;

		
		$password = $_POST["password"];
		$user_type = $_POST["user_type"];
		$login_type = $_POST["login_type"];
		$email_exist = "";
		$plain_email = $email;
		
		//Check if email id is registered
		$enc_email = base64_encode(str_rot13($email));
		$result = $connect->query("CALL e_emailExist('$enc_email')");				
		if($result->num_rows > 0) 
		{
			$email_exist = 'yes';
			$m = "e-mail id exists";
			$url="return.php?data=".$m."";
			Header("Location: $url");
		}
		else
		{
			$email_exist = 'no';
		}
		
		mysqli_free_result($result);   
		mysqli_next_result($connect);
		
		//Server Validations
		if(preg_match("/^[a-zA-Z\s]+$/",$name))
		{
			$vresult++;/////////////////////count = 1
		}
		else
		{
			$m = "invalid name";
			$url="return.php?data=".$m."";
			Header("Location: $url");
		}
		
		if(strlen($password)>=6)
		{
			$vresult++;/////////////////////////count = 2
		}
		else
		{
			$m = "invalid password";
			$url="return.php?data=".$m."";
			Header("Location: $url");
		}
		
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
		{
			$vresult++;////////////////////////////count = 3
		}
		else
		{
			$m = "invalid email address";
			$url="return.php?data=".$m."";
			Header("Location: $url");
		}

		if($password!=NULL && $name!=NULL && $email!=NULL && $user_type!=NULL && $login_type!=NULL)
		{
			if($vresult==3)
			{
				if($email_exist=="no")
				{
					//4-Digit Random Number
					$four_digit_random_number = mt_rand(1000, 9999);
					
					//Encrypt E-Mail ID
					$email = base64_encode(str_rot13($email));
					//Hash Password
					$password = password_hash($password, PASSWORD_DEFAULT);
					
					//Registering Account
					$sql=mysqli_query($connect,"CALL e_registerUser('$email','$password','$name','$loc_city','$user_type','$login_type','$four_digit_random_number')");
					$sql = $connect->query('SELECT @user_id'); 
					$result = $sql->fetch_assoc();
					$user_id = $result['@user_id'];
					
					//Sending E-Mail
					$subject = "UniPortal Account Verification";
					$message="<title></title><div><span style=font-size:14px>Hello,</span></div><div> </div><div style=text-align:justify><span style=font-size:14px>This email address has been signed up to UniPortal. We would like to verify that you are the person who has signed up for the same, therefore please verify yourself by entering the following 4-digit verification code in the website or application:</span></div><div> </div><div><span style=font-size:14px>Verification Code: ".$four_digit_random_number."</span></div><div> </div><div><span style=font-size:14px>Regards,</span></div><div><span style=font-size:14px>UniPortal</span></div><div><span style=font-size:14px>Team HexAxle</span></div>";
					$res = sendmail($plain_email, $subject, $message);
					
					/*/Sending Push Notification
					$pb->AliasData(1, $token, $email);
					$pb->setAlias();
					$pb->Alias($email);
					$pb->Alert("Your device is registered successfully!");
					$pb->Platform(array("0","1"));
					$pb->Push();*/
					
					if($sql)
					{
						$m = "success".$user_id;
						$url="return.php?data=".$m."";
						Header("Location: $url");
					}
					else
					{
						$m = "fail";
						$url="return.php?data=".$m."";
						Header("Location: $url");
					}
					
				}
				else
				{
					$m = "e-mail id exists";
					$url="return.php?data=".$m."";
					Header("Location: $url");
				}
			}
		}
		else
		{
			$m = "required fields cannot be empty";
			$url="return.php?data=".$m."";
			Header("Location: $url");
		}
	}
?>
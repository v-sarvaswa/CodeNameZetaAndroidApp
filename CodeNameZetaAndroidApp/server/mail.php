<?php 
header('Access-Control-Allow-Origin: *');  
 ?>	
<html>
	<body>
	<?php
        
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
					$url="return.php?data=".$m."";
					Header("Location: $url");
				}
				else
				{
					$m = "fail mail";
					$url="return.php?data=".$m."";
					Header("Location: $url");
				}
			}
			else
			{
				$m = "fail";
				$url="return.php?data=".$m."";
				Header("Location: $url");
			}
		}
		
	?>
	</body>
</html>
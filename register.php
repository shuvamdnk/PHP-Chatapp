<!--
//register.php
!-->

<?php

include('database_connection.php');

session_start();

$message = '';

if(isset($_SESSION['user_id']))
{
	header('location:index.php');
}

if(isset($_POST["register"]))
{
	$username = trim($_POST["username"]);
	$password = trim($_POST["password"]);
	$check_query = "
	SELECT * FROM login 
	WHERE username = :username
	";
	$statement = $connect->prepare($check_query);
	$check_data = array(
		':username'		=>	$username
	);
	if($statement->execute($check_data))	
	{
		if($statement->rowCount() > 0)
		{
			$message .= '<p><label class="badge bg-danger col-12 shadow-sm">Username already taken</label></p>';
		}
		else
		{
			if(empty($username))
			{
				$message .= '<p><label class="badge bg-danger col-12 shadow-sm">Username is required</label></p>';
			}
			if(empty($password))
			{
				$message .= '<p><label class="badge bg-danger col-12 shadow-sm">Password is required</label></p>';
			}
			else
			{
				if($password != $_POST['confirm_password'])
				{
					$message .= '<p><label class="badge bg-danger col-12 shadow-sm">Password not match</label></p>';
				}
			}
			if($message == '')
			{
				$data = array(
					':username'		=>	$username,
					':password'		=>	password_hash($password, PASSWORD_DEFAULT)
				);

				$query = "
				INSERT INTO login 
				(username, password) 
				VALUES (:username, :password)
				";
				$statement = $connect->prepare($query);
				if($statement->execute($data))
				{
					$message = "<label class='badge bg-success col-12 shadow-sm'>Registration Completed</label>";
				}
			}
		}
	}
}

?>

<html>  
    <head>  
        <title>Chat</title>  
         <meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
		<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  		<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  			<style>
  			.container .shadow-sm{
                 outline: none;
                 border: none;
  			}
  		</style>
    </head>  
    <body>  
    	<nav class="navbar navbar-light bg-light shadow">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
    Chatyfy
    </a>
  </div>
</nav>
        <div class="container">
			<br />
			
			<div class="row">
  				<div class="col-md-6 shadow">
  				<h4>Register</h4>
                    <div class="col-12 mb-3">
					<span><?php echo $message; ?>
					</span>
                    </div>
					<form method="post">
						
						<div class="form-group">
							<input type="text" name="username" class="form-control shadow-sm mb-4 bg-light" placeholder="Username" />
						</div>
						<div class="form-group">
							<input type="password" name="password" class="form-control shadow-sm mb-4 bg-light" placeholder="Password" />
						</div>
						<div class="form-group">
							<input type="password" name="confirm_password" class="form-control shadow-sm mb-3 bg-light" placeholder="Re-enter password" />
						</div>
						<div class="form-group">
							<input type="submit" name="register" class="btn btn-outline-success shadow-sm mb-4" style="float: right;" value="Register" />
						</div>
						<div align="center"  class="my-4">
							<i>Already have an account <a href="login.php">Login</a></i>
						</div>
					</form>
					</div>
				</div>
			</div>
    </body>  
</html>

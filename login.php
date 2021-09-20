<!--
//login.php
!-->

<?php

include('database_connection.php');

session_start();

$message = '';

if(isset($_SESSION['user_id']))
{
	header('location:index.php');
}

if(isset($_POST['login']))
{
	$query = "
		SELECT * FROM login 
  		WHERE username = :username
	";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
			':username' => $_POST["username"]
		)
	);	
	$count = $statement->rowCount();
	if($count > 0)
	{
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			if(password_verify($_POST["password"], $row["password"]))
			{
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['username'] = $row['username'];
				$sub_query = "
				INSERT INTO login_details 
	     		(user_id) 
	     		VALUES ('".$row['user_id']."')
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute();
				$_SESSION['login_details_id'] = $connect->lastInsertId();
				header('location:index.php');
			}
			else
			{
				$message = '<label>Wrong Password</label>';
			}
		}
	}
	else
	{
		$message = '<label>Wrong Username</labe>';
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
  					<h4>Login</h4>
                    <div class="col-12" style="height: 30px;">
					<p class="badge bg-danger col-12 shadow-sm"><?php echo $message; ?></p>
                    </div>
					<form method="post">
						<div class="form-group">
						
							<input type="text" name="username" class="form-control shadow-sm mb-4 bg-light" required placeholder="Username" />
						</div>
						<div class="form-group">
						
							<input type="password" name="password" class="form-control shadow-sm mb-3 bg-light" required  placeholder="Password" />
						</div>
						<div class="form-group">
							<input type="submit" name="login" class="btn btn-outline-success shadow-sm mb-4" style="float: right;" value="Login" />
						</div>
						<div align="center" class="my-4">
						 <i>Create an account <a href="register.php">Register</a></i>
						</div>
					</form>
					</div>
			</div>
		</div>

    </body>  
</html>
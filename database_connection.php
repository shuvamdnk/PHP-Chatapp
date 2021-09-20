<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
<?php

//database_connection.php

$connect = new PDO("mysql:host=localhost;dbname=chat;charset=utf8mb4", "root", "");

date_default_timezone_set('Asia/Kolkata');

function fetch_user_last_activity($user_id, $connect)
{
	$query = "
	SELECT * FROM login_details 
	WHERE user_id = '$user_id' 
	ORDER BY last_activity DESC 
	LIMIT 1
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['last_activity'];
	}
}

function fetch_user_chat_history($from_user_id, $to_user_id, $connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE (from_user_id = '".$from_user_id."' 
	AND to_user_id = '".$to_user_id."') 
	OR (from_user_id = '".$to_user_id."' 
	AND to_user_id = '".$from_user_id."') 
	
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<ul class="list-unstyled">';
	foreach($result as $row)
	{
		$user_name = '';
		$dynamic_background = '';
		$chat_message = '';
		if($row["from_user_id"] == $from_user_id)
		{
			if($row["status"] == '2')
			{
				$chat_message = '<em style="color:green;"><b>You removed this message.</b></em>';
				$user_name = '<b class="text-success">You</b>';
			}
			else
			{
				$chat_message = $row['chat_message'];
				$user_name = '<button type="button" style="outline:none; border:none; border-radius:50%; padding:4px 5px 4px 5px;" class="btn btn-outline-danger shadow-sm btn remove_chat" id="'.$row['chat_message_id'].'"><i class="fas fa-trash-alt"></i></button>&nbsp;<b class="text-success">You</b>';
			}
			

			$dynamic_background = 'background-color:#E6FFDB;';
		}
		else
		{
			if($row["status"] == '2')
			{
				$chat_message = '<em style="color:red;"><b>This message has been removed</b></em>';
			}
			else
			{
				$chat_message = $row["chat_message"];
			}
			$user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
			$dynamic_background = 'background-color:#FFEADB;';
		}
		$output .= '
		<li class="mb-2 shadow-sm" style="padding:0px 6px 0px 6px; margin:0px; width:auto; height:auto; border-radius:10px;'.$dynamic_background.'">
			<p>'.$user_name.' &nbsp;&nbsp; <small><em>'.$row['timestamp'].'</em></small> <br> '.$chat_message.'
			</p>
		</li>
		';
	}
	$output .= '</ul>';
	$query = "
	UPDATE chat_message 
	SET status = '0' 
	WHERE from_user_id = '".$to_user_id."' 
	AND to_user_id = '".$from_user_id."' 
	AND status = '1'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $output;
}

function get_user_name($user_id, $connect)
{
	$query = "SELECT username FROM login WHERE user_id = '$user_id'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['username'];
	}
}

function count_unseen_message($from_user_id, $to_user_id, $connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE from_user_id = '$from_user_id' 
	AND to_user_id = '$to_user_id' 
	AND status = '1'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$count = $statement->rowCount();
	$output = '';
	if($count > 0)
	{
		$output = '<span class="badge rounded-pill bg-success">'.$count.'</span>';
	}
	return $output;
}

function fetch_is_type_status($user_id, $connect)
{
	$query = "
	SELECT is_type FROM login_details 
	WHERE user_id = '".$user_id."' 
	ORDER BY last_activity DESC 
	LIMIT 1
	";	
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		if($row["is_type"] == 'yes')
		{
			$output = ' - <small><em><span class="text-muted">Typing...</span></em></small>';
		}
	}
	return $output;
}

function fetch_group_chat_history($connect)
{
	$query = "
	SELECT * FROM chat_message 
	WHERE to_user_id = '0'  
	
	";

	$statement = $connect->prepare($query);

	$statement->execute();

	$result = $statement->fetchAll();

	$output = '<ul class="list-unstyled shadow-sm">';
	foreach($result as $row)
	{
		$user_name = '';
		$dynamic_background = '';
		$chat_message = '';
		if($row["from_user_id"] == $_SESSION["user_id"])
		{
			if($row["status"] == '2')
			{
				$chat_message = '<em style="color:green;"><b>You removed this message.</b></em>';
				$user_name = '<b class="text-success">You</b>';
			}
			else
			{
				$chat_message = $row["chat_message"];
				$user_name = '<button type="button" style="outline:none; border:none; border-radius:50%; padding:4px 5px 4px 5px;" class="btn btn-outline-danger shadow-sm remove_chat" id="'.$row['chat_message_id'].'"><i class="fas fa-trash-alt"></i></button>&nbsp;<b class="text-success">You</b>';
			}
			
			$dynamic_background = 'background-color:#E6FFDB;';
		}
		else
		{
			if($row["status"] == '2')
			{
				$chat_message = '<em style="color:red;"><b>This message has been removed</b></em>';
			}
			else
			{
				$chat_message = $row["chat_message"];
			}
			$user_name = '<b class="text-danger">'.get_user_name($row['from_user_id'], $connect).'</b>';
			$dynamic_background = 'background-color:#FFEADB;';
		}

		$output .= '

		<li class="mb-1 shadow-sm" style="border:none;padding:0px 6px 0px 6px; margin:0px; width:auto; height:auto; border-radius:10px;'.$dynamic_background.'">
			<p>'.$user_name.'&nbsp;&nbsp;<small><em>'.$row['timestamp'].'</em></small> <br> '.$chat_message.' 
			</p>
		</li>
		';
	}
	$output .= '</ul>';
	return $output;
}


?>
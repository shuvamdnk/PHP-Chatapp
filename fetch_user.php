<?php

//fetch_user.php

include('database_connection.php');

session_start();

$query = "
SELECT * FROM login 
WHERE user_id != '".$_SESSION['user_id']."' 
";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$output = '
<table class="table table-hover">
	<tr class="bg-light shadow">
		<th width="70%">Friends</td>
		<th width="20%"></td>
		<th width="10%"></td>
	</tr>
';

foreach($result as $row)
{
	$status = '';
	$current_timestamp = strtotime(date("Y-m-d H:i:s") . '- 10 second');
	$current_timestamp = date('Y-m-d H:i:s', $current_timestamp);
	$user_last_activity = fetch_user_last_activity($row['user_id'], $connect);
	if($user_last_activity > $current_timestamp)
	{
		$status = '<span class="my-1 badge rounded-pill bg-success">Online</span>';
	}
	else
	{
		$status = '<span class="my-1 badge rounded-pill bg-danger">Offline</span>';
	}
	$output .= '
	<tr class="shadow-sm">
		<td style="padding-top:10px; font-weight:bold;">'.$row['username'].' '.count_unseen_message($row['user_id'], $_SESSION['user_id'], $connect).' '.fetch_is_type_status($row['user_id'], $connect).'</td>

		<td>'.$status.'</td>

		<td><button type="button" class="btn btn-outline-success btn-block shadow-sm start_chat" data-touserid="'.$row['user_id'].'" data-tousername="'.$row['username'].'" id="btn"><i class="far fa-envelope"></i></button></td>

	</tr>
	';
}

$output .= '</table>';

echo $output;

?>
<?php

//upload.php

if(!empty($_FILES))
{
	if(is_uploaded_file($_FILES['uploadFile']['tmp_name']))
	{
		$ext = pathinfo($_FILES['uploadFile']['name'], PATHINFO_EXTENSION);
		$allow_ext = array('jpg', 'png','jpeg');
		if(in_array($ext, $allow_ext))
		{
			$_source_path = $_FILES['uploadFile']['tmp_name'];
			$target_path = 'upload/' . $_FILES['uploadFile']['name'];
			if(move_uploaded_file($_source_path, $target_path))
			{
				echo '<p><img src="'.$target_path.'" class="img-fluid shadow-sm"/></p><br />';
			}
			//echo $ext;
		}
	}
}

?>
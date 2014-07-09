<?php

//�������� �� ������������� ����������
//$cover_path = is_dir('../../..'.$_POST['cover_path']);
$isbn = trim($_POST['isbn']);
$path = '../../../'.$_POST['cover_path'];
$cover_path = is_dir($path);
//scan_dir($path);exit;
//echo $path;
//echo $_POST['cover_path'];exit;
//echo 'ISBN:'. $isbn. "\n";
if($cover_path)
{
	$isbn = $_POST['isbn'];
//	echo $isbn.' cp '.$cover_path;exit;
	$files = scandir($path);//�������� ������ ������ � ���������
	foreach($files as $element)
	{
//		echo $element."\n";
		$f_arr = preg_split('/(\.|_)/U', $element);
//		$str = '';
//		foreach ($f_arr as $key => $var) {
//			$str .= '_'. $key .'-'. $var;
//		}
//		echo $str ."\n";
		//�������� �� ��� �����
//		if ($file_name == $isbn && $file_name != '')

//		print_r($f_arr);echo "\n";
		if (array_search($isbn, $f_arr)!==false)
		{
//			echo "Catch\n";
			//�������� �� ���������� �����
			$img_type = array('jpg','png','bmp','gif');
			$type = strtolower($f_arr[count($f_arr)-1]);
//			echo $type. "\n";
			if (array_search($type,$img_type)!==false)
			{
//				echo 'Catch';
				$path_valid = $_POST['cover_path']."/".$element;
				echo $path_valid;
				exit();
			}
		}				
	}   
}
else
{
	//folder not found
	echo "1";
}

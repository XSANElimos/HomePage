<!doctype html>
<html>
<head>
<meta charset="utf-8" name="viewport" content="width=device-width,height=device-height,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0,user-scalable=no"> 
<title>正在处理</title>
</head>
<body>
<?php

if ($_POST["buttonclick"]=="picsubmit"){
        
        $name = date("m.d");
        $dir = $name;
        $Select = "Img_".$_POST["selectoption"]."/";
        $dir = $Select . $dir;

        if (!file_exists($dir)){
            $oldumask = umask(0);
            mkdir ($dir,0777,true);
            umask($oldumask);
        }
              //echo "文件临时存储的位置: " . $_FILES["file"]["tmp_name"];
        // 允许上传的图片后缀
        $allowedExts = array("jpeg", "jpg", "png");
        $temp = explode(".", $_FILES["file"]["name"]);
        //echo $_FILES["file"]["size"];
        $extension = end($temp);     // 获取文件后缀名
        if ((($_FILES["file"]["type"] == "image/jpeg")
        || ($_FILES["file"]["type"] == "image/jpg")
        || ($_FILES["file"]["type"] == "image/pjpeg")
        || ($_FILES["file"]["type"] == "image/x-png")
        || ($_FILES["file"]["type"] == "image/png"))
        && ($_FILES["file"]["size"] < 10000000)   // 小于 10000 kb
        && in_array($extension, $allowedExts))
        {
            if ($_FILES["file"]["error"] > 0)
            {
                echo "错误：: " . $_FILES["file"]["error"] . "<br>";
            }
            else
            {
                $_FILES["file"]["name"] = $_POST["FileNumber"].' '.$_POST["FileName"].'.'.$extension;
                rename($_FILES["file"]["name"] , $_POST["FileNumber"].' '.$_POST["FileName"].'.'.$extension);
                $fullfilename =  $dir.'/' . $_FILES["file"]["name"]; 
                if (file_exists($dir.'/' . $_FILES["file"]["name"]))
                {
                unlink($dir.'/'.$_FILES["file"]["name"]);
                }
                move_uploaded_file($_FILES["file"]["tmp_name"],$dir.'/'.$_FILES["file"]["name"]);
                echo "文件存储在: " . $dir. "/".$_FILES["file"]["name"];
                echo "<img src='".$dir."/".$_FILES["file"]["name"]."' height=80%/>";
            }
        }

        else
        {
            echo "文件格式不对！！要传图片 大小不能超过5M";
        }
}else if($_POST["buttonclick"]=="showless"){
            function read_all_dir ( $dir ){
            $result = array();
            $handle = opendir($dir);//读资源
            if ($handle){
                while (($file = readdir($handle)) !== false ){
        		if ($file != '.' && $file != '..'){
        		$file=substr($file,6);
        		echo $file." ";
        		$file=explode(' ',$file,-1);
        		$result[]=$file;		
                	}
        	}
        	$result = array_reduce($result, 'array_merge', array());
                closedir($handle);
            }
            return $result;
            }
            $Select = "Img_".$_POST["selectoption"]."/";
            $foldername = $Select.date("m.d");
            $OrderNum = read_all_dir($foldername."/");
            asort($OrderNum);
            $InderNum=array("01","03","05","06","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","25","27","28","29","31","32","33","34","35","36","37","38","39","40");
            
            foreach ($InderNum as $val)
            {
            
                if(in_array($val,$OrderNum))
                {
                    //echo "<br>"."已找到:".$val;
                }
                else
                {
                    echo "<br>"."!!未发现:".$val;
                }
            }
  
    
}else if($_POST["buttonclick"]=="packagedownload"){
        function addFileToZip($path,$zip){
        $handler=opendir($path); //打开当前文件夹由$path指定。
        while(($filename=readdir($handler))!==false){
        if($filename != "." && $filename !=".."){//文件夹文件名字为'.'和‘..'，不要对他们进行操作
          if(is_dir($path."/".$filename)){// 如果读取的某个对象是文件夹，则递归
          addFileToZip($path."/".$filename, $zip);
          }else{ //将文件加入zip对象
          $zip->addFile($path."/".$filename);
          }
         }
         }
         @closedir($path);
        }
        $zip=new ZipArchive();
        $Select = "Img_".$_POST["selectoption"]."/";
        $foldername = date("m.d").".zip";
        $oldtempzip = $Select.date("m.d",strtotime("-1 day")).".zip";
        $tempzippath = $Select.$foldername;
        $packtemp = $Select.date("m.d");
        copy("Img.zip" , $tempzippath);
        
        if($zip->open($tempzippath, ZipArchive::OVERWRITE)=== TRUE){
        addFileToZip($packtemp , $zip); //调用方法，对要打包的根目录进行操作，并将ZipArchive的对象传递给方法
        $zip->close(); //关闭处理的zip文件
        }
        
        ob_end_clean();
        header ( "Content-Type: application/zip" );
        header ( "Content-Transfer-Encoding: Binary" );
        header ( "Content-Length: " . filesize ( $tempzippath ) );
        header ( "Content-Disposition: attachment; filename=\"" . basename ( $tempzippath ) . "\"" );
        
        readfile ( $tempzippath );
        
        @unlink ( $oldtempzip );
    
}


?>
</body>
</html>

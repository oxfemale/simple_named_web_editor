<?php
$UseSUID = 0;
$confpath = "/var/named/";
$backupdirname = "dnsbackups";
$diff = time();
$pwd = getcwd();
$backupdir2day = $pwd."/".$backupdirname."/".$diff;
$backupdir = $pwd."/".$backupdirname."/";

if($UseSUID == 0){
    $reloadScript = "reload.sh";
}else{
    $reloadScript = "suid";
}


function listzones($confpath,$backupdir2day,$diff){
echo "<pre>";
    foreach(scandir($confpath) as $item){
	if (!($item == '.')) {
            if (!($item == '..')) {
        	if(strstr($item,".zone")){
        	    $from = $confpath.$item;
		    $dns = str_replace(".zone", "", $item);
                    echo " :: <a href='?view=1&domain=".$item."'>view</a> : <a href='?name=".$item."&edit=1'>edit</a> :: [".$dns."]\n";
		}
	    }
	}
    }
echo "</pre>";
return 1;
}

//view
if(isset($_GET['view']))
{
    if(!isset($_GET['domain']))
    {
	echo "zone error";
	exit(0);
    }
    $zone = $_GET['domain'];
    $file = $confpath.$zone;
    echo "<a href='?what=1'>restart</a> - <a href='?what=2'>status</a>  - <a href='/'>list</a><hr>";
    if(file_exists($file)){
	$content = file_get_contents($file);
	echo "<pre>".$content."</pre>";	
    }else{
	echo "Zone file not found.";
	exit(0);
    }
    exit(0);
}

//Revert from last backup
if(isset($_GET['revert']))
{
    if(!isset($_GET['zone']))
    {
	echo "zone error";
	exit(0);
    }
    if(!isset($_GET['diff']))
    {
	echo "diff error";
	exit(0);
    }
    $zone = $_GET['zone'];
    $diff = $_GET['diff'];
    $file = $backupdir2day."/".$zone;
    $bad = $confpath.$zone;
    echo "<a href='?what=1'>restart</a> - <a href='?what=2'>status</a>  - <a href='/'>list</a><hr>";
    if(file_exists($file)){
	echo "backup: ".$file."<br>";
	echo "bad config: ".$bad."<br>";
	unlink($bad);
	copy($file,$bad);
	echo "<a href='/'>list</a><hr>";    
	$bind = $pwd."/bind.sh";
	$status = $pwd."/status.sh";
	echo "<pre>".shell_exec($bind)."</pre>";
	echo "<pre>".shell_exec($status)."</pre>";
	$new = file_get_contents($file);
	echo "<pre>".$new."</pre>";	
    }else {
	echo "Backup path not found<br>";
    }
exit(0);    
}


//UPDATE DNS CONFIG
if(isset($_POST['zone']))
{
    $zone = $_POST['zone'];
    echo "<a href='/'>list</a> - ";    

    if(!isset($_POST['diff']))
    {
	echo "diff  error";
	exit(0);
    }
    $diff = $_POST['diff'];
    
    if(!isset($_POST['text']))
    {
	echo "text error";
	exit(0);
    }
    
    $text = $_POST['text'];
    $file = $confpath.$zone;
    if(file_exists($file)){
	echo "updating: ".$zone."<br>";
	//echo "<pre>".$text."</pre>";
	//echo "<br>";
	file_put_contents($file,$text);
	$new = file_get_contents($file);
	echo "<pre>".$new."</pre>";	
	echo "<a href=\"/?diff=".$diff."&zone=".$zone."&revert=1\">Revert changes from backup [".$diff."]</a><br>";
	$bind = $pwd."/bind.sh";
	$status = $pwd."/status.sh";
	echo "Restart service: <pre>".shell_exec($bind)."</pre>";
	echo "Status service: <pre>".shell_exec($status)."</pre>";
    }
    
exit(0);
}


//edit
if(isset($_GET['name']))
{
    echo "<a href='?what=1'>restart</a> - <a href='?what=2'>status</a>  - <a href='/'>list</a><hr>";
    $name = $_GET['name'];
    $file = $backupdir2day."/".$name;

    mkdir($backupdir2day, 0700);
    if(!is_dir($backupdir2day)){
	echo "dirs error";
	exit(0);
    }
    $from = $confpath.$name;
    $to = $backupdir2day."/".$name;
    if (!copy($from, $to)) {
	echo "file backup errors";
	exit(0);
    }
    $serial = "";
    if(file_exists($file))
    {
	$handle = fopen($file, "r");
    	$lineone = fgets($handle, 4096);
    	$buffer = "";
    	$end = "";
    	$last = "";
    	$lastnew = "";
	while (!feof($handle)) {
    	    $line = fgets($handle, 4096);
    	    if(strstr($line,"serial")){
    		$end = $line;
    		$serialArray = explode( ";", trim($line) );
    		$serial = trim($serialArray[0]);
    		//echo $serial."<br>";
    	    }
    	    $buffer = $buffer.$line;
        }
        fclose($handle);
    	if(!strstr($lineone,";last/"))
    	{
    	    $lastnew = ";last/".$diff."/".$name."/".$serial."\r\n".$lineone;
    	}else{
	    $pathArray = explode( "/", $lineone);
    	    $oldDiff = trim($pathArray[1]);
    	    $revert = "Backup: ".$lineone." :: <a href='/?revert=1&zone=".$name."&diff=".$oldDiff."'>Revert old versiob from backup [".$oldDiff."]</a><br>";
    	    $last = ";last/".$diff."/".$name."/".$serial."\r\n";
    	}
    $buffer = $last.$lastnew.$buffer;
    $text = htmlspecialchars($buffer);
    $num = intval(trim($serial))+1;
    $serialNext = "".$num."";
    $text = str_replace($serial, $serialNext,$text);
    
    //echo "<b>Backup version: ".$diff." zonefile: ".$name."</b> LAST SEARIAL: [".$serial."]<br>";
    echo "<form action=\"\" method=\"post\">";
    echo "<p><input type='hidden'  name='diff' value=".$diff."></p>";
    echo "<p><input type='hidden'  name='zone' value=".$name."></p>";
    echo "<p><textarea rows='30' cols='120' name='text'>".$text."</textarea></p>";
    echo " <p><input type=\"submit\" /></p></form>";
    }else echo "error: file name not found - ".$name;
    exit(0);
}





if(isset($_GET['what']))
{
    if($_GET['what'] == 1){
	$bind = $pwd."/suid";
	$output = "";
	exec($bind, $output, $ret);
	$ret = shell_exec($bind);
	echo "Return:<br><pre>".$ret."</pre>";
    }
}

if(isset($_GET['what']))
{
    if($_GET['what'] == 2){
	$bind = $pwd."/status.sh";
	echo "<pre>".shell_exec($bind)."</pre>";
    }
}


echo "<a href='?what=1'>restart</a> - <a href='?what=2'>status</a>  - <a href='/'>list</a><hr>";
listzones($confpath,$backupdir2day,$diff);

?>

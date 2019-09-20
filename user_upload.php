<?php

function arguments($argv) {
$_ARG = array();
$seperator = array("-","--");
foreach ($argv as $arg) {
         $e=explode("=",$arg);
        if(count($e)==2)
            $_ARG[str_replace($seperator,'',$e[0])]=$e[1];
        else {  
            $e=explode("--",$arg);
			if(count($e)==2)
			$_ARG[$e[1]]=$e[1];
		}
    }
return $_ARG;
}

$arr = arguments($argv);
//var_dump($argv);

if (array_key_exists("help",$arr)) {
	echo "my help goes here";
	exit;
}



$conn = new mysqli($arr['h'], $arr['u'], $arr['p']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	exit;
}
// Create database
$sql = "CREATE DATABASE IF NOT EXISTS users";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: \n" . $conn->error . "\n";
	exit;
}




$link = mysqli_connect($arr['h'], $arr['u'], $arr['p'], "users");

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
	exit;
}

$sql = "CREATE Table IF NOT EXISTS users(
id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
name VARCHAR(30) NOT NULL,
surname VARCHAR(30) NOT NULL,
email VARCHAR(30) NOT NULL UNIQUE)";

if(mysqli_query($link, $sql)){
    echo "Table created successfully.\n";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link) . "\n";
	exit;
}
 
if (array_key_exists("create_table",$arr)) {
$sql = "TRUNCATE TABLE users";

if(mysqli_query($link, $sql)){
    echo "No data imported.\n";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link) . "\n";
}
exit;	
}

if (!array_key_exists("file",$arr)) {
	echo "ERROR: No file parameter\n";
	exit;
}


$fileName = $arr["file"];



$file = fopen($fileName, "r");
$message="";

if (!array_key_exists("dry_run",$arr)) {
$flag = true; 
$dup=0;
$r=0;
$inv=0;

while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
	
	if($flag) { $flag = false; continue; }
	
	
	if (filter_var( trim(addslashes($column[2])), FILTER_VALIDATE_EMAIL)) {
    $sqlInsert = "INSERT into users (name,surname,email)
		   values ('" . ucfirst(strtolower(addslashes($column[0]))) . "','" . ucfirst(strtolower(addslashes($column[1]))) . "','" . strtolower(addslashes($column[2])) . "')";
	$result = mysqli_query($link, $sqlInsert);
	if ($result) $r++; else $dup++;
	}
	else $inv++;
	
}
	$message = $r." rows data imported into the database\n".$inv." rows invalid email\n".$dup." rows duplicate email @\n" ;
	
}
  // Close connection
$link->close();  
 
echo $message;
?>

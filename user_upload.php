<?php
error_reporting(1);

function arguments($argv) {
    $_ARG = array();
    foreach ($argv as $arg) {
      if (ereg('--([^=]+)=(.*)',$arg,$reg)) {
        $_ARG[$reg[1]] = $reg[2];

      } 
	  
	  elseif (ereg('-([^=]+)=(.*)',$arg,$reg)) {
        $_ARG[$reg[1]] = $reg[2];

      } 
	  
	  elseif(ereg('--([^=]+)',$arg,$reg)) {
            $_ARG[$reg[1]] = 'true';
        }
  
    }
  return $_ARG;
}

$arr = arguments($argv);


$help = $arr["help"];

if ($help) {
	echo "my help goes here";
	exit;
}



$conn = new mysqli($arr['h'], $arr['u'], $arr['p']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Create database
$sql = "CREATE DATABASE IF NOT EXISTS users";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: \n" . $conn->error . "\n";
}




$link = mysqli_connect($arr['h'], $arr['u'], $arr['p'], "users");

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
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
}
 
if ($arr["create_table"])
exit;




$fileName = $arr["file"];

$file = fopen($fileName, "r");

$dry_run = $arr["dry_run"];

if (!$dry_run) {
$flag = true;      
while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
	
	if($flag) { $flag = false; continue; }
	
	$sqlInsert = "INSERT into users (name,surname,email)
		   values ('" . $column[0] . "','" . $column[1] . "','" . $column[2] . "')";
	$result = mysqli_query($link, $sqlInsert);
	
	if (! empty($result)) {
		$type = "success";
		$message = "CSV Data Imported into the Database\n";
	} else {
		$type = "error";
		$message = "Problem in Importing CSV Data\n";
	}
}
}
  // Close connection
$link->close();  
mysqli_close($link);  
echo $message;
echo $type;

?>

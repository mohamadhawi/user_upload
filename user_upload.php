<?php
/**
 * user_upload
 *
 * Import CSV file of users to database via command line
 * @author    Moe Hawi <mohamadhawi@gmail.com>

*/


	/**
     * function arguments to parse the arguments from the command line and return an array.
    */ 
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

	/**
     * read the arguments and parse it.
     */ 

$arr = arguments($argv);

	/**
     * help option.
     */ 

if (array_key_exists("help",$arr)) {
	echo "--file [csv file name] – this is the name of the CSV to be parsed\n
--create_table – this will cause the MySQL users table to be built (and no further action will be taken)\n
--dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered\n
-u – MySQL username\n
-p – MySQL password\n
-h – MySQL host\n
--help – which will output the above list of directives with details.\n";
	exit;
}

	/**
     * try to open a connection to database.
     */ 


$conn = new mysqli($arr['h'], $arr['u'], $arr['p']);

	/**
     * No connection, print error message.
     */ 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
	exit;
}

	/**
     * create database called `dbusers`.
	   print error if no database created.
     */ 

$sql = "CREATE DATABASE IF NOT EXISTS dbusers";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully\n";
} else {
    echo "Error creating database: \n" . $conn->error . "\n";
	exit;
}


	/**
     * open a connection to the database.
	   print error if no selection successfully.
     */ 


$link = mysqli_connect($arr['h'], $arr['u'], $arr['p'], "dbusers");

if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
	exit;
}

	/**
     * create table `users`.
	   print error if no create successfully.
     */ 

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

	/**
     * clear table `users` for option create_table and stop execution.
     */ 
if (array_key_exists("create_table",$arr)) {
$sql = "TRUNCATE TABLE users";

if(mysqli_query($link, $sql)){
    echo "No data imported.\n";
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link) . "\n";
}
exit;	
}

	/**
     * check if parameter file was provided.
     */

if (!array_key_exists("file",$arr)) {
	echo "ERROR: No file parameter\n";
	exit;
}


$fileName = $arr["file"];


	/**
     * open the csv file.
     */

$file = fopen($fileName, "r");

	/**
     * define variables for the import.
     */


$message=""; 
$flag = true; // to escape the header
$dup=0; // duplicate email counter
$r=0; // valid rows
$inv=0; // invalid email counter

while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
	
	if($flag) { $flag = false; continue; }
	
	
	if (filter_var( trim(addslashes($column[2])), FILTER_VALIDATE_EMAIL)) { // check valid email
    $sqlInsert = "INSERT into users (name,surname,email)
		   values ('" . ucfirst(strtolower(addslashes($column[0]))) . "','" . ucfirst(strtolower(addslashes($column[1]))) . "','" . strtolower(addslashes($column[2])) . "')";

	/**
     * if dry_run option, no insert into the DB.
     */

	if (!array_key_exists("dry_run",$arr)) {
	$result = mysqli_query($link, $sqlInsert);
	if ($result) $r++; else $dup++;
	}
	
	}
	else $inv++; // invalid email
	
}
	//report of data to be print.
	$message = $r." rows data imported into the database\n".$inv." rows invalid email\n".$dup." rows duplicate email @\n" ;
	

// Close connection
$link->close();  
 
echo $message;
?>

<?PHP
// Loof from 1 to 100
for($i=1;$i<=100;$i++) {
	// if the number is divisible by three (3) output the word “foo”
	if ($i%3==0) echo "foo"; 
	
	//if the number is divisible by five (5) output the word “bar”
	if ($i%5==0) echo "bar";
	
	//if the number is not divisible by five (5) and three (3) output the number
	if (($i%3<>0) && ($i%5<>0))
	echo $i;
	
	// output , seperated except the last number
	if ($i<>100)
	echo ", ";
}

?>
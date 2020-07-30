<?php
include 'autoloader.php';
$now = time();
exec('ulimit -S -n 10240');
$min = date('i');


$ch = date('i',strtotime('0'));

echo "\n\n\n\n\n\n\n\n\r";
echo "Date:: ".date('Y-m-d');
echo "\nTime:: ".date('H:i:s')."\n";
echo "Min:: $min\n";

$full = array(00,10,20,30,40,50);
$half = array(05,15,25,35,45,55);


$lockFile = "/tmp/split.lock";
if (!file_exists($lockFile)) {
	touch($lockFile); 
	echo "Ready To start Spliting\n";
}else{
	exit("task already running\n$lockFile\n");
}

if (in_array($min, $full)) {
	echo "\nAction:: Moving Files\n";
	$runner = $runner->MoveFiles($now);
}else{
	echo "\nAction:: Splitting Files\n";
	$runner = $runner->SplitFile($now);
}

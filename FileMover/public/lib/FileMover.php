<?php

class FileMover extends Config{
	
	function __construct(){
	$this->config = Config::config();
	}

	public function MoveFiles($time){
		
		$config = $this->config;

		$count = 0;
		$rootDir = $config->path;
		$destination = $config->destination;

		$this->CheckTime($time);
		exec('ulimit -S -n 10240');
		$cdir =scandir($rootDir);
		if (!$cdir) {
			exit("Failed To Open $cdir to Move Files\n");
		}
				foreach ($cdir as  $content){	
				if (!in_array($content,array(".",".."))){
					$path = $rootDir.$content;
					
					$dh = opendir($path);
					if (!is_dir($path)) {
						exit("$path Is not A Directory");
					}
					if (!is_resource($dh)) {
						echo("\n\nCoulf Not Open Directory $path.\n\n");
						exit();
					}

					
					while (($file = readdir($dh)) == true){
						if( $file == '.' || $file == '..'){
							$this->CheckTime($time);
						}else{
							$count ++;
				      for ($i=0; $i < 7; $i++) { 
				      	
				      	$this->CheckTime($time);
				      	$check = '-00'.$i.'.mp3';

				      	$newstring = substr($file, -8);
				      	echo "####################Counter:$count#####################\n";
				      	echo "File:: $file\nChecking:: $check\nNew String:: $newstring\n";
				      	if ($newstring == $check) {
				      		if (file_exists($path.'/'.$file)) {

				      			$oldfilepath = $path.'/'.$file;
				      			$newFilePath = $destination.$file;
				      			if (rename($oldfilepath, $newFilePath)) {
				      			   echo "File $file Moved To $newFilePath\n\n";
				      			   //sleep(3);
				      		    } else {
				      			   echo "Failed to Move\n$oldfilepath TO  $newFilePath\n\n";
				      		    }
				      		} else {
				      			echo "\n";
				      			echo "$file Does Not Exist\n\n";
				      		}
				      			
				      		
						}else{
							$this->CheckTime($time);
							echo "No File TO Move\nUnable To Move $file\n";
							$this->CheckTime($time);
						}
				      	 
				      }
				    }
				}
				    if(closedir($dh)){
				    	echo "$dh Closed Successfully\n\n";
				    	$this->CheckTime($time);
				    }else{
				    echo "Unable To Close $dh\n";
				    }
				}
			}
			$this->RenameFiles($time);
		}

		public function SplitFile($time){
			$count = 0;
			$config =$this->config;
			//$split = $config->split;
			$done_path = $config->done;
			$rootDir = $config->path;

			// $this->RenameFiles($time);
			
			$this->CheckTime($time);
			//exec('ulimit -S -n 10240');
		$cdir =scandir($rootDir);
		///echo "<pre>".print_r($cdir);die();
		if (!$cdir) {
			echo("Failed To Open $cdir\n");
		}
				foreach ($cdir as  $content){	
				if (!in_array($content,array(".",".."))){
					$path = $rootDir.$content;

					$dh = opendir($path);
					if (!is_dir($path)) {
						exit("$path Is not A Directory");
					}
					if (!is_resource($dh)) {
						echo("\n\nCould Not Open Directory $path.\n\n");
						exit();
					}

					
					while (($file = readdir($dh)) == true){
						$this->CheckTime($time);
						if( $file == '.' || $file == '..'){
							$this->CheckTime($time);
						}else{
						$count ++;
				      for ($i=0; $i < 7; $i++) { 
				      	echo "####################Counter:$count#####################\n";
				      	$this->CheckTime($time);

				      	$check = '-00'.$i.'.mp3';

				      	$newstring = substr($file, -8);
				      	echo "File:: $file\nChecking:: $check\nNew String:: $newstring\n";sleep(5);
				      	if ($newstring != $check) {
				      		if (file_exists($path.'/'.$file)) {

				      			//$mv = shell_exec("mv $path".'/'."*-00$i.mp3 /home/dennis/done/");
				      			//echo "$mv\n";sleep(5);

				      			$oldfilepath = $path.'/'.$file;
				      			$newFilePath = $done_path.$file;

				      			//$size = filesize($oldfilepath);
				      			//echo "File: $file \nSize:: $size\n";
					      			//if ($size > 1000000) {
					      				$cmd = "/home/kinyua/FileMover/ffsplit.sh $oldfilepath 600";
					      				$output = shell_exec($cmd);

					      				if ($output) {
										echo "Success\n";
										echo "$cmd\n";
										echo "$output\n";
										if(rename($oldfilepath, $newFilePath)){
											echo "Raw file Moved To $newFilePath\n";
											$this->CheckTime($time);
											//$this->MoveFiles($time);
											$i = 6;
										}else{
											echo "File Not Moved\n";
										}
									}else{
										echo "Failed To Split\n";
										echo "FILE:: $file\n";
										echo "CMD:: $cmd\n";
										echo "<pre>$output</pre>\n"; 
									}
				      			/*}else{
									$this->CheckTime($time);
									$this->MoveFiles($time);

									echo("File $file Is Below 1MB\n");
								}*/
				      			
				      		} else {
				      			echo "\n";
				      			echo "Split Error!!!!\n".$path.'/'.$file ."\nDoes Not Exist\n\n";
				      			$this->CheckTime($time);
				      		}
				      			
				      		
						}else{
							$this->CheckTime($time);
							echo "Cant Chop $file\n";
								}
				      		}
				  		}
				    }
				    if(closedir($dh)){
				    	echo "$dh Closed Successfully\n\n";
				    }else{
				    echo "Unable To Close $dh\n";
				    }
				}
			}
				if(unlink('/tmp/split.lock')){
					echo "Lock File Removed\n";
				}else{
					echo "Unable To Remove Lock File\n";
				}
			
		}

		public function  CheckTime($time){
			global $dh;
			$lockFile = "/tmp/split.lock";
			$now = time(); // Checking the time now when home page starts.
			$expire = $time + (270);

			if ($now > $expire) {
				unlink($lockFile);
				closedir($dh);
				exit("\n\n Timeout\nLock Have Been Removed\nBye!!");
			}else{
				echo "Still There Is Time\n";
			}

		}

		public function RenameFiles($time){
			echo "2. Renaming Files.......\n";
			$config = $this->config;

			$count = 0;
			$rootDir = $config->destination;
			$doneDir = $config->splits;
			
		$cdir =scandir($rootDir);
		if (!$cdir) {
			exit("Failed To Open $cdir to Move Files\n");
		}
				foreach ($cdir as  $file){	
					//$this->CheckTime($time);
				if (!in_array($file,array(".",".."))){
					$ext = $this->getExtension($file);
					$count++;
					echo "$file\n";
					$path = $rootDir.$file;
					for ($i=1; $i < 7; $i++) {
						$check = '-00'.$i.'.'.$ext;
						
						$newstring = substr($file, -8);
				      	echo "File:: $file\nChecking:: $check\nNew String:: $newstring\n";
				      	if ($newstring == $check) {
				      		if (file_exists($rootDir.$file)) {
				      			

					$arr_explode = explode('_', $file);
					
					$filename = $arr_explode[0];
					$date = $arr_explode[1];
					$time = $arr_explode[2];

					$ext = $this->getExtension($file);

					$code = $this->getStationCode($filename);
					$our_date = $this->getFormatDate($date);
					$our_time = $this->getFormatTime($time);

					$new_filename = $code.'_'.$our_date.'_'.$our_time.'.'.$ext;
					$new_path = $doneDir.$new_filename;
					if (rename($path, $new_path)) {
						echo "\n##############################$count######################################\n";
						echo "Renamed and Moved Successfully From $path to $new_path\n";
						echo "####################################################################\n";
					} else {
						echo "\n####################################################################\n";
						echo "Failed TO Rename\n";
						echo "####################################################################\n";
					}
					
					
							}
						}
					}	
				}
			}
			$lockFile = "/tmp/split.lock";
			if(unlink($lockFile)){
					echo "Lock File Removed\n";
				}else{
					echo "Unable To Remove Lock File\n";
				}
		}

		public function getStationCode($name){
			$config =$this->config;
			$stations = $config->stations;
			$country_code = $config->Country_code;

			$code = array_search($name, $stations);
			$code = $country_code."_".$code;

			return $code;
		}

		public function getFormatDate($date){
			$array_date = explode("-", $date);
			$year = $array_date[0];
			$month = $array_date[1];
			$day = $array_date[2];
			if (checkdate($month, $day, $year)) {
				if (strlen($year)!=4) {
					$date ="false";
				}else if(strlen($month)!=2){
					$date ="false";
				}else if(strlen($day)!=2){
					$date ="false";
				} else {
					$date = $year.'_'.$month.'_'.$day;
				}
			} else {
				$date ='false';
			}
			return $date;
		}

		public function getFormatTime($time){
			$array = explode('-', $time);

			$hour = $array[0];
			$min = $array[1];
			$sec = $array[2];
			$split = $array[3];
			echo "############################################ $time #################\n";
			$test = $this->validateTime($hour,$min)."\n\n";
			if ($this->validateTime($hour,$min)) {
				//echo "Time Is Valid\n";
				$stamp = $hour.":".$min.":00";
				if ((strtotime($stamp)%3600) == 0) {
					$min = $this->getMinutes($split);
				}else{
					echo "Not Whole Hour \n";
					echo "Function To BE written soon\n";
				}

				$time = $hour."-".$min."-"."00";
				//$time = date('H-i-s',strtotime($time));

				return $time;
			} else {
				echo "Time Is Invalid\n";
				return false;
				echo "############################################\n";
			}
		}

		public function validateTime($hour,$min){
			//$hour = 77;
			if (empty($hour) || empty($min)) {
				return false;
			} else {
				if ($hour < 00 || $hour > 23) {
					echo "Invalid Hour\n";
					return false;
				}
				if($min < 00 || $min > 59){
					echo "Invalid Min\n";
					return false;
				}
				return true;
			}
		}
		public function getMinutes($split){
			$arr = explode('.', $split);
			$time = $arr[0];

			switch ($time) {
				case 001:
					$min = '00';
					return $min;
					break;
				case 002:
					$min = 10;
					return $min;
					break;
				case 003:
					$min = 20;
					return $min;
					break;
				case 004:
					$min = 30;
					return $min;
					break;
				case 005:
					$min = 40;
					return $min;
					break;
				case 006:
					$min = 50;
					return $min;
					break;
				default:
					$min = 00;
					return $min;
					break;
			}
		}

		public function getExtension($file){
			$ext = pathinfo($file, PATHINFO_EXTENSION);
			return $ext;
		}
	}

//}

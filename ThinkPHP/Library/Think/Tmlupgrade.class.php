<?php
namespace Think;
class Tmlupgrade {
	var $locale = 'SC';
	var $charset = 'UTF8';
	public function fetch_updatefile_list($upgradeinfo) {
		$file = APP_PATH.'./upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']/updatelist.tmp';
		$upgradedataflag = true;
		$upgradedata = @file_get_contents($file);
		if(!$upgradedata) {
			$upgradedata = dfsockopen(C("ONLINE_UPGRADE").substr($upgradeinfo['upgradelist'], 0, -4).strtolower('_'.$this->locale.'_'.$this->charset).'.txt');
			$upgradedataflag = false;
		}
		 
		$return = array();
		$upgradedataarr = explode("\r\n", $upgradedata);

		foreach($upgradedataarr as $k => $v) {
			if(!$v) {
				continue;
			}
			$return['file'][$k] = trim(substr($v, 34));
			$return['md5'][$k] = substr($v, 0, 32);
			if(trim(substr($v, 32, 2)) != '*') {
				@unlink($file);
				return array();
			}

		}
		if(!$upgradedataflag) {
			$this->mkdirs(dirname($file));
			$fp = fopen($file, 'w');
			if(!$fp) {
				return array();
			}
			fwrite($fp, $upgradedata);
		}
		 
		return $return;
	}

	public function compare_basefile($upgradeinfo, $upgradefilelist) {
		if(!$discuzfiles = @file( APP_PATH.'./upgrade/tmlfile.md5')) {
			return array();
		}
		 
		$newupgradefilelist = array();
		foreach($upgradefilelist as $v) {
			$newupgradefilelist[$v] = md5_file(APP_PATH.'./'.$v);
		}
		 
		$modifylist = $showlist = $searchlist = $ignorelist= array();
		foreach($discuzfiles as $line) {
			$file = trim(substr($line, 34));
			$md5datanew[$file] = substr($line, 0, 32); 
			if(isset($newupgradefilelist[$file])) {
				if($md5datanew[$file] != $newupgradefilelist[$file]) {
					//if(!$upgradeinfo['isupdatetemplate'] && preg_match('/\.htm$/i', $file)) {
					//	$ignorelist[$file] = $file;
					//	$searchlist[] = "\r\n".$file;
					//	continue;
					//}
					$modifylist[$file] = $file;
				} else {
					$showlist[$file] = $file;
				}
			}
		}
		if($searchlist) {
			$file = APP_PATH.'./upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']/updatelist.tmp';
			$upgradedata = file_get_contents($file);
			$upgradedata = str_replace($searchlist, '', $upgradedata);
			$fp = fopen($file, 'w');
			if($fp) {
				fwrite($fp, $upgradedata);
			}
		}

		return array($modifylist, $showlist, $ignorelist);
	}

	public function compare_file_content($file, $remotefile) {
		if(!preg_match('/\.php$|\.htm$/i', $file)) {
			return false;
		}
		$content = preg_replace('/\s/', '', file_get_contents($file));
		$ctx = stream_context_create(array('http' => array('timeout' => 60)));
		$remotecontent = preg_replace('/\s/', '', file_get_contents($remotefile, false, $ctx));
		if(strcmp($content, $remotecontent)) {
			return false;
		} else {
			return true;
		}
	}

	public function check_folder_perm($updatefilelist) {
		foreach($updatefilelist as $file) {
			if(!file_exists(APP_PATH.$file)) {
				if(!$this->test_writable(dirname(APP_PATH.$file))) {
					return false;
				}
			} else {
				if(!is_writable(APP_PATH.$file)) {
					return false;
				}
			}
		}
		return true;
	}

	public function test_writable($dir) {
		$writeable = 0;
		$this->mkdirs($dir);
		if(is_dir($dir)) {
			if($fp = @fopen("$dir/test.txt", 'w')) {
				@fclose($fp);
				@unlink("$dir/test.txt");
				$writeable = 1;
			} else {
				$writeable = 0;
			}
		}
		return $writeable;
	}

	public function download_file($upgradeinfo, $file, $folder = 'upload', $md5 = '', $position = 0, $offset = 0) {
		$dir = APP_PATH.'./upgrade/TML'.$upgradeinfo['latestversion'].' Release['.$upgradeinfo['latestrelease'].']/';
		$this->mkdirs(dirname($dir.$file));
		$downloadfileflag = true;

		if(!$position) {
			$mode = 'wb';
		} else {
			$mode = 'ab';
		}
		$fp = fopen($dir.$file, $mode);
		if(!$fp) {
			return 0;
		}
		$response = dfsockopen(C("ONLINE_UPGRADE").$upgradeinfo['latestversion'].'/'.$upgradeinfo['latestrelease'].'/'.$this->locale.'_'.$this->charset.'/'.$folder.'/'.$file.'.html', $offset, '', '', FALSE, '', 120, TRUE, 'URLENCODE', FALSE, $position);
		
		if($response) {
			if($offset && strlen($response) == $offset) {
				$downloadfileflag = false;
			}
			fwrite($fp, $response);
		}
		fclose($fp);
		if($downloadfileflag) {
			return 2;
			if(md5_file($dir.$file) == $md5) {
				return 2;
			} else {
				return 0;
			}
		} else {
			return 1;
		}
	}

	public function mkdirs($dir) {
		if(!is_dir($dir)) {
			if(!self::mkdirs(dirname($dir))) {
				return false;
			}
			if(!@mkdir($dir, 0777)) {
				return false;
			}
			@touch($dir.'/index.htm');
			@chmod($dir.'/index.htm', 0777);
		}
		return true;
	}

	public function copy_file($srcfile, $desfile, $type) {
		if(!is_file($srcfile)) {
			return false;
		}
		if($type == 'file') {
			$this->mkdirs(dirname($desfile));
			copy($srcfile, $desfile);
		}/* elseif($type == 'ftp') {
			$siteftp = $_GET['siteftp'];
			$siteftp['on'] = 1;
			$siteftp['password'] = authcode($siteftp['password'], 'ENCODE', md5($_G['config']['security']['authkey']));
			$ftp = & discuz_ftp::instance($siteftp);
			$ftp->connect();
			$ftp->upload($srcfile, $desfile);
			if($ftp->error()) {
				return false;
			}
		}*/
		return true;
	}

	//public function versionpath() {
	//	$versionpath = '';
	//	foreach(explode(' ', substr(DISCUZ_VERSION, 1)) as $unit) {
	//		$versionpath = $unit;
	//		break;
	//	}
	//	return $versionpath;
	//}

	function copy_dir($srcdir, $destdir) {
		$dir = @opendir($srcdir);
		while($entry = @readdir($dir)) {
			$file = $srcdir.$entry;
			if($entry != '.' && $entry != '..') {
				if(is_dir($file)) {
					self::copy_dir($file.'/', $destdir.$entry.'/');
				} else {
					self::mkdirs(dirname($destdir.$entry));
					copy($file, $destdir.$entry);
				}
			}
		}
		closedir($dir);
	}

	function rmdirs($srcdir) {
		$dir = @opendir($srcdir);
		while($entry = @readdir($dir)) {
			$file = $srcdir.$entry;
			if($entry != '.' && $entry != '..') {
				if(is_dir($file)) {
					self::rmdirs($file.'/');
				} else {
					@unlink($file);
				}
			}
		}
		closedir($dir);
		rmdir($srcdir);
	}
}
?>
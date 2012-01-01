<?php
class ZipArchiveDir extends ZipArchive {
	var $_count = 0;
	var $_countb = 0;
	
    public function addDir($dirname, $localname, $exclude) {
		if ( (substr($dirname, -1)=='/') || (substr($dirname, -1)=='/') ){ // Remove trailing slash if it exists.
			$dirname = substr($dirname,0,strlen($dirname)-1);
		}
        $this->addEmptyDir($localname);
        $iter = new RecursiveDirectoryIterator($dirname);
        foreach ($iter as $fileinfo) {
            if (! $fileinfo->isFile() && !$fileinfo->isDir()) {
                continue;
            }
			if ( $fileinfo->getFilename() != $exclude ) {
				if ( $fileinfo->isFile() ) {
					$this->addFile($fileinfo->getPathname(), $localname .'/'. $fileinfo->getFilename());
				} else {
					$this->addDir($fileinfo->getPathname(), $localname .'/'. $fileinfo->getFilename(), $exclude);
				}
				
				$this->_newAddedFilesCounter++;
				if ($this->_newAddedFilesCounter >= 200) { // Handle file descriptor limit bug in PHP.
					parent::close();
					//return parent::close();
					parent::open($this->_archiveFileName, $this->_archiveFlags);
					//echo 'opened then closed';
				}
				
				// Send status updates to browser.
				$this->_count++;
				if ($this->_count >= 50) { // Every 50 files, print a period.
					echo '.';
					flush();
					$this->_count = 0;
					$this->_countb++;
					if ($this->_countb >= 50) { // Every 50 periods, print a new line.
						echo "<br />\n";
						flush();
						$this->_countb = 0;
					}
				}
			}
        }
    }
	
	public function open($fileName, $flags) {
        $this->_archiveFileName = $fileName;
        $this->_newAddedFilesCounter = 0;
		$this->_archiveFlags = $flags;
        return parent::open($fileName,$flags);
    }
}
?>
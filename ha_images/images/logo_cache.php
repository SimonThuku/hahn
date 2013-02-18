<?php

class LogoCache
{
	private $logos_width = 122;
	private $logos_height = 78;

	private $chars = array(
		'A' => 0,
		'B' => 1,
		'C' => 2,
		'D' => 3,
		'E' => 4,
		'F' => 5,
		'G' => 6,
		'H' => 7,
		'I' => 8,
		'J' => 9,
		'K' => 10,
		'L' => 11,
		'M' => 12,
		'N' => 13,
		'O' => 14,
		'P' => 15,
		'Q' => 16,
		'R' => 17,
		'S' => 18,
		'T' => 19,
		'U' => 20,
		'V' => 21,
		'W' => 22,
		'X' => 23,
		'Y' => 24,
		'Z' => 25,
		'0' => 26,
		'1' => 27,
		'2' => 28,
		'3' => 29,
		'4' => 30,
		'5' => 31,
		'6' => 32,
		'7' => 33,
		'8' => 34,
		'9' => 35
	);

	private $logoDir = '';
	private $xmlCache = '';
	private $notFoundLogo = '';
	private $gifLogos = '';
	
	private $doc = NULL;
	private $cacheUpToDate = FALSE;
	
	private $numNodes = -1;
	private $numFiles = -1;

	function __construct($LogoDir, $XmlCacheFile, $NotFoundLogoFile, $GifLogosFile)
	{
		if ( !is_dir($LogoDir) )
			throw new Exception("LogoDir '{$LogoDir}' not found!");
			
		if ( !is_file($NotFoundLogoFile) )
			throw new Exception("NotFoundLogoFile '{$NotFoundLogoFile}' not found!");
	
		$this->logoDir = $LogoDir;
		$this->xmlCache = $XmlCacheFile;
		$this->notFoundLogo = $NotFoundLogoFile;
		$this->gifLogos = $GifLogosFile;
		
		$this->doc = @simplexml_load_file($XmlCacheFile);
	}
	
	public function sendImage()
	{
		if ( $this->doc )
		{
			if ( !$this->isCacheUpToDate() )
			{
				if ( !$this->renderImage() )
				{
					return FALSE;
				}
			}
		}
		else
		{
			$this->createLogoCache();
			if ( !$this->renderImage() )
			{
				return FALSE;
			}
		}
		
		$im = imagecreatefromjpeg($this->gifLogos);
		header('Content-Type: image/jpeg');
		header('Last-Modified: ' . date('D, d M J G:i:s T', filemtime($this->gifLogos)));
		imagejpeg($im);
		imagedestroy($im);
		
		return TRUE;
	}
	
	private function renderImage()
	{
		//$im_out = imagecreatetruecolor(count($this->chars) * $this->logos_width, count($this->chars) * $this->logos_height);
		$im_out = imagecreatetruecolor($this->countFiles() * $this->logos_width, $this->logos_height);
		/*
		foreach ( $this->chars as $key_x => $val_x )
		{
			foreach ( $this->chars as $key_y => $val_y )
			{
				$filename = $this->logoDir . '/' . $key_x . $key_y . '.gif';
				//$im_in = ( file_exists($filename) ) ? imagecreatefromgif($filename) : imagecreatefromgif($this->notFoundLogo);
				if ( !file_exists($filename) ) 
					continue;
				$im_in = imagecreatefromgif($filename);
				imagecopy($im_out, $im_in, $val_x*$this->logos_width, $val_y*$this->logos_height, 0, 0, $this->logos_width, $this->logos_height);
				imagedestroy($im_in);
			}
		}
		*/
		
		if ($dh = opendir($this->logoDir)) 
		{
			$i = 0;
			while (($file = readdir($dh)) !== false) 
			{
				if ( !preg_match("/([A-Z0-9])([A-Z0-9])\.gif$/i", strtoupper($this->logoDir.'/'.$file), $matches) )
					continue;
					
				$im_in = imagecreatefromgif($this->logoDir.'/'.$file);
				//imagecopy($im_out, $im_in, $this->chars[$matches[1]]*$this->logos_width, $this->chars[$matches[2]]*$this->logos_height, 0, 0, $this->logos_width, $this->logos_height);
				imagecopy($im_out, $im_in, $i*$this->logos_width, 0 /*$this->logos_height*/, 0, 0, $this->logos_width, $this->logos_height);
				imagedestroy($im_in);
				$i++;
			}
			closedir($dh);
		}		

		imagejpeg($im_out, $this->gifLogos);
		imagedestroy($im_out);
		
		return TRUE;
	}
	
	private function isCacheUpToDate()
	{
		if ( !$this->doc )
		{
			$this->createLogoCache();
			return FALSE;
		}
		else if ( !file_exists($this->gifLogos) )
			return FALSE;
		else
		{
			if ( $this->countNodes() != $this->countFiles() )
			{
				return FALSE;
			}
			else
			{
				foreach ( $this->doc as $logo )
				{
					$filename = $this->logoDir . '/' . $logo['name'];
					if ( file_exists($filename) )
					{
						if ( filemtime($filename) > $logo['timestamp'] )
						{
							return FALSE;
						}
					}
					else
					{
						return FALSE;
					}
				}
			}
		}
		
		return TRUE;
	}
	
	private function countNodes()
	{
		if ( $this->numNodes == -1 )
		{
			if ( $this->doc )
			{
				$this->numNodes = 0;
				foreach ( $this->doc as $logo )
				{
					$this->numNodes++;
				}
			}
		}
		
		return $this->numNodes;
	}
	
	private function countFiles()
	{
		if ( $this->numFiles == -1 )
		{
			if ($dh = opendir($this->logoDir)) 
			{
				$this->numFiles = 0;
				while (($file = readdir($dh)) !== false) 
				{
					if ( $file == '.' || $file == '..' ) 
						continue;
						
					$this->numFiles++;
				}
				closedir($dh);
			}
		}
		
		return $this->numFiles;
	}
	
	private function createLogoCache()
	{
		if ( !$this->doc )
		{
			$this->doc = new DomDocument();
		}
		
		$rootNode = $this->doc->createElement('cache');
		
		if ($dh = opendir($this->logoDir)) 
		{
			$this->numFiles = 0;
			while (($file = readdir($dh)) !== false) 
			{
				if ( $file == '.' || $file == '..' ) 
					continue;
					
			    $elementNode = $this->doc->createElement('logo');
			    $elementNode->setAttribute("name", $file);
			    $elementNode->setAttribute("id", $this->numFiles);
			    $elementNode->setAttribute("timestamp", filemtime($this->logoDir.'/'.$file));
			    $rootNode->appendChild($elementNode);
			    $this->numFiles++;
			}
			closedir($dh);
		}
		
		$this->doc->appendChild($rootNode);
		$this->doc->save($this->xmlCache);
		$this->cacheUpToDate = TRUE;
	}
}

?>

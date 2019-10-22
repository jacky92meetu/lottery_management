<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

ini_set('memory_limit','128M');
ini_set('max_execution_time', 0);

class CImage{

	var $file_array;
	var $cur_image;
	var $ip = "";
	var $dt = "";
	var $limit = 102400;
	var $nheight = 480;
	var $nwidth = 640;
	var $thumb_nheight = 96;
	var $thumb_nwidth = 128;
	var $quality = 85;
	var $save_type = 2;
	var $image_type_filter = array(1,2,3,6); //1:gif, 2:jpg, 3:png, 6:bmp
	var $imgpath = "";
	var $resize_full = true;
	var $resize_force = false;
	var $transparent = false;
	
	function __construct(){
		$this->ip = $_SERVER['REMOTE_ADDR'];
		$this->dt = date("YmdHis");
		$this->imgpath = APPPATH."tmp/";
		$this->file_array = array();
		$this->cur_image = new stdClass;
		$this->cur_image->resource = null;
		$this->cur_image->file_size = 0;
		$this->cur_image->file_path = null;
	}
	
	function __destruct(){		
		$this->clearFile();
	}
	
	function& setLimit($size){
		$this->limit = $size;
	}
	
	function standardSize($size=0,$type=0){
		$temp = $size;
		$typelist = array("Bytes","KB","MB","GB","TB");		
		if($temp>1024){
			$temp = round($size/1024,2);						
			return $this->standardSize($temp,($type+1));
		}
		
		return floor($temp).$typelist[$type];				
	}
	
	function& getImage(){
		return $this->file_array;
	}

	function setImage(){
		if(!is_dir($this->imgpath)){				
				mkdir($this->imgpath,0777);					
		 }	

		foreach($_FILES as $userfile)
		{
			 $tmp_name = $userfile['tmp_name'];
			 if($tmp_name!=""){
				 $type = $userfile['type'];
				 $name = $userfile['name'];
				 
				 $size = $userfile['size'];
				 $temp = array();
				 
				 $tmp = getimagesize($tmp_name);						 

				 if($tmp_name!="" && (!$tmp || !in_array($tmp[2],$this->image_type_filter))){
					echo'
					<script language="javascript" type="text/javascript">
						alert("System only accept image(s) with JPEG, PNG, BMP or GIF format!");
						document.location = "register.html";
					</script>
					';
					exit();
				 }
				 
				if($size>0){									
					$nf = $this->imgpath.$this->ip.$this->dt.$name.".JPG";				
					$result = $this->image_process($tmp_name,'resize');
					if($result){
						$this->image_show($nf);
						$temp = $userfile;				
						$temp['nf'] = $nf;
						$temp['nname'] = $name.".JPG";				
						array_push($this->file_array,$temp);	
					}
				}	
			}
		}
		
		//$this->createZipFile();
	}
	
	function createZipFile(){
		if(sizeof($this->file_array)>0){
			$nf = $this->imgpath.$this->ip.$this->dt."_compress.zip";
			$zip = new ZipArchive;
			if ($zip->open($nf, ZipArchive::CREATE) === TRUE) {
				foreach($this->file_array as $file)
				{
					if (is_file($file['nf'])){		
						$zip->addFile($file['nf'], $file['nname']);						
					}			
				}					
				$zip->close();		
				$this->clearFile();				
				$temp['nf'] = $nf;
				$temp['nname'] = $this->ip.$this->dt."_compress.zip";				
				array_push($this->file_array,$temp);
			}
		}
	}
	
	function clearFile(){
		while($file = array_pop($this->file_array))
		{
			if (is_file($file['nf'])){		
				unlink($file['nf']);
			}			
		}
	}

	function crop($param){
		extract(array_merge(array('x1'=>0,'x2'=>0,'nwidth'=>0,'nheight'=>0),$param));
		$tmpimg = $this->cur_image->resource;
		$newimage = imagecreatetruecolor($nwidth,$nheight);
		if(imagecopyresampled($newimage, $tmpimg, 0, 0, $x1, $y1, $nwidth, $nheight, $nwidth, $nheight)){
			$this->cur_image->resource = $newimage;
			return TRUE;
		}
		return FALSE;
	}

	function rotate($param){
		extract(array_merge(array('degree'=>-90),$param));
		$newimage = $this->cur_image->resource;
		if(imagerotate($newimage, $degree, -1, 1)){
			$this->cur_image->resource = $newimage;
			return TRUE;
		}
		return FALSE;
	}

	function watermark(){		
		$overlay='application/assets/images/watermark1.png';
		$info = getimagesize($overlay);
		$tmpimg = $this->image_create_gd($overlay);		
		$newimage = $this->cur_image->resource;
		$sx = imagesx($newimage);
		$sy = imagesy($newimage);
		$bg = imagecolorallocate($tmpimg, 0,0,0);
		imagecolortransparent($tmpimg, $bg);		
		if(imagecopyresampled($newimage, $tmpimg, 0, 0, 0, 0, $sx, $sy, $info[0], $info[1])){
			$this->cur_image->resource = $newimage;
			return TRUE;
		}		
		return FALSE;		 
	}

	function watermark2(){
		$overlay='application/assets/images/watermark2.png';		
		$tmptile = $this->image_create_gd($overlay);		
		$newimage = $this->cur_image->resource;
		$sx = imagesx($newimage);
		$sy = imagesy($newimage);
		$tmpimg = imagecreatetruecolor($sx, $sy);
		$bg = imagecolorallocatealpha($tmpimg, 255, 255, 255, 127);
		imagefill($tmpimg, 0, 0, $bg);		
		imagesettile($tmpimg, $tmptile);
		imagefilledrectangle($tmpimg, 0, 0, $sx, $sy, IMG_COLOR_TILED);				
		if(imagecopyresampled($newimage, $tmpimg, 0, 0, 0, 0, $sx, $sy, $sx, $sy)){
			$this->cur_image->resource = $newimage;
			return TRUE;
		}		
		return FALSE;
	}
	
	function resize($param){
		extract(array_merge(array('nwidth'=>$this->nwidth,'nheight'=>$this->nheight),$param));
		$img = $this->cur_image->resource;
		$width = imagesx( $img );
		$height = imagesy( $img );
		$newwidth = $width;
		$newheight = $height;

		if($this->resize_force==true || ($width>$nwidth || $height>$nheight) || $this->cur_image->file_size>$this->limit){
			if ($width > $height) {
					$newwidth = $nwidth;
					$divisor = $width / $nwidth;
					$newheight = floor( $height / $divisor);
			}
			else {
					$newheight = $nheight;
					$divisor = $height / $nheight;
					$newwidth = floor( $width / $divisor );
			}
			// Create a new temporary image.
			$tmpimg = imagecreatetruecolor( $newwidth, $newheight );			
			// Copy and resize old image into new image.
			imagecopyresampled( $tmpimg, $img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height );
			$img = $tmpimg;
		}

		$width = imagesx( $img );
		$height = imagesy( $img );
		if($this->resize_full==true && ($width<$nwidth || $height<$nheight)){
			$newwidth = $nwidth;
			$newheight = $nheight;
			$sx = round(($newwidth-$width)/2);
			$sy = round(($newheight-$height)/2);
			// Create a new temporary image.
			$tmpimg = imagecreatetruecolor( $newwidth, $newheight );
			if($this->transparent){
				$bg = imagecolorallocate($tmpimg, 0, 0, 0);
				imagecolortransparent($tmpimg, $bg);
			}else{
				$bg = imagecolorallocate($tmpimg, 255, 255, 255);
				imagefill($tmpimg, 0, 0, $bg);
			}
			// Copy and resize old image into new image.
			imagecopy( $tmpimg, $img, $sx, $sy, 0, 0, $width, $height);
			$img = $tmpimg;
		}

		if(!is_null($img)){
			$this->cur_image->resource = $img;
			return TRUE;
		}
		return FALSE;
	}

	function manual_thumbnail(){
		return $this->resize(array("nwidth"=>$this->thumb_nwidth,"nheight"=>$this->thumb_nheight));
	}

	function center_thumbnail(){
		$img = $this->cur_image->resource;
		$width = imagesx( $img );
		$height = imagesy( $img );
		$nwidth = $this->thumb_nwidth;
		$nheight = $this->thumb_nheight;

		if ($width < $height) {
				$newwidth = $nwidth;
				$divisor = $width / $nwidth;
				$newheight = floor( $height / $divisor);
				$x1 = 0;
				$y1 = floor(($newheight-$nheight)/2);
		}
		else {
				$newheight = $nheight;
				$divisor = $height / $nheight;
				$newwidth = floor( $width / $divisor );
				$x1 = floor(($newwidth-$nwidth)/2);
				$y1 = 0;
		}

		$this->resize(array("nwidth"=>$newwidth,"nheight"=>$newheight));
		return $this->crop(array('x1'=>$x1,'y1'=>$y1,'nwidth'=>$nwidth,'nheight'=>$nheight));
	}

	function image_process($sourcefile,$action){			
		$this->cur_image->resource = $this->image_create_gd($sourcefile);
		if($this->cur_image->resource){
			$this->cur_image->file_path = $sourcefile;
			$this->cur_image->file_size = filesize($sourcefile);
		}
		if(!is_array($action)){
			$action = array($action=>array());
		}
		foreach($action as $method => $param){
			if(method_exists($this, $method)){
				$this->$method($param);
			}
		}

		return $this->cur_image->resource;
	}
	
	function ConvertBMP2GD($src, $dest = false) {
		if(!($src_f = fopen($src, "rb"))) {
		return false;
		}
		if(!($dest_f = fopen($dest, "wb"))) {
		return false;
		}
		$header = unpack("vtype/Vsize/v2reserved/Voffset", fread($src_f,
		14));
		$info = unpack("Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vimagesize/Vxres/Vyres/Vncolor/Vimportant",
		fread($src_f, 40));

		extract($info);
		extract($header);

		if($type != 0x4D42) {	 // signature "BM"
		return false;
		}

		$palette_size = $offset - 54;
		$ncolor = $palette_size / 4;
		$gd_header = "";
		// true-color vs. palette
		$gd_header .= ($palette_size == 0) ? "\xFF\xFE" : "\xFF\xFF";
		$gd_header .= pack("n2", $width, $height);
		$gd_header .= ($palette_size == 0) ? "\x01" : "\x00";
		if($palette_size) {
		$gd_header .= pack("n", $ncolor);
		}
		// no transparency
		$gd_header .= "\xFF\xFF\xFF\xFF";

		fwrite($dest_f, $gd_header);

		if($palette_size) {
		$palette = fread($src_f, $palette_size);
		$gd_palette = "";
		$j = 0;
		while($j < $palette_size) {
		$b = $palette{$j++};
		$g = $palette{$j++};
		$r = $palette{$j++};
		$a = $palette{$j++};
		$gd_palette .= "$r$g$b$a";
		}
		$gd_palette .= str_repeat("\x00\x00\x00\x00", 256 - $ncolor);
		fwrite($dest_f, $gd_palette);
		}

		$scan_line_size = (($bits * $width) + 7) >> 3;
		$scan_line_align = ($scan_line_size & 0x03) ? 4 - ($scan_line_size &
		0x03) : 0;

		for($i = 0, $l = $height - 1; $i < $height; $i++, $l--) {
		// BMP stores scan lines starting from bottom
		fseek($src_f, $offset + (($scan_line_size + $scan_line_align) *
		$l));
		$scan_line = fread($src_f, $scan_line_size);
		if($bits == 24) {
		$gd_scan_line = "";
		$j = 0;
		while($j < $scan_line_size) {
		$b = $scan_line{$j++};
		$g = $scan_line{$j++};
		$r = $scan_line{$j++};
		$gd_scan_line .= "\x00$r$g$b";
		}
		}
		else if($bits == 8) {
		$gd_scan_line = $scan_line;
		}
		else if($bits == 4) {
		$gd_scan_line = "";
		$j = 0;
		while($j < $scan_line_size) {
		$byte = ord($scan_line{$j++});
		$p1 = chr($byte >> 4);
		$p2 = chr($byte & 0x0F);
		$gd_scan_line .= "$p1$p2";
		}
		$gd_scan_line = substr($gd_scan_line, 0, $width);
		}
		else if($bits == 1) {
		$gd_scan_line = "";
		$j = 0;
		while($j < $scan_line_size) {
		$byte = ord($scan_line{$j++});
		$p1 = chr((int) (($byte & 0x80) != 0));
		$p2 = chr((int) (($byte & 0x40) != 0));
		$p3 = chr((int) (($byte & 0x20) != 0));
		$p4 = chr((int) (($byte & 0x10) != 0));
		$p5 = chr((int) (($byte & 0x08) != 0));
		$p6 = chr((int) (($byte & 0x04) != 0));
		$p7 = chr((int) (($byte & 0x02) != 0));
		$p8 = chr((int) (($byte & 0x01) != 0));
		$gd_scan_line .= "$p1$p2$p3$p4$p5$p6$p7$p8";
		}
		$gd_scan_line = substr($gd_scan_line, 0, $width);
		}

		fwrite($dest_f, $gd_scan_line);
		}
		fclose($src_f);
		fclose($dest_f);
		return true;
	}

	function imagecreatefrombmp($filename) {	
		$tmp_name = $this->imgpath.md5($filename);
		if($this->ConvertBMP2GD($filename, $tmp_name)) {
		$img = imagecreatefromgd($tmp_name);
		unlink($tmp_name);
		return $img;
		}
		return false;
	}

	// --------------------------------------------------------------------

	function image_create_gd($sourcefile="",$imtype=""){
		if ($sourcefile == ''){
			return false;
		}
		if($imtype==""){
			$timg = getimagesize($sourcefile);
			$imtype = $timg[2];
		}
		if(in_array($imtype,$this->image_type_filter)){
			switch($imtype){
				case 1: return imagecreatefromgif($sourcefile);
								break;
				case 2: return imagecreatefromjpeg($sourcefile);
								break;
				case 3: return imagecreatefrompng($sourcefile);
								break;
				case 6: return $this->imagecreatefrombmp($sourcefile);
								break;
			}
		}

		return false;
	}

	function image_show($filename = ""){
		$resource = $this->cur_image->resource;
		if($filename!=""){
			// Save into a file.
			$this->image_save_gd($resource, $filename);
		}else{
			// Display 
			$this->image_display_gd($resource);
		}
	}

	function image_save_gd($resource,$filename)
	{
		if($this->transparent && @imagepng($resource)){
			return TRUE;
		}

		switch ($this->save_type)
		{
			case 1 :
						if ( @imagegif($resource, $filename)){					
							return TRUE;
						}
				break;
			case 2	:						
						if ( @imagejpeg($resource, $filename, $this->quality)){							
							return TRUE;
						}
				break;
			case 3	:
						if ( @imagepng($resource, $filename)){							
							return TRUE;
						}
				break;
			default		:														
				break;
		}

		return FALSE;
	}

	function image_display_gd($resource)
	{
		$mime_type = image_type_to_mime_type($this->save_type);
		header("Content-Disposition: filename=example;");
		header("Content-Type: {$mime_type}");
		header('Content-Transfer-Encoding: binary');
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', time()).' GMT');

		if($this->transparent && imagepng($resource)){
			exit;
		}
		
		switch ($this->save_type)
		{
			case 1		:	imagegif($resource);
				break;
			case 2		:	imagejpeg($resource, '', $this->quality);
				break;
			case 3		:	imagepng($resource);
				break;
			default		:	echo 'Unable to display the image';
				break;
		}

		exit;
	}
}

?>
<?php
// +----------------------------------------------------------------------
// | Copyright (C) 2008-2012 OSDU.Net    www.osdu.net    admin@osdu.net
// +----------------------------------------------------------------------
// | Licensed: ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author:   左手边的回忆 QQ:858908467 E-mail:858908467@qq.com
// +----------------------------------------------------------------------

class Thumb {

	public function __construct($file, $windth=48, $height=48){
		$this->resFile   = trim($file);
		$this->tmbWidth  = intval($windth);
		$this->tmbHeight = intval($height);
	}

	protected function getName($num=3){
		$files = array(
			DATA_PATH.'Cache/'.substr(md5($this->resFile),2,12).'.jpg',
			DATA_PATH.'Public/'.'nothumb.jpg'
		);
		return isset($files[$num])?$files[$num]:$files;
	}

	protected function getImageInfo() {
        $imageInfo = getimagesize($this->resFile);
        if( $imageInfo!== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]),1));
            $imageSize = filesize($this->resFile);
            $info = array(
                'width'=>$imageInfo[0], 'height'=>$imageInfo[1],
                'type'=>$imageType, 'size'=>$imageSize,
                'mime'=>$imageInfo['mime']
            );
            return $info;
        }else {
            return false;
        }
    }

	protected function tmbEffects($resWidth, $resHeight, $tmbWidth, $tmbHeight, $crop = true) {
		$x = $y = 0;
		$size_w = $size_h = 0;

		$scale1  = $resWidth / $resHeight;
		$scale2  = $tmbWidth / $tmbHeight;
		if ($scale1 < $scale2){
			$size_w = $resWidth;
			$size_h = round($size_w * ($tmbHeight / $tmbWidth));
			$y = ceil(($resHeight - $size_h)/2);
		}else{
			$size_h = $resHeight;
			$size_w = round($size_h * ($tmbWidth / $tmbHeight));
			$x = ceil(($resWidth - $size_w)/2);
		}
		return array($x, $y, $size_w, $size_h);
	}

	public function get(){
		return is_file($this->getName(0));
	}
	public function del(){
		if($this->get()) unlink($this->getName(0));
		return;
	}

	public function show($file=''){
		$tmp = $this->getName();
		$tmp = empty($file)?(is_file($tmp[0])?$tmp[0]:$tmp[1]):$file;
		header('Content-type: image/jpeg');
		header('Content-length: '.filesize($tmp));
		readfile($tmp);
	}

	public function create(){
		$info = $this->getImageInfo();
		$tmb  = $this->getName(0);

		if(!$info) return false;
		$new_info = $this->tmbEffects($info['width'], $info['height'], $this->tmbWidth, $this->tmbHeight);
		if($info['mime'] == 'image/jpeg'){
			$img = imagecreatefromjpeg($this->resFile);
		}elseif ($info['mime'] == 'image/png'){
			$img = imagecreatefrompng($this->resFile);
		}elseif ($info['mime'] == 'image/gif'){
			$img = imagecreatefromgif($this->resFile);
		}else{
			return false;
		}

		//dump($info);dump($new_info);
		if ($img &&  false != ($tmp = imagecreatetruecolor($this->tmbWidth, $this->tmbHeight))){
			if (!imagecopyresampled($tmp, $img, 0, 0, $new_info[0], $new_info[1], $this->tmbWidth, $this->tmbHeight, $new_info[2], $new_info[3])) {
				return false;
			}
			$result = imagejpeg($tmp, $tmb, 90);
			imagedestroy($img);
			imagedestroy($tmp);
		}
		return $result ? true : false;
	}
}
?>
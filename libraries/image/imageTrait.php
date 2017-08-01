<?php
namespace packages\ghafiye;
use \packages\base\image;
use \packages\base\options;
use \packages\base\IO\file;
use \packages\base\packages;
use \packages\base\db\dbObject;
trait imageTrait{
	public function getImage(int $width, int $height, string $key = 'image'){
		if($this->$key === null){
			$this->$key = options::get('packages.ghafiye.getImage.defaultImage');
			return $this->getImage($height, $width, $key);
		}
		static $package;
		if(!$package){
			$package = packages::package('ghafiye');
		}
		if(preg_match('/\\/(\w+)\\.(png|jpg|gif)$/', $this->$key, $matches)){
			$name = $matches[1];
			$suffix = $matches[2];
			$path = "storage/public/resized/{$name}_{$height}x{$width}.{$suffix}";
			$resized = new file\local($package->getFilePath($path));
			if($resized->exists()){
				return  $package->url($path);
			}
			$avatar = new file\local($package->getFilePath($this->$key));
			switch($suffix){
				case('jpg'):
					$image = new image\jpeg($avatar);
					break;
				case('gif'):
					$image = new image\gif($avatar);
					break;
				case('png'):
					$image = new image\png($avatar);
					break;
			}
			if(!$resized->getDirectory()->exists()){
				$resized->getDirectory()->make(true);
			}
			$image->resize($width, $height)->saveToFile($resized);
			return $package->url($path);
		}
	}
}

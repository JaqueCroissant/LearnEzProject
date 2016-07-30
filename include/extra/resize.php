<?php
class Resize
{
    private $image;
    private $width;
    private $height;
    private $image_resized;

    function __construct($fileName)
    {
        $this->image = $this->open_image($fileName);

        $this->width  = imagesx($this->image);
        $this->height = imagesy($this->image);
    }

    private function open_image($file)
    {
        $extension = strtolower(strrchr($file, '.'));

        switch($extension)
        {
            case '.jpg':
            case '.jpeg':
                $img = @imagecreatefromjpeg($file);
                break;
            case '.gif':
                $img = @imagecreatefromgif($file);
                break;
            case '.png':
                $img = @imagecreatefrompng($file);
                break;
            default:
                $img = false;
                break;
        }
        return $img;
    }

    public function resize_image($newWidth, $newHeight, $option="auto")
    {
        $optionArray = $this->get_dimensions($newWidth, $newHeight, $option);

        $optimalWidth  = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];


        
        $this->image_resized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        imagealphablending($this->image_resized, false);
        imagesavealpha($this->image_resized, true);
        $transparentindex = imagecolorallocatealpha($this->image_resized, 255, 255, 255, 127);
        imagefill($this->image_resized, 0, 0, $transparentindex);
        imagecopyresampled($this->image_resized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight, $this->width, $this->height);

        if ($option == 'crop') {
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
        }
    }

    private function get_dimensions($newWidth, $newHeight, $option)
    {
       switch ($option)
        {
            case 'exact':
                $optimalWidth = $newWidth;
                $optimalHeight= $newHeight;
                break;
            case 'portrait':
                $optimalWidth = $this->get_size_fixed_by_height($newHeight);
                $optimalHeight= $newHeight;
                break;
            case 'landscape':
                $optimalWidth = $newWidth;
                $optimalHeight= $this->get_size_fixed_by_width($newWidth);
                break;
            case 'auto':
                $optionArray = $this->get_size_by_auto($newWidth, $newHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
            case 'crop':
                $optionArray = $this->get_optimal_crop($newWidth, $newHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
        }
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    private function get_size_fixed_by_height($newHeight)
    {
        $ratio = $this->width / $this->height;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }

    private function get_size_fixed_by_width($newWidth)
    {
        $ratio = $this->height / $this->width;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }

    private function get_size_by_auto($newWidth, $newHeight)
    {
        if ($this->height < $this->width)
        // *** Image to be resized is wider (landscape)
        {
            $optimalWidth = $newWidth;
            $optimalHeight= $this->get_size_fixed_by_width($newWidth);
        }
        elseif ($this->height > $this->width)
        // *** Image to be resized is taller (portrait)
        {
            $optimalWidth = $this->get_size_fixed_by_height($newHeight);
            $optimalHeight= $newHeight;
        }
        else
        // *** Image to be resizerd is a square
        {
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight= $this->get_size_fixed_by_width($newWidth);
            } else if ($newHeight > $newWidth) {
                $optimalWidth = $this->get_size_fixed_by_height($newHeight);
                $optimalHeight= $newHeight;
            } else {
                $optimalWidth = $newWidth;
                $optimalHeight= $newHeight;
            }
        }

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }

    private function get_optimal_crop($newWidth, $newHeight)
    {

        $heightRatio = $this->height / $newHeight;
        $widthRatio  = $this->width /  $newWidth;

        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }

        $optimalHeight = $this->height / $optimalRatio;
        $optimalWidth  = $this->width  / $optimalRatio;

        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }


    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
    {
        $cropStartX = ( $optimalWidth / 2) - ( $newWidth /2 );
        $cropStartY = ( $optimalHeight/ 2) - ( $newHeight/2 );

        $crop = $this->image_resized;
        
        $this->image_resized = imagecreatetruecolor($newWidth , $newHeight);
        imagecopyresampled($this->image_resized, $crop , 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight , $newWidth, $newHeight);
    }

    public function save_image($savePath, $imageQuality="100")
    {
        $extension = strrchr($savePath, '.');
           $extension = strtolower($extension);

        switch($extension)
        {
            case '.jpg':
            case '.jpeg':
                if (imagetypes() & IMG_JPG) {
                    imagejpeg($this->image_resized, $savePath, $imageQuality);
                }
                break;

            case '.gif':
                if (imagetypes() & IMG_GIF) {
                    imagegif($this->image_resized, $savePath);
                }
                break;

            case '.png':
                $scaleQuality = round(($imageQuality/100) * 9);
                $invertScaleQuality = 9 - $scaleQuality;

                if (imagetypes() & IMG_PNG) {
                    
                    imagepng($this->image_resized, $savePath, $invertScaleQuality);
                }
                break;
                
            default:
                break;
        }

        imagedestroy($this->image_resized);
    }

}
?>
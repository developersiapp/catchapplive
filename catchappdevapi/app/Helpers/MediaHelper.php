<?php


namespace catchapp\Helpers;
use \Lakshmaji\Thumbnail\Facade\Thumbnail as Thumbnail;

class MediaHelper
{
    public static function generateThumbnail($video_path,$filename)
    {

// set storage path to store the file (image generated for a given video)
        $thumbnail_path   = base_path().'/public/uploads/VideoThumbnail/';

        $f[]= explode('.',$filename);
        // set thumbnail image name
//        $thumbnail_image  = $f[0][0].".jpg";
        $thumbnail_image  = $filename.".jpg";

        // get video length and process it
        // assign the value to time_to_image (which will get screenshot of video at that specified seconds)
        $time_to_image    = 2;

        $thumbnail_url="";
        $thumbnail_status = Thumbnail::getThumbnail($video_path,$thumbnail_path,$thumbnail_image,$time_to_image);
        if($thumbnail_status) {
            $thumbnail_url = env('APP_URL') . '/uploads/VideoThumbnail/' . $thumbnail_image;
        }
        return $thumbnail_url;
    }

    // Compress image
    public static function compressImage($source, $destination, $quality) {
        // Get image info
        $imgInfo = getimagesize($source);
        $mime = $imgInfo['mime'];

        // Create a new image from file
        switch($mime){
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            default:
                $image = imagecreatefromjpeg($source);
        }

        // Save image
        imagejpeg($image, $destination, $quality);

        // Return compressed image
        return $destination;
    }
}

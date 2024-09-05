<?php


namespace Izifir\Core\Helpers;


use Bitrix\Main\Application;

class Picture
{
    public static function getWebp($picture)
    {
        $docRoot = Application::getDocumentRoot();

        if (!empty($picture)) {
            if (intval($picture) > 0)
                $picturePath = \CFile::GetPath($picture);
            else
                $picturePath = $picture;

            if (function_exists('imageWebp')) {
                $pathInfo = pathinfo($picturePath);
                $img = false;
                $webPath = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['filename'] . '.webp';

                if (!file_exists($docRoot . $webPath)) {
                    $res = false;

                    switch ($pathInfo['extension']) {
                        case 'jpg':
                            $img = imageCreateFromJpeg($docRoot . $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename']);
                            break;
                        case 'png':
                            $img = imageCreateFromPng($docRoot . $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename']);
                            imagepalettetotruecolor($img);
                            imagealphablending($img, false);
                            imagesavealpha($img, true);
                            break;
                        case 'gif':
                            $img = imageCreateFromGif($docRoot . $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename']);
                            break;
                    }

                    if ($img) {
                        $res = imagewebp($img, $docRoot . $webPath);
                        imagedestroy($img);
                    }

                    if (!$res || !$img)
                        return false;
                } else {
                    $fileSize = filesize($docRoot . $webPath);
                    if ($fileSize == 0) {
                        unlink($docRoot . $webPath);
                        return self::getWebp($picture);
                    }
                }

                return $webPath;
            }
        }
        return false;
    }
}

<?php

namespace Models;

class ImageManager
{
    public static function getUniqImageName( $imgFile )
    {
        return $imgFile = md5( uniqid( rand(), 1 ) );
    }

    public static function getImageNameWithoutExtension( $imgFile )
    {
        return pathinfo( $imgFile, PATHINFO_FILENAME );
    }

    public static function getImageExtensionWithoutName( $imgFile )
    {
        return "." . pathinfo( $imgFile, PATHINFO_EXTENSION );
    }

    public static function getFullUniqImageFileName( $imgName, $imgExtension )
    {
        return "$imgName" . "$imgExtension";
    }

    public static function validateImageLocation( $FileLocation )
    {
        if (is_file( $FileLocation ) && file_exists( $FileLocation )) {
            return true;
        } else {
            return false;
        }
    }

    public static function deleteImageFile( $imgName )
    {
        if (unlink( $imgName )) {
            return true;
        } else {
            return false;
        }
    }

    public static function moveUploadedImageFile( $imgTmpName, $location )
    {
        return move_uploaded_file( $imgTmpName, $location );
    }

    public static function cutImagePathForDB( $imgPath, $needle )
    {
        $needPoss = strpos( $imgPath, $needle );
        $imgPath = substr( $imgPath, $needPoss );
        return $imgPath;
    }
}
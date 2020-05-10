<?php

namespace AppBundle\Service;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploadService
 *
 * @author PedroRossi
 */
class UploadService
{

    public static function compress($source, $destination, $quality)
    {
        $info = getimagesize($source);
        if ($info['mime'] == 'image/jpeg') {
            $image = imagecreatefromjpeg($source);
        } elseif ($info['mime'] == 'image/gif') {
            $image = imagecreatefromgif($source);
        } elseif ($info['mime'] == 'image/png') {
            $image = imagecreatefrompng($source);
        }

        imagejpeg($image, $destination, $quality);
        return $destination;
    }

    public static function validation($source, $destination)
    {
        if ($source->getMimeType() == 'application/msword' || $source->getMimeType() == 'application/pdf' || $source->getMimeType() == 'application/x-pdf') {
            move_uploaded_file($source->getPathname(), $destination);
        } else {
            throw new \Exception("Formato inválido!");
        }

        return $destination;

    }

    public static function upload($source, $destination)
    {
        move_uploaded_file($source->getPathname(), $destination);
        return $destination;

    }
}

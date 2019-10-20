<?php

namespace App\Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;

class CommonHelper
{

    /**
     * @param Validator $validator
     *
     * @return null|string
     */
    public static function formatErrorsMessage($errors = [])
    {
        $errMgs = [];

        foreach ($errors as $key => $error) {
            $errMgs = array_merge($errMgs, $error);
        }

        $errMgs = array_values(array_unique($errMgs));

        if (!$errMgs) {
            return null;
        }

        return implode('<br>', $errMgs);
    }

    public static function renameUnique($path, $filename)
    {
        $pathFile = "$path/$filename";
        if (!file_exists($pathFile)) {
            return $filename;
        }

        $fileNameNotExisted = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $i = 1;
        while (file_exists("$path/$fileNameNotExisted($i).$ext")) {
            $i++;
        }

        return "$fileNameNotExisted($i).$ext";
    }

    public static function deleteFile($pathFile)
    {
        try {
            unlink($pathFile);
        } catch (\Exception $ex) {
            return false;
        }

        return true;
    }

    public static function getAuth($key = '')
    {
        try {
            $user = JWTAuth::user();
            return $key != '' ? (empty($user->{$key}) ? null : $user->{$key}) : $user;
        } catch (\Exception $ex) {
            return null;
        }
    }

    public static function convertVersionToNumber($strVersion)
    {
        $number = str_replace('.', '', $strVersion);

        return (int)$number;
    }

    public static function increaseCode($prefixCode, $codeExisting, $padLength = 5)
    {
        $code = explode('-', $codeExisting);
        if (count($code) >= 2) {
            $num = $code[1] + 1;
            $num = str_pad($num, $padLength, '0', STR_PAD_LEFT);
            $codeNext = $prefixCode . $num;
            return $codeNext;
        }

        return $codeExisting;
    }
}
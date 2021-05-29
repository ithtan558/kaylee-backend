<?php

namespace App\Helpers;

use App\Model\Config;
use App\Models\Order;
use Exception;
use stdClass;
use Tymon\JWTAuth\Facades\JWTAuth;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Str;

class CommonHelper
{

    /**
     * @param Validator $validator
     *
     * @return null|string
     */
    public static function formatErrorsMessage($errors = [])
    {
        $errors_tmp = ERRORS;
        $errMgs     = [];

        foreach ($errors as $key => $error) {
            $obj = new stdClass();
            foreach ($errors_tmp as $key_tmp => $error_tmp) {
                if ($key == $key_tmp) {
                    $obj->code    = $error_tmp;
                    $obj->message = $error[0];
                    break;
                }
            }
            $errMgs[] = $obj;
        }

        if (!$errMgs) {
            return null;
        }

        return $errMgs;
    }

    public static function renameUnique($path, $filename)
    {
        $pathFile = "$path/$filename";
        if (!file_exists($pathFile)) {
            return $filename;
        }

        $fileNameNotExisted = pathinfo($filename, PATHINFO_FILENAME);
        $ext                = pathinfo($filename, PATHINFO_EXTENSION);

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
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    public static function getAuth($key = '')
    {
        try {
            $user = JWTAuth::user();
            return $key != '' ? (empty($user->{$key}) ? null : $user->{$key}) : $user;
        } catch (Exception $ex) {
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
            $num      = $code[1] + 1;
            $num      = str_pad($num, $padLength, '0', STR_PAD_LEFT);
            $codeNext = $prefixCode . $num;
            return $codeNext;
        }

        return $codeExisting;
    }

    public static function uploadImage($request, $width = 300, $height = 300)
    {
        $filename = '';
        if ($request->hasFile('image')) {
            $image        = $request->file('image');
            $ext          = '.' . $request->image->getClientOriginalExtension();
            $filename     = Str::slug(str_replace($ext, microtime(), $request->image->getClientOriginalName())).$ext;
            $image_resize = Image::make($image->getRealPath());
            $image_resize->orientate();
            $image_resize->resize($width, $height, function ($s) {
                $s->aspectRatio();
            });
            $image_resize->save(public_path(DIR_UPLOAD . $filename));
        }
        return $filename;
    }

    public static function createRandomCode($length = 10)
    {


        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        do {
            for ($i = 0; $i <= 10; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            $order = Order::where("code", $randomString)->get()->pluck("value", "key");
            if (count($order) == 0)
                break;
        } while(true);

        return $randomString;
    }

    public static function createRandomOtp($length = 4)
    {


        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        do {
            for ($i = 0; $i <= 3; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            break;
        } while(true);

        return $randomString;
    }

    public static function randomNumberSequence($requiredLength = 7, $highestDigit = 8) {
        $sequence = '';
        for ($i = 0; $i < $requiredLength; ++$i) {
            $sequence .= mt_rand(0, $highestDigit);
        }
        return $sequence;
    }

    public static function fullAddress($city, $district, $wards, $location) {
        return $location .', '. $wards .' - '. $district .' - '. $city;
    }

    public static function secondsToWords($seconds)
    {
        /*** get the days ***/
        $days = intval(intval($seconds) / (3600*24));
    }

    /**
     * @param Validator $validator
     *
     * @return null|string
     */
    public static function removeInformationForDefaultAccount(&$data = [], $client_id, $brand_id = false)
    {
        unset($data['id']);
        $data['client_id'] = $client_id;
        if ($brand_id) {
            $data['brand_id'] = $brand_id;
        }
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

}

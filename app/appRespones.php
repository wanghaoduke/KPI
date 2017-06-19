<?php namespace App;

class AppResponse
{
    public static function result($isBool, $data = null)
    {
        $status = $isBool ? 200 : 400;

        $jsonAry['data'] = $data;
        return response()->json($jsonAry, $status);
    }

}
<?php
namespace App\Services;

use PDO;
use DB;
use Cache;
use Log;
use Session;
use Input;

class MainServices
{
	public static function maskId($id)
	{
	    if(env('DISABLE_ID_CRYPT')) return $id;
	    if (null === $id) return null;
		$integer = $id << 32;
		$bin = decbin($integer);

		$mask = decbin(1);

		$newInteger = $bin & $mask;
		$newInteger = bindec($newInteger);

		return (int)($integer.$newInteger);
	}

	public static function unmaskId($id)
	{
        if(env('DISABLE_ID_CRYPT')) return $id;
        if (null === $id) return null;
		$id = (int)substr($id, 0, -1);

		return $id >> 32;
	}
}
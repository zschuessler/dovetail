<?php
namespace SquareBit\Dovetail;

class Facade extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dovetail';
    }
}
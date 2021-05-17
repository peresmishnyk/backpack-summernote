<?php


namespace Peresmishnyk\BackpackSummernote;


use Illuminate\Support\Facades\Facade;

class HTMLCleaner extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'html-filter';
    }
}

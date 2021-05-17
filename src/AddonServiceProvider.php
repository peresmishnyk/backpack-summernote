<?php

namespace Peresmishnyk\BackpackSummernote;

use Illuminate\Support\ServiceProvider;

class AddonServiceProvider extends ServiceProvider
{
    use AutomaticServiceProvider;

    protected $vendorName = 'peresmishnyk';
    protected $packageName = 'backpack-summernote';
    protected $commands = [];
}

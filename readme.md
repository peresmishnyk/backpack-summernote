# BackpackSummernote

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![The Whole Fruit Manifesto](https://img.shields.io/badge/writing%20standard-the%20whole%20fruit-brightgreen)](https://github.com/the-whole-fruit/manifesto)

This package provides additional functionality for projects that use
the [Backpack for Laravel](https://backpackforlaravel.com/) administration panel.

More exactly, it adds pre configured summernote field and HTML cleaner so that you can easily edit rich content and has
clear HTML after.

## Screenshots

> **// TODO: add a screenshot and delete these lines;**
> to add a screenshot to a github markdown file, the easiest way is to
> open an issue, upload the screenshot there with drag&drop, then close the issue;
> you now have that image hosted on Github's servers; so you can then right-click
> the image to copy its URL, and use that URL wherever you want (for example... here)

![Backpack Toggle Field Addon](https://via.placeholder.com/600x250?text=screenshot+needed)

## Installation

Via Composer

``` bash
composer require peresmishnyk/backpack-summernote
```

## Usage

To use the field this package provides, inside your custom CrudController do:

```php
$this->crud->addField([
    'name' => 'article',
    'label' => 'Article body',
    'type' => 'summernote',
    'view_namespace' => 'peresmishnyk.backpack-summernote::fields',
]);
```

Notice the ```view_namespace``` attribute - make sure that is exactly as above, to tell Backpack to load the field from
this _addon package_, instead of assuming it's inside the _Backpack\CRUD package_.

To clear submited HTML, in your model do:

```php
HTMLCleaner::clear($value);
```

OR

```php
    public static function boot()
    {
        parent::boot();

        // Setup event bindings...
        News::saving(function (News $news) {
            // Use custom rules
            $rules = 'video,source,strong,b,u,i,br,p[class],span[class|style],a[href|target],h1,h2,h3,h4,h5,h6,img[src|style|width|height|data-filename],hr,code,blockquote,ul,ol,li,iframe,font[color],table,tr,td,th,thead,colgroup,col,tfoot,tbody,strike,sup,sub';
            $news->body = HTMLCleaner::clear($news->body, rules);
        });
    }
```

## Overwriting

If you need to change the field in any way, you can easily publish the file to your app, and modify that file any way
you want. But please keep in mind that you will not be getting any updates.

**Step 1.** Copy-paste the blade file to your directory:

```bash
# create the fields directory if it's not already there
mkdir -p resources/views/vendor/backpack/crud/fields

# copy the blade file inside the folder we created above
cp -i vendor/peresmishnyk/backpack-summernote/src/resources/views/fields/field_name.blade.php resources/views/vendor/backpack/crud/fields/field_name.blade.php
```

**Step 2.** Remove the vendor namespace wherever you've used the field:

```diff
$this->crud->addField([
    'name' => 'agreed',
    'type' => 'toggle',
    'label' => 'I agree to the terms and conditions',
-   'view_namespace' => 'peresmishnyk.backpack-summernote::fields'
]);
```

**Step 3.** Uninstall this package. Since it only provides one file, and you're no longer using that file, it makes no
sense to have the package installed:

```bash
composer remove peresmishnyk/backpack-summernote
```

## Change log

Changes are documented here on Github. Please see
the [Releases tab](https://github.com/peresmishnyk/backpack-summernote/releases).

## Testing

``` bash
composer test
```

## Contributing

Please see [contributing.md](contributing.md) for a todolist and howtos.

## Security

If you discover any security related issues, please email michkire@gmail.com instead of using the issue tracker.

## Credits

- [Michkire Dmytro][link-author]
- [All Contributors][link-contributors]

## License

This project was released under MIT, so you can install it on top of any Backpack & Laravel project. Please see
the [license file](license.md) for more information.

However, please note that you do need Backpack installed, so you need to also abide by
its [YUMMY License](https://github.com/Laravel-Backpack/CRUD/blob/master/LICENSE.md). That means in production you'll
need a Backpack license code. You can get a free one for non-commercial use (or a paid one for commercial use)
on [backpackforlaravel.com](https://backpackforlaravel.com).


[ico-version]: https://img.shields.io/packagist/v/peresmishnyk/backpack-summernote.svg?style=flat-square

[ico-downloads]: https://img.shields.io/packagist/dt/peresmishnyk/backpack-summernote.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/peresmishnyk/backpack-summernote

[link-downloads]: https://packagist.org/packages/peresmishnyk/backpack-summernote

[link-author]: https://github.com/peresmishnyk

[link-contributors]: ../../contributors

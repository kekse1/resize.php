<img src="https://kekse.biz/php/count.php?override=github:resize.php&text=`resize.php`" />

# Overview
Just a image resizer; but it supports both **BROWSER** and **CLI** mode!

Either by real pixel size, or by a floating point factor (>0.0 and <1.0).

The output image will also keep it's proportions, unless you define the `?quad` mode,
or a target pixel size with `!` suffix in the console.

## Usage
In the **browser** you can call this script via `...?input=file&size=(int|float)[&quad]`. In the **command line** you'll see the
syntax by just calling this script w/o (or with less than 3) parameters. The regular syntax is: `$0 <input file> <output file> <(int|float)[!]>`.

Here the **`!`** sign after an (integer!) size is the replacement for the browser's `?quad`, so the resulting image won't be
scaled in it's proportions, but result in a quadratic image. A **float** size (needs to be greater than 0.0 and lower than 1.0)
will scale the image by this ratio, an **integer** will result in this real pixel size.

> **Warning**
> **UP**-scaling is _never ever_ allowed, only **DOWN**-scaling.. so the output image always needs to be smaller than the input image;
> which is one the one hand because larger outputs never got the same quality, and, on the other hand (which weights even more!) so
neither the disk drive nor the bandwidth/traffic is affected that much! **;)~**

## Configuration
.. is simply put on top of the script:

| define()'d **KEY**             | Default **VALUE** | Meaning                                                                   |
| -----------------------------: | :---------------- | :------------------------------------------------------------------------ |
| **`KEKSE_RESIZE_DIRECTORY`**   | `getcwd()`        | For relative paths this will be inserted at their beginnings.             |
| **`KEKSE_RESIZE_ANY_BROWSER`** | `true`            | `any` means any input image ... and no output pixel limit of 512 ..       |
| **`KEKSE_RESIZE_ANY_CLI`**     | `true`            | .. otherwise only emojis are supported (file type and file size limits).. |

## Download
* [**`resize.php`** v**0.5.0**](php/resize.php)
* [**`resize.sh`** wrapper](sh/resize.sh)

## Dependencies
* [`count.php`](https://github.com/kekse1/count.php/)

This `count.php` dependency because I just wrote really nice functions in there (yes, it's my own implementation), so
e.g. the most used `getParam()`, which is a really secure way of handling the `$_GET[]`, also with paths (so injection
is not really possible here, e.g.).

It also got nice console features (with ANSI colors, styles, etc.); and especially for such cases I also coded there the
`KEKSE_RAW` feature, to use everything in other scripts, without the real `counter()` function to be called automatically.

The `count.php` needs to be located in the same directory where this script is put into. A symbolic link is also valid! ;)~

## Example screenshot
![Example screenshot](docs/example.png)

## Bugs and TODO
_Problem_ is (**here**!): I also wanted to resize animated emojis, but animation seems not to be supported.. at least in my
PHP version with it's own GD library.. additionally I've got problems with `WebP (.webp)` images; dunno..

And the TODO, especially for _thumbnails_, is: a **cache** in the file system! So only one time necessary (for each requested size!!).
But that's somewhere in the future, I'm not going to support this script as much as my other ones.. just got no more time.

## Copyright and License
The Copyright is [(c) Sebastian Kucharczyk](COPYRIGHT.txt),
and it's licensed under the [MIT](LICENSE.txt) (also known as 'X' or 'X11' license).

<img src="https://kekse.biz/php/count.php?override=github:resize.php&text=`resize.php`" />

# Overview
Supports both BROWSER and CLI mode. Either via real size (1..512), or by a floating factor (>0.0 and <1.0).

ONLY DOWNscaling allowed. Everything else makes no real sence (for security, but also because this script wasn't only
intended for on-demand emoji scaling (because my original files are bigger than the requested target sizes..), but also
for _automatic **thumbnail** creation_; .. and last but not least: UPscaling doesn't increase the image _quality_. ;)~

## TODO
Especially for thumbnails: a **cache** in the file system! So only one time necessary (for each requested size!!). ;)~
But that's future, I'm not going to support this script as much as my other ones.. just got no more time.

## Example screenshot
![Example screenshot](docs/example.png)

## Dependencies
* [`count.php`](https://github.com/kekse1/count.php/)

## Security
Partially by the `count.php` exports (which are great! ;-) .. and by setting the `KEKSE_RESIZE_ANY{,CLI}` (..etc.?);

## More details ..
... will follow. Please **read the fucking source** until then.

## Download
* [**`resize.php`** v**0.3.1**](php/resize.php)
* [**`resize.sh`** wrapper](sh/resize.sh)

## Copyright and License
The Copyright is [(c) Sebastian Kucharczyk](COPYRIGHT.txt),
and it's licensed under the [MIT](LICENSE.txt) (also known as 'X' or 'X11' license).

<?php

/*
 * Copyright (c) Sebastian Kucharczyk <kuchen@kekse.biz>
 * https://github.com/kekse1/
 *
 * v0.3.1 (.. erste test-version);
 * ... read the fucking source, pls.
 *
 * //comments/
 * php v8.2.7: animierte bilder ohne animation resized, und .webp gehen erstmal garnicht.
 * .. weder in CLI noch im browser. :/~
 *
 */

//
namespace kekse;

//
define('KEKSE_RAW', true);
require_once(__DIR__ . '/count.php');

//
namespace kekse\resize;

//
define('KEKSE_RESIZE_VERSION', '0.4.1');
define('KEKSE_RESIZE_WEBSITE', 'https://github.com/kekse1/resize.php/');
define('KEKSE_RESIZE_DIRECTORY', getcwd());
define('KEKSE_RESIZE_ANY_BROWSER', true);//will check if (IMG_WEBP | IMG_GIF) and if (size == 512)! otherwise all images are supported, including a resize FACTOR (float) instead of 0..512(int)!
define('KEKSE_RESIZE_ANY_CLI', true);//in CLI mode not only emojies supported... :)~

//
define('KEKSE_RESIZE_ANY', ((KEKSE_CLI && KEKSE_RESIZE_ANY_CLI) || (!KEKSE_CLI && KEKSE_RESIZE_ANY_BROWSER)));

//
function write($_message = '', $_exit = null, $_errStr = true)
{
	if(!is_string($_message))
	{
		$_message = '';
	}
	
	if(!is_int($_exit)) $_exit = null;
	
	if(KEKSE_CLI)
	{
		if($_message === '')
		{
			$_message = PHP_EOL;
			if($_exit === null || $_exit === 0) printf($_message);
			else fprintf(\STDERR, $_message);
		}
		else if($_exit === 0 || $_exit === null) printf(($_message === '' ? '' : ' >> ') . $_message . PHP_EOL);
		else fprintf(\STDERR, ' >> ' . ($_errStr ? '[ERROR] ' : '') . $_message . PHP_EOL);
	}
	else
	{
		header('Content-Type: text/plain;charset=utf-8');
		header('Content-Length: ' . strlen($_message));
		echo $_message;
	}
	
	if($_exit !== null) exit(abs($_exit) % 256);
}

//
if(!extension_loaded('gd'))
{
	return write('The `GD` library isn\'t available! So nothing\'s possible..', 254);
}

//
$param = array('input' => null, 'output' => null, 'size' => null, 'scale' => null);

//
function checkParameters()
{
	//
	global $param;
	
	//
	if(KEKSE_CLI)
	{
		if($GLOBALS['KEKSE_ARGC'] < 4)
		{
			return write('Syntax: <input> <output> <integer/float[`!`]>', 1);
		}
		else
		{
			$param['input'] = $GLOBALS['KEKSE_ARGV'][1];
			$param['output'] = $GLOBALS['KEKSE_ARGV'][2];
			$param['size'] = $GLOBALS['KEKSE_ARGV'][3];
			
			if($param['size'][strlen($param['size']) - 1] === '!')
			{
				$param['size'] = substr($param['size'], 0, -1);
				$param['scale'] = true;
			}

			$param['size'] = (float)$param['size'];
		}
	}
	else
	{
		$param['input'] = \kekse\getParam('input');
		$param['output'] = null;
		$param['size'] = \kekse\getParam('size', true, true, true);
		$param['scale'] = isset($_GET['scale']);
	}

	if(is_float($param['size']) && fmod($param['size'], 1) == 0) $param['size'] = (int)$param['size'];
	if(is_float($param['size'])) $param['scale'] = null;

	if(is_int($param['size']))
	{
		if(!KEKSE_RESIZE_ANY && ($param['size'] < 1 || $param['size'] > 512)) return write('Size exceeds limit [1..512]!', 11);
	}
	else if(is_float($param['size']))
	{
		if(!KEKSE_RESIZE_ANY) return write('You may not scale by float factor in emoji mode!', 13);
		else if($param['size'] <= 0.0) return write('Size scale factor is too low', 14);
		else if($param['size'] >= 1.0) return write('Size scale factor is too high', 15);
	}
	else
	{
		return write('Invalid `size` parameter (integer [1..512] expected)', 2);
	}
	
	//
	if(KEKSE_RESIZE_DIRECTORY)
	{
		if($param['input'] && $param['input'][0] !== '/') $param['input'] = KEKSE_RESIZE_DIRECTORY . '/' . $param['input'];
		if($param['output'] && $param['output'][0] !== '/') $param['output'] = KEKSE_RESIZE_DIRECTORY . '/' . $param['output'];
	}

	if(! is_file($param['input']))
	{
		return write('Invalid `input` parameter (no such file)', 4);
	}
	else if($param['output'])
	{
		if(file_exists($param['output']))
		{
			return write('Output file already exists!', 5);
		}

		$dir = dirname($param['output']);
		
		if(file_exists($dir))
		{
			if(!is_dir($dir)) return write('File can\'t be created in a non-directory!', 6);
		}
		else
		{
			return write('Directory `' . $dir . '` (where the output file was meant to be created) doesn\'t exist', 7);
		}
	}
	
	//
	return $param;
}

//
if(KEKSE_CLI)
{
	write(KEKSE_WEBSITE);
	write();
	write('Copyright (c) ' . KEKSE_COPYRIGHT);
	write(KEKSE_RESIZE_WEBSITE);
	write('v' . KEKSE_RESIZE_VERSION);
	write();
	if(!KEKSE_RESIZE_ANY) write('You are only allowed to resize emojis, btw...' . PHP_EOL);
}

//
checkParameters();

//
function resize(&$_param)
{
	//
	$inputMeasure = getimagesize($_param['input']);
	$width = $inputMeasure[0]; $height = $inputMeasure[1];
	//$type = $inputMeasure[2];
	$mime = $inputMeasure['mime'];

	//
	if(!KEKSE_RESIZE_ANY && ($width != 512 || $height != 512))
	{
		return write('Seems not to be a valid emoji (=> size)..', 8);
	}
	else if(!KEKSE_RESIZE_ANY && !($mime === 'image/webp' || $mime === 'image/gif'))
	{
		return write('Seems not to be a valid emoji (=> type)..', 9);
	}
	
	//
	$func = array('resize' => 'imagecopyresampled', 'create' => '', 'output' => '');
	
	switch($mime)
	{
		case 'image/webp':
			$func['create'] = 'imagecreatefromwebp';
			$func['output'] = 'imagewebp';
			break;
		case 'image/gif':
			$func['create'] = 'imagecreatefromgif';
			$func['output'] = 'imagegif';
			break;
		case 'image/jpeg';
			$func['create'] = 'imagecreatefromjpeg';
			$func['output'] = 'imagejpeg';
			break;
		case 'image/png':
			$func['create'] = 'imagecreatefrompng';
			$func['output'] = 'imagepng';
			break;
		default:
			return write('Invalid MIME type', 10);
	}

	if(! ($func['create'] && $func['output'])) return write('Couldn\'t find matching create and/or output image functions.', 11);

	//
	$setDetails = function(&$_image)
	{
		if(!$_image) return $_image = false;
		imagesavealpha($_image, true);
		imagealphablending($_image, true);
		imageinterlace($_image, true);
		return $_image;
	};
	
	//
	$targetWidth = null;
	$targetHeight = null;

	if(is_int($_param['size']))
	{
		if($_param['scale'] && !($width === $height))
		{
			$max = max($width, $height);
			$scale = ((float)$_param['size'] / (float)$max);
			$targetWidth = ((float)$width * $scale);
			$targetHeight = ((float)$height * $scale);
		}
		else
		{
			$targetWidth = $targetHeight = $_param['size'];
		}
	}
	else
	{
		$targetWidth = ((float)$width * $_param['size']);
		$targetHeight = ((float)$height * $_param['size']);
	}

	$targetWidth = (int)round($targetWidth);
	$targetHeight = (int)round($targetHeight);

	//
	$input = null;
	$output = null;

	//
	if(($input = $func['create']($_param['input'])) === false) return write('Couldn\'t load input image!', 12);
	$setDetails($input);
	if(($output = imagecreatetruecolor($targetWidth, $targetHeight)) === false) return write('Couldn\'t initialize output image!', 12);
	$setDetails($output);
	$transparent = imagecolorallocatealpha($output, 255, 255, 255, 127);
	imagefill($output, 0, 0, $transparent);
	if($func['resize']($output, $input, 0, 0, 0, 0, $targetWidth, $targetHeight, imagesx($input), imagesy($input)) === false)
	return write('Unable to resize the image!', 13);
	imagedestroy($input);
	if(!$mime) $mime = image_type_to_mime_type(image_type($output));

	//
	if(!$_param['output']) header('Content-Type: ' . $mime);
	$func['output']($output, $_param['output']);
	
	if(KEKSE_CLI)
	{
		write('         `' . basename($_param['input']) . '` => `' . basename($_param['output']) . '`');
		write(' [width] ' . $width . ' => ' . $targetWidth);
		write('[height] ' . $height . ' => ' . $targetHeight);
		write('  [size] ' . filesize($_param['input']) . ' => ' . filesize($_param['output']));
	}
	
	//
	imagedestroy($output);
	return $output;
}

//
$res = resize($param);

//

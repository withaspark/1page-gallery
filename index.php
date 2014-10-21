<?php
/*
The MIT License (MIT)

Copyright (c) 2014 Stephen Parker

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/



////////////////////////////////////
// CONFIGURATION
////////////////////////////////////

// Heading to title page
$siteName = '1Page Gallery';

// Duration to display each slide in milliseconds
$slideTime = 5000;

// Your logo; leave blank if wish not to use one
$logo = '';

// Your Google Analytics ID
$googleAnalytics = '';

// END OF CONFIGURATION



// Location of zip file containing all images for download
$zip = '';

// Array of all images
$images = array();

// Array of all thumbnails
$thumbs = array();

// Directory images were generated to; this should match build script
$dir = 'images/';

// Directory thumbnails were generated to; this should match build script
$thumbDir = 'thumbs/';

// Get zip of all images if available
if ($handle = opendir(getcwd())) {
	while (($file = readdir($handle)) !== false) {
		if (strstr(strtolower($file), '.zip')) {
			$zip = $file;
			break;
		}
	}
	closedir($handle);
}

// Get all images in the directory
if ($handle = opendir($dir)) {
	while (($image = readdir($handle)) !== false) {
		if (
			strstr(strtolower($image), '.jpg') ||
			strstr(strtolower($image), '.jpeg')
		) {
			// If thumbnail exists, add
			if (file_exists($thumbDir.$image)) {
				$images[] = str_replace(' ','%20', $dir . $image);
				$thumbs[] = str_replace(' ','%20', $thumbDir . $image);
			}
		}
	}
	closedir($handle);
}

// If no images, complain and exit
if (sizeof($images) < 1) {
        echo "
<!doctype html>
<html lang='en'>
        <head>
                <meta charset='utf-8'>
                <title>Error: 1Page Gallery</title>
        </head>
        <body>
                <h1>Error: 1Page Gallery</h1>
                <p>No images found! You must create 2 directories containing images:</p>
                <p><code>images</code> should contain your high resolution images</p>
                <p><code>thumbs</code> should contain thumbnails named identical to your high resolution images</p>
                <p>Optionally, you can upload a zip file to the same directory as this file and from a terminal type</p>
                <pre>./build</pre>
                <p>to generate correctly sized images for both in the correct location</p>
        </body>
</html>";
	exit;
}
?>
<!doctype html>
<html lang='en'>
<head>
	<meta charset='utf-8'>
	<title><?php echo $siteName; ?></title>

	<script src='//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js'></script>
	<link href='http://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
	<style type='text/css'>
	body {
		padding: 1em;
		background-color: #444;
		color: #fff;
		font-family: 'Montserrat', Verdana, sans-serif;
		font-size: 0.8em;
	}
	h1 {
		margin: 0em 0em 0.5em 0em;
		font-size: 2em;
		font-weight: normal;
		text-align: center;
	}
	h1 img {
		max-width: 33%;
		min-width: 200px;
	}
	a, a:hover, a:link, a:visited, a:active {
		color: #ddd;
		outline: 0;
	}
	a:hover {
		text-decoration: none;
	}
	.clear { clear: both; }
	#page {
		max-width: 700px;
		margin: auto auto;
		padding: 1em;
		opacity: 0.9;
		background-color: #222;
	}
	#nav {
		margin: 0.5em auto;
		text-align: right;
	}
	#footer {
		max-width: 700px;
		margin: auto auto;
		padding: 1em;
		opacity: 0.7;
	}
	#stage {
		position: relative;
		width: 100%;
		max-height: 450px;
		object-fit: contain;
		text-align: center;
		overflow: hidden;
	}
	#stage img {
		max-height: 120%;
		min-height: 100%;
		width: auto;
		max-width: 100%;
	}
	#stage .control {
		position: absolute;
		width: 50px;
		top: 0px;
		bottom: 0px;
		font-size: 3em;
		text-align: center;
		background-color: #222;
		opacity: 0;
		cursor: pointer;
	}
	#stage .control.show {
		opacity: 0.3;
	}
	#stage .control.hover {
		opacity: 0.7;
	}
	.control.previous {
		left: 0px;
		right: auto;
	}
	.control.next {
		left: auto;
		right: 0px;
	}
	.control a {
		position: absolute;
		top: 50%;
		left: 0px;
		width: 100%;
		margin-top: -50%;
		text-decoration: none;
		vertical-align: middle;
	}
	#selector {
		position: relative;
		width: 100%;
		height: 5em;
		margin: 1em 0em;
		padding: 0em;
		list-style: none;
		overflow-x: scroll;
		overflow-y: hidden;
		white-space: nowrap;
	}
	.thumbnail {
		display: inline-block;
		margin-left: 0px;
	}
	.thumbnail img {
		height: 3.5em;
		width: auto;
		opacity: 0.2;
		cursor: pointer;
	}
	.currentThumb, .thumbnail img:hover {
		opacity: 1.0 !important;
	}
	</style>
</head>
<body>
<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>

	<div id='page'>
		<h1>
<?php
		if ($logo != '')
			echo "			<img src='$logo' alt='$siteName'>";
		else
			echo "			$siteName";
?>
		</h1>

		<div id='slideshow'>
			<div id='stage'>
				<img id='centerStage' src='.jpg' alt='Gallery Image'>
				<div class='control previous'><a class='previous' href='#'>&lt;</a></div>
				<div class='control next'><a class='next' href='#'>&gt;</a> </div>
				<div class='clear'> </div>
			</div>
			<ul id='selector'>
<?php
$counter = 0;
foreach ($thumbs as $thumb) {
	echo "				<li class='thumbnail'><img class='changer' id='changeTo$counter' src='$thumb' alt='Gallery Thumbnail'></li>\n";
	$counter++;
}
?>
			</ul>
			<div class='clear'> </div>
		</div>

<?php
if ($zip != "") {
	echo "		<div id='nav'><a href='$zip'>Download All Images</a></div>";
}
?>

	</div>

	<div id='footer'>
		A <a href='http://withaspark.com/1page/'>1Page Gallery</a> by <a href='http://withaspark.com'>Stephen Parker</a>.<br>Available on <a href='https://github.com/withaspark/1page-gallery'>GitHub</a>.
	</div>



	<script type='text/javascript'>
	var curImage = 0;
	var autoPlay = true;
	var timer = 0;
	var images = [
<?php
foreach ($images as $image)
	echo "		'$image',\n";
?>
	];

	$(document).ready(function() {
		// Load the first image
		ChangeTo(0);

		// Scale stage based on size of screen
		setTimeout(function() {
			var height = 0.75 * $('#stage').width();
			var maxHeight = 0.6 * $(window).height();
			if (height > maxHeight)
				height = maxHeight;
			$('#stage').height(height);
		}, 100);

		// Poll every second to display new image
		setInterval(function() {
			if (autoPlay) {
				timer = timer + 1000;
				if (timer > <?php echo $slideTime; ?>) {
					timer = 0;
						NextImage();
				}
			}
		}, 1000);
	});

	// Clicking the main image will advance to the next image
	$('#centerStage').click(function() {
		NextImage();
	});

	// Clicking a thumbnail image will change to it
	$('.changer').click(function() {
		var index = $(this).attr('id').replace(/ChangeTo/gi, '');
		ChangeTo(index);
	});

	// Hovering over center stage shows next and previous links
	$('#centerStage').parent().hover(function() {
		$('.control').addClass('show');
	}, function() {
		$('.control').removeClass('show');
	});

	// Hovering over control accents next and previous links
	$('.control').hover(function() {
		$(this).addClass('hover');
	}, function() {
		$(this).removeClass('hover');
	});

	// If click any elements with next class advance
	$('.next').click(function() {
		NextImage();
		return false;
	});

	// If click any elements with previous class go back
	$('.previous').click(function() {
		PreviousImage();
		return false;
	});

	// Function to change to the next image
	function NextImage() {
		autoplay = true;
		timer = 0;
		if (curImage + 1 < images.length)
			curImage++;
		else
			curImage = 0;
		ChangeTo(curImage);
	}

	// Function to change to the previous image
	function PreviousImage() {
		autoplay = false;
		timer = 0;
		if (curImage > 0)
			curImage--;
		else
			curImage = images.length - 1;
		ChangeTo(curImage);
	}

	// Function to change to a specific image
	function ChangeTo(index) {
		var elpos = $('#changeTo' + index).position().left;
		var fadeDuration = 400;
		autoplay = true;
		timer = 0;
		curImage = parseInt(index);
		$('#centerStage').fadeOut(fadeDuration);
		setTimeout(function() {
			$('#centerStage').attr('src', images[index]).fadeIn();
		}, fadeDuration);
		$('.changer').removeClass('currentThumb');
		$('#changeTo' + index).addClass('currentThumb');
		$('#selector').scrollLeft($('.changer').outerWidth() * index);
	}

	// Function to preload images
	preload(images);
	function preload(imageArray) {
		$(imageArray).each(function() {
			$('<img/>').attr('src', this).appendTo('body').hide();
		});
	}
	</script>

<?php
if ($googleAnalytics != '') {
?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $googleAnalytics; ?>', 'auto');
  ga('require', 'displayfeatures');
  ga('require', 'linkid', 'linkid.js');
  ga('send', 'pageview');
</script>
<?php
}
?>

</body>
</html>

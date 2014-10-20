1Page Gallery
=============

An auto-generated, simple, single-page jQuery, HTML5, CSS3, PHP photo gallery from a zip file of images.

1Page Gallery is simply the easiest photo gallery to deploy for non-programmers and developers needing to give clients a simple photo gallery with minimal footprint. The 1Page Gallery is a jQuery, HTML5, CSS3, PHP slideshow gallery which will automatically create a simple, elegant desktop and mobile-ready image gallery.

Simply add the index.php, build script, and a zip of your high resolution images and a simple,
elegant jQuery, HTML5, CSS3, PHP photo gallery will be auto-magically generated.

Free for all purposes, personal and commercial under the MIT license. See [LICENSE](LICENSE). Attribution in the footer is certainly appreciated, but not required; however, license text must remain intact on index.php.



###Dependencies:###
- php5
- imagemagick (to use build script)
- unzip

To install dependencies,

```sh
sudo apt-get update && sudo apt-get install php5 imagemagick unzip
```
or
```sh
sudo yum update && sudo yum install php5 imagemagick unzip
```

###Installation:###
1. Clone/checkout this repo
2. Place a zip file containing your high resolution images at the same level as index.php
3. Run build script from same directory as zip file to generate thumbnails and reasonably small images
   ```sh
   ./build
   ```

###Configuration###
- In index.php
```php
// Heading to title page
$siteName = '1Page Gallery';

// Duration to display each slide in milliseconds
$slideTime = 5000;
```

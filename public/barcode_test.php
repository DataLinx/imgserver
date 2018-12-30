<!DOCTYPE html>
<?php
require '../vendor/autoload.php';
require '../config/barcode.config.php';

use \DataLinx\ImgServer\BarcodeHelper;
?>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Barcode generator view test</title>
    </head>
    <body>
		<h1>EAN-13 Code: 9313920040041</h1>

		<h2>Embed as SVG</h2>
		<img src="<?= BarcodeHelper::embedSVG('9313920040041') ?>"/>
		<h2>Embed as PNG</h2>
		<img src="<?= BarcodeHelper::embedPNG('9313920040041') ?>"/>
		<h2>Embed as JPG</h2>
		<img src="<?= BarcodeHelper::embedJPG('9313920040041') ?>"/>
		<h2>Embed as HTML</h2>
		<?= BarcodeHelper::html('9313920040041') ?>

		<h2>Width: 1</h2>
		<img src="<?= BarcodeHelper::embedSVG('9313920040041', BarcodeHelper::DEFAULT_TYPE, 1) ?>"/>
		<h2>Width: 3</h2>
		<img src="<?= BarcodeHelper::embedSVG('9313920040041', BarcodeHelper::DEFAULT_TYPE, 3) ?>"/>

		<h2>Height: 50px</h2>
		<img src="<?= BarcodeHelper::embedSVG('9313920040041', BarcodeHelper::DEFAULT_TYPE, BarcodeHelper::DEFAULT_WIDTH_FACTOR, 50) ?>"/>

		<h2>Color: red</h2>
		<img src="<?= BarcodeHelper::embedSVG('9313920040041', BarcodeHelper::DEFAULT_TYPE, BarcodeHelper::DEFAULT_WIDTH_FACTOR, BarcodeHelper::DEFAULT_HEIGHT, 'red') ?>"/>

		<h2>Code 39</h2>
		<img src="<?= BarcodeHelper::embedSVG('9313920040041', BarcodeHelper::TYPE_CODE_39) ?>"/>

		<h1>UPC Code: 1234567899992</h1>
		<?php
		$upc = new BarcodeHelper(BarcodeHelper::DEFAULT_FORMAT, BarcodeHelper::TYPE_UPC_A);
		$upc->setColor('blue')
				->setHeight(60);
		?>
		<h2>Embed as blue SVG 60px</h2>
		<img src="<?= $upc->getBarcode('1234567899992') ?>"/>

    </body>
</html>

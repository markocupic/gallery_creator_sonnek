<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'MCupic',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Src
	'MCupic\GalleryCreatorSonnek\GalleryCreatorSonnek' => 'system/modules/gallery_creator_sonnek/src/classes/GalleryCreatorSonnek.php',
	'MCupic\RunScheduler'                              => 'system/modules/gallery_creator_sonnek/src/modules/RunScheduler.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'fe_start_scheduler' => 'system/modules/gallery_creator_sonnek/templates',
));

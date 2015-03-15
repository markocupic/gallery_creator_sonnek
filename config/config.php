<?php

$GLOBALS['TL_HOOKS']['gc_generateFrontendTemplate'][] = array('MCupic\GalleryCreatorSonnek\GalleryCreatorSonnek', 'modifyTemplate');

/**
 * Front end module
 */
array_insert($GLOBALS['FE_MOD'], 2, array('scheduler' => array('start_scheduler' => 'MCupic\RunScheduler')));



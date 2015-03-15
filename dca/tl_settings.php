<?php

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('{gallery_creator_legend:hide}', '{gallery_creator_legend:hide},gc_upload_folder_observer_delete_orphaned_entries', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_settings']['fields']['gc_upload_folder_observer_delete_orphaned_entries'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_settings']['gc_upload_folder_observer_delete_orphaned_entries'],
    'inputType' => 'checkbox',
    'eval'      => array('fieldType' => 'checkbox', 'tl_class' => 'clr')
);
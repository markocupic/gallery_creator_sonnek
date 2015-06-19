<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package Gallery Creator
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */



/**
 * Table tl_gallery_creator_albums
 */
// Sortierreihenfolge im Backend (asc = aufsteigend, desc = absteigend)
// default date, desc
$GLOBALS['TL_DCA']['tl_gallery_creator_albums']['list']['sorting']['field'] = 'date';
$GLOBALS['TL_DCA']['tl_gallery_creator_albums']['list']['sorting']['direction'] = 'desc';

// Labelformatierung in der Albenauflistung
$GLOBALS['TL_DCA']['tl_gallery_creator_albums']['list']['label']['format'] = '<span style="#padding-left#"><a href="#href#" title="#title#"><img src="#icon#"></span> <span style="color:#b3b3b3; padding-left:3px;">[%s] [#count_pics# images]</span></a> <span>#datum#</span>';


// Callback Funktionen registrieren
$GLOBALS['TL_DCA']['tl_gallery_creator_albums']['config']['onload_callback'][] = array('tl_gallery_creator_albums_sonnek', 'onloadCbSetUpPalettes');
$GLOBALS['TL_DCA']['tl_gallery_creator_albums']['config']['onload_callback'][] = array('tl_gallery_creator_albums_sonnek', 'setAlbumSorting');


// Fields
$GLOBALS['TL_DCA']['tl_gallery_creator_albums']['fields']['observeAssignedDir'] = array(

    'label'            => &$GLOBALS['TL_LANG']['tl_gallery_creator_albums']['observeAssignedDir'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'eval'             => array('doNotShow' => false, 'submitOnChange' => false),
    'sql'              => "char(1) NOT NULL default ''"
);

// Fields
$GLOBALS['TL_DCA']['tl_gallery_creator_albums']['fields']['deleteOrphanedDatarecords'] = array(

    'label'            => &$GLOBALS['TL_LANG']['tl_gallery_creator_albums']['deleteOrphanedDatarecords'],
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'eval'             => array('doNotShow' => false, 'submitOnChange' => false),
    'sql'              => "char(1) NOT NULL default ''"
);

/**
 * Class tl_gallery_creator_albums_sonnek
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @copyright  Marko Cupic
 * @author     Marko Cupic
 * @package    GalleryCreator
 */
class tl_gallery_creator_albums_sonnek extends Backend
{
    /**
     * onload-callback
     * create the palette
     */
    public function onloadCbSetUpPalettes()
    {
        // Entfernen von event_location
        $strDefault = $GLOBALS['TL_DCA']['tl_gallery_creator_albums']['palettes']['default'];
        $GLOBALS['TL_DCA']['tl_gallery_creator_albums']['palettes']['default'] = str_replace('event_location', '', $strDefault);

        // Add Field to default palette
        $GLOBALS['TL_DCA']['tl_gallery_creator_albums']['palettes']['default'] = str_replace('assignedDir', 'assignedDir,observeAssignedDir,deleteOrphanedDatarecords', $GLOBALS['TL_DCA']['tl_gallery_creator_albums']['palettes']['default']);

    }

    /**
     * onload-callback
     * Set album sorting
     */
    public function setAlbumSorting()
    {
        $sortingField = strlen($GLOBALS['TL_DCA']['tl_gallery_creator_albums']['list']['sorting']['field']) ? $GLOBALS['TL_DCA']['tl_gallery_creator_albums']['list']['sorting']['field'] : 'date';
        $arrField = array();
        $objAlbums = MCupic\GalleryCreatorAlbumsModel::findAll();
        if($objAlbums === null)
        {
            return;
        }
        while($objAlbums->next())
        {
            $arrField[$objAlbums->id] = strtolower($objAlbums->$sortingField);
        }
        if($GLOBALS['TL_DCA']['tl_gallery_creator_albums']['list']['sorting']['direction'] == 'desc')
        {
            // desc
            arsort($arrField);
        }
        else
        {
            // asc
            asort($arrField);
        }
        $sorting = 100;
        foreach($arrField as $albumId => $v)
        {
            $objAlbum = MCupic\GalleryCreatorAlbumsModel::findByPk($albumId);
            if($objAlbum === null)
            {
                continue;
            }
            $sorting = $sorting + 100;
            $objAlbum->sorting = $sorting;
            $objAlbum->save();
        }
    }
}



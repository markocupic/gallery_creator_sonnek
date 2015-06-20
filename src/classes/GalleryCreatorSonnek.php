<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package Gallery Creator
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */
/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace MCupic\GalleryCreatorSonnek;

/**
 * Class GcHelpers
 * Provide methods for using the gallery_creator extension
 *
 * @copyright  Marko Cupic 2015
 * @author     Marko Cupic, Oberkirch, Switzerland ->  mailto: m.cupic@gmx.ch
 * @package    GalleryCreatorSonnek
 */
class GalleryCreatorSonnek extends \System
{

    /*
     * Accepted files
     */
    public static $acceptedFiles = 'jpeg,jpg,png,gif,tiff';

    /**
     * observerUploadFolder
     */
    public static function observeUploadFolder()
    {
        // Disable E_NOTICE
        error_reporting(E_ERROR | E_WARNING | E_PARSE);

        // Get album model
        $objAlbum = \MCupic\GalleryCreatorAlbumsModel::findAll();

        if ($objAlbum === null)
        {
            return;
        }
        while ($objAlbum->next())
        {
            if (!$objAlbum->observeAssignedDir)
            {
                continue;
            }

            $arrImgSRC = [];

            // Case 1:
            // Clean Datarecords if there is no related file on the server

                $objPictureModel = \MCupic\GalleryCreatorPicturesModel::findByPid($objAlbum->id);
                if ($objPictureModel !== null)
                {
                    while ($objPictureModel->next())
                    {
                        if (\Validator::isUuid($objPictureModel->uuid))
                        {
                            $objFilesModel = \FilesModel::findByUuid($objPictureModel->uuid);
                            if ($objFilesModel !== null)
                            {
                                $arrImgSRC[] = $objFilesModel->path;
                                if($objAlbum->deleteOrphanedDatarecords)
                                {
                                    if (!is_file(TL_ROOT . '/' . $objFilesModel->path))
                                    {
                                        \System::log('DELETE FROM tl_gallery_creator_pictures WHERE id=' . $objPictureModel->id, __METHOD__, TL_GENERAL);
                                        $objPictureModel->delete();
                                    }
                                }
                            }
                        }
                    }
                }


            // Case 2:
            // Check the album directory for some new files
            $arrNewFiles = array();
            $objFolderModel = \FilesModel::findByUuid($objAlbum->assignedDir);
            if ($objFolderModel === null)
            {
                continue;
            }
            if ($objFolderModel->type != 'folder')
            {
                continue;
            }

            // Scan the album directory
            $arrFiles = scan(TL_ROOT . '/' . $objFolderModel->path);

            foreach ($arrFiles as $strPath)
            {

                if (is_file(TL_ROOT . '/' . $objFolderModel->path . '/' . $strPath))
                {
                    $strFileSRC = $objFolderModel->path . '/' . $strPath;
                    $objFile = new \File($strFileSRC);
                    if ($objFile->isGdImage)
                    {
                        if (in_array($strFileSRC, $arrImgSRC))
                        {
                            // Continue, if the image-file is already member ob the album
                            continue;
                        }

                        if (in_array($objFile->extension, explode(',', self::$acceptedFiles)))
                        {

                            // clean filename
                            $strNewName = $objFile->dirname . '/' . $objFile->filename . '.' . $objFile->extension;
                            $strNewName = str_replace(TL_ROOT . '/', '', $strNewName);
                            $strNewName = strip_tags($strNewName);
                            $strNewName = utf8_romanize($strNewName);
                            $strNewName = str_replace('"', '', $strNewName);
                            $strNewName = str_replace(' ', '_', $strNewName);
                            if (preg_match('/\.$/', $strNewName))
                            {
                                throw new Exception($GLOBALS['TL_LANG']['ERR']['invalidName']);
                            }

                            if ($objFile->renameTo($strNewName))
                            {
                                $arrNewFiles[] = $strNewName;
                            }
                        }
                    }
                }
            }
            asort($arrNewFiles);
            foreach ($arrNewFiles as $newFileSRC)
            {
                // Add new entry in the dbafs
                \Dbafs::addResource($newFileSRC);
                // Write new entry to tl_gallery_creator_pictures
                \MCupic\GalleryCreator\GcHelpers::createNewImage($objAlbum->id, $newFileSRC);
            }
        }
    }

    /**
     * @param \Module $objModule
     * @param null $objAlbum
     * @return mixed
     */
    public function modifyTemplate(\Module $objModule, $objAlbum = null)
    {
        return $objModule->Template;
        //die(print_r($objModule->Template,true));
        global $objPage;
        $objPage->pageTitle = 'Bildergalerie';
        if ($objAlbum !== null)
        {
            // display the album name in the head section of your page (title tag)
            $objPage->pageTitle = specialchars($objAlbum->name);
            // display the album comment in the head section of your page (description tag)
            $objPage->description = specialchars(strip_tags($objAlbum->comment));
            // add the album name to the keywords in the head section of your page (keywords tag)
            $GLOBALS['TL_KEYWORDS'] .= ',' . specialchars($objAlbum->name) . ',' . specialchars($objAlbum->event_location);
        }
    }
}

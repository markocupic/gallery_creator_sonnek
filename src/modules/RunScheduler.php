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
 * Run in a custom namespace, so the class can be replaced
 */

namespace MCupic;

/**
 * Class RunScheduler
 * @package Contao
 */
class RunScheduler extends \Module
{
    protected $strTemplate = 'fe_start_scheduler';

    /**
     * Parse the template
     * @return string
     */
    public function generate()
    {
        if(!is_file(TL_ROOT . '/system/modules/cron/public/CronController.php'))
        {
            return '';
        }
        return parent::generate();

    }

    /**
     * Generate the module
     */
    protected function compile()
    {
        $this->Template->baseUrl = \Environment::get('base');
    }



}
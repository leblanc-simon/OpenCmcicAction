<?php
/*
 * This file is part of the OpenCmcicAction package.
 * (c) 2011  Simon Leblanc <contact@leblanc-simon.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/lib/sfConfig.class.php';
require_once dirname(__FILE__).'/lib/sfException.class.php';
require_once dirname(__FILE__).'/lib/sfToolkit.class.php';
require_once dirname(__FILE__).'/lib/sfWebBrowserInvalidResponseException.class.php';
require_once dirname(__FILE__).'/lib/sfCurlAdapter.class.php';
require_once dirname(__FILE__).'/lib/sfFopenAdapter.class.php';
require_once dirname(__FILE__).'/lib/sfSocketsAdapter.class.php';
require_once dirname(__FILE__).'/lib/sfWebBrowser.class.php';
require_once dirname(__FILE__).'/lib/Cmcic.class.php';

// define folder
sfConfig::set('sf_data_dir', dirname(__FILE__).DIRECTORY_SEPARATOR.'data');
sfConfig::set('sf_log_dir', dirname(__FILE__).DIRECTORY_SEPARATOR.'log');

// define options browser
sfConfig::set('cmcic_options', array(
  'cookies'         => true,
  'verbose'         => true,
  'verbose_log'     => true,
  'useragent'       => 'Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.9.2.23; OpenCmcicAction) Gecko/20110921 Ubuntu/10.04 (lucid) Firefox/3.6.23',
  'SSL_VERIFYPEER'  => false,
));

// define connection parameter
sfConfig::set('cmcic_login', '[your login]');
sfConfig::set('cmcic_pass',  '[your password]');
sfConfig::set('cmcic_tpe',   '[your tpe number]');


/**
 * Récupère l'objet Cmcic
 *
 * @param   array   $options    Le tableau des options du browser
 * @return  Cmcic               L'oject Cmcic
 */
function getCmcic($options)
{
  // Init browser
  Cmcic::setClassName('sfWebBrowser');
  Cmcic::setAdapter('sfCurlAdapter');
  Cmcic::setOptions(array_merge(sfConfig::get('cmcic_options'), $options));
  
  // Launch browser
  return new Cmcic(sfConfig::get('cmcic_login'), sfConfig::get('cmcic_pass'), sfConfig::get('cmcic_tpe'));
}
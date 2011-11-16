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
$options = array(
  'cookies'         => true,
  /*'verbose'         => false,*/
  'verbose_log'     => true,
  'useragent'       => 'Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.9.2.23) Gecko/20110921 Ubuntu/10.04 (lucid) Firefox/3.6.23',
  'SSL_VERIFYPEER'  => false,
);

try {
  // Init browser
  Cmcic::setClassName('sfWebBrowser');
  Cmcic::setAdapter('sfCurlAdapter');
  Cmcic::setOptions($options);
  
  // Launch browser
  $cmcic = new Cmcic('[your login]', '[your password]', '[your tpe ident]');
  
  // Get all recurrent payment between begin and end
  $begin  = new DateTime();
  $end    = new DateTime();
  
  $begin->setDate(2011, 01, 01);
  $end->setDate(2011, 12, 31);
  
  $payments = $cmcic->getRecurrentPayments($begin, $end);
  
  echo "\n\n****************\nrecurrent payments\n";
  var_dump($payments);*/
  
  // Cancel one recurrent payment with a reference
  /*$res = $cmcic->cancelPayment('[reference to cancel]');
  echo "\n\n****************\ncancel payment\n";
  var_dump($res);*/
  
} catch (Exception $e) {
  echo "******************************************\n";
  echo "*               ERROR                    *\n";
  echo "******************************************\n";
  echo $e->getMessage()."\n";
}

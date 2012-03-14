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
  // Get date
  if ($argc !== 3) {
    throw new Exception('Nombre d\'argument incorrect');
  }
  $debut = $argv[1];
  $fin   = $argv[2];
  
  if (preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $debut, $matches_debut) == 0) {
    throw new Exception('La date de debut n\'est pas correcte : format yyyy-mm-dd');
  }
  if (preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $fin, $matches_fin) == 0) {
    throw new Exception('La date de fin n\'est pas correcte : format yyyy-mm-dd');
  }
  
  
  // Init browser
  Cmcic::setClassName('sfWebBrowser');
  Cmcic::setAdapter('sfCurlAdapter');
  Cmcic::setOptions($options);
  
  // Launch browser
  $cmcic = new Cmcic('[your login]', '[your password]', '[your tpe ident]');
  
  // Get all recurrent payment between begin and end
  $begin  = new DateTime();
  $end    = new DateTime();
  
  $begin->setDate((int)$matches_debut[1], (int)$matches_debut[2], (int)$matches_debut[3]);
  $end->setDate((int)$matches_fin[1], (int)$matches_fin[2], (int)$matches_fin[3]);
  
  $payments = $cmcic->getRecurrentPayments($begin, $end);
  
  $csv = '';
  foreach ($payments as $payment) {
    $csv .= '"'.$payment['date'].'",';
    $csv .= '"'.$payment['reference'].'",';
    $csv .= '"'.$payment['amount'].'"'."\n";
  }
  
  file_put_contents(__DIR__.'/recurrents.csv', $csv);
  
  exit(0);
  
} catch (Exception $e) {
  echo "******************************************\n";
  echo "*               ERROR                    *\n";
  echo "******************************************\n";
  echo $e->getMessage()."\n";
  exit(1);
}

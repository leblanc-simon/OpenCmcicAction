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
  if ($argc !== 2) {
    throw new Exception('Nombre d\'argument incorrect');
  }
  $nb_occurences = $argv[1];
  if (is_numeric($nb_occurences) === false) {
    throw new Exception('L\'argument doit être un entier');
  }
  // Init browser
  Cmcic::setClassName('sfWebBrowser');
  Cmcic::setAdapter('sfCurlAdapter');
  Cmcic::setOptions($options);
  
  // Launch browser
  $cmcic = new Cmcic('[your login]', '[your password]', '[your tpe ident]');
  
  // Cancel all payments with more than $nb_occurences occurences
  $references = $cmcic->getPaymentsWithMore($nb_occurences);
  foreach ($references as $reference) {
    $res = $cmcic->cancelPayment($reference);
    if ($res === true) {
      echo "Traitement reussi de la reference : ".$reference."\n";
    } else {
      echo "Erreur lors du traitement de la reference : ".$reference."\n";
    }
  }
  
  exit(0);
  
} catch (Exception $e) {
  echo "******************************************\n";
  echo "*               ERROR                    *\n";
  echo "******************************************\n";
  echo $e->getMessage()."\n";
  exit(1);
}

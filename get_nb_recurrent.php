<?php
/*
 * This file is part of the OpenCmcicAction package.
 * (c) 2011  Simon Leblanc <contact@leblanc-simon.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__).'/config.inc.php';

try {
  // check args
  if ($argc !== 1) {
    throw new Exception('Nombre d\'argument incorrect');
  }
  
  // Launch browser
  $cmcic = getCmcic();
  
  // Launch program
  $nb_payments = $cmcic->getNbCurrentPayments();
  
  if ($nb_payments === false) {
    throw new Exception('Erreur lors de la récupération du nombre de paiements récurrents : '.print_r($cmcic->getErrors(), true));
  }
  
  echo $nb_payments;
  
  exit(0);
  
} catch (Exception $e) {
  echo "******************************************\n";
  echo "*               ERROR                    *\n";
  echo "******************************************\n";
  echo $e->getMessage()."\n";
  
  exit(1);
}

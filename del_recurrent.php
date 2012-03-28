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
  // Check args
  if ($argc !== 2) {
    throw new Exception('Nombre d\'argument incorrect');
  }
  
  $nb_occurences = $argv[1];
  if (is_numeric($nb_occurences) === false) {
    throw new Exception('L\'argument doit être un entier');
  }
  
  // Launch browser
  $cmcic = getCmcic();
  
  // Get all payments with more than $nb_occurences occurences
  $references = $cmcic->getPaymentsWithMore($nb_occurences);
  
  // Check result
  if ($references === false) {
    throw new Exception('Erreur lors de la récupération des références : '.print_r($cmcic->getErrors(), true));
  }
  
  // For each payment with more than $nb_occurences, cancel it
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

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
  if ($argc !== 3) {
    throw new Exception('Nombre d\'argument incorrect');
  }
  
  // Get dates
  $debut = $argv[1];
  $fin   = $argv[2];
  
  if (preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $debut, $matches_debut) == 0) {
    throw new Exception('La date de debut n\'est pas correcte : format yyyy-mm-dd');
  }
  if (preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/', $fin, $matches_fin) == 0) {
    throw new Exception('La date de fin n\'est pas correcte : format yyyy-mm-dd');
  }
  
  // Get all recurrent payment between begin and end
  $begin  = new DateTime();
  $end    = new DateTime();
  
  $begin->setDate((int)$matches_debut[1], (int)$matches_debut[2], (int)$matches_debut[3]);
  $end->setDate((int)$matches_fin[1], (int)$matches_fin[2], (int)$matches_fin[3]);
  
  // Launch browser
  $cmcic = getCmcic();
  
  // Launch program
  $payments = $cmcic->getRecurrentPayments($begin, $end);
  
  // Check result
  if ($payments === false) {
    throw new Exception('Erreur lors de la récupération des paiements : '.print_r($cmcic->getErrors(), true));
  }
  
  // Write result
  $csv = '';
  foreach ($payments as $payment) {
    $csv .= '"'.$payment['date'].'",';
    $csv .= '"'.$payment['reference'].'",';
    $csv .= '"'.$payment['amount'].'"'."\n";
  }
  
  file_put_contents(__DIR__.'/recurrents.csv', $csv, FILE_APPEND);
  
  exit(0);
  
} catch (Exception $e) {
  echo "******************************************\n";
  echo "*               ERROR                    *\n";
  echo "******************************************\n";
  echo $e->getMessage()."\n";
  
  exit(1);
}

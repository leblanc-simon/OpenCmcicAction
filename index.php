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
  // Launch browser
  $cmcic = getCmcic();
  
  // Get all recurrent payment between begin and end
  $begin  = new DateTime();
  $end    = new DateTime();
  
  $begin->setDate(2011, 01, 01);
  $end->setDate(2012, 01, 01);
  
  $payments = $cmcic->getRecurrentPayments($begin, $end);
  
  echo "\n\n****************\nrecurrent payments\n";
  var_dump($payments);
  
  // Cancel one recurrent payment with a reference
  /*$res = $cmcic->cancelPayment('[reference to cancel]');
  echo "\n\n****************\ncancel payment\n";
  var_dump($res);*/
  
  exit(0);
  
} catch (Exception $e) {
  echo "******************************************\n";
  echo "*               ERROR                    *\n";
  echo "******************************************\n";
  echo $e->getMessage()."\n";
  
  exit(1);
}

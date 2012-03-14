<?php
/*
 * This file is part of the OpenCmcicAction package.
 * (c) 2011  Simon Leblanc <contact@leblanc-simon.eu>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cmcic action class
 *
 * @package    OpenCmcicAction
 * @author     Simon Leblanc <contact@leblanc-simon.eu>
 * @version    0.1
 */
class Cmcic
{
  const URL_BASE = 'https://paiement.creditmutuel.fr/bo'; // url de base de l'application
  const SLEEP_CALL = 3; // Nombre de seconde entre chaque appel
  
  static private $browser = null;
  static private $options = array();
  static private $class_name = null;
  static private $adapter = null;
  
  static private $tpe = null;
  
  static private $errors = array();
  
  static private $last_call = 0;
  
  /**
   * Constructeur : login à l'application
   *
   * @param   string  $login      Le login de l'administration du TPE
   * @param   string  $password   Le mot de passe de l'administration du TPE
   * @param   string  $tpe        L'identifiant du tpe virtuel
   * @access  public
   */
  public function __construct($login, $password, $tpe)
  {
    if ($this->login($login, $password) === false) {
      throw new Exception('unable to login');
    }
    
    if (is_string($tpe) === false) {
      throw new Exception('tpe must be a string');
    }
    
    self::$tpe = (string)$tpe;
  }
  
  
  /**
   * Annule un paiement
   *
   * @param   string  $reference  La référence du paiement à annuler
   * @return  bool                True en cas de succes, False sinon
   * @access  public
   */
  public function cancelPayment($reference)
  {
    if (is_string($reference) === false) {
      throw new Exception('reference must be a string');
    }
    
    self::checkSleepCall();
    
    self::getBrowser()->get(self::URL_BASE.'/liste_paiements.cgi?TPE='.self::$tpe.'&type=PR&ref='.$reference);
    if (self::checkValid() === false) {
      return false;
    }
    
    $dom = self::getBrowser()->getResponseDom();
    $xpath = new DomXpath($dom);
    
    if ($radio = $xpath->query('//input[@type="radio" and @value="S"]')->item(0)) {
      $name = $radio->getAttribute('name');
    } else {
      return false;
    }
    
    self::checkSleepCall();
    
    self::getBrowser()->setField($name, 'S')->click('Exécuter');
    if (self::checkValid() === false) {
      return false;
    }
    
    return true;
  }
  
  
  /**
   * Récupère la liste des paiements récurrent durant une période
   *
   * @param   DateTime  $begin  La date de début de la période
   * @param   DateTime  $end    La date de fin de la période
   * @return  array             La liste des paiements durant la période
   * @access  public
   */
  public function getRecurrentPayments(DateTime $begin, DateTime $end)
  {
    $payments = array();
    
    // Si les date ne sont pas dans le bon ordre, on les replace correctement
    if ($begin > $end) {
      $end_tmp = clone $end;
      $end = $begin;
      $begin = $end_tmp;
    }
    
    while ($begin < $end) {
      $url  = self::URL_BASE.'/journee.cgi?TPE='.self::$tpe.'&tri=paiement';
      $url .= '&jour='.$begin->format('d').'&mois='.$begin->format('m').'&annee='.$begin->format('Y');
      
      self::checkSleepCall();
      
      self::getBrowser()->get($url);
      if (self::checkValid() === false) {
        continue;
      }
      
      $dom = self::getBrowser()->getResponseDom();
      
      $xpath = new DomXpath($dom);
      
      $links = $xpath->query('//td/a[@onmouseenter]');
      foreach ($links as $link) {
        $link = self::URL_BASE.'/'.$link->getAttribute('href');
        
        self::checkSleepCall();
        
        self::getBrowser()->get($link);
        if (self::checkValid() === false) {
          continue;
        }
        
        $dom = self::getBrowser()->getResponseDom();
        
        $xpath = new DomXpath($dom);
        
        $date = $xpath->query('//table[3]/tr[3]/td[1]')->item(0);
        $reference = $xpath->query('//table[3]/tr[3]/td[3]')->item(0);
        $amount = $xpath->query('//table[3]/tr[3]/td[4]')->item(0);
        
        if ($date && $reference && $amount) {
          $payments[] = array(
            'date'      => $date->nodeValue,
            'reference' => $reference->nodeValue,
            'amount'    => (float)str_replace(' EUR', '', $amount->nodeValue),
          );
        }
      }
      
      $begin->modify('+1 day');
    }
    
    return $payments;
  }
  
  
  /**
   * Récupère le nombre de paiement récurrent en cours
   *
   * @return  int       Le nombre de paiement récurrent en cours
   * @access  public
   */
  public function getNbCurrentPayments()
  {
    $url = self::URL_BASE.'/liste_paiements.cgi?TPE='.self::$tpe.'&type=PR&ref=';
    self::getBrowser()->get($url);
    if (self::checkValid() === false) {
      continue;
    }
    
    $dom = self::getBrowser()->getResponseDom();
    
    file_put_contents(sfConfig::get('sf_data_dir').'/cmcic'.__METHOD__.'.txt', $dom->saveHTML());
    
    $xpath = new DomXpath($dom);
    $nb_items = $xpath->query('//form[1]/table[1]/tr')->length - 1;
    
    return $nb_items;
  }
  
  
  /**
   * Récupère les référence avec un nombre d'occurence minimum 
   *
   * @param   int   $nb_occurence   Le nombre d'occurence limite
   * @return  array                 Les références de plus de $nb_occurance
   * @access  public
   */
  public function getPaymentsWithMore($nb_occurence = 12)
  {
    $url = self::URL_BASE.'/liste_paiements.cgi?TPE='.self::$tpe.'&type=PR&ref=';
    self::getBrowser()->get($url);
    if (self::checkValid() === false) {
      continue;
    }
    
    $dom = self::getBrowser()->getResponseDom();
    
    file_put_contents(sfConfig::get('sf_data_dir').'/cmcic'.__METHOD__.'.txt', $dom->saveHTML());
    
    $xpath = new DomXpath($dom);
    $items = $xpath->query('//form[1]/table[1]/tr[td[8]>='.$nb_occurence.']/td[2]');
    
    $references = array();
    for ($i = 0; $i < $items->length; $i++) {
        $references[] = strip_tags($items->item($i)->nodeValue);
    }
    
    return $references;
  }
  
  
  /**
   * Indique si une requête a échouée
   *
   * @return  bool  True en cas d'erreur, false sinon
   * @access  public
   */
  public function hasError()
  {
    return (bool)count(self::$errors);
  }
  
  
  /**
   * Retourne l'ensemble des erreurs survenues
   *
   * @return  array   Le tableau contenant l'ensemble des erreurs (les dernieres en premier dans le tableau)
   * @access  public
   */
  public function getErrors()
  {
    return self::$errors;
  }
  
  
  /**
   * Retourne la dernière erreur
   *
   * @return  array   Le tableau (code, browser, exception) contenant l'erreur
   * @access  public
   */
  public function getLastError()
  {
    if ($this->hasError() === true) {
      return null;
    }
    
    return self::$errors[0];
  }
  
  
  /**
   * Défini les options
   *
   * @param   array     $options    Un tableau contenant les options de l'adaptateur
   * @access  public
   * @static
   */
  static public function setOptions($options)
  {
    if (is_array($options) === false) {
      throw new Exception('option mustbe an array');
    }
    
    self::$options = $options;
  }
  
  
  /**
   * Défini la classe utilisé pour la navigation
   *
   * @param   string  $class_name   Le nom de la classe a utiliser pour naviguer dans le site
   * @access  public
   * @static
   */
  static public function setClassName($class_name)
  {
    if (is_string($class_name) === false) {
      throw new Exception('class_name must be a string');
    }
    
    if (class_exists($class_name) === false) {
      throw new Exception($class_name.' doesn\'t exist');
    }
    
    self::$class_name = (string)$class_name;
  }
  
  
  /**
   * Défini l'adaptateur
   *
   * @param   string  $adapter    Le nom de l'adaptateur à utiliser
   * @access  public
   * @static
   */
  static public function setAdapter($adapter)
  {
    if (is_string($adapter) === false) {
      throw new Exception('adapter must be a string');
    }
    
    if (class_exists($adapter) === false) {
      throw new Exception($adapter.' doesn\'t exist');
    }
    
    self::$adapter = (string)$adapter;
  }
  
  
  /**
   * Connection à l'application de gestion du TPE
   *
   * @param   string  $login      Le login de l'administration du TPE
   * @param   string  $password   Le mot de passe de l'administration du TPE
   * @return  bool                True si la connection est valide
   * @access  private   
   */
  private function login($login, $password)
  {
    self::checkSleepCall();
    
    self::getBrowser()
      ->post(
        self::URL_BASE.'/identification.cgi',
        array(
          'identifiant' => $login,
          'mot_de_passe' => $password,
        )
      );
      
    return $this->checkValid();
  }
  
  
  /**
   * Patiente entre 2 requêtes
   *
   * @access  private
   * @static
   */
  static private function checkSleepCall()
  {
    $time = time();
    $next_call = self::$last_call + self::SLEEP_CALL;
    
    if ($time < $next_call) {
      sleep($next_call - $time);
    }
    
    self::$last_call = time();
  }
  
  
  /**
   * Vérifie que le retour est valide
   *
   * @return  bool  True si l'appel renvoie un code 200, False sinon
   * @access  private
   */
  private function checkValid()
  {
    if (self::getBrowser()->responseIsError() === true) {
      $error = array(
        'code'      => self::getBrowser()->getResponseCode(),
        'browser'   => self::getBrowser(),
        'exception' => new Exception(),
      );
      array_unshift(self::$errors, $error);
      
      return false;
    }
    
    return true;
  }
  
  
  /**
   * Retourne l'objet permettant de naviguer dans l'administration du TPE
   *
   * @return  Browser   un objet de type Browser
   * @access  private
   * @static
   */
  static private function getBrowser()
  {
    if (self::$browser === null) {
      if (self::$class_name === null) {
        throw new Exception('class_name must de defined');
      }
      
      if (self::$adapter === null) {
        throw new Exception('adapter must be defined');
      }
      
      $class = self::$class_name;
      
      self::$browser = new $class(array(), self::$adapter, self::$options);
    }
    
    return self::$browser;
  }
}

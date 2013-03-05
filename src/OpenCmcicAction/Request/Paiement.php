<?php
/**
 * This file is part of the OpenCmcicAction package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenCmcicAction\Request;

use OpenCmcicAction\Core\Config;
use OpenCmcicAction\Exception\Exception;
use OpenCmcicAction\Exception\Paiement as EPaiement;

use Goutte\Client;


/**
 * Paiement request class
 *
 * @package     OpenCmcicAction\Request
 * @version     1.0.0
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
class Paiement
{
    /**
     * The beginning date of the search
     * 
     * @access  private
     */
    private $begin_date = null;
    
    /**
     * The ending date of the search
     * 
     * @access  private
     */
    private $end_date = null;
    
    /**
     * Constructor
     *
     * @param   \DateTime   $begin  the beginning date of the search
     * @param   \DateTime   $end    the ending date of the search
     */
    public function __construct(\DateTime $begin = null, \DateTime $end = null)
    {
        if ($begin !== null) {
            $this->begin_date = $begin;
        } else {
            $this->begin_date = new \DateTime();
            $this->begin_date->sub(new \DateInterval('P1Y'));
        }
        
        if ($end !== null) {
            $this->end_date = $end;
        } else {
            $this->end_date = new \DateTime();
        }
        
        if ($this->begin_date > $this->end_date) {
            list($this->begin_date, $this->end_date) = array($this->end_date, $this->begin_date);
        }
    }
    
    
    /**
     * Get the beginning date for the search
     *
     * @param   string  $format     the string format of the date
     * @return  string              the beginning date formating
     * @access  public
     */
    public function getBeginDate($format = 'Y-m-d')
    {
        return $this->begin_date->format($format);
    }
    
    
    /**
     * Get the ending date for the search
     *
     * @param   string  $format     the string format of the date
     * @return  string              the ending date formating
     * @access  public
     */
    public function getEndDate($format = 'Y-m-d')
    {
        return $this->end_date->format($format);
    }
    
    
    /**
     * Get the datas of the paiements
     *
     * @return  array   the datas of the paiements
     * @access  public
     */
    public function process()
    {
        $client = new Client();
        
        $client->setServerParameters(array(
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.9.2.23; OpenCmcicAction) Gecko/20110921 Ubuntu/10.04 (lucid) Firefox/3.6.23'
        ));
        
        // Authentification in the website
        $crawler = $client->request('GET', Config::get('cmcic_web_server').'identification/default.cgi');
        $form = $crawler->selectButton('Se connecter')->form();
        
        $crawler = $client->submit($form, array(
            '_cm_user' => Config::get('cmcic_web_username'),
            '_cm_pwd' => Config::get('cmcic_web_password'),
        ));
        
        // Get the search form
        $crawler = $client->request('GET', Config::get('cmcic_web_server').'client/Paiement/Paiement_RechercheAvancee.aspx');
        $datas = array();
        
        // Get hidden values
        $hidden_values = array(
            'tpe_id',
            '__EVENTTARGET',
            '__EVENTARGUMENT',
            '__VIEWSTATE',
        );
        foreach ($hidden_values as $hidden_value) {
            $datas[$hidden_value] = $crawler->filter('#'.$hidden_value)->attr('value');
        }
        
        $datas = array_merge($datas, array(
            'SelectionCritere' => 'Achat',
            'Date_Debut' => $this->getBeginDate('d/m/Y'),
            'Date_Fin' => $this->getEndDate('d/m/Y'),
            'SelectionAffichage' => 'Ecran',
            'Paye' => 'on',
            'Currency' => 'EUR',
            'Reference' => '',
            'Paye.p' => '',
            'Annule.p' => '',
            'Refuse.p' => '',
            'PartiellementPaye.p' => '',
            'Enregistre.p' => '',
            'CarteNonSaisie.p' => '',
            'EnCours.p' => '',
            'Montant_Min' => '',
            'Montant_Max' => '',
            'AdresseMail' => '',
            'Btn.Find.x' => '63',
            'Btn.Find.y' => '11',
            'NumeroTpe' => $datas['tpe_id'],
            'export' => 'XML',
        ));
        
        $parameter = '';
        foreach ($datas as $key => $value) {
            if (empty($parameter) === true) {
                $parameter .= '?';
            } else {
                $parameter .= '&';
            }
            
            $parameter .= $key.'='.urlencode($value);
        }
        
        // Get the XML with all paiements
        $client->request('GET', Config::get('cmcic_web_server').'client/Paiement/Paiement_RechercheAvancee.aspx'.$parameter);
        $response = $client->getResponse();
        
        if ($response->getStatus() !== 200) {
            throw new EPaiement('Status error : '.$response->getStatus());
        }
        
        $content = $response->getContent();
        
        $dom = new \DOMDocument();
        if ($dom->loadXML($content) === false) {
            throw new EPaiement('Impossible to load XML : '.$content);
        }
        
        $results = array();
        $commandes = $dom->getElementsByTagName('Commande');
        
        // Get data of the paiements
        foreach ($commandes as $commande) {
            $results[] = $this->parseCommande($commande);
        }
        
        unset($dom, $response, $form, $crawler, $client);
        
        return $results;
    }
    
    
    /**
     * Parse a paiement and return the datas
     *
     * @param   \DOMElement $node   the XML node with the datas of the paiement
     * @return  array               the datas of the paiement
     * @access  private
     */
    private function parseCommande(\DOMElement $node)
    {
        $datas = array();
        
        $reference_nodes = $node->getElementsByTagName('Reference');
        if ($reference_nodes->length > 0) {
            $datas['reference'] = $reference_nodes->item(0)->nodeValue;
        }
        
        $date_nodes = $node->getElementsByTagName('DatePaiement');
        if ($date_nodes->length > 0) {
            $datas['date'] = $date_nodes->item(0)->nodeValue;
        }
        
        $paiement_nodes = $node->getElementsByTagName('Montant');
        if ($paiement_nodes->length > 0) {
            $paiement_node = $paiement_nodes->item(0);
            $amount_nodes = $paiement_node->getElementsByTagName('Valeur');
            $currency_nodes = $paiement_node->getElementsByTagName('Devise');
            
            if ($amount_nodes->length > 0) {
                $datas['amount'] = $amount_nodes->item(0)->nodeValue;
            }
            
            if ($currency_nodes->length > 0) {
                $datas['currency'] = $currency_nodes->item(0)->nodeValue;
            }
        }
        
        return $datas;
    }
}
<?php

namespace OpenCmcicAction\Request;

use OpenCmcicAction\Core\Config;
use OpenCmcicAction\Exception\Exception;

use Goutte\Client;

class Paiement
{
    const PAYE = 1;
    const ANNULE = 2;
    const PARTIAL = 3;
    
    
    public function __construct($type = self::PAYE)
    {
        
    }
    
    
    public function process()
    {
        $client = new Client();
        
        $client->setServerParameters(array(
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.9.2.23; OpenCmcicAction) Gecko/20110921 Ubuntu/10.04 (lucid) Firefox/3.6.23'
        ));
        
        $crawler = $client->request('GET', Config::get('cmcic_web_server').'identification/default.cgi');
        $form = $crawler->selectButton('Se connecter')->form();
        
        $crawler = $client->submit($form, array(
            '_cm_user' => Config::get('cmcic_web_username'),
            '_cm_pwd' => Config::get('cmcic_web_password'),
        ));
        
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
            'Date_Debut' => '01/02/2013',
            'Date_Fin' => '31/03/2013',
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
            'paging' => 500,
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
        
        $crawler = $client->request('GET', Config::get('cmcic_web_server').'client/Paiement/Paiement_RechercheAvancee.aspx'.$parameter);
        
        $results = $crawler->filter('#m_PanelResultat table tbody tr')->each(function($node, $i) {
            $tds = $node->childNodes;
            if ($tds->length == 4) {
                $link_node = $tds->item(0);
                $date_node = $tds->item(1);
                $amount_node = $tds->item(2);
                $status_node = $tds->item(3);
                
                $datas = array(
                    'date' => $date_node->nodeValue,
                    'amount' => $amount_node->nodeValue,
                    'status' => $status_node->nodeValue,
                );
                
                if ($link_node->hasChildNodes() === true) {
                    $a_node = $link_node->firstChild;
                    if ($a_node !== null && $a_node->hasAttributes() === true) {
                        $datas['link'] = $a_node->getAttribute('href');
                    }
                }
                
                return $datas;
            }
            return null;
        });
        
        var_dump($results);
    }
}
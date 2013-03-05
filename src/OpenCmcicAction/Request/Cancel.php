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

use OpenCmcicAction\Cmcic\Tpe;
use OpenCmcicAction\Cmcic\Hmac;
use OpenCmcicAction\Core\Config;
use OpenCmcicAction\Exception\Exception;


/**
 * Cancel request class
 *
 * @package     OpenCmcicAction\Request
 * @version     1.0.0
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
class Cancel extends Request implements IRequest
{
    private $reference          = null;
    private $amount             = null;
    private $amount_already_get = null;
    private $devise             = null;
    private $language           = null;
    private $date_cancel        = null;
    private $date_commande      = null;
    
    
    public function __construct($reference = null, $amount = null, $amount_already_get = null, $date_commande = null, $devise = 'EUR', $language = 'FR')
    {
        $reference !== null ? $this->setReference($reference) : null;
        $amount !== null ? $this->setAmount($amount) : null;
        $amount_already_get !== null ? $this->setAmountAlreadyGet($amount_already_get) : null;
        $devise !== null ? $this->setDevise($devise) : null;
        $language !== null ? $this->setLanguage($language) : null;
        $date_commande !== null ? $this->setDateCommande($date_commande) : null;
    }
    
    public function check()
    {
        $datas_to_check = array(
            'reference',
            'amount',
            'amount_already_get',
            'devise',
            'language',
            'date_commande',
        );
        
        foreach ($datas_to_check as $data) {
            if ($this->$data === null) {
                throw new Exception($data.' can\'t be null');
            }
        }
    }
    
    
    /**
     * @return OpenCmcicAction\Response\Response
     */
    public function process()
    {
        $this->check();
        
        $this->initDateCancel();
        
        $tpe = new Tpe($this->language);
        $hmac = new Hmac($tpe);
        
        $control_hmac = sprintf(Tpe::CTLHMAC, $tpe->sVersion, $tpe->sNumero, $hmac->computeHmac(sprintf(Tpe::CTLHMACSTR, $tpe->sVersion, $tpe->sNumero)));
        
        $datas = sprintf('%s*%s*%s%s%s*%s*%s*%s*%s*%s*',
            $tpe->sNumero,
            $this->formatDate($this->date_cancel),
            '0'.$this->devise,
            $this->amount_already_get.$this->devise,
            '0'.$this->devise,
            $this->reference,
            '',
            $tpe->sVersion,
            $tpe->sLangue,
            $tpe->sCodeSociete
        );
        
        $mac = $hmac->computeHmac($datas);
        
        $datas = array(
            'version'               => $tpe->sVersion,
            'TPE'                   => $tpe->sNumero,
            'date'                  => $this->formatDate($this->date_cancel),
            'date_commande'         => $this->formatDate($this->date_commande),
            'montant'               => $this->amount.$this->devise,
            'montant_a_capturer'    => '0'.$this->devise,
            'montant_deja_capture'  => $this->amount_already_get.$this->devise,
            'montant_restant'       => '0'.$this->devise,
            'stoprecurrence'        => 'OUI',
            'reference'             => $this->reference,
            'texte-libre'           => '',
            'lgue'                  => $tpe->sLangue,
            'societe'               => $tpe->sCodeSociete,
            'MAC'                   => $mac
        );
        
        return $this->send('capture_paiement.cgi', $datas, 'Cancel');
    }
    
    public function setReference($v)
    {
        if (is_string($v) === false) {
            throw new Exception('reference must be a string');
        }
        
        $this->reference = (string)$v;
    }
    
    public function setAmount($v)
    {
        if (is_numeric($v) === false) {
            throw new Exception('amount must be a numeric');
        }
        
        $this->amount = (float)$v;
    }
    
    public function setAmountAlreadyGet($v)
    {
        if (is_numeric($v) === false) {
            throw new Exception('amount_already_get must be a numeric');
        }
        
        $this->amount_already_get = (float)$v;
    }
    
    
    public function setDevise($v)
    {
        if (is_string($v) === false || strlen($v) !== 3) {
            throw new Exception('devise must be a string with strlen is 3 (ISO4217)');
        }
        
        $this->devise = strtoupper((string)$v);
    }
    
    
    public function setLanguage($v)
    {
        if (is_string($v) === false || strlen($v) !== 2) {
            throw new Exception('language must be a string with strlen is 2');
        }
        
        $this->language = strtoupper((string)$v);
    }
    
    
    public function setDateCommande($v)
    {
        if (is_string($v) === true && preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $v)) {
            $this->date_commande = new \DateTime($v);
        } elseif ($v instanceof \DateTime) {
            $this->date_commande = $v;
        } elseif (is_numeric($v) === true) {
            $this->date_commande = \DateTime::createFromFormat('U', $v);
        } else {
            throw new Exception('date_commande must be a date (string format, timestamp or \DateTime)');
        }
    }
    
    
    public function initDateCancel()
    {
        $this->date_cancel = new \DateTime();
    }
}
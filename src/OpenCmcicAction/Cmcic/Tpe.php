<?php
/**
 * This file is part of the OpenCmcicAction package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenCmcicAction\Cmcic;

use OpenCmcicAction\Exception\Exception;
use OpenCmcicAction\Core\Config;


/**
 * TPE parameter class
 *
 * @package     OpenCmcicAction\Cmcic
 * @version     1.0.0
 * @license     http://opensource.org/licenses/MIT  MIT
 * @author      Simon Leblanc <contact@leblanc-simon.eu>
 */
class Tpe
{
    const CTLHMAC = "V1.04.sha1.php--[CtlHmac%s%s]-%s";
    const CTLHMACSTR = "CtlHmac%s%s";
    const CGI2_RECEIPT = "version=2\ncdr=%s";
    const CGI2_MACOK = "0";
    const CGI2_MACNOTOK = "1\n";
    const CGI2_FIELDS = "%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*";
    const CGI1_FIELDS = "%s*%s*%s%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s*%s";
    const URLPAIEMENT = "paiement.cgi";
    
    public $sVersion;       // TPE Version (Ex : 3.0)
    public $sNumero;        // TPE Number (Ex : 1234567)
    public $sCodeSociete;   // Company code (Ex : companyname)
    public $sLangue;        // Language (Ex : FR, DE, EN, ..)
    public $sUrlOK;         // Return URL OK
    public $sUrlKO;         // Return URL KO
    public $sUrlPaiement;   // Payment Server URL (Ex : https://paiement.creditmutuel.fr/paiement.cgi)

    private $_sCle;         // The Key
    
    
    public function __construct($sLangue = "FR")
    {
        $this->_checkTpeParams();

        $this->sVersion = Config::get('cmcic_version');
        $this->_sCle = Config::get('cmcic_key');
        $this->sNumero = Config::get('cmcic_tpe');
        $this->sUrlPaiement = Config::get('cmcic_server').self::URLPAIEMENT;

        $this->sCodeSociete = Config::get('cmcic_company_code');
        $this->sLangue = $sLangue;

        $this->sUrlOK = Config::get('cmcic_url_ok');
        $this->sUrlKO = Config::get('cmcic_url_ko');

    }
    
    
    public function getKey()
    {
        return $this->_sCle;
    }
    
    
    private function _checkTpeParams()
    {
        $params = array(
            'cmcic_version',
            'cmcic_key',
            'cmcic_tpe',
            'cmcic_server',
            'cmcic_company_code',
            'cmcic_url_ok',
            'cmcic_url_ko',
        );
        
        foreach ($params as $param) {
            if (Config::get($param, null) === null) {
                throw new Exception('The required param : '.$param.' isn\'t defined. Check your config file');
            }
        }
    }
}
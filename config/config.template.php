<?php
/**
 * This file is part of the OpenCmcicAction package.
 *
 * (c) Simon Leblanc <contact@leblanc-simon.eu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

OpenCmcicAction\Core\Config::add(array(
    'cmcic_version' => '3.0',
    'cmcic_server' => 'https://paiement.creditmutuel.fr/test/',
    'cmcic_key' => '',
    'cmcic_tpe' => '',
    'cmcic_company_code' => '',
    'cmcic_url_ok' => '',
    'cmcic_url_ko' => '',
    
    'cmcic_web_server' => 'https://www.cmcicpaiement.fr/fr/test/',
    'cmcic_web_username' => '',
    'cmcic_web_password' => '',
    
    'log_dir' => dirname(__DIR__).DIRECTORY_SEPARATOR.'log',
));
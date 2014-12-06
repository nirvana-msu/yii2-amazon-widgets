<?php

namespace nirvana\amazon;

use yii\base\Object;

/**
 * Includes IPToAmazon database by Stephen Morley which is used to determine
 * the appropriate Amazon country based on IP address.
 * Provides various static useful for creating Amazon widgets and affiliate URLs
 *
 * @license      MIT
 * @author       Alexander Stepanov <student_vmk@mail.ru>
 * @link         https://github.com/nirvana-msu/yii2-amazon-widgets
 * IPToAmazon    IPToAmazon created by Stephen Morley
 * @link         http://code.stephenmorley.org/php/mapping-visitor-ip-addresses-to-amazon-sites/
 */
class AmazonStatic extends Object
{
    /**
     * Country constants
     */
    const AMAZON_COM = 0;
    const AMAZON_CA = 1;
    const AMAZON_CN = 2;
    const AMAZON_DE = 3;
    const AMAZON_ES = 4;
    const AMAZON_FR = 5;
    const AMAZON_IT = 6;
    const AMAZON_CO_JP = 7;
    const AMAZON_CO_UK = 8;
    const AMAZON_IN = 9;

    /**
     * Match type constants
     * @var AmazonStatic::MATCH_COUNTRY the IP address corresponds to a country with its own Amazon site
     * @var AmazonStatic::MATCH_LANGUAGE the IP address corresponds to a country whose primary language is the same as that used by an Amazon site
     * @var AmazonStatic::MATCH_NEITHER neither of the above conditions are true for the IP address
     * @var AmazonStatic::MATCH_RESERVED the IP address is a reserved address and hasn't been assigned to a country yet
     */
    const MATCH_COUNTRY = 0;
    const MATCH_LANGUAGE = 1;
    const MATCH_NEITHER = 2;
    const MATCH_RESERVED = 3;

    /**
     * @var string path to the database used to resolve ip address into amazon country.
     * This parameter is optional and defaults to IPToAmazon.data distributed with this extension.
     */
    public static $databasePath;

    /**
     * @var string the IP address for which to determine the appropriate Amazon country.
     * This parameter is optional and defaults to the visitor's IP address.
     */
    public static $ipAddress;

    /*
     * Country specific aspects:
     * 
     * Some needed in the plugin code:
     * - lang         -> language identifier (see Microsoft Translate)
     * - domain       -> domain of amazon site
     * - site         -> link to affiliate program site
     * - country_name -> full name of country
     *
     * Some only needed for templates:
     * - mplace       -> market place of amazon site, used in Amazon Scripts
     * - mplace_id    -> market place id of amazon locale, used in Amazon Scripts
     * - rcm          -> amazon domain for location of scripts - backward compatible / depreciated
     * - region       -> advert region prefix for iframes & banners (amazon-adsystem.com) & widgets
     * - imp          -> advert prefix for serving impression tracking images
     * - buy_button   -> example buy button stored on Amazon Servers
     * - language     -> Language of each locale.
     */
    private static $countryData = array(
        self::AMAZON_COM => array('mplace' => 'US', 'mplace_id' => '1', 'lang' => 'en', 'domain' => 'com', 'language' => 'English', 'region' => 'na', 'imp' => 'ir-na', 'rcm' => 'rcm.amazon.com', 'site' => 'https://affiliate-program.amazon.com', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/01/buttons/buy-from-tan.gif', 'country_name' => 'United States'),
        self::AMAZON_CA => array('mplace' => 'CA', 'mplace_id' => '15', 'lang' => 'en', 'domain' => 'ca', 'language' => 'English', 'region' => 'na', 'imp' => 'ir-ca', 'rcm' => 'rcm-ca.amazon.ca', 'site' => 'https://associates.amazon.ca', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/15/buttons/buy-from-tan.gif', 'country_name' => 'Canada'),
        self::AMAZON_CN => array('mplace' => 'CN', 'mplace_id' => '28', 'lang' => 'zh-CHS', 'domain' => 'cn', 'language' => 'з®ЂдЅ“дё­ж–‡', 'region' => 'cn', 'imp' => 'ir-cn', 'rcm' => 'rcm-cn.amazon.cn', 'site' => 'https://associates.amazon.cn', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/28/buttons/buy-from-tan.gif', 'country_name' => 'China'),
        self::AMAZON_DE => array('mplace' => 'DE', 'mplace_id' => '3', 'lang' => 'de', 'domain' => 'de', 'language' => 'Deutsch', 'region' => 'eu', 'imp' => 'ir-de', 'rcm' => 'rcm-de.amazon.de', 'site' => 'https://partnernet.amazon.de', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/03/buttons/buy-from-tan.gif', 'country_name' => 'Germany'),
        self::AMAZON_ES => array('mplace' => 'ES', 'mplace_id' => '30', 'lang' => 'es', 'domain' => 'es', 'language' => 'EspaГ±ol', 'region' => 'eu', 'imp' => 'ir-es', 'rcm' => 'rcm-es.amazon.es', 'site' => 'https://afiliados.amazon.es', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/30/buttons/buy-from-tan.gif', 'country_name' => 'Spain'),
        self::AMAZON_FR => array('mplace' => 'FR', 'mplace_id' => '8', 'lang' => 'fr', 'domain' => 'fr', 'language' => 'FranГ§ais', 'region' => 'eu', 'imp' => 'ir-fr', 'rcm' => 'rcm-fr.amazon.fr', 'site' => 'https://partenaires.amazon.fr', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/08/buttons/buy-from-tan.gif', 'country_name' => 'France'),
        self::AMAZON_IT => array('mplace' => 'IT', 'mplace_id' => '29', 'lang' => 'it', 'domain' => 'it', 'language' => 'Italiano', 'region' => 'eu', 'imp' => 'ir-it', 'rcm' => 'rcm-it.amazon.it', 'site' => 'https://programma-affiliazione.amazon.it', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/29/buttons/buy-from-tan.gif', 'country_name' => 'Italy'),
        self::AMAZON_CO_JP => array('mplace' => 'JP', 'mplace_id' => '9', 'lang' => 'ja', 'domain' => 'co.jp', 'language' => 'ж—Ґжњ¬иЄћ', 'region' => 'fe', 'imp' => 'ir-jp', 'rcm' => 'rcm-jp.amazon.co.jp', 'site' => 'https://affiliate.amazon.co.jp', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/09/buttons/buy-from-tan.gif', 'country_name' => 'Japan'),
        self::AMAZON_CO_UK => array('mplace' => 'GB', 'mplace_id' => '2', 'lang' => 'en', 'domain' => 'co.uk', 'language' => 'English', 'region' => 'eu', 'imp' => 'ir-uk', 'rcm' => 'rcm-eu.amazon-adsystem.com', 'site' => 'https://affiliate-program.amazon.co.uk', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/02/buttons/buy-from-tan.gif', 'country_name' => 'United Kingdom'),
        self::AMAZON_IN => array('mplace' => 'IN', 'mplace_id' => '31', 'lang' => 'hi', 'domain' => 'in', 'language' => 'Hindi', 'region' => 'in', 'imp' => 'ir-in', 'rcm' => 'ws-in.amazon-adsystem.com', 'site' => 'https://associates.amazon.in', 'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/31/buttons/buy-from-tan.gif', 'country_name' => 'India'),
        //'br'             => array('mplace' => 'BR', 'mplace_id' => '33', 'lang' => 'pt-br',  'domain' => 'com.br','language' => 'Portuguese',   'region' => 'na', 'imp' => 'ir-br', 'rcm' => 'rcm-br.amazon-adsystem.br',    'site' => 'https://associados.amazon.com.br/',         'buy_button' => 'https://images-na.ssl-images-amazon.com/images/G/33/buttons/buy-from-tan.gif', 'country_name' => 'Brazil'),
    );

    /**
     * @param $country
     * @param $property
     * @return array
     */
    public static function getCountryData($country, $property)
    {
        $countryData = self::$countryData;
        if ($country === null && $property === null)
            return $countryData;
        elseif ($property === null)
            return $countryData[(int)$country];
        elseif ($country === null) {
            /** @noinspection PhpUnusedParameterInspection */
            array_walk($countryData, function (&$v, $k, $param) {
                $v = $v[$param];
            }, $property);
            return $countryData;
        } else
            return $countryData[(int)$country][$property];
    }

    public static function getValidCountries()
    {
        return array_keys(self::$countryData);
    }

    /**
     * @param array $config
     */
    public function __construct($config = [])
    {
        // Default database path. Can be overridden through $config
        self::$databasePath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'IPToAmazon.data';

        // If an IP address is not specified through $config, use the visitor's IP address
        self::$ipAddress = $_SERVER['REMOTE_ADDR'];

        parent::__construct($config);
    }

    /**
     * Resolves IP Address to most relevant Amazon Country and provides Match Type
     * @return array most relevant Amazon Country and Match Type
     */
    public static function IPToAmazon()
    {
        // throw an error if the database is missing
        if (!is_file(self::$databasePath)) {
            trigger_error('Database path is invalid',
                E_USER_ERROR
            );
        }

        // convert the IP address to binary
        $ipAddress = vsprintf('%c%c%c%c', array_map('intval', explode('.', self::$ipAddress)));

        // open the database
        $database = fopen(self::$databasePath, 'r');

        // initialise the search bounds
        $start = 0;
        $end = filesize(self::$databasePath) / 5 - 1;

        // loop until the search bounds are equal
        while ($start != $end) {
            // determine the index of the midpoint
            $midpoint = floor(($start + $end) / 2);

            // determine the IP address bounds for the midpoint
            fseek($database, $midpoint * 5);
            $ipAddressStart = fread($database, 4);
            fread($database, 1);
            $ipAddressEnd = fread($database, 4);

            // update the start or end point depending on the IP address bounds
            if ($ipAddress < $ipAddressStart) {
                $end = $midpoint - 1;
            } elseif ($ipAddress >= $ipAddressEnd) {
                $start = $midpoint + 1;
            } else {
                $start = $midpoint;
                break;
            }
        }

        // read the data
        fseek($database, $start * 5 + 4);
        $data = fread($database, 1);

        // close the database
        fclose($database);

        // store the country and match type
        $amazon['country'] = ord($data) >> 2;
        $amazon['matchType'] = ord($data) & 3;
        return $amazon;
    }
}
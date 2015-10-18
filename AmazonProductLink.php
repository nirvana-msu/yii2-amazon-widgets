<?php
/**
 * @link https://github.com/nirvana-msu/yii2-amazon-widgets
 * @copyright Copyright (c) 2015 Alexander Stepanov
 * @license MIT
 */

namespace nirvana\amazon;

use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Renders iframe widget with Amazon Product Link.
 *
 * @author Alexander Stepanov <student_vmk@mail.ru>
 */
class AmazonProductLink extends Widget
{
    // Constants for old template price options
    const PRICE_OPTION_SHOW_ALL_PRICES = 'Show All Prices';
    const PRICE_OPTION_SHOW_NEW_PRICES_ONLY = 'Show New Prices Only';   // Not supported by IT and ES
    const PRICE_OPTION_HIDE_PRICES = 'Hide Prices';

    /**
     * Required. Used by both old and new templates.
     * @var array ASINs to display. If multiple are specified, amazon will render one at random
     */
    public $asins = [];

    /**
     * Required. Used by both old and new templates.
     * @var integer one of country constants defined in AmazonStatic
     */
    public $country;

    /**
     * Required. Used by both old and new templates.
     * @var string Tracking ID, valid in a given country
     */
    public $trackingId;

    /**
     * Used by both old and new templates.
     * @var boolean whether to open link in a new window
     */
    public $openLinkInNewWindow = true;

    /**
     * Used by both old and new templates.
     * @var boolean whether to show border
     */
    public $showBorder = true;

    /**
     * Used by both old and new templates.
     * @var string background color
     */
    public $backgroundColor = 'FFFFFF';

    /**
     * Used by new template only.
     * @var string title color
     */
    public $titleColor = '0066C0';

    /**
     * Used by new template only.
     * @var string price color
     */
    public $priceColor = '333333';

    /**
     * Used by old template only.
     * @var boolean whether to use larger image
     */
    public $useLargerImage = true;

    /**
     * Used by old template only.
     * @var string price display option
     */
    public $priceOption = self::PRICE_OPTION_SHOW_ALL_PRICES;

    /**
     * Used by old template only.
     * @var string text color
     */
    public $textColor = '000000';

    /**
     * Used by old template only.
     * @var string link color
     */
    public $linkColor = '0000FF';

    // Templates
    private $TEMPLATE_NEW = '<iframe style="width:120px;height:240px;" marginwidth="0" marginheight="0" scrolling="no" frameborder="0" src="//{{rcm}}/widgets/q?ServiceVersion=20070822&OneJS=1&Operation=GetAdHtml&MarketPlace={{mplace}}&ad_type=product_link&tracking_id={{tracking_id}}&marketplace=amazon&region={{mplace}}&placement={{asins}}&asins={{asins}}&linkId=&show_border={{show_border}}&link_opens_in_new_window={{link_opens_in_new_window}}&price_color={{price_color}}&title_color={{title_color}}&bg_color={{bg_color}}"></iframe>';
    private $TEMPLATE_OLD = '<iframe src="http://{{rcm}}/e/cm?t={{tracking_id}}&o={{mplace_id}}&p=8&l=as1&asins={{asins}}&fc1={{fc1}}{{IS}}&lt1={{lt1}}&m=amazon&lc1={{lc1}}&bc1={{bc1}}&bg1={{bg1}}&f=ifr{{price_option}}" style="width:120px;height:240px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>';

    /**
     * Initializes the widget
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->validate();
    }

    /**
     * Renders the widget
     */
    public function run()
    {
        $newTemplateCountries = [AmazonStatic::AMAZON_US, AmazonStatic::AMAZON_GB, AmazonStatic::AMAZON_DE, AmazonStatic::AMAZON_FR];
        $oldTemplateCountries = [AmazonStatic::AMAZON_CA, AmazonStatic::AMAZON_IT, AmazonStatic::AMAZON_ES];

        if (in_array($this->country, $newTemplateCountries)) {
            $template = $this->TEMPLATE_NEW;

            $params = [
                'rcm' => AmazonStatic::getCountryData($this->country, 'rcm'),
                'mplace' => AmazonStatic::getCountryData($this->country, 'mplace'),
                'tracking_id' => $this->trackingId,
                'asins' => implode(',', $this->asins),
                'link_opens_in_new_window' => $this->openLinkInNewWindow ? 'true' : 'false',
                'show_border' => $this->showBorder ? 'true' : 'false',
                'bg_color' => $this->backgroundColor,
                'title_color' => $this->titleColor,
                'price_color' => $this->priceColor,
            ];
        } elseif (in_array($this->country, $oldTemplateCountries)) {
            $template = $this->TEMPLATE_OLD;

            $priceOption = '';  // Corresponds to self::PRICE_OPTION_SHOW_ALL_PRICES
            switch ($this->priceOption) {
                case self::PRICE_OPTION_SHOW_NEW_PRICES_ONLY:
                    $priceOption = '&nou=1';
                    break;
                case self::PRICE_OPTION_HIDE_PRICES:
                    $priceOption = '&npa=1';
                    break;
            }

            $params = [
                'rcm' => AmazonStatic::getCountryData($this->country, 'rcm'),
                'mplace_id' => AmazonStatic::getCountryData($this->country, 'mplace_id'),
                'tracking_id' => $this->trackingId,
                'asins' => implode(',', $this->asins),
                'lt1' => $this->openLinkInNewWindow ? '_blank' : '_top',
                'bc1' => $this->showBorder ? '000000' : 'FFFFFF',
                'IS' => $this->useLargerImage ? '&IS2=1' : '&IS1=1',
                'price_option' => $priceOption,
                'bg1' => $this->backgroundColor,
                'fc1' => $this->textColor,
                'lc1' => $this->linkColor,
            ];
        } else {
            throw new InvalidConfigException("Product Link widget does not support a given Amazon locale yet!");
        }

        $keys = array_map(function ($p) {
            return '{{' . $p . '}}';
        }, array_keys($params));
        $html = str_replace($keys, array_values($params), $template);

        echo $html;
    }

    /**
     * Validates widget inputs
     * @throws InvalidConfigException
     */
    private function validate()
    {
        if (empty($this->asins)) {
            throw new InvalidConfigException("Please specify item ASIN(s)!");
        }

        $validCountries = AmazonStatic::getValidCountries();
        if (!in_array($this->country, $validCountries)) {
            throw new InvalidConfigException("Please specify Amazon locale using constants defined in AmazonStatic class!");
        }

        if (empty($this->trackingId)) {
            throw new InvalidConfigException("Please specify Tracking ID!");
        }

        $validPriceDisplayOptions = [self::PRICE_OPTION_SHOW_ALL_PRICES, self::PRICE_OPTION_SHOW_NEW_PRICES_ONLY, self::PRICE_OPTION_HIDE_PRICES];
        if (!in_array($this->priceOption, $validPriceDisplayOptions)) {
            throw new InvalidConfigException(sprintf("Invalid price display option: %s!", $this->priceOption));
        }
    }
}
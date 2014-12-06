<?php

namespace nirvana\amazon;

use yii\base\InvalidConfigException;
use yii\base\Widget;

class AmazonProductLink extends Widget
{
    /**
     * @var string Associate Tag
     */
    public $associateTag;

    /**
     * @var string foreground color
     */
    public $foreground = '000000';

    /**
     * @var string background color
     */
    public $background = 'FFFFFF';

    /**
     * @var string border color
     */
    public $bordercolor = 'FFFFFF';

    /**
     * @var string link color
     */
    public $linkcolor = '0000FF';

    /**
     * @var string how to display prices
     */
    public $priceDisplay = 'showAllPrices';

    /**
     * @var boolean whether to open link in a new window
     */
    public $openLinkInNewWindow = true;

    /**
     * @var boolean whether to use a larger image
     */
    public $useLargerImage = true;

    /**
     * @var array ASINs to display
     */
    public $asins = array();

    /**
     * @var int Amazon Country
     */
    public $country = AmazonStatic::AMAZON_COM;

    private $AMAZON_PRODUCT_LINK_TEMPLATE = '<iframe src="http://{{RCM}}/e/cm?t={{associateTag}}&o={{mplaceId}}&p=8&l=as1&asins={{asins}}&fc1={{foreground}}{{useLargerImage}}&lt1={{openLinkInNewWindow}}&m=amazon&lc1={{linkcolor}}&bc1={{bordercolor}}&bg1={{background}}&f=ifr{{priceDisplay}}" style="width:120px;height:240px;" scrolling="no" marginwidth="0" marginheight="0" frameborder="0"></iframe>';

    /**
     * All possible price display options
     * @var array
     */
    private $possiblePriceDisplayOptions = array('showAllPrices', 'showNewPricesOnly', 'hidePrices');

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
        $params = array(
            'RCM' => AmazonStatic::getCountryData($this->country, 'rcm'),
            'mplaceId' => AmazonStatic::getCountryData($this->country, 'mplace_id'),
            'associateTag' => $this->associateTag,
            'asins' => implode(',', $this->asins),
            'foreground' => $this->foreground,
            'linkcolor' => $this->linkcolor,
            'bordercolor' => $this->bordercolor,
            'background' => $this->background,
        );

        switch ($this->priceDisplay) {
            case 'showAllPrices':
                $params['priceDisplay'] = '';
                break;
            case 'showNewPricesOnly':
                $params['priceDisplay'] = '&nou=1';
                break;
            case 'hidePrices':
                $params['priceDisplay'] = '&npa=1';
                break;
        }

        if ($this->openLinkInNewWindow)
            $params['openLinkInNewWindow'] = '_blank';
        else
            $params['openLinkInNewWindow'] = '_top';

        if ($this->useLargerImage)
            $params['useLargerImage'] = '&IS2=1';
        else
            $params['useLargerImage'] = '&IS1=1';

        $keys = array_map(function ($p) {
            return '{{' . $p . '}}';
        }, array_keys($params));
        $html = str_replace($keys, array_values($params), $this->AMAZON_PRODUCT_LINK_TEMPLATE);

        echo $html;
    }

    /**
     * Validates widget inputs
     * @throws InvalidConfigException
     */
    private function validate()
    {
        if (false === in_array($this->priceDisplay, $this->possiblePriceDisplayOptions)) {
            throw new InvalidConfigException(sprintf(
                "Invalid price display option: %s! Possible options: %s",
                $this->priceDisplay,
                implode(', ', $this->possiblePriceDisplayOptions)
            ));
        }
    }
}

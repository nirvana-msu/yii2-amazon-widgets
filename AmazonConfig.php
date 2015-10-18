<?php
/**
 * @link https://github.com/nirvana-msu/yii2-amazon-widgets
 * @copyright Copyright (c) 2015 Alexander Stepanov
 * @license MIT
 */

namespace nirvana\amazon;

use yii\base\Object;
use yii\base\InvalidParamException;

/**
 * Object for storing and retrieving Tracking IDs for different Amazon locales, to be used as an application component
 *
 * @author Alexander Stepanov <student_vmk@mail.ru>
 */
class AmazonConfig extends Object
{
    /**
     * @var array associate array with keys representing Amazon locales from AmazonStatic and values being corresponding Tracking IDs
     */
    public $trackingIds;

    /**
     * Returns Tracking ID for a given Amazon locale
     * @param $country integer one of country constants defined in AmazonStatic
     * @return string
     */
    public function getTrackingId($country)
    {
        if (!array_key_exists($country, $this->trackingIds)) {
            throw new InvalidParamException("Tracking ID for a given Amazon locale is not set!");
        }
        return $this->trackingIds[$country];
    }
}
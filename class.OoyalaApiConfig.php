<?php
/**
 * Configuration file for Ooyala Video Downloader.
 */

namespace MorganEstes\OVD;

/**
 * Class OoyalaApiConfig
 */
class OoyalaApiConfig
{
    /**
     * API key from your Oolaya account.
     *
     * @var string
     */
    public static $api_key;

    /**
     * Secret key for the Oolaya account.
     *
     * @var string
     */
    public static $secret_key;

    /**
     * Additional parameters to pass to initial request.
     *
     * @var array
     */
    public static $parameters = [];

    /**
     * Class constructor.
     *
     * @param string $api_key
     * @param string $secret_key
     * @param array  $parameters Optional. Additional parameters to pass to initial request.
     */
    public static function init($api_key, $secret_key, $parameters = [])
    {
        self::$api_key    = $api_key;
        self::$secret_key = $secret_key;

        if (!empty($parameters) && is_array($parameters)) {
            self::$parameters = $parameters;
        } else {
            self::$parameters = [
                'asset_type' => 'video',
                'limit'      => 1000,
            ];
        }
    }
}

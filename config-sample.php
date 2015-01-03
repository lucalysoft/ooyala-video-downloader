<?php
/**
 * Configuration file for Ooyala Video Downloader.
 */

$ovdConfig = new OVD_Config(
    'api_key',
    'secret_key',
    array(
        'asset_type' => 'video',
        'limit'      => 1000,
    ));

/**
 * Class OVD_Config
 */
class OVD_Config
{
    /**
     * API key from your Oolaya account.
     *
     * @var string
     */
    public $api_key;

    /**
     * Secret key for the Oolaya account.
     *
     * @var string
     */
    public $secret_key;

    /**
     * Additional parameters to pass to initial request.
     *
     * @var array
     */
    public $parameters = [];

    /**
     * Class constructor.
     *
     * @param string $api_key
     * @param string $secret_key
     * @param array  $parameters Optional. Additional parameters to pass to initial request.
     */
    function __construct($api_key, $secret_key, $parameters = [])
    {
        $this->api_key    = $api_key;
        $this->secret_key = $secret_key;
        array_merge($this->parameters, $parameters);
    }
}

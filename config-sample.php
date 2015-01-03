<?php
/**
 * Configuration file for Ooyala Video Downloader.
 */

$config = new Config('api_key', 'secret_key');

/**
 * Class Config
 */
class Config
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
     * Class constructor.
     *
     * @param string $api_key
     * @param string $secret_key
     */
    function __construct($api_key, $secret_key)
    {
        $this->api_key    = $api_key;
        $this->secret_key = $secret_key;
    }
}

<?php
/**
 * Configuration file for Ooyala Video Downloader.
 */

$config = new Config('api_key from your Ooyala account', 'secret_key from Ooyala');

class Config
{
    public $api_key;
    public $secret_key;

    function __construct($api_key, $secret_key)
    {
        $this->api_key    = $api_key;
        $this->secret_key = $secret_key;
    }

}

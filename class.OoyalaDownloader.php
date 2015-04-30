<?php
/**
 * Download the original videos from your Ooyala account.
 *
 * @author  Morgan Estes <morgan.estes@gmail.com>
 * @version 1.0.1
 *
 * @uses    OoyalaApi
 * @link    http://api.ooyala.com/docs/v2/assets
 *
 * @license GPLv3 or later
 *
 * This file is part of Ooyala Video Downloader.
 * Ooyala Video Downloader is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Ooyala Video Downloader is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Ooyala Video Downloader.  If not, see <http://www.gnu.org/licenses/>.
 *
 * Ooyala is a trademark of Ooyala, Inc.
 * This program is not released or approved by Ooyala, Inc.
 */

namespace MorganEstes\OVD;

use OoyalaApi;
use MorganEstes\OVD\OoyalaApiConfig as Config;

/** Includes the {@link $config} object with API credentials. */
require_once __DIR__ . '/class.OoyalaApiConfig.php';

/** Include the Ooyala API SDK. */
require_once __DIR__ . '/sdk/OoyalaApi.php';


/**
 * Class OoyalaDownloader
 * @package OoyalaDownloader
 */
class OoyalaDownloader
{
    /** @var \OoyalaApi $api */
    public static $api = null;

    /**
     * Initialize the API connection and initiate the download of assets.
     *
     * @throws \Exception
     */
    public static function init()
    {
        /**
         * The results of the API call to get all assets and their associated data.
         *
         * @var object $results
         */
        $results = self::getApi()->get('assets', Config::$parameters);

        /**
         * The list of all the `asset_type` items in the account.
         *
         * @var array $assets
         */
        $assets = $results->items;

        echo count($assets) . ' assets in Ooyala.' . "\r\n";

        /**
         * The details of the source files for download.
         * This is a multidimensional array keyed to the embed code from Ooyala.
         *
         * @var array $files ['embedCode'] {
         *          @type string 'name' The source file name.
         *          @type string 'url'  The URL of the original file.
         *          @type int    'size' The size in bytes of the original file.
         *           }
         *
         */
        $files = self::getSourceFiles($assets);

        foreach ($files as $file) {
            try {
                self::downloadFiles($file['url'], $file['size'], $file['name']);
            } catch (\Exception $e) {
                throw new \Exception('Unable to download the source file.', 0, $e);
            }
        }

    }

    /**
     * Singleton pattern to access the API.
     *
     * @return \OoyalaApi An instance of the API connection.
     */
    public static function getApi()
    {
        if (!is_a(self::$api, 'OoyalaApi')) {
            return self::$api;
        } else {
            return new \OoyalaApi(Config::$api_key, Config::$secret_key);
        }
    }

    /**
     * Gets the file info about the original files uploaded to Ooyala.
     *
     * @link http://api.ooyala.com/docs/v2/assets#Source+files
     *
     * @param array $assets The list of all the items in the account.
     *
     * @return array $files Info about the specified files, keyed to the embed code.
     */
    public static function getSourceFiles($assets)
    {
        /**
         * An array of all the original files available for download.
         *
         * @var array $files
         */
        $files = [];

        foreach ($assets as $asset) {
            /** @var string $ooyalaEmbedCode */
            $ooyalaEmbedCode = $asset->embed_code;

            // Throw in a little random pause so we don't flood the server with requests.
            sleep(rand(2, 7));

            if ('video' == $asset->asset_type) {
                /**
                 * Info about the original file uploaded.
                 *
                 * @var object $sourceFile
                 */
                $sourceFile = self::getApi()->get("assets/$ooyalaEmbedCode/source_file_info");

                $files[$ooyalaEmbedCode] = [
                    'name' => (string)$sourceFile->original_file_name,
                    'url'  => (string)$sourceFile->source_file_url,
                    'size' => (int)$sourceFile->file_size,
                ];
            }
        }

        return $files;
    }

    /**
     * Download the specified file from Ooyala servers.
     *
     * @param string $downloadUrl   The URL to download the file from.
     * @param int    $fileSize      The size of the file to download.
     * @param string $localFileName Name to save the file as.
     * @param string $localFolder   Optional. Folder name to save the downloaded file.
     *
     * @return mixed|null True or result on success or false on failure. Null if file already exists.
     */
    public static function downloadFiles($downloadUrl, $fileSize, $localFileName, $localFolder = 'videos')
    {
        // Create the folder if it doesn't exist.
        if (!is_dir($localFolder)) {
            mkdir($localFolder);
        }

        // Sanity checks:
        // Does the file exist? No, keep going. Yes, but it's the wrong size: keep going.
        if ((!file_exists("$localFolder/$localFileName")) ||
            (file_exists("$localFolder/$localFileName") && (int)$fileSize !== filesize("$localFolder/$localFileName"))
        ) {
            $fp = fopen("$localFolder/$localFileName", 'w');

            echo "Downloading $localFileName from $downloadUrl and saving to $localFolder.";
            $ch = curl_init($downloadUrl);
            curl_setopt($ch, CURLOPT_FILE, $fp);

            $data = curl_exec($ch);
            curl_close($ch);
            fclose($fp);

            return $data;
        } else {
            echo "$localFileName already exists and will be skipped.\n";
            return null;
        }
    }
}
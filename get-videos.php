<?php
/**
 * Download the original videos from your Ooyala account.
 *
 * @author  Morgan Estes <morgan.estes@gmail.com>
 * @version 0.2.0
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
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/sdk/OoyalaApi.php';

$api = new OoyalaApi($config->api_key, $config->secret_key);

$parameters = array(
    'asset_type' => 'video',
    'limit'      => 1000,
);

/**@var object $results */
$results = $api->get('assets', $parameters);

/**@var array $assets The list of all the `asset_type` items in the account. */
$assets = $results->items;

echo count($assets) . ' assets in Ooyala.' . "\r\n";
//get_video_streams();
get_source_files($assets);

/**
 * Gets the file info about the original files uploaded to Ooyala.
 *
 * @link http://api.ooyala.com/docs/v2/assets#Source+files
 *
 * @param array $assets The list of all the `asset_type` items in the account.
 *
 * @return array $file Info about the specified file.
 */
function get_source_files($assets)
{
    global $api;
    
    foreach ($assets as $asset) {
        /**@var string $ooyala_embed_code */
        $ooyala_embed_code = $asset->embed_code;

        // Throw in a little random pause so we don't flood the server with requests.
        sleep(rand(2, 7));

        if ('video' == $asset->asset_type) {
            /**@var array $source_file Info about the original file uploaded. */
            $source_file = $api->get("assets/$ooyala_embed_code/source_file_info");
            //print_r( $source_file );

            $file_name = $source_file->original_file_name;
            $file_url  = $source_file->source_file_url;
            $file_size = $source_file->file_size;

            $file = array(
                'name' => $file_name,
                'url'  => $file_url,
                'size' => $file_size,
            );

            try {
                download_files($file['name'], $file['url'], $file['size']);
            } catch (Exception $e) {
                throw new Exception('Something really gone wrong', 0, $e);
            }
        }
    }
    
    return $file;
}

function download_files($file_name, $download_url, $file_size, $download_location = 'videos')
{
    // Create the folder if it doesn't exist.
    if ( ! is_dir($download_location)) {
        mkdir($download_location);
    }

    // Sanity checks:
    // Does the file exist? No, keep going. Yes, but it's the wrong size: keep going.
    if (( ! file_exists("$download_location/$file_name")) ||
        (file_exists("$download_location/$file_name")
         && $file_size !== filesize("$download_location/$file_name"))
    ) {

        $fp = fopen("$download_location/$file_name", 'w');

        echo "Downloading $file_name from $download_url and saving to $download_location.";
        $ch = curl_init($download_url);
        curl_setopt($ch, CURLOPT_FILE, $fp);

        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    } else {
        echo "$file_name already exists and will be skipped.\n";
    }
}

function get_video_streams($ooyala_embed_code)
{
    global $api;

    /**@var array $streams All the video collections. */
    $streams = $api->get("assets/$ooyala_embed_code/streams");

    $count = 0;

    foreach ($streams as $stream) {
        if ($stream->is_source) {
            $file = array(
                'embed_code' => $ooyala_embed_code,
                'file_name'  => $stream->file_name,
                'file_size'  => $stream->file_size,
                'url'        => $stream->url,
            );
            $count ++;
        }
    }
}

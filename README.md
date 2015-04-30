# Ooyala Video Downloader

## Overview

Download the original video files from your account using the Ooyala API.

## Instructions

* Clone this repository (including submodules) to a local location.
* Invoke `OoyalaApiConfig::init()`, passing your API and secret keys as parameters.
* From the command line, run `php get-videos.php`.

## Version History

== 1.0.1 ==
- Remove the empty call to `OoyalaApiConfig::init()`.
- Update documentation to reflect how to set up with your own keys.

== 1.0.0 ==
First Composer-enabled release with a massive rewrite:

- Namespaced classes.
- Only methods related to retrieving and saving files are left.
- No more database storage requirements.
- All configuration takes place in OoyalaApiConfig.
 
== 0.2.0 ==
- Update the SDK and references to it.
- Unbundle the SDK and re-add it as a Git submodule.
- Update minimum PHP version to 5.4.

== 0.1.0 ==
- Initial Release

[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/morganestes/ooyala-video-downloader/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

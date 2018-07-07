# Micros
Extends WordPress to add a decentralised micro-customisation distribution &amp; management system.

## Introduction

Small Customisations to WordPress specific to:

 * WordPress Core
 * Plugins
 * Themes (including Launchpad)
 * Specific Site
 * and different combinations of the above.

Powered by Github Gists.

## Features
 * Browse & Install Micros
 * Rate, Review & Report Broken
 * Fork, Edit Forks
 * Drop in, can be uploaded with FTP
 * Recipes: Combinations of micros. Free to create, publish and share
 * Feeds: Anyone can create a public or private feed. Users can subscribe to any feed they like.
 * Publish Support:
     * to Feed (from a form on edit screen)
     * to Github Gist (using API)

## Technical
 * CodeMirror Editor
 * WP Code Sandbox
 * Gist API
     * Create New Gist: https://developer.github.com/v3/gists/#create-a-gist 
     * Fetch Gist: https://developer.github.com/v3/gists/#get-a-single-gist 
     * Edit Gist: https://developer.github.com/v3/gists/#edit-a-gist 
     * Fork Gist: https://developer.github.com/v3/gists/#fork-a-gist
 * WP Plugin
     * Installation into wp-content/bwp-micros/
     * Loading into WordPress load sequence
     * Local/Remote Forking (Simple file copies)
     * Publish Form (Power User Mode) to create package.json and schema information on Feed.
     * Github API integration for Gist publish
     * Feed Subscription
 * Server Plugin
     * Feed publishing
 * Schema & Sample Micros
     * Schema: https://gist.github.com/actual-saurabh/12a486173f4dee5cd52e63d85f38220f
     * Simple Micro: https://gist.github.com/actual-saurabh/2aee7bdce054ac9cb130dd318e971398
     * Complex Micro: https://gist.github.com/actual-saurabh/4e5c5b24940b9fa70da8b8343da6ec4f
     * Recipe Sample: https://gist.github.com/actual-saurabh/6b698fc68e800a65b672cd744c3866e2  

## References:
 * https://make.wordpress.org/core/2017/10/22/code-editing-improvements-in-wordpress-4-9/
 * https://www.elegantthemes.com/blog/resources/codemirror-and-the-coding-sandbox-introduced-in-wordpress-4-9 

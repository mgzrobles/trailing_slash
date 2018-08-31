TRAILING SLASH DRUPAL MODULE
----------------------------

What is it?
-----------
Adds trailing slashes to all Drupal generated clean URLs.
For example: example.com/user/.

How do I install it?
--------------------
1. Install and enable this module using one of the following methods:
https://www.drupal.org/docs/8/extending-drupal-8/installing-drupal-8-modules

Configuration
-------------
1. To configure the module go to /admin/config/trailing-slash/settings
2. On this page you have the option to enable/disable the configuration of this module.
3. List of paths
<p>Write a path per line where you want a trailing slash. Paths start with slash. (e.g., '/book')</p>
4. Enabled entity types
<p>You can choose the entity types that you want to have a slash, for example, the taxonomy terms of a particular vocabulary or nodes of a bundle</p> 

REQUIREMENTS
------------
 * drupal::language
 * php:7.1

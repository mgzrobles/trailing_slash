TRAILING SLASH DRUPAL MODULE
----------------------------

What is it?
-----------
Adds trailing slashes to all Drupal generated URLs.

How do I install it?
--------------------
1. Install and enable this module using one of the following methods:
http://drupal.org/documentation/install/modules-themes/modules-7

2. Add a redirect for your website that enforces trailing slashes (good for SEO):
Apache mod_rewrite example (in .htaccess):

RewriteEngine On
RewriteBase /
RewriteRule ^(.*(?:^|/)[^/\.]+)$ $1/ [L,R=301]

IIS URL Rewrite example (in web.config):

<configuration>
	<system.webServer>
		<rewrite>
			<rules>
				<rule name="Redirect to Trailing Slashes" enabled="true" stopProcessing="true">
 					<match url="^(.*(?:^|/)[^/\.]+)$" />
					<action type="Redirect" url="{R:1}/" />
				</rule>
			</rules>
		</rewrite>
	</system.webServer>
</configuration>

3. Go to Administration > Configuration > Search and metadata > Clean URLs in
Drupal and ensure that Enable trailing slashes is checked. Easy as that!!

Known Issues/Bugs
-----------------
Negates the GlobalRedirect module's Deslash function.

Sponsors
--------
Development of this module was sponsored by the Australian War Memorial
[http://www.awm.gov.au/]

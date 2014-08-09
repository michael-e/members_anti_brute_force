# Members Anti Brute Force

This extension adds tracking of Members login and password reset to the Anti Brute Force extension.

It is derived from the ABF Filter extension, which has been written by Thomas Appel for Nathan Hornby and Crocodile RCS. With kind permission from Crocodile RCS, Nathan has open-sourced the code on GitHub: <https://github.com/nathanhornby/abffilter>.

Things have been simplified a lot here, mainly because new delegates in the Members extension allowed this. There are some improvements as well.

The "unban a Member using email verification" feature has been removed.


## Dependencies

Please consult the meta XML file for existing dependencies.


## Templating

The extension can not assume anything about frontend pages, and so it will not introduce any magic behaviour. It simply adds two parameters to Symphony's param pool (on every page):

	<remote-address-banned>yes|no</remote-address-banned>
	<remote-address-blacklisted>yes|no</remote-address-blacklisted>

These parameters reflect the status of the visitor's IP address, as tracked by the Anti Brute Force extension.

In order to do anything with banned or blacklisted IP addresses on frontend pages, you must use these parameters in your XSLT templates. Basic usage:

	<xsl:choose>
		<xsl:when test="not(/data/params/remote-address-blacklisted) or not(/data/params/remote-address-banned)">
			<p>Error: ABF Ban Status is missing. Is the extension installed at all?</p>
		</xsl:when>
		<xsl:when test="not(/data/params/remote-address-blacklisted = 'no')">
			<p>Your IP address has been blacklisted.</p>
		</xsl:when>
		<xsl:when test="not(/data/params/remote-address-banned = 'no')">
			<p>Your IP address has been banned.</p>
		</xsl:when>
		<xsl:otherwise>
			<p>Congratulations, you are neither banned nor blacklisted!</p>
		</xsl:otherwise>
	</xsl:choose>

(Note that, due to the logic of the Anti Brute Force extension, `remote-address-banned` will be `no` if `remote-address-blacklisted` is `yes`, so you should not reverse the test order.)

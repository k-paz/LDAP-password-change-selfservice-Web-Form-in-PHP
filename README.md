# LDAP-password-change-selfservice-Web-Form-in-PHP
The PHP Web form for a selfservice LDAP password user change

LDAP password change selfservice Web Form in PHP
Copyright (c) 2015 - 2016 Krzyszof Paz. GNU GPL v3
@category  ldapchpwd.php
@package   ldapchpwd.php
@author    Krzysztof Paz
@copyright 2016 (C) Krzysztof Paz. GNU General Public License, version 3 (GPL-3.0).
@license   https://opensource.org/licenses/GPL-3.0
@version   Release: 1.0
@webpage   https://github.com/k-paz/LDAP-password-change-selfservice-Web-Form-in-PHP
@link      https://github.com/k-paz/LDAP-password-change-selfservice-Web-Form-in-PHP

Forked from:	     http://technology.mattrude.com/2010/11/ldap-php-change-password-webpage/
Initial credits:   Matt Rude <http://mattrude.com>

My changes over Matt's version:
+ custom LDAP port definition added, 
+ salting and hashing for SHA512 encoded passwords added, 
+ code simplified only to LDAP password change - mail&search sideprocedures removed,
+ updated the LDAP password change code for making it work with Apache Directory Server, 
+ full page background image (bg.jpg) support added and form is centered by the HTML/CSS part.

Recommendation: 	Use this page with the HTTPS/SSL/443 connections!
Warning:	Regardless of HTTPS, default LDAP ports like 389, 10389, etc. usually runs unencryped.
ToDo:		Adding smooth support for the LDAPS/SSL ports like 636, 10636,etc. would be nice to have.

This code is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 3.0 of the License, or (at your option) any later version.

This code is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

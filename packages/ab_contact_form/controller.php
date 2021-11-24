<?php

/**
 * Advanced Contact Form package for Concrete5
 * Copyright Copyright 2017-2020, Alex Borisov
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * @author Alex Borisov <linuxoidoz@gmail.com>
 * @package Concrete\Package\ab_contact_form
 */
 
namespace Concrete\Package\AbContactForm;

use Concrete\Core\Package\Package;
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Captcha\Library;

class Controller extends Package {

	protected $pkgHandle = 'ab_contact_form';
	protected $appVersionRequired = '8.3.0';
	protected $pkgVersion = '2.1.1';
	protected $pkgAutoloaderMapCoreExtensions = true;
	
	public function getPackageDescription() 
	{
		return t('Add contact form with advanced security features');
	}
	
	public function getPackageName() 
	{
		return t("Advanced Contact Form");
	}
	
	public function install() 
	{
		$pkg = parent::install();
		BlockType::installBlockType('ab_contact_form', $pkg);
		
		if (is_null(Library::getByHandle('ab_captcha'))) {
            Library::add('ab_captcha', t('Customized Securimage Captcha'), $pkg);
            Library::getByHandle('ab_captcha')->activate();
		}
	}

    public function uninstall() 
    {
        $pkg = parent::uninstall();
        $db = $this->app->make('database')->connection();
        $q = 'DROP TABLE IF EXISTS btAbContactFormSpam';
        $v = [''];
        $db->executeQuery($q, $v);
        
        if (!is_null(Library::getByHandle('ab_captcha'))) {
            Library::getByHandle('ab_captcha')->delete();
        }
    }

}

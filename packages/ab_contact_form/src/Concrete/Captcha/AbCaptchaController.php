<?php
namespace Concrete\Package\AbContactForm\Captcha;

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

use Securimage;
use Securimage_Color;

class AbCaptchaController extends \Concrete\Core\Captcha\SecurimageController
{
    public function displayCaptchaPicture()
    {
        //$this->securimage->image_width = 240;
		//$this->securimage->image_height = 60;
		$this->securimage->image_bg_color = new Securimage_Color('#d9edf7');
		$this->securimage->line_color = new Securimage_Color('#696969');
		
		$this->securimage->text_color = new Securimage_Color('#fe6b61');
		$this->securimage->use_transparent_text = true;
		$this->securimage->text_transparency_percentage = 20;
		
		$this->securimage->image_signature = parse_url(BASE_URL, PHP_URL_HOST);
		$this->securimage->signature_color = new Securimage_Color('#363636');
		$this->securimage->code_length = rand(4, 6);
		$this->securimage->num_lines = rand(3, 10);
		
        return parent::displayCaptchaPicture();
    }
}

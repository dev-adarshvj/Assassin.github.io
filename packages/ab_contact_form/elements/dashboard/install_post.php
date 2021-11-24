<?php defined('C5_EXECUTE') or die("Access Denied.");

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

use Concrete\Core\Support\Facade\Url;

$captcha_link = '<a href="' . URL::to('/dashboard/system/permissions/captcha/') . '" target="_blank" style="color: #0099ff; text-decoration: none;">' . t('captcha configuration') . '</a>';
$securimage_link = '<a href="https://www.phpcaptcha.org/" target="_blank" style="color: #0099ff; text-decoration: none;">' . 'Securimage' . '</a>';

?>

<fieldset>
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading"><?php echo  t('Securimage open-source free PHP Captcha script') ?></h4>
        <p><?php echo t('You installed the new contact form with advanced security features. Go to the system %s to complete captcha setup.', $captcha_link); ?></p>
        <p><?php echo t('Check out Securimage site if you want to learn more about how to get started with the %s captcha system.', $securimage_link); ?></p>
    </div>
</fieldset>

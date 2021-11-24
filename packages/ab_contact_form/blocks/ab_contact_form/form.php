<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * Advanced Contact Form package for Concrete5
 * Copyright Copyright 2017-2019, Alex Borisov
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

?>

<style>
.need {
    display: inline-block;
    text-align: left;
    margin: 0 auto;
    margin-left: 10px;
    color: #ff4100;
    font-size: 0.7em;
    font-weight: 400;
    opacity: 0.5;
}

.text-muted {
    font-size: 0.8em;
}
</style>

<fieldset>
    <div class="form-group">
        <?php
        echo $form->label('form_title', t('Form title'));
        echo $form->text('form_title', $form_title ? $form_title : t("Contact Form"), ['maxlength' => 100]);
        echo '<p class="text-muted">' . t('Max 100 symbols') . '</p>';
        ?>
    </div>
        
    <div class="form-group">
        <?php
        echo $form->label('email_to', t('Send form to email') . '<span class="need">' . t('Required') . '</span>');
        echo $form->text('email_to', $email_to, ['maxlength' => 100]);
        echo '<p class="text-muted">' . t('Max 100 symbols') . '</p>';
        ?>
    </div>
        
    <div class="form-group">
        <div class="checkbox" data-checkbox-wrapper="show_submit_error">
            <label>
                <?php
                echo $form->checkbox('show_submit_error', 1, $show_submit_error);
                echo t('Show the following message if form submission fails?');
                ?>
            </label>
        </div>
    </div>
    <div data-fields="show_submit_error" style="display: none">
        <div class="well">
            <div class="form-group">
                <?php
                echo '<p>' . sprintf($error_submit, '<b>' . t('[YOUR EMAIL]') . '</b>') . '</p>';
                echo '<p style="font-size: 0.8em; color: #ff4100;">' . t('Note: That [YOUR EMAIL] address will be shown on the form if submission fails') . '</p>';
                ?>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <?php
        echo $form->label('email_subject', t('Email subject'));
        echo $form->text('email_subject', $email_subject ? $email_subject : t("Question from website"), ['maxlength' => 100]);
        echo '<p class="text-muted">' . t('Max 100 symbols') . '</p>';
        ?>
    </div>
        
    <div class="form-group">
        <?php
        echo $form->label('send_button_text', t('Form send button text'));
        echo $form->text('send_button_text', $send_button_text ? $send_button_text : t("Send"), ['maxlength' => 100]);
        echo '<p class="text-muted">' . t('Max 100 symbols') . '</p>';
        ?>
    </div>
    
    <div class="form-group">
        <?php
        echo $form->label('wait_time', t('Wait (seconds) between form submissions') . '<span id="errmsg" style="margin-left: 10px; font-size: 0.8em; color: #ff4100;"></span>');
        echo $form->number('wait_time', $wait_time ? $wait_time : '60', ['min' => '0', 'max' => '86400', 'class' => "decimals"]);
        echo '<p class="text-muted">' . t('Values from 0 to 86400 (86400 sec = 24 h)') . '</p>';
        ?>
    </div>
        
    <div class="form-group">
        <div class="checkbox" data-checkbox-wrapper="popup">
            <label>
                <?php
                echo $form->checkbox('popup', 1, $popup);
                echo t('Lightboxed form?');
                ?>
            </label>
        </div>
    </div>
    <div data-fields="popup" style="display: none">
        <div class="well">
            <div class="form-group">
                <?php
                echo $form->label('form_button_text', t('Form popup button text'));
                echo $form->text('form_button_text', $form_button_text ? $form_button_text : t("Contact Form"), ['maxlength' => 100]);
                echo '<p class="text-muted">' . t('Max 100 symbols') . '</p>';
                ?>
            </div>
        </div>
    </div>
    
    <div class="form-group">
        <div class="checkbox" data-checkbox-wrapper="domains">
            <label>
                <?php
                echo $form->checkbox('domains', 1, $blacklisted_domains ? true : false);
                echo t('Blacklist domains?');
                ?>
            </label>
        </div>
    </div>
    <div data-fields="domains" style="display: none">
        <div class="well">
            <div class="form-group">
                <?php
                echo $form->label('blacklisted_domains', t('Blacklisted domains (comma separated)'));
                echo $form->text('blacklisted_domains', $blacklisted_domains ? $blacklisted_domains : "email.com,email.ru", ['maxlength' => 1000]);
                echo '<p class="text-muted">' . t('Max 1000 symbols') . '</p>';
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="checkbox" data-checkbox-wrapper="show_captcha">
            <label>
                <?php
                echo $form->checkbox('show_captcha', 1, $show_captcha);
                echo t('Show Captcha?');
                ?>
            </label>
        </div>
    </div>
    <div data-fields="show_captcha" style="display: none">
        <div class="well">
            <div class="form-group">
                <?php
                $captcha_libs = \Concrete\Core\Captcha\Library::getList();
                $captcha_active = \Concrete\Core\Captcha\Library::getActive()->getSystemCaptchaLibraryHandle();
                $ab_captcha_active = ($captcha_active === 'ab_captcha') ? true : false;
                $captcha_link = '<a href="' . URL::to('/dashboard/system/permissions/captcha/') . '" target="_blank" style="color: #0099ff; text-decoration: none;">' . t('captcha configuration') . '</a>';
                echo '<p>' . t('Available captcha libraries: ') . '</p>';
                echo '<ul style="square">';
                foreach ($captcha_libs as $lib) {
                    echo '<li>';
                    if ($lib->getSystemCaptchaLibraryHandle() === 'ab_captcha' && !$ab_captcha_active) {
                        echo $lib->getSystemCaptchaLibraryName() . '<span style="color: #ff4100;">*</span>';
                    }
                    elseif ($lib->getSystemCaptchaLibraryHandle() === 'ab_captcha' && $ab_captcha_active) {
                        echo '<span style="color: #75ca2a;">' . $lib->getSystemCaptchaLibraryName() . ' ' . t('(currently in use)') . '</span>';
                    }
                    else {
                        echo $lib->getSystemCaptchaLibraryName();
                    }
                    echo '</li>';
                }
                echo '</ul>';
                echo (!$ab_captcha_active) ? 
                    '<p style="font-size: 0.8em; color: #ff4100;">* ' . t('You installed the new contact form with advanced security features. Go to the system %s to complete captcha setup.', $captcha_link) . '</p>' : 
                    '<p>' . t('Go to %s if you want to change the system captcha library', $captcha_link) . '</p>';
                ?>
            </div>
        </div>
    </div>

</fieldset>

<script>
$(document).ready(function() {
    $('#ccm-form-submit-button').on('click', function(e){
        var v = $.trim($('#email_to').val());
        var email = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+)(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,10})$/;
        if (!email.test(v) || v.length < 8 || v.length > 100) {
            e.preventDefault();  // stop form from submitting
            e.stopPropagation(); // stop anything else from listening to our event and screwing things up
            ConcreteAlert.error({
                title: <?php echo json_encode(t('Email address error:')); ?>,
                message: <?php echo json_encode(t('Email address entered is not valid!')); ?>
            });
        }
    });
    
    $('.decimals').keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
            ConcreteAlert.error({
                title: <?php echo json_encode(t('Oops')); ?>,
                message: <?php echo json_encode(t('Numbers only!')); ?>
            });
        }
    });
    
    $('#show_submit_error').on('change', function() {
        $('div[data-fields=show_submit_error]').toggle($(this).is(':checked'));

    }).trigger('change');

    $('#popup').on('change', function() {
        $('div[data-fields=popup]').toggle($(this).is(':checked'));

        if (!$(this).is(':checked')) {
            $('#form_button_text').val('');
        }
    }).trigger('change');

    $('#domains').on('change', function() {
        $('div[data-fields=domains]').toggle($(this).is(':checked'));

        if (!$(this).is(':checked')) {
            $('#blacklisted_domains').val('');
        }
    }).trigger('change');

    $('#show_captcha').on('change', function() {
        $('div[data-fields=show_captcha]').toggle($(this).is(':checked'));
    }).trigger('change');

});

</script>

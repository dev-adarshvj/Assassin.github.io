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
 
use Concrete\Core\Captcha\Library;

$buid = $controller->getBlockUID($b);
$form_action = $view->action('submit', $app->make('token')->generate('contact_form_'.$buid));

?>

<div class="ab-contact-form">
    <?php if ($popup) {?>
        <a href="#contact-form-<?php echo $buid; ?>" class="contact-form-a" id="contact_form_a_<?php echo $buid; ?>" data-buid="<?php echo $buid; ?>"><i class='fa fa-envelope'></i><?php echo $form_button_text; ?></a>
    <?php }?>

    <div class="row <?php if ($popup) { echo "mfp-hide"; } ?> contact-form-block" id="contact-form-<?php echo $buid; ?>">
        <div class="form-group">
            <div id="spinner_img_<?php echo $buid; ?>" class="spinner-img hidden"><i class="fa fa-spinner fa-spin"></i></div>
            <div id="success_<?php echo $buid; ?>" class="success hidden"></div>
            <div id="errors_<?php echo $buid; ?>" class="errors hidden"></div>
        </div>
            
        <form id="contact_form_<?php echo $buid; ?>" 
            class="contact-form" 
            enctype="multipart/form-data" 
            action="<?php echo $form_action?>" 
            method="post" 
            accept-charset="utf-8">
            
            <h2><?php echo $form_title; ?></h2>
            
            <input type="hidden" name="buid" data-buid="<?php echo $buid; ?>" data-jq="<?php echo h($jq_data); ?>">
            
            <div class="form-group">
                <?php 
                echo $form->label('name_'.$buid, $entry_name . '<span class="need">' . t('Required') . '</span>');
                echo $form->text('name_'.$buid, "", ['maxlength'=>"60", 'data-tag'=>'name']);
                ?>
                <div id="tip_name_<?php echo $buid; ?>" class="tip-name tip"><?php echo $entry_name_tip; ?></div>
                <div id="error_name_<?php echo $buid; ?>" class="error-name tip hidden"><?php echo $error_name; ?></div>
            </div>
            
            <div class="form-group">
                <?php 
                echo $form->label('email_'.$buid, $entry_email . '<span class="need">' . t('Required') . '</span>');
                echo $form->text('email_'.$buid, "", ['maxlength'=>"100", 'data-tag'=>'email']);
                ?>
                <div id="tip_email_<?php echo $buid; ?>" class="tip-email tip"><?php echo $entry_email_tip; ?></div>
                <div id="error_email_<?php echo $buid; ?>" class="error-email tip hidden"><?php echo $error_email; ?></div>
            </div>
            
            <div class="form-group">
                <?php 
                echo $form->label('message_'.$buid, $entry_message . '<span class="need">' . t('Required') . '</span>');
                echo $form->textarea('message_'.$buid, "", ['rows'=>"3", 'data-tag'=>'message']);
                ?>
                <div id="tip_message_<?php echo $buid; ?>" class="tip-message tip"><?php echo $entry_message_tip; ?></div>
                <div id="error_message_<?php echo $buid; ?>" class="error-message tip hidden"><?php echo $error_message; ?></div>
            </div>
            
            <?php 
            if ($show_captcha) {
                $captcha = $app->make('captcha');
                if (Library::getActive()->getSystemCaptchaLibraryHandle() === 'ab_captcha') {
                    $captcha->setPictureAttributes([
                        'id' => 'captcha_img_'.$buid,
                    ]);
                    ?>
                    
                    <div class="form-group">
                        <?php 
                        $captcha->display();
                        ?>
                        <div class="tip"><?php echo $entry_code_img; ?></div>
                    </div>

                    <div class="form-group">
                        <?php 
                        echo $form->label('code_'.$buid, $entry_code . '<span class="need">' . t('Required') . '</span>');
                        echo $form->text('code_'.$buid, "", ['maxlength'=>"6", 'data-tag' => "code"]);
                        ?>
                        <div id="tip_code_<?php echo $buid; ?>" class="tip-code tip"><?php echo $entry_code_tip; ?></div>
                        <div id="error_code_<?php echo $buid; ?>" class="error-code tip hidden"><?php echo $error_code; ?></div>
                    </div>
                <?php 
                }
                elseif (Library::getActive()->getSystemCaptchaLibraryHandle() === 'securimage') {
                ?>
                    <div class="form-group">
                        <?php 
                        $captcha->display();
                        ?>
                        <div class="tip"><?php echo $entry_code_img; ?></div>
                    </div>

                    <div class="form-group">
                        <?php 
                        echo $form->label('code_'.$buid, $entry_code . '<span class="need">' . t('Required') . '</span>');
                        echo $form->text('code_'.$buid, "", ['maxlength'=>"6", 'data-tag' => "code"]);
                        ?>
                        <div id="tip_code_<?php echo $buid; ?>" class="tip-code tip"><?php echo $entry_code_tip; ?></div>
                        <div id="error_code_<?php echo $buid; ?>" class="error-code tip hidden"><?php echo $error_code; ?></div>
                    </div>
                <?php 
                }
                else {
                ?>
                    <div class="form-group">
                        <?php 
                        $captcha->display();
                        $captcha->showInput();
                        ?>
                    </div>
                <?php 
                }
            }
            ?>
            
            <div class="form-group">
                <?php echo $form->submit('submit_'.$buid, $send_button_text, ['class' => "btn-lizard"]); ?>
            </div>
        </form>
    </div>
</div>

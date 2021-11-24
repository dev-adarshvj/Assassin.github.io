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

$(function(){
    $('.contact-form').submit(function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var jq_data = $(this).find('input[name=buid]').attr('data-jq');
        if (jq_data !== ''){
            jq_data = $.parseJSON(jq_data);
        }
        var buid = $(this).find('input[name=buid]').attr('data-buid');
        var form = $('#contact_form_' + buid);
        var code = $('#code_' + buid);
        var success = $('#success_' + buid);
        var errors = $('#errors_' + buid);
        var ccm_captcha_img = $('.ccm-captcha-image');
        
        success.empty();
        errors.empty();
        
        var spinner_img = $('#spinner_img_' + buid);
        
        var error_list = $('<ul id="error_list_' + buid + '"></ul>');
        
        var post_data = {
        'name': $('#name_' + buid).val(),
        'email': $('#email_' + buid).val(),
        'message': $('#message_' + buid).val(),
        'ccmCaptchaCode': $('#code_' + buid).val(),
        'buid': buid,
        };
        
        spinner_img.removeClass('hidden');
        $.ajax({
            dataType: 'json',
            type: 'post',
            data: post_data,
            url: form.attr('action'),
        })
        .done(function(response) {
            error_list.empty();
            var alert = null;
            spinner_img.addClass('hidden');
            if (response['status'] === 'ok') {
                errors.addClass('hidden');
                success.removeClass('hidden');
                alert = $('<div class="alert alert-success"><span class="alert-link">' + jq_data.jq_success + '</span></div>');
                success.append(alert);
                form[0].reset();
                if (jq_data.jq_popup == true) {
                    form.addClass('hidden');
                }
                ccm_captcha_img.trigger('click');
            }
            else {
                success.addClass('hidden');
                errors.removeClass('hidden');
                alert = $('<div class="alert alert-danger"><span class="alert-link">' + jq_data.jq_errors + '</span></div>');
                alert.append(error_list);
                $.each(response['data'], function(i, v){
                    error_list.append('<li>' + v + '</li>');
                });
                alert.append('<p>' + jq_data.jq_submit_error + '</p>');
                errors.append(alert);
                code.val('');
                var t = (new Date()).getTime();
                ccm_captcha_img.each(function(i, v) {
                    $(v).attr('src', $(v).attr('src').replace(/([?&]nocache=)(\d+)/, '$1' + t));
                });
            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            spinner_img.addClass('hidden');
            success.addClass('hidden');
            errors.removeClass('hidden');
            alert = $('<div class="alert alert-danger"><span class="alert-link">' + jq_data.jq_errors + '</span></div>');
            alert.append('<p>' + errorThrown + '</p>');
            alert.append('<p>' + jq_data.jq_submit_error + '</p>');
            errors.append(alert);
            code.val('');
            var t = (new Date()).getTime();
            ccm_captcha_img.each(function(i, v) {
                $(v).attr('src', $(v).attr('src').replace(/([?&]nocache=)(\d+)/, '$1' + t));
            });
        });
        
        $('#contact_form_' + buid + ' input').blur();
        $('#contact_form_' + buid + ' textarea').blur();
    });

    $('.contact-form-a').on('click', function(e) {
        var buid = $(this).attr('data-buid');
        var form = $('#contact_form_'+buid);
        var success = $('#success_'+buid);
        var errors = $('#errors_'+buid);
        var captcha_img = $('#captcha_img_' + buid);
        var ccm_captcha_img = $('.ccm-captcha-image');
        
        var jq_data = form.find('input[name=buid]').attr('data-jq');
        
        if (jq_data !== ''){
            jq_data = $.parseJSON(jq_data);
        }
        
        if (jq_data.jq_popup === true) {
            form.removeClass('hidden');
            form[0].reset();
            form.find('.problem').removeClass('problem');
            form.find('[id^="tip_"]').removeClass('hidden');
            form.find('[id^="error_"]').addClass('hidden');
            success.empty();
            errors.empty();
            if (captcha_img.length) {
                captcha_img.attr('src', captcha_img.attr('src').replace(/([?&]nocache=)(\d+)/, '$1' + ((new Date()).getTime())));
            }
            
            e.preventDefault();
            $.magnificPopup.open({
                items: {
                    src: '#contact-form-' + buid,
                    type: 'inline',
                },
                focus: '#name_' + buid,
                alignTop: false,
                fixedContentPos: 'auto',
                fixedBgPos: true,
                overflowY: 'auto',
                closeBtnInside: true,
                preloader: false,
                midClick: true,
                removalDelay: 300,
                mainClass: 'my-mfp-zoom-in',

                callbacks: {
                    beforeOpen: function() {
                        if($(window).width() < 700) {
                            this.st.focus = false;
                        } else {
                            this.st.focus = '#name_' + buid;
                        }
                    }
                }
            });
        }
    });
    
    $('.contact-form input[type=text]').on('blur', function(e) {
        switch($(this).data('tag')) {
            case 'name':
                validate_contact_name(e);
                break;
            case 'email':
                validate_contact_email(e);
                break;
            case 'code':
                validate_contact_code(e);
                break;
        } 
    });
    
    $('.contact-form textarea').on('blur', function(e) {
        validate_contact_message(e);
    });
    $('.contact-form textarea').attr('maxlength', '1000');
    
    function validate_contact_name(e) {
        el = $('#'+e.target.id);
        id = e.target.id;
        v = $('#'+e.target.id).val();
        if (!v || v.length < 2 || v.length > 100) {
            $('#error_'+id).removeClass('hidden');
            $('#tip_'+id).addClass('hidden');
            $(el).addClass('problem');
            return false;
        }
        else {
            $('#error_'+id).addClass('hidden');
            $('#tip_'+id).removeClass('hidden');
            $(el).removeClass('problem');
            return true;
        }
    }
        
    function validate_contact_email(e) {
        el = $('#'+e.target.id);
        id = e.target.id;
        v = $('#'+e.target.id).val();
        email = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+)(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,10})$/;
        if (!email.test(v) || v.length < 8 || v.length > 100) {
            $('#error_'+id).removeClass('hidden');
            $('#tip_'+id).addClass('hidden');
            $(el).addClass('problem');
            return false;
        }
        else {
            $('#error_'+id).addClass('hidden');
            $('#tip_'+id).removeClass('hidden');
            $(el).removeClass('problem');
            return true;
        }
    }
        
    function validate_contact_message(e) {
            el = $('#'+e.target.id);
            id = e.target.id;
            v = $('#'+e.target.id).val();
            if (!v || v.length < 10 || v.length > 1000) {
                $('#error_'+id).removeClass('hidden');
                $('#tip_'+id).addClass('hidden');
                $(el).addClass('problem');
                return false;
            }
            else {
                $('#error_'+id).addClass('hidden');
                $('#tip_'+id).removeClass('hidden');
                $(el).removeClass('problem');
                return true;
            }
    }
        
    function validate_contact_code(e) {
            el = $('#'+e.target.id);
            id = e.target.id;
            v = $('#'+e.target.id).val();
            if (!v || v.length < 4 || v.length > 6) {
                $('#error_'+id).removeClass('hidden');
                $('#tip_'+id).addClass('hidden');
                $(el).addClass('problem');
                return false;
            }
            else {
                $('#error_'+id).addClass('hidden');
                $('#tip_'+id).removeClass('hidden');
                $(el).removeClass('problem');
                return true;
            }
    }
});

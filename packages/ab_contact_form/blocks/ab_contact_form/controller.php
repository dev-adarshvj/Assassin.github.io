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

namespace Concrete\Package\AbContactForm\Block\AbContactForm;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Package\Package;
use Concrete\Core\Support\Facade\Config;

class Controller extends BlockController
{
    protected $btWrapperClass = 'ccm-ui';
    protected $btInterfaceWidth = "600";
    protected $btInterfaceHeight = "400";
    protected $btTable = 'btAbContactForm';
    protected $btDefaultSet = 'form';

    protected $form_errors = array();
    protected $form_success = array();
    
    protected $error_name = '';
    protected $error_email = '';
    protected $error_message = '';
    protected $error_banned_words = '';
    protected $error_code = '';
    protected $error_ip = '';
    protected $error_domain = '';
    protected $error_no_domain = '';
    protected $error_token = '';
    protected $error_submit = '';
    protected $error_num_submit = '';
    protected $errors = '';
    protected $success = '';
    
    protected $proxy_name = '';
    protected $proxy_ip = '';
    protected $host_name = '';
    protected $host_ip = '';
    
    protected $wait_time = 60;
    
    public function getBlockTypeDescription() 
    {
        return t("Add contact form with advanced security features");
    }

    public function getBlockTypeName() 
    {
        return t("Advanced Contact Form");
    }

    public function getBlockUID($b = null) {
        if ($b == null) return null;
        $proxyBlock = $b->getProxyBlock();
        return $proxyBlock? $proxyBlock->getBlockID() : $b->bID;
    }
    
    public function on_start() 
    {
        $this->error_name = t('Name must be 2 to 100 symbols');
        $this->error_email = t('Email entered incorrectly');
        $this->error_message = t('Message must be 10 to 1000 symbols');
        $this->error_banned_words = t('Banned words detected');
        $this->error_code = t('Code from picture entered incorrectly');
        $this->error_ip = t('To prevent spam and abusing contact form, sending messages from IP address %s is prohibited');
        $this->error_domain = t('To prevent spam and abusing contact form, sending messages with domain %s is prohibited');
        $this->error_no_domain = t('Confirmation of validity of domain %s failed');
        $this->error_token = t('Something went wrong, please try again');
        $this->error_submit = t('Message has not been sent. If you experience difficulties sending your message, please send it by email to %s.');
        $this->error_num_submit = t('Your message has already been sent, please wait for %s before sending another one');
        $this->errors = t('Errors found:');
        $this->success = t('Your message has been sent. Thank you.');
        
        $this->set('app', $this->app);
    }

    public function registerViewAssets($outputContent = '')
    {
        if ($this->popup) {
            $this->requireAsset('core/lightbox');
        }
        $this->requireAsset('javascript', 'jquery');
        $this->requireAsset('css', 'font-awesome');
    }
    
    public function view() 
    {
        $entry_name = t('Name');
        $entry_name_tip = t('Max 100 symbols');
        $entry_email = t('Email');
        $entry_email_tip = t('Max 100 symbols');
        $entry_message = t('Message');
        $entry_message_tip = t('Max 1000 symbols');
        $entry_code = t('Code check');
        $entry_code_tip = t('Code from picture 4-6 symbols');
        $entry_code_img = t('Click on picture to reload code');
        
        $this->set('entry_name', $entry_name);
        $this->set('entry_name_tip', $entry_name_tip);
        $this->set('error_name', $this->error_name);
        $this->set('entry_email', $entry_email);
        $this->set('entry_email_tip', $entry_email_tip);
        $this->set('error_email', $this->error_email);
        $this->set('entry_message', $entry_message);
        $this->set('entry_message_tip', $entry_message_tip);
        $this->set('error_message', $this->error_message);
        $this->set('entry_code', $entry_code);
        $this->set('entry_code_tip', $entry_code_tip);
        $this->set('error_code', $this->error_code);
        $this->set('entry_code_img', $entry_code_img);
        
        $jq_data = [
            'jq_errors' => $this->errors,
            'jq_submit_error' => sprintf($this->error_submit, '<span>' . $this->email_to . '</span>'),
            'jq_success' => $this->success,
            'jq_popup' => $this->popup ? true : false,
        ];
        $this->set('jq_data', json_encode($jq_data, JSON_UNESCAPED_UNICODE));
        
        $pkg = Package::getByHandle('ab_contact_form');
        $img_url = $pkg->getRelativePath() . '/blocks/ab_contact_form/images/';
        $this->set('img_url', $img_url);
    }

    public function action_submit($token = false, $bID = false) 
    {
        $data = $this->request->request->all();
        
        if ($this->bID != $bID) {
            return false;
        }
        elseif ($this->app->make('token')->validate('contact_form_'.$data['buid'], $token)) {
            if ($this->validateForm($data)) {
                $this->mailForm($data);
                echo json_encode(['status' => 'ok', 'data' => $this->form_success], JSON_UNESCAPED_UNICODE);
            }
            else {
                echo json_encode(['status' => 'error', 'data' => $this->form_errors], JSON_UNESCAPED_UNICODE);
            }
        }
        else {
            echo json_encode(['status' => 'error', 'data' => $this->error_token], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
    
    public function validateForm($data) 
    {
        $name = trim($data['name']);
        $email = mb_strtolower(preg_replace('((?:\n|\r|\t|%0A|%0D|%08|%09)+)i' , '', trim($data['email'])), 'UTF-8');
        $message = trim(strip_tags(html_entity_decode($data['message'], ENT_QUOTES, 'UTF-8')));
        $domain_name = substr(strrchr($email, "@"), 1);
        
        $HTTP_X_FORWARDED_FOR = trim($this->app->request->server->get('HTTP_X_FORWARDED_FOR'));
        $HTTP_VIA = trim($this->app->request->server->get('HTTP_VIA'));
        $REMOTE_ADDR = trim($this->app->request->server->get('REMOTE_ADDR'));
        $REMOTE_HOST = trim($this->app->request->server->get('REMOTE_HOST'));
        
        if (!empty($HTTP_X_FORWARDED_FOR)) {
            $this->proxy_name = $HTTP_VIA;
            $this->proxy_ip = $REMOTE_ADDR;
            $this->host_ip = $HTTP_X_FORWARDED_FOR;
        }
        elseif (!empty($REMOTE_ADDR) and (filter_var($REMOTE_ADDR, FILTER_VALIDATE_IP))) {
            $this->host_ip = $REMOTE_ADDR;
        }
        if (ip2long($this->host_ip) < 1) {
            $this->host_ip = "127.0.0.1";
        }
        $this->host_name = !empty($REMOTE_HOST) ? $REMOTE_HOST : @gethostbyaddr($this->host_ip);
        
        $ip = $this->app->make('ip');
        if ($ip->isBlacklisted()) {
            array_push($this->form_errors, sprintf($this->error_ip, '<span>' . $this->host_ip . '</span>'));
        }

        if ((mb_strlen($name, 'UTF-8') < 2) || (mb_strlen($name, 'UTF-8') > 100)) {
            array_push($this->form_errors, $this->error_name);
        }
        
        if ((mb_strlen($email, 'UTF-8') < 8) || (mb_strlen($email, 'UTF-8') > 100)) {
            array_push($this->form_errors, $this->error_email);
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($this->form_errors, $this->error_email);
        }

        if ($this->blacklisted_domains && in_array($domain_name, explode(',', $this->blacklisted_domains))) {
            array_push($this->form_errors, sprintf($this->error_domain, '<span>' . $domain_name . '</span>'));
        }
        
        if (!checkdnsrr($domain_name . '.',"MX")) {
            array_push($this->form_errors, sprintf($this->error_no_domain, '<span>' . $domain_name . '</span>'));
        }

        if ((mb_strlen($message, 'UTF-8') < 10) || (mb_strlen($message, 'UTF-8') > 1000)) {
            array_push($this->form_errors, $this->error_message);
        }
        
        if (Config::get('conversations.banned_words') && (
            $this->app->make('helper/validation/banned_words')->hasBannedWords($name) ||
            $this->app->make('helper/validation/banned_words')->hasBannedWords($message))) {
            array_push($this->form_errors, $this->error_banned_words);
        }
        
        $db = $this->app->make('database')->connection();
        $q = 'SELECT timestamp FROM btAbContactFormSpam WHERE ip = INET_ATON (?) ORDER BY timestamp';
        $v = [$this->host_ip];
        $r = $db->fetchAssoc($q, $v);
        
        $wait = $this->wait_time . ' ' . t('seconds');
        if ($this->wait_time > 60 && $this->wait_time <= 3600) {
            $wait = ceil($this->wait_time / 60) . ' ' . t('minutes');
        }
        elseif ($this->wait_time > 3600) {
            $wait = ceil($this->wait_time / 3600) . ' ' . t('hours');
        }
        if ($r && (time() - $r['timestamp']) < $this->wait_time) {
            array_push($this->form_errors, sprintf($this->error_num_submit, '<span>' . $wait . '</span>'));
        }

        if ($this->show_captcha) {
            $captcha = $this->app->make('captcha');
            if (!$captcha->check()) {
                array_push($this->form_errors, $this->error_code);
            }
        }
                
        if (!$this->form_errors) {
            return true;
        }
        else {
            return false;
        }
    }
	    
    public function mailForm($data) 
    {
        $dh = $this->app->make('helper/date');
        $security_service = $this->app->make('helper/security');
        $name = $security_service->sanitizeString($data['name']);
        $email = $security_service->sanitizeString($data['email']);
        $message = trim(strip_tags(html_entity_decode($data['message'], ENT_QUOTES, 'UTF-8')));
        
        $subject = html_entity_decode(sprintf($this->email_subject . " %s", BASE_URL), ENT_QUOTES, 'UTF-8');
        
        $txt_message = '';
        $txt_message .= $this->app->make('site')->getSite()->getSiteName() . "\r\n";
        $txt_message .= t('Date: ') . $dh->formatDateTime(date('H:i:s T O l, j F Y')) . "\r\n\r\n";
        $txt_message .= $subject . "\r\n\r\n";
        $txt_message .= t('Host IP: ') . $this->host_ip . "\r\n";
        $txt_message .= t('Host Name: ') . $this->host_name . "\r\n";
        $txt_message .= t('Proxy IP: ') . $this->proxy_ip . "\r\n";
        $txt_message .= t('Proxy Name: ') . $this->proxy_name . "\r\n\r\n";
        $txt_message .= t('Name: ') . $name . "\r\n";
        $txt_message .= t('Email: ') . $email . "\r\n\r\n";
        $txt_message .= t('Message: ') . "\r\n\r\n";
        $txt_message .= $data['message'];
        
        $html_message = '';
        $html_message .= '<!DOCTYPE html>';
        $html_message .= '<html>';
        $html_message .= '<head>';
        $html_message .= '<meta charset="utf-8">';
        $html_message .= '</head>';
        $html_message .= '<body>';
        $html_message .= '<p style="color: #0099ff;">';
        $html_message .= '<span style="font-weight: bold;">' . $this->app->make('site')->getSite()->getSiteName() . '</span><br />';
        $html_message .= '<span style="font-weight: bold;">' . t('Date: ') . '</span>' . $dh->formatDateTime(date('H:i:s T O l, j F Y')) . '<br /><br />';
        $html_message .= '<span style="font-weight: bold;">' . $subject . '</span><br /><br />';
        $html_message .= '<span style="font-weight: bold;">' . t('Host IP: ') . '</span>' . $this->host_ip . '<br />';
        $html_message .= '<span style="font-weight: bold;">' . t('Host Name: ') . '</span>' . $this->host_name . '<br />';
        $html_message .= '<span style="font-weight: bold;">' . t('Proxy IP: ') . '</span>' . $this->proxy_ip . '<br />';
        $html_message .= '<span style="font-weight: bold;">' . t('Proxy Name: ') . '</span>' . $this->proxy_name;
        $html_message .= '</p>';
        $html_message .= '<p>';
        $html_message .= '<span style="font-weight: bold;">' . t("Name: ") . '</span>' . $name . '<br />';
        $html_message .= '<span style="font-weight: bold;">' . t("Email: ") . '</span>' . $email;
        $html_message .= '</p>';
        $html_message .= '<p><span style="font-weight: bold;">' . t("Message: ") . '</span></p>';
        $html_message .= '<p>' . str_replace("\r\n",'<br />', $data['message']) . '</p>';
        $html_message .= '</body>';
        $html_message .= '</html>';
        
        $mh = $this->app->make('mail');
        $mh->to($this->email_to);
        $mh->from($email, $name);
        $mh->replyto($email, $name);
        $mh->setSubject($subject);
        $mh->setBody($txt_message);
        $mh->setBodyHTML($html_message);
        
        @$mh->sendMail();
        
        $db = $this->app->make('database')->connection();
        $q = 'INSERT INTO btAbContactFormSpam (ip, timestamp) VALUES(INET_ATON (?),?) ON DUPLICATE KEY UPDATE timestamp = ?';
        $v = [$this->host_ip, time(), time()];
        $db->executeQuery($q, $v);
    }
    
    public function add() 
    {
        $this->set('error_submit', $this->error_submit);
    }

    public function edit() 
    {
        $this->set('error_submit', $this->error_submit);
    }

    public function save($data) 
    {
        $data['email_to'] = isset($data['email_to']) ? trim($data['email_to']) : '';
        $data['show_submit_error'] = isset($data['show_submit_error']) ? 1 : 0;
        $data['email_subject'] = isset($data['email_subject']) ? trim($data['email_subject']) : '';
        $data['form_button_text'] = isset($data['form_button_text']) ? trim($data['form_button_text']) : '';
        $data['send_button_text'] = isset($data['send_button_text']) ? trim($data['send_button_text']) : '';
        $data['popup'] = isset($data['popup']) ? 1 : 0;
        $data['show_captcha'] = isset($data['show_captcha']) ? 1 : 0;
        $data['blacklisted_domains'] = isset($data['blacklisted_domains']) ? trim(str_replace(' ', '', $data['blacklisted_domains'])) : '';
        $data['wait_time'] = isset($data['wait_time']) ? $data['wait_time'] : 60;
        parent::save($data);
    }

    public function validate($data)
    {
        $e = $this->app->make('helper/validation/error');
        $email_to = trim($data['email_to']);
        
        if ((mb_strlen($email_to, 'UTF-8') < 8) || (mb_strlen($email_to, 'UTF-8') > 100) || !filter_var($email_to, FILTER_VALIDATE_EMAIL)) {
            $e->add(t('Email entered incorrectly'));
        }

        return $e;
    }

}

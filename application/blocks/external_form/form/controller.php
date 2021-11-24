<?php defined('C5_EXECUTE') or die("Access Denied.");

class FormBlockStatistics extends Concrete5_Controller_Block_FormStatistics {}
class MiniSurvey extends Concrete5_Controller_Block_FormMinisurvey {}
	
class FormBlockController extends Concrete5_Controller_Block_Form {
	
	public $enablePlaceholders = false;
	
	public function on_page_view() {
		$html = Loader::helper('html');
		
		if ($this->enablePlaceholders) {
			$bv = new BlockView();
			$bv->setBlockObject($this->getBlockObject());
			$blockURL = $bv->getBlockURL();
			$this->addFooterItem($html->javascript("{$blockURL}/jquery.placeholder.min.js", null, array('handle' => 'jquery.placeholder', 'version' => '2.0.7')));
			$this->addFooterItem($html->script('$(document).ready(function() { $(".formblock input, .formblock textarea").placeholder(); });'));
		}
		
		//C5 only includes jquery.form.js when user is logged in.
		//This is safe to call even if C5 is including it, though, because it will catch the duplicate
		// and only output it once.
		$this->addFooterItem($html->javascript('jquery.form.js'));
		
		//Include JQueryUI *if* the form includes a date or datetime field
		//(this is an improvement over C5.6, which always includes JQUI even when not needed)
		if ($this->viewRequiresJqueryUI()) {
			$this->addHeaderItem($html->css('jquery.ui.css'));
			$this->addFooterItem($html->javascript('jquery.ui.js'));
		}
	}
	private function viewRequiresJqueryUI() {
		$whereInputTypes = "inputType = 'date' OR inputType = 'datetime'";
		$sql = "SELECT COUNT(*) FROM {$this->btQuestionsTablename} WHERE questionSetID = ? AND bID = ? AND ({$whereInputTypes})";
		$vals = array(intval($this->questionSetId), intval($this->bID));
		$count = Loader::db()->GetOne($sql, $vals);
		return (bool)$count;
	}
	
	public function view() {
		//Set up nice clean variables for the view to use.
		//Note that we don't call parent::view(), because built-in form block controller doesn't have one(!!)
		
		$miniSurvey = new BootstrapMiniSurvey();
		$miniSurvey->frontEndMode = true;
                $miniSurvey->enablePlaceholders = $this->enablePlaceholders;

		$bID = intval($this->bID);
		$qsID = intval($this->questionSetId);
		
		$formDomId = "miniSurveyView{$bID}";
		$hasFileUpload = false;

		$questionsRS = $miniSurvey->loadQuestions($qsID, $bID);
		$questions = array();
		while ($questionRow = $questionsRS->fetchRow()) {
			$question = $questionRow;
			$question['input'] = $miniSurvey->loadInputType($questionRow, false);
                        $question['labelClasses'] = '';
                        
			if ($questionRow['inputType'] == 'fileupload') {
				$hasFileUpload = true;
			}
	
			//Make type names common-sensical
			if ($questionRow['inputType'] == 'text') {
				$question['type'] = 'textarea';
			} else if ($questionRow['inputType'] == 'field') {
				$question['type'] = 'text';
			} else {
				$question['type'] = $questionRow['inputType'];
			}
	
                        //Construct Label pieces
                        switch ($question['type']) {
                            case 'checkboxlist':
                            case 'radios':
                                //do nothing
                                break;
                            case 'fileupload':
                            case 'textarea':
                                $question['labelFor'] = 'for="Question' . $questionRow['msqID'] . '"';
                                break;
                            
                            Default:
                                //Everything else gets a normal label
                                //Which can be hidden if placeholders are on
                                
                                $question['labelFor'] = 'for="Question' . $questionRow['msqID'] . '"';
                                if ($this->enablePlaceholders) {
                                    //Hide for fields with a placeholder
                                    $question['labelClasses'] .= ' sr-only';
                                }
                        }
                        			
			//Remove hardcoded style on textareas
                        //Not sure if this is neeeded, but leave to be safe
                        if ($question['type'] == 'textarea') {
                                $question['input'] = str_replace('style="width:95%"', '', $question['input']);
			}
				
			$questions[] = $question;
		}

		//Prep thank-you message
		$success = ($_GET['surveySuccess'] && $_GET['qsid'] == intval($qsID));
		$thanksMsg = $this->thankyouMsg;

		//Prep error message(s)
		$errorHeader = $formResponse;
		$errors = is_array($errors) ? $errors : array();
		if ($invalidIP) {
			$errors[] = $invalidIP;
		}

		//Prep captcha
		$surveyBlockInfo = $miniSurvey->getMiniSurveyBlockInfoByQuestionId($qsID, $bID);
		$captcha = $surveyBlockInfo['displayCaptcha'] ? Loader::helper('validation/captcha') : false;
		
		//Send data to the view
		$this->set('formDomId', $formDomId);
		$this->set('hasFileUpload', $hasFileUpload);
		$this->set('qsID', $qsID);
		$this->set('pURI', $pURI);
		$this->set('success', $success);
		$this->set('thanksMsg', $thanksMsg);
		$this->set('errorHeader', $errorHeader);
		$this->set('errors', $errors);
		$this->set('questions', $questions);
		$this->set('captcha', $captcha);
		$this->set('enablePlaceholders', $this->enablePlaceholders);
		$this->set('formName', $surveyBlockInfo['surveyName']); //for GA event tracking
	}
}	


class BootstrapMiniSurvey extends MiniSurvey {

    public $enablePlaceholders = false;
    
    public function loadInputType($questionData, $showEdit) {

        if ($showEdit) {
            //Fallback to the default C5 method
            //Prevents conflicts with the internal Bootstrap CSS when
            //editing forms via the Dashboard
            return parent::loadInputType($questionData, $showEdit);
        } else {
            //Work our magic :)
            $options = explode('%%', $questionData['options']);
            $msqID = intval($questionData['msqID']);
            $placeholderAttr = $this->enablePlaceholders ? 
                    'placeholder="' . $question['question'] . ($question['required'] ? ' *' : '') . '" ' : '';
            $datetime = loader::helper('form/date_time');
            switch ($questionData['inputType']) {
                case 'checkboxlist':
                    
                    for ($i = 0; $i < count($options); $i++) {
                        if (strlen(trim($options[$i])) == 0) {
                            continue;                        
                        }
                        
                        $checked = ($_REQUEST['Question' . $msqID . '_' . $i] == trim($options[$i])) ? 'checked' : '';
                        $html.= '  <div class="checkbox"><label><input name="Question' 
                                . $msqID . '_' . $i . '" type="checkbox" value="' 
                                . trim($options[$i]) . '" ' . $checked . ' />&nbsp;' . $options[$i] . '</label></div>' . "\r\n";
                    }
                    
                    return $html;

                case 'select':
                    if ($this->frontEndMode) {
                        $selected = (!$_REQUEST['Question' . $msqID]) ? 'selected="selected"' : '';
                        $html.= '<option value="" ' . $selected . '>----</option>';
                    }
                    foreach ($options as $option) {
                        $checked = ($_REQUEST['Question' . $msqID] == trim($option)) ? 'selected="selected"' : '';
                        $html.= '<option ' . $checked . '>' . trim($option) . '</option>';
                    }
                    return '<select name="Question' . $msqID . '" id="Question' . $msqID . '" >' . $html . '</select>';

                case 'radios':
                    foreach ($options as $option) {
                        if (strlen(trim($option)) == 0) {
                            continue;
                        }
                        
                        $checked = ($_REQUEST['Question' . $msqID] == trim($option)) ? 'checked' : '';
                        $html.= '<div class="radio"><label><input name="Question' 
                                . $msqID . '" type="radio" value="' . trim($option) . '" ' 
                                . $checked . ' />&nbsp;' . $option . '</label></div>';
                    }
                    return $html;

                case 'fileupload':
                    $html = '<input type="file" name="Question' . $msqID . '" id="Question' . $msqID . '" />';
                    return $html;

                case 'text':
                    $val = ($_REQUEST['Question' . $msqID]) ? Loader::helper('text')->entities($_REQUEST['Question' . $msqID]) : '';
                    return '<textarea class="form-control" name="Question' . $msqID . '" id="Question' . $msqID . '" rows="' . $questionData['height'] . '">' . $val . '</textarea>';
                case 'url':
                    $val = ($_REQUEST['Question' . $msqID]) ? $_REQUEST['Question' . $msqID] : '';
                    
                    return '<input class="form-control" name="Question' . $msqID . '" 
                        id="Question' . $msqID . '" type="url" 
                        value="' . stripslashes(htmlspecialchars($val)) . '" ' . $placeholderAttr . '/>';
                case 'telephone':
                    $val = ($_REQUEST['Question' . $msqID]) ? $_REQUEST['Question' . $msqID] : '';
                    return '<input class="form-control" name="Question' . $msqID . '" id="Question' . $msqID . '" type="tel" value="' . stripslashes(htmlspecialchars($val)) . '" ' . $placeholderAttr . '/>';
                case 'email':
                    $val = ($_REQUEST['Question' . $msqID]) ? $_REQUEST['Question' . $msqID] : '';
                    return '<input class="form-control" name="Question' . $msqID . '" id="Question' . $msqID . '" type="email" value="' . stripslashes(htmlspecialchars($val)) . '" ' . $placeholderAttr . '/>';
                case 'date':
                    $val = ($_REQUEST['Question' . $msqID]) ? $_REQUEST['Question' . $msqID] : '';
                    return $datetime->date('Question' . $msqID, ($val !== '' ? $val : 'now'));
                case 'datetime':
                    $val = ($_REQUEST['Question' . $msqID]) ? $_REQUEST['Question' . $msqID] : '';
                    return $datetime->datetime('Question' . $msqID, $val);
                case 'field':
                default:
                    $val = ($_REQUEST['Question' . $msqID]) ? $_REQUEST['Question' . $msqID] : '';
                    return '<input class="form-control" name="Question' . $msqID . '" id="Question' . $msqID . '" type="text" value="' . stripslashes(htmlspecialchars($val)) . '" ' . $placeholderAttr . '/>';
            }
        }
    }
}
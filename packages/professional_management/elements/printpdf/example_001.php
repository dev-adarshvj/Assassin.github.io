<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
use \Concrete\Package\FormidablePdfReport\Src\MailExtended;



require_once('tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(400, 300), true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
//$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}



// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('courier', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
/*$html = <<<EOD
<h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
<i>This is the first example of TCPDF library.</i>
<p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
<p>Please check the source code documentation and other examples for further information.</p>
<p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>
EOD;*/
$html = $pddftable;

//echo $pddftable_image;exit;



//echo $pddftable;exit;
// Print text using writeHTMLCell()
$pdf->writeHTMLCell(0, 0, '', '', $pddftable, 0, 1, 0, true, '', true);

$pdf->AddPage();
$pdf->writeHTMLCell(0, 0, '', '', $pddftable_image, 0, 1, 0, true, '', true);
// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
//$pdf->Output('example_001.pdf', 'I');
$pdfName='mfpd_open-burning-form_'.rand(1000,5000).'.pdf';;

$pdf->Output(strtolower($pdfName), 'I');

$FileNamePath = __DIR__ .'/pdf_download/'.strtolower($pdfName);

    //Save the document to the server
//$QuotationAttachment = $pdf->Output($FileNamePath, 'F');


$pdf->Output(__DIR__ .'/pdf_download/'.strtolower($pdfName),'F');

$mh = Loader::helper('mail');
				
                $mh->to($to_mail);
							
                $mh->from($from_mail);
		
				$mh->setSubject('subject');
				$mh->setBody('body');
                $mh->addParameter('answerID', $formid);
               // $afiles            = array();


                $afiles = array();
                $pdffilepath       = __DIR__ .'/pdf_download/'.strtolower($pdfName);
                $afiles[0]['path'] = $pdffilepath;
                $afiles[0]['mime'] = 'application/pdf';
                $afiles[0]['name'] = basename($pdffilepath);

    //$afiles2 =  \Concrete\Core\File\File::getByID(331);
                //$mh->addAttachment($afiles);           
               
				

    

       // if(Config::get('TO_EMAIL_ADDRESS') == ''){
					//$msg = t('To address is empty on Pdf generator Email Settings');
				 //   Log::addEntry($msg,'Pdf generator');
				//	}elseif(Config::get('FROM_EMAIL_ADDRESS') == ''){
				//	$msg = t('From address is empty on Pdf generator Email Settings');
				  //  Log::addEntry($msg,'Pdf generator');
				//	}else{
                //@$mh->sendMail();




//============================================================+
// END OF FILE
//============================================================+

<?php

// Copyright © 2004 Frédéric Jaqcuot
// Copyright © 2007-2009 Johan Cwiklinski
//
// This file is part of Galette (http://galette.tuxfamily.org).
//
// Galette is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Galette is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Galette. If not, see <http://www.gnu.org/licenses/>.

/**
 * Generation d'un PDF d'étiquettes
 *
 * La création des étiquettes au format pdf se fait 
 * depuis la page de gestion des adhérents en sélectionnant
 * les adhérents  dans la liste
 *
 * Le format des étiquettes et leur mise en page est définie
 * dans l'écran des préférences
 *
 * @package    Galette
 *
 * @author     Frédéric Jaqcuot
 * @copyright  2004 Frédéric Jaqcuot
 * @copyright  2007-2009 Johan Cwiklinski
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 * @version    $Id$
 */

require_once('includes/galette.inc.php');

if( !$login->isLogged() ) {
	header("location: index.php");
	die();
}
if ( !$login->isAdmin() ) {
	header("location: voir_adherent.php");
	die();
}

require_once(WEB_ROOT . 'classes/pdf.class.php');
require_once(WEB_ROOT . 'classes/members.class.php');
require_once(WEB_ROOT . 'classes/varslist.class.php');

if( isset($_SESSION['galette']['varslist']) ){
	$varslist = unserialize( $_SESSION['galette']['varslist'] );
} else {
	$log->log('No member selected to generate labels', PEAR_LOG_INFO);
	if( $login->isAdmin )
		header('location:gestion_adherent.php');
}

$members = Members::getArrayList($varslist->selected);

if( !is_array($members) || count($members) < 1 ) die();

$doc_title = _T("Member's Labels");
$doc_subject = _T("Generated by Galette");
$doc_keywords = _T("Labels");
// Create new PDF document
$pdf = new PDF("P","mm","A4",true,"UTF-8"); 

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor(PDF_AUTHOR);
$pdf->SetTitle($doc_title);
$pdf->SetSubject($doc_subject);
$pdf->SetKeywords($doc_keywords);

// No hearders and footers
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->setFooterMargin(0);
$pdf->setHeaderMargin(0);

// Show full page
$pdf->SetDisplayMode("fullpage");

// Disable Auto Page breaks
$pdf->SetAutoPageBreak(false,0);

// Set colors
$pdf->SetDrawColor(160,160,160);
$pdf->SetTextColor(0);

// Set margins
$pdf->SetMargins(PREF_ETIQ_MARGES_H, PREF_ETIQ_MARGES_V);
// Set font
$pdf->SetFont("FreeSerif","",PREF_ETIQ_CORPS);

// Set origin
// Top left corner		
$yorigin=round(PREF_ETIQ_MARGES_V);
$xorigin=round(PREF_ETIQ_MARGES_H);
// Label width
$w = round(PREF_ETIQ_HSIZE);
// Label heigth
$h = round(PREF_ETIQ_VSIZE);
// Line heigth
$line_h=round($h/5);
$nb_etiq=0;

foreach($members as $member){
	// Detect page breaks
	if ($nb_etiq % (PREF_ETIQ_COLS * PREF_ETIQ_ROWS)==0){
		$pdf->AddPage();
	}
	// Compute label position
	$col=$nb_etiq % PREF_ETIQ_COLS;
	$row=($nb_etiq/PREF_ETIQ_COLS) % PREF_ETIQ_ROWS;
	// Set label origin
	$x = $xorigin + $col*(round(PREF_ETIQ_HSIZE)+round(PREF_ETIQ_HSPACE));
	$y = $yorigin + $row*(round(PREF_ETIQ_VSIZE)+round(PREF_ETIQ_VSPACE));
	// Draw a frame around the label
	$pdf->Rect($x,$y,$w,$h);
	// Print full name
	$pdf->SetXY($x,$y);
	$pdf->Cell($w,$line_h, $member->spoliteness . ' ' . strtoupper($member->name) . ' ' . ucfirst(strtolower( $member->surname )) ,0,0,"C",0);
	// Print first line of adress
	$pdf->SetXY($x,$y+$line_h);
	$pdf->Cell($w,$line_h, $member->adress,0,0,"C",0);
	// Print second line of adress
	$pdf->SetXY($x,$y+$line_h*2);
	$pdf->Cell($w,$line_h, $member->adress2,0,0,"C",0);
	// Print zip code and town
	$pdf->SetXY($x,$y+$line_h*3);
	$pdf->Cell($w,$line_h,$member->zipcode . ' - ' . $member->town,0,0,"C",0);
	// Print country
	$pdf->SetXY($x,$y+$line_h*4);
	$pdf->Cell($w,$line_h, $member->country,0,0,"C",0);
	$nb_etiq++;

}

// Send PDF code to browser
$pdf->Output(_T("Labels").".pdf","D");
?>

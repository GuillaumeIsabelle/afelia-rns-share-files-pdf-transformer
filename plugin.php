<?php
/*
Plugin Name: RN Share Files JG
Plugin URI: http://www.mattytemple.com.org/projects/yourls-share-files/
Description: (RN JG) Upload PDF and create text , word document
Version: 2.1
Author: Matt Temple & JG
Author URI: http://www.mattytemple.com/jgwill.com
*/

// Register our plugin admin page
yourls_add_action( 'plugins_loaded', 'matt_rnjg_share_files_add_page' );
function matt_rnjg_share_files_add_page() {
	yourls_register_plugin_page( 'rn_share_files_jg', 'RN Share Files JG', 'matt_rnjg_share_files_do_page' );
	// parameters: page slug, page title, and function that will display the page itself
}

// Display admin page
function matt_rnjg_share_files_do_page() {

	// Check if a form was submitted
	if(isset($_FILES['file_upload']['name'])) {
		matt_rnjg_share_files_save_files();
	}
	echo '
				
				<h1>RN Share Files JG</h1>
				<p>This plugin allows you to upload PDF to RN Quick Reader</p>
				<br>
				<p>It will create a text if it is a PDF...</p>
				<form method="post" enctype="multipart/form-data">
				<p><label for="file_upload">Select file to Upload</label> <input type="file" id="file_upload" name="file_upload" /></p>
				<p><label for="custom_keyword">Custom Keyword</label> <input size="80" type="text" id="custom_keyword" name="custom_keyword" /></p>
				<p><input type="submit" value="Upload File" /></p>
				</form>';
}

// Update option in database
function matt_rnjg_share_files_save_files() {
	$matt_rnjg_url = 'http://jgwill.com/fichiers/';
	//$matt_rnjg_uploaddir = $_SERVER['DOCUMENT_ROOT'].'/www.jgwill.com/files/';
	$matt_rnjg_uploaddir = '/www/fichiers/';
	//
	$matt_rnjg_extension = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
	$matt_rnjg_filename = pathinfo($_FILES['file_upload']['name'], PATHINFO_FILENAME);
	$matt_rnjg_filename_trim = trim($matt_rnjg_filename);
	$matt_rnjg_RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-])", "(-{2,})" ); 
	$matt_rnjg_ReplaceWith = array("-", "", "-"); 
	$matt_rnjg_safe_filename = preg_replace($matt_rnjg_RemoveChars, $matt_rnjg_ReplaceWith, $matt_rnjg_filename_trim); 
	$matt_rnjg_count = 2;
	$matt_rnjg_path = $matt_rnjg_uploaddir.$matt_rnjg_safe_filename.'.'.$matt_rnjg_extension;

//by JG 2016-12-29, Goal: create a PDFtoText when a new file is a PDF   myfile.pdf.txt	
$matt_rnjg_path_text = $matt_rnjg_uploaddir.$matt_rnjg_safe_filename.'.'.$matt_rnjg_extension . '.' . 'txt';

$matt_rnjg_filename = $matt_rnjg_filename . '-txt';
$matt_rnjg_final_file_name_text = $matt_rnjg_safe_filename.'.'.$matt_rnjg_extension . '.' . 'txt';

	$matt_rnjg_final_file_name = $matt_rnjg_safe_filename.'.'.$matt_rnjg_extension;

	//add   -01... if file exist...
	while(file_exists($matt_rnjg_path)) {
		$matt_rnjg_path = $matt_rnjg_uploaddir.$matt_rnjg_safe_filename.'-'.$matt_rnjg_count.'.'.$matt_rnjg_extension;
		$matt_rnjg_path_text = $matt_rnjg_uploaddir.$matt_rnjg_safe_filename.'-' . $matt_rnjg_count. '.'.$matt_rnjg_extension . '.' . 'txt';
		$matt_rnjg_final_file_name_text = $matt_rnjg_safe_filename . '-' . $matt_rnjg_count.'.'.$matt_rnjg_extension . '.' . 'txt';

		$matt_rnjg_final_file_name = $matt_rnjg_safe_filename.'-'.$matt_rnjg_count.'.'.$matt_rnjg_extension;
		$matt_rnjg_count++;	
	}
	/*$test_file = fopen($matt_rnjg_uploaddir.'test-file.txt', w);
	fwrite($test_file, 'testing file writes');
	fclose($test_file);
	die(); */
	if(copy($_FILES['file_upload']['tmp_name'], $matt_rnjg_path)) {
		//todo jg logic to save (create the text file
	///usr/bin/pdftotext
		
		//create the TEXT if extension is PDF
		if ($matt_rnjg_extension == 'pdf') {
			shell_exec('/usr/bin/pdftotext ' . $matt_rnjg_path . ' ' . $matt_rnjg_path_text);
			
	} //end if ext was pdf

		if(isset($_POST['custom_keyword']) && $_POST['custom_keyword'] != '') {
			 $matt_rnjg_custom_keyword = $_POST['custom_keyword'];
			// $matt_rnjg_short_url = yourls_add_new_link($matt_rnjg_url.$matt_rnjg_final_file_name, $matt_rnjg_custom_keyword, $matt_rnjg_filename);
			
			//todo jg add logic to create a link for the text file.			
			$matt_rnjg_short_url_text =  '';
			if ($matt_rnjg_extension == 'pdf') {
					$matt_rnjg_custom_keyword_text = $_POST['custom_keyword'] . '/txt';
					
				$matt_rnjg_short_url_text = 
				yourls_add_new_link($matt_rnjg_url.$matt_rnjg_final_file_name_text, $matt_rnjg_custom_keyword_text, 
				$matt_rnjg_custom_keyword);
			}
			
			//echo 'Your file was saved successfully at '.$matt_rnjg_short_url['shorturl'];
			
			// echo 'Your file was saved successfully at <a target="_blank"  href="'
				// . $matt_rnjg_short_url['shorturl'] . '">' 
				// . $matt_rnjg_short_url['shorturl'] . '</a>';
			
//echo if PDF extension			
			if ($matt_rnjg_extension == 'pdf') {
			echo 'Additional Text file created and was saved successfully at <a target="_blank"  href="'
				. $matt_rnjg_short_url_text['shorturl'] . '">' 
				. $matt_rnjg_short_url_text['shorturl'] . '</a>';
			}
		} 
		else{ //no custom_keyword set
			
			$matt_rnjg_short_url = yourls_add_new_link($matt_rnjg_url.$matt_rnjg_final_file_name, $matt_rnjg_final_file_name, 
				$matt_rnjg_custom_keyword);
			
			echo 'Your file was saved successfully at <a target="_blank" href="'
			. $matt_rnjg_short_url['shorturl'] . '">' 
			. $matt_rnjg_short_url['shorturl'] . '</a>';
								
			if ($matt_rnjg_extension == 'pdf') {
					
				$matt_rnjg_short_url_text = yourls_add_new_link(
				$matt_rnjg_url.$matt_rnjg_final_file_name_text, 
				$matt_rnjg_final_file_name_text, 
				$matt_rnjg_custom_keyword);
			echo 'Additional Text file created and was saved successfully at <a target="_blank" href="'
			. $matt_rnjg_short_url_text['shorturl'] . '">' 
			. $matt_rnjg_short_url_text['shorturl'] . '</a>';
			}
		}
	} else {
		echo 'something went wrong when saving your file';
	}
}

function jg_get_url_base_path($path)
{
	$tst = substr($path,0,3);
	if (substr($path,0,3) == 'http' || substr($path,0,1) == '/'|| substr($path,0,2) == 'ftp')
	{	 $name = basename($path); // to get file name
        $ext = pathinfo($path, PATHINFO_EXTENSION); // to get extension
		return $name . '.' . $ext;
	}
	else 
		return $url ; //most likely not a URL
}

function jg_get_current_url($url)
{
	
	$r = parse_url($url);
	$host = $r["host"];
	$scheme = $r["scheme"];	
	$port = $r["port"];
	$user = $r["user"];
	$pass = $r["pass"];
	$path = $r["path"];
	$query  = $r["query"];
	$fragment = $r["fragment"];
	
	return $scheme . '//' . $host . $path ;
}
<?php
/*
Plugin Name: Share Files JG
Plugin URI: http://www.mattytemple.com.org/projects/yourls-share-files/
Description: (JG) A simple plugin that allows you to easily share files and create text if PDF, word document
Version: 2.0
Author: Matt Temple & JG
Author URI: http://www.mattytemple.com/jgwill.com
*/

// Register our plugin admin page
yourls_add_action( 'plugins_loaded', 'matt_jg_share_files_add_page' );
function matt_jg_share_files_add_page() {
	yourls_register_plugin_page( 'share_files_jg', 'Share Files JG', 'matt_jg_share_files_do_page' );
	// parameters: page slug, page title, and function that will display the page itself
}

// Display admin page
function matt_jg_share_files_do_page() {

	// Check if a form was submitted
	if(isset($_FILES['file_upload']['name'])) {
		matt_jg_share_files_save_files();
	}
	echo '
				<h1>RN Share Files JG</h1>
				<p>This plugin allows you to upload PDF to RN Quick Reader</p>
				<p>It will create a text if it is a PDF...</p>
				<form method="post" enctype="multipart/form-data">
				<p><label for="file_upload">Select file to Upload</label> <input type="file" id="file_upload" name="file_upload" /></p>
				<p><label for="custom_keyword">Custom Keyword</label> <input type="text" id="custom_keyword" name="custom_keyword" /></p>
				<p><input type="submit" value="Upload File" /></p>
				</form>';
}

// Update option in database
function matt_jg_share_files_save_files() {
	$matt_jg_url = 'http://jgwill.com/fichiers/';
	//$matt_jg_uploaddir = $_SERVER['DOCUMENT_ROOT'].'/www.jgwill.com/files/';
	$matt_jg_uploaddir = '/www/fichiers/';
	//
	$matt_jg_extension = pathinfo($_FILES['file_upload']['name'], PATHINFO_EXTENSION);
	$matt_jg_filename = pathinfo($_FILES['file_upload']['name'], PATHINFO_FILENAME);
	$matt_jg_filename_trim = trim($matt_jg_filename);
	$matt_jg_RemoveChars  = array( "([\40])" , "([^a-zA-Z0-9-])", "(-{2,})" ); 
	$matt_jg_ReplaceWith = array("-", "", "-"); 
	$matt_jg_safe_filename = preg_replace($matt_jg_RemoveChars, $matt_jg_ReplaceWith, $matt_jg_filename_trim); 
	$matt_jg_count = 2;
	$matt_jg_path = $matt_jg_uploaddir.$matt_jg_safe_filename.'.'.$matt_jg_extension;

//by JG 2016-12-29, Goal: create a PDFtoText when a new file is a PDF   myfile.pdf.txt	
$matt_jg_path_text = $matt_jg_uploaddir.$matt_jg_safe_filename.'.'.$matt_jg_extension . '.' . 'txt';

$matt_jg_filename = $matt_jg_filename . '-txt';
$matt_jg_final_file_name_text = $matt_jg_safe_filename.'.'.$matt_jg_extension . '.' . 'txt';

	$matt_jg_final_file_name = $matt_jg_safe_filename.'.'.$matt_jg_extension;

	//add   -01... if file exist...
	while(file_exists($matt_jg_path)) {
		$matt_jg_path = $matt_jg_uploaddir.$matt_jg_safe_filename.'-'.$matt_jg_count.'.'.$matt_jg_extension;
		$matt_jg_path_text = $matt_jg_uploaddir.$matt_jg_safe_filename.'-' . $matt_jg_count. '.'.$matt_jg_extension . '.' . 'txt';
		$matt_jg_final_file_name_text = $matt_jg_safe_filename . '-' . $matt_jg_count.'.'.$matt_jg_extension . '.' . 'txt';

		$matt_jg_final_file_name = $matt_jg_safe_filename.'-'.$matt_jg_count.'.'.$matt_jg_extension;
		$matt_jg_count++;	
	}
	/*$test_file = fopen($matt_jg_uploaddir.'test-file.txt', w);
	fwrite($test_file, 'testing file writes');
	fclose($test_file);
	die(); */
	if(copy($_FILES['file_upload']['tmp_name'], $matt_jg_path)) {
		//todo jg logic to save (create the text file
	///usr/bin/pdftotext
		
		//create the TEXT if extension is PDF
		if ($matt_jg_extension == 'pdf') {
			shell_exec('/usr/bin/pdftotext ' . $matt_jg_path . ' ' . $matt_jg_path_text);
			
	} //end if ext was pdf

		if(isset($_POST['custom_keyword']) && $_POST['custom_keyword'] != '') {
			$matt_jg_custom_keyword = $_POST['custom_keyword'];
			$matt_jg_short_url = yourls_add_new_link($matt_jg_url.$matt_jg_final_file_name, $matt_jg_custom_keyword, $matt_jg_filename);
			
			//todo jg add logic to create a link for the text file.			
			$matt_jg_short_url_text =  '';
			if ($matt_jg_extension == 'pdf') {
					$matt_jg_custom_keyword_text = $_POST['custom_keyword'] . '/txt';
				$matt_jg_short_url_text = 
				yourls_add_new_link($matt_jg_url.$matt_jg_final_file_name_text, $matt_jg_custom_keyword_text, $matt_jg_filename_text);
			}
			
			//echo 'Your file was saved successfully at '.$matt_jg_short_url['shorturl'];
			
			echo 'Your file was saved successfully at <a target="_blank"  href="'
									. $matt_jg_short_url['shorturl'] . '">' 
									. $matt_jg_short_url['shorturl'] . '</a>';
			
//echo if PDF extension			
			if ($matt_jg_extension == 'pdf') {
			echo 'Additional Text file created and was saved successfully at <a target="_blank"  href="'
									. $matt_jg_short_url_text['shorturl'] . '">' 
									. $matt_jg_short_url_text['shorturl'] . '</a>';
			}
		} 
		else{ //no custom_keyword set
			
			$matt_jg_short_url = yourls_add_new_link($matt_jg_url.$matt_jg_final_file_name, NULL, $matt_jg_filename);
			
			echo 'Your file was saved successfully at <a target="_blank" href="'
                                . $matt_jg_short_url['shorturl'] . '">' 
                                . $matt_jg_short_url['shorturl'] . '</a>';
								
			if ($matt_jg_extension == 'pdf') {
					
				$matt_jg_short_url_text = yourls_add_new_link($matt_jg_url.$matt_jg_final_file_name_text, NULL, $matt_jg_filename_text);
			echo 'Additional Text file created and was saved successfully at <a target="_blank" href="'
                                . $matt_jg_short_url_text['shorturl'] . '">' 
                                . $matt_jg_short_url_text['shorturl'] . '</a>';
			}
		}
	} else {
		echo 'something went wrong when saving your file';
	}
}

<?php
/*
Plugin Name: CRM Portal
Plugin URI: http://preitmeyer.github.com/CRMPortal
Description: Contact Form and Case Management for Dynamics CRM 2011 Online and IFD
Author: Srini Raja, Paul Reitmeyer, Dobroslav Kolev
Version: 1.0.0
Author URI: http://www.21logs.com/
*/

/*

Copyright 2010-2011 Twentyone Logs Inc, 2012 Planet Technologies Inc

file: 21logs-dynamicscrm-plugin.php

author:Srini Raja, Paul Reitmeyer, Dobroslav Kolev

version: 1.0.0

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

     http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/

require_once('AdapterFactory.php');
	

add_action('admin_init', 'leads2dynamicscrmoptions_init' );
add_action('admin_menu', 'leads2dynamicscrmoptions_add_page');

// Init plugin options to white list our options
function leads2dynamicscrmoptions_init(){
	register_setting( 'leads2dynamicscrmoptions_init_options', 'leads', 'leads2dynamicscrmoptions_validate' );
}

// Add menu page
function leads2dynamicscrmoptions_add_page() {
	add_options_page('Dynamics CRM Configuration Settings', 'Dynamics CRM ', 'manage_options', 'dynamicscrm_options', 'dynamicscrm_options_do_page');
}

add_shortcode('dynamicscrm', 'dynamicscrm_form_shortcode');	

function dynamicscrm_form_shortcode($is_sidebar = false) {
	$options = get_option("leads");

	$content = "";
	if (isset($_POST['dynasubmit'])) {
		$error = false;
		$values = array();

		$firstname = $_POST['firstname'];
		if(empty($firstname)){
			$e_msg .= '<br/>Please enter a First Name.';
			$error = true;
		}
		
		$values['firstname'] = esc_attr(strip_tags(stripslashes($firstname)));

		$lastname = $_POST['lastname'];
		if(empty($lastname)){
			$e_msg .= '<br/>Please enter a Last Name.';
			$error = true;
		}

		$values['lastname'] = esc_attr(strip_tags(stripslashes($lastname)));
		
		
		$email = $_POST['email'];

		if(!is_email($email)){
			$e_msg .= '<br/>The email address you entered is not a valid email address.';
			$error = true;
		}

		$values['email'] = esc_attr(strip_tags(stripslashes($email)));
		
		$phonenumber = $_POST['phonenumber'];
		
		$values['phonenumber'] = esc_attr(strip_tags(stripslashes($phonenumber)));
		
		
		$description = $_POST['description'];
		$values['description'] = esc_attr(strip_tags(stripslashes($description)));
		
		$topic = 'CRM Portal Form Submission';
		$values['topic'] = $topic;
		
		
		if($error){
			$content .= dynamicscrm_form($values,$e_msg);
		}else{
			if(postto_dynamicscrm_form($values,$options)=="nono"){
				$content .='Unable to Save Info';
			}else{
				$content .= 'Thank you for contacting us. We will get back to you shortly.';
			}
			
			
		}
		
	}else{
		$content .= dynamicscrm_form($values);
	}
	
	return $content;
}

function dynamicscrm_form($values,$e_msg=""){
	$content .= '<style type="text/css">
		#contactus .dynalabel{width:150px;display:block;}
		#contactus input{display:block;}
		#e_msg{color:#ff0000;}
	</style>';
	
	$content .= '<div id="e_msg">' . $e_msg . '</div>'; 
	
	$content .= "\n".'<form  id="contactus" method="post">'."\n";
	
	$content .= "\t".'<label for="firstname" class="dynalabel">First Name *</label>';
	
	$content .= '<input id="firstname" name="firstname" type="text" value="'.$values['firstname'].'"/>';

	$content .= "\t".'<label for="lastname" class="dynalabel">Last Name *</label>';
	
	$content .= '<input id="lastname" name="lastname" type="text" value="'.$values['lastname'].'"/>';

	$content .= "\t".'<label for="email" class="dynalabel">Email *</label>';
	
	$content .= '<input id="email" name="email" type="text" value="'.$values['email'].'"/>';

	$content .= "\t".'<label for="phonenumber" class="dynalabel">Phone number </label>';
	
	$content .= '<input id="phonenumber" name="phonenumber" type="text" value="'.$values['phonenumber'].'"/>';
	
	$content .= "\t".'<label for="description" class="dynalabel">Message </label>';
	
	$content .= '<textarea id="description" name="description" rows="6" cols="40" value="'.$values['description'].'"></textarea>';

	
	$submit = stripslashes($options['submitbutton']);
	if (empty($submit))
		$submit = "Submit";
	$content .= "\t".'<br><input type="submit" name="dynasubmit"  value="Contact Us "/>';
	$content .= '</form>'."\n";

	$content .= '<p></p>';

	return $content;
}

function postto_dynamicscrm_form($values,$options) {
	$adapter = AdapterFactory::CreateAdapter();
	
	//$adapter->setEmail($options['email']);
	//$adapter->setPassword($options['password']);
	//$adapter->doAuth();
	return $adapter->createLead($values,$options['orgurl']);
	
}
//BEGIN CASES PAGE CODE

add_shortcode('casescode', 'cases_form_shortcode');

function cases_form_shortcode($is_sidebar = false) {
	$options = get_option("leads");

	$content = "";
	if (isset($_POST['casesubmit'])) {
		$error = false;
		$values = array();

		$firstname = $_POST['firstname'];
		if(empty($firstname)){
			$e_msg .= '<br/>Please enter a Topic for the case.';
			$error = true;
		}
		
		$values['firstname'] = esc_attr(strip_tags(stripslashes($firstname)));

		/*$lastname = $_POST['lastname'];
		if(empty($lastname)){
			$e_msg .= '<br/>Please enter a Last Name.';
			$error = true;
		}

		$values['lastname'] = esc_attr(strip_tags(stripslashes($lastname)));
		
		
		$email = $_POST['email'];

		if(!is_email($email)){
			$e_msg .= '<br/>The email address you entered is not a valid email address.';
			$error = true;
		}

		$values['email'] = esc_attr(strip_tags(stripslashes($email)));
		*/
		
		$phonenumber = $_POST['phonenumber'];
		
		$values['phonenumber'] = esc_attr(strip_tags(stripslashes($phonenumber)));
		
		
		$description = $_POST['description'];
		$values['description'] = esc_attr(strip_tags(stripslashes($description)));
		
		$topic = 'CRM Portal Form Submission';
		$values['topic'] = $topic;
		
		global $current_user;
		get_currentuserinfo();
		$guid = get_user_meta($current_user->ID, 'description', true);
		$values['guid'] = $guid;
		
		$entityname = 'account';
		$values['entity'] = $entityname;
		
				
		if($error){
			$content .= cases_form($values,$e_msg);
		}else{
			if(postto_cases_form($values,$options)=="nono"){
				$content .='Unable to create case.';
			}else{
				$content .= 'Case creation has been successful, we will respond to you as soon as possible.';
			}
			
			
		}
		
	}else{
		$content .= cases_form($values);
	}
	
	return $content;
}

function cases_form($values,$e_msg=""){
	$content .= '<style type="text/css">
		#contactus .dynalabel{width:150px;display:block;}
		#contactus input{display:block;}
		#e_msg{color:#ff0000;}
	</style>';

	$content .= '<div id="e_msg">' . $e_msg . '</div>'; 
	
	$content .= "\n".'<form  id="contactus" method="post">'."\n";
	
	$content .= "\t".'<label for="firstname" class="dynalabel">Case Topic *</label>';
	
	$content .= '<input id="firstname" name="firstname" type="text" value="'.$values['firstname'].'"/>';

	$content .= "\t".'<label for="phonenumber" class="dynalabel">Phone number </label>';
	
	$content .= '<input id="phonenumber" name="phonenumber" type="text" value="'.$values['phonenumber'].'"/>';
	
	$content .= "\t".'<label for="description" class="dynalabel">Problem Details </label>';
	
	$content .= '<textarea id="description" name="description" rows="6" cols="40" value="'.$values['description'].'"></textarea>';

	
	$submit = stripslashes($options['submitbutton']);
	if (empty($submit))
		$submit = "Submit";
	$content .= "\t".'<br><input type="submit" name="casesubmit"  value="Create Case "/>';
	$content .= '</form>'."\n";

	$content .= '<p></p>';

	return $content;

	
}

function postto_cases_form($values,$options) {
	$adapter = AdapterFactory::CreateAdapter();
	
	//$adapter->setEmail($options['email']);
	//$adapter->setPassword($options['password']);
	$adapter->doAuth();
	return $adapter->createCase($values,$options['orgurl']);
	
}

//END CASES PAGE CODE

//BEGIN NEW ALL CASES PAGE
//BEGIN CASES PAGE CODE

add_shortcode('dispallcases', 'dispCases');

function dispCases($values,$options) {

	$values = array();
	
	global $current_user;
	get_currentuserinfo();
	$guid = get_user_meta($current_user->ID, 'description', true);
	$values['guid'] = $guid;
	
	
	$options = get_option("leads");
	$adapter = AdapterFactory::CreateAdapter();
	
	//$adapter->setEmail($options['email']);
	//$adapter->setPassword($options['password']);
	$adapter->doAuth();
	$accountsArray = $adapter->getallCase($values,$options['orgurl']);

?>			
			<style type="text/css">
			table.gridtable { background:#f7f7f7;
			 border:1px solid gray;
			 border-collapse:collapse;
			 color:#fff;
			 font:normal 12px verdana, arial, helvetica, sans-serif;
			}
			table.gridtable caption { border:1px solid #575757;
			 color:#575757;
			 font-weight:bold;
			 letter-spacing:20px;
			 padding:6px 4px 8px 0px;
			 text-align:center;
			 text-transform:uppercase;
			}
			table.gridtable td, th { color:#363636;
			 padding:.4em;
			}
			table.gridtable tr { border:1px dotted gray;
			}
			table.gridtable thead th, tfoot th { background:#575757;
			 color:#FFFFFF;
			 padding:3px 10px 3px 10px;
			 text-align:left;
			 text-transform:uppercase;
			}
			table.gridtable tbody td a { color:#363636;
			 text-decoration:none;
			}
			table.gridtable tbody td a:visited { color:gray;
			 text-decoration:line-through;
			}
			table.gridtable tbody td a:hover { text-decoration:underline;
			}
			table.gridtable tbody th a { color:#363636;
			 font-weight:normal;
			 text-decoration:none;
			}
			table.gridtable tbody th a:hover { color:#363636;
			}
			table.gridtable tbody td+td+td+td a { background-image:url('bullet_blue.png');
			 background-position:left center;
			 background-repeat:no-repeat;
			 color:#707070;
			 padding-left:15px;
			}
			table.gridtable tbody td+td+td+td a:visited { background-image:url('bullet_white.png');
			 background-position:left center;
			 background-repeat:no-repeat;
			}
			table.gridtable tbody th, tbody td { text-align:left;
			 vertical-align:top;
			}
			table.gridtable tfoot td { background:#575757;
			 color:#FFFFFF;
			 padding-top:3px;
			}
			table.gridtable .odd { background:#fff;
			}
			table.gridtable tbody tr:hover { background:#c0c0c0;
			 border:1px solid #707070;
			 color:#000000;
			}
			</style>
			<table border="5" cellpadding="5" cellspacing="5" width="100%" class="gridtable" >
				<tr><td><b>Title</b></td><td><b>Case Number</b></td><td><b>Status</b></td></tr>
<?php		foreach($accountsArray as $account){	?>
				<tr onclick="location.href='/support/case-details/?myvar=<?php echo $account->accountId?>'"><td><?php echo $account->name?></td><td><?php echo $account->address?></td><td><?php echo $account->telephone?></td></tr>							
<?php		}		?>			
			</table>

<?php	




	
}



//END NEW ALL CASES PAGE

//BEGIN CASE DETAILS PAGE
add_shortcode('casedetails', 'dispCaseDetails');

function dispCaseDetails($values,$options) {

	$idval = $_GET['myvar'];
	//echo $idval;

	$values = array();
	
	//global $current_user;
	//get_currentuserinfo();
	//$guid = get_user_meta($current_user->ID, 'aim', true);
	$values['guid'] = $idval;
	
	
	$options = get_option("leads");
	$adapter = AdapterFactory::CreateAdapter();
	
	//$adapter->setEmail($options['email']);
	//$adapter->setPassword($options['password']);
	$adapter->doAuth();
	$caseArray = $adapter->getCaseDetails($values,$options['orgurl']);
	//echo $caseArray;

?>			
			<style type="text/css">
			table.gridtable { background:#f7f7f7;
			 border:1px solid gray;
			 border-collapse:collapse;
			 color:#fff;
			 font:normal 12px verdana, arial, helvetica, sans-serif;
			}
			table.gridtable caption { border:1px solid #575757;
			 color:#575757;
			 font-weight:bold;
			 letter-spacing:20px;
			 padding:6px 4px 8px 0px;
			 text-align:center;
			 text-transform:uppercase;
			}
			table.gridtable td, th { color:#363636;
			 padding:.4em;
			}
			table.gridtable tr { border:1px dotted gray;
			}
			table.gridtable thead th, tfoot th { background:#575757;
			 color:#FFFFFF;
			 padding:3px 10px 3px 10px;
			 text-align:left;
			 text-transform:uppercase;
			}
			table.gridtable tbody td a { color:#363636;
			 text-decoration:none;
			}
			table.gridtable tbody td a:visited { color:gray;
			 text-decoration:line-through;
			}
			table.gridtable tbody td a:hover { text-decoration:underline;
			}
			table.gridtable tbody th a { color:#363636;
			 font-weight:normal;
			 text-decoration:none;
			}
			table.gridtable tbody th a:hover { color:#363636;
			}
			table.gridtable tbody td+td+td+td a { background-image:url('bullet_blue.png');
			 background-position:left center;
			 background-repeat:no-repeat;
			 color:#707070;
			 padding-left:15px;
			}
			table.gridtable tbody td+td+td+td a:visited { background-image:url('bullet_white.png');
			 background-position:left center;
			 background-repeat:no-repeat;
			}
			table.gridtable tbody th, tbody td { text-align:left;
			 vertical-align:top;
			}
			table.gridtable tfoot td { background:#575757;
			 color:#FFFFFF;
			 padding-top:3px;
			}
			table.gridtable .odd { background:#fff;
			}
			table.gridtable tbody tr:hover { background:#c0c0c0;
			 border:1px solid #707070;
			 color:#000000;
			}
			</style>
			<table border="5" cellpadding="5" cellspacing="5" width="100%" class="gridtable" >
				<tr><td><b>Subject</b></td><td><b>Note</b></td></tr>
<?php		foreach($caseArray as $cases){	?>
				<tr><td><?php echo $cases->subject?></td><td><?php echo $cases->notetext?></td></tr>							
<?php		}		?>			
			</table>
			</br>

<?php	


	$options = get_option("leads");

	$content = "";
	if (isset($_POST['casesubmit'])) {
		$error = false;
		$values = array();
		/*
		$firstname = $_POST['firstname'];
		if(empty($firstname)){
			$e_msg .= '<br/>Please enter a Topic for the case.';
			$error = true;
		}
		
		$values['firstname'] = esc_attr(strip_tags(stripslashes($firstname)));

		$lastname = $_POST['lastname'];
		if(empty($lastname)){
			$e_msg .= '<br/>Please enter a Last Name.';
			$error = true;
		}

		$values['lastname'] = esc_attr(strip_tags(stripslashes($lastname)));
		
		
		$email = $_POST['email'];

		if(!is_email($email)){
			$e_msg .= '<br/>The email address you entered is not a valid email address.';
			$error = true;
		}

		$values['email'] = esc_attr(strip_tags(stripslashes($email)));
		
		
		$phonenumber = $_POST['phonenumber'];
		
		$values['phonenumber'] = esc_attr(strip_tags(stripslashes($phonenumber)));
		*/
		
		$description = $_POST['description'];
		if(empty($description)){
			$e_msg .= '<br/>Please enter a note for the case.';
			$error = true;
		}		
		$values['description'] = esc_attr(strip_tags(stripslashes($description)));
		
		$now = time() - (5 * 60 * 60);
		$today = date('m/d/Y g:i a', $now);
		$topic = 'Note created on '.$today.' by Customer';
		$values['topic'] = $topic;
		
		$idval = $_GET['myvar'];
		$values['guid'] = $idval;
		
		$entityname = 'account';
		$values['entity'] = $entityname;
		
				
		if($error){
			$content .= note_form($values,$e_msg);
		}else{
			if(postto_note_form($values,$options)=="nono"){
				$content .='Unable to create note.';
			}else{
				$content .= 'Note creation has been successful, we will respond to you as soon as possible.';
			}
			
			
		}
		
	}else{
		$content .= note_form($values);
	}
	
	return $content;
}

function note_form($values,$e_msg=""){
	$content .= '<style type="text/css">
		#contactus .dynalabel{width:150px;display:block;}
		#contactus input{display:block;}
		#e_msg{color:#ff0000;}
	</style>';

	$content .= '<div id="e_msg">' . $e_msg . '</div>'; 
	
	$content .= "\n".'<form  id="contactus" method="post">'."\n";
	
	$content .= "\t".'<label for="description" class="dynalabel">Update Case</label>';
	
	$content .= '<textarea id="description" name="description" rows="7" cols="60" value="'.$values['description'].'"></textarea>';

	
	$submit = stripslashes($options['submitbutton']);
	if (empty($submit))
		$submit = "Submit";
	$content .= "\t".'<br><input type="submit" name="casesubmit"  value="Create Note "/>';
	$content .= '</form>'."\n";

	$content .= '<p></p>';

	return $content;
}

function postto_note_form($values,$options) {
	$adapter = AdapterFactory::CreateAdapter();
	
	//$adapter->setEmail($options['email']);
	//$adapter->setPassword($options['password']);
	$adapter->doAuth();
	return $adapter->createNote($values,$options['orgurl']);
	
}


//END SUBFORM FOR NEW ANNOTATION






//END CASE DETAILS PAGE




// Draw the menu page itself
function dynamicscrm_options_do_page() {
	?>
	<div class="wrap">
		<h2>CRM Portal for Microsoft Dynamics CRM 2011 </h2>
		<h3>Configuration Settings</h3>
		<form method="post" action="options.php">
			<?php settings_fields('leads2dynamicscrmoptions_init_options'); ?>
			<?php $options = get_option('leads'); ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Email/User Id</th>
					<td><input type="text" name="leads[email]" style="width:200px;" value="<?php echo $options['email']; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Password</th>
					<td><input type="password" name="leads[password]" style="width:200px;" /></td>
				</tr>
				<tr valign="top"><th scope="row">Organization url</th>
					<td><input type="text" name="leads[orgurl]" style="width:600px;" value="<?php echo $options['orgurl']; ?>" />
					<br>Ex: https://yourCRMOnlineInstance.crm.dynamics.com/XRMServices/2011/Organization.svc</td>
				</tr>
				<tr>
				<th colspan="2">
				</th>	
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function leads2dynamicscrmoptions_validate($input) {
	// Our first value is either 0 or 1
	$input['option1'] = ( $input['option1'] == 1 ? 1 : 0 );
	
	// Say our second option must be safe text with no HTML tags
	$input['sometext'] =  wp_filter_nohtml_kses($input['sometext']);
	
	return $input;
}

?>
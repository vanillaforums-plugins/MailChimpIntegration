<?php if (!defined('APPLICATION')) exit();

$PluginInfo['MailChimpIntegration'] = array(
	'Name' => 'Mail Chimp Integration',
	'Description' => 'Mail Chimp Integration Plugin. Autosubscribe new users.',
	'Version' => '0.2',
	'RequiredApplications' => array('Vanilla' => '2.0'),
	'RequiredTheme' => FALSE,
	'RequiredPlugins' => FALSE,
	'SettingsUrl' => 'settings/mailchimp',
	'SettingsPermission' => 'Garden.Settings.Manage',
	'Author' => "Alessandro Miliucci",
	'AuthorEmail' => 'lifeisfoo@gmail.com',
	'AuthorUrl' => 'http://forkwait.net',
	'License' => 'GPL v3'
);

class MailChimpIntegrationPlugin implements Gdn_IPlugin {
	
	public function SettingsController_MailChimp_Create($Sender) {
	        $Sender->Permission('Garden.Plugins.Manage');
		$Sender->AddSideMenu();
		$Sender->Title('MailChimp Integration');
		$ConfigurationModule = new ConfigurationModule($Sender);
		$ConfigurationModule->RenderAll = True;
		$Schema = array( 'Plugins.MailChimpIntegration.APIKey' => 
				 array('LabelCode' => 'API Key', 
				       'Control' => 'TextBox', 
				       'Default' => C('Plugins.MailChimpIntegration.APIKey', '')
				       ),
			 	'Plugins.MailChimpIntegration.ListID' => 
				 array('LabelCode' => 'List ID', 
				       'Control' => 'TextBox', 
				       'Default' => C('Plugins.MailChimpIntegration.ListID', '')
				       )
		);
		$ConfigurationModule->Schema($Schema);
		$ConfigurationModule->Initialize();
		$Sender->View = dirname(__FILE__) . DS . 'views' . DS . 'mchimpsettings.php';
		$Sender->ConfigurationModule = $ConfigurationModule;
		$Sender->Render();
	}
	
	public function PluginController_MailChimp_Create($Sender) {
	  $OptIn = (strcmp($Sender->Request->Post("OptIn", "0"), "1") == 0 ? TRUE : FALSE);
	  $Sender->Permission('Garden.Plugins.Manage');
	  $Action = ArrayValue('0', $Sender->RequestArgs, 'default');
	  switch($Action){
	    case "bulkSubscribe":
	      self::bulk($OptIn);
	      echo T("<b>Please go back with your browser.</b>");
	      break;
	  }
	}

	private function bulk($OptIn = TRUE) {//prevent spammy bug
	  $EmailToSub = array();
	  $Sender->UserData = Gdn::SQL()->Select('User.Email')->From('User')->OrderBy('User.Name')->Where('Deleted',false)->Get();
	  foreach ($Sender->UserData->Result() as $User) {
	    $EmailToSub[] = array('EMAIL'=>$User->Email);
	  }

	  include_once(dirname(__FILE__) . DS . 'MCAPI' . DS .'MCAPI.class.php');

	  $ApiKey = C('Plugins.MailChimpIntegration.APIKey', '');
	  $ListID = C('Plugins.MailChimpIntegration.ListID', '');
	  /*PHP < 5.5 compatibility http://php.net/manual/en/function.empty.php*/
	  $ApiTrim = trim($ApiKey);
	  if( empty($ApiTrim) ){
	    //TODO:return an error
	  }
	  /* * */
	  $Api = new MCAPI($ApiTrim);

	  $optin = $OptIn; //send/don't send optin emails
	  $up_exist = true; // yes, update currently subscribed users
	  $replace_int = false; // no, add interest, don't replace

	  $RetVals = $Api->listBatchSubscribe($ListID, $EmailToSub, $optin, $up_exist, $replace_int);

	  if ($Api->errorCode){
	    echo "Batch Subscribe failed!\n";
	    echo "code:".$Api->errorCode."\n";
	    echo "msg :".$Api->errorMessage."\n";
	  } else {
	    echo "added:   ".$RetVals['add_count']."\n";
	    echo "updated: ".$RetVals['update_count']."\n";
	    echo "errors:  ".$RetVals['error_count']."\n";
	    foreach($RetVals['errors'] as $val){
	      echo $val['email_address']. " failed\n";
	      echo "code:".$val['code']."\n";
	      echo "msg :".$val['message']."\n";
	    }
	  }
	}
	
	public function EntryController_RegistrationSuccessful_Handler($Sender){
	  include_once(dirname(__FILE__) . DS . 'MCAPI' . DS .'MCAPI.class.php');

	  $EmailAddress = GetValue("Email", $Sender->Form->FormValues());
	  $ApiKey = C('Plugins.MailChimpIntegration.APIKey', '');
	  $ListID = C('Plugins.MailChimpIntegration.ListID', '');
	  /*PHP < 5.5 compatibility http://php.net/manual/en/function.empty.php*/
	  $ApiTrim = trim($ApiKey);
	  if( empty($ApiTrim) ){
	    //TODO:return an error
	  }
	  /* * */

	  $Api = new MCAPI($ApiTrim);

	  // By default this sends a confirmation email - you will not see new members        
	  // until the link contained in it is clicked!                                      
	  $Retval = $Api->listSubscribe( $ListID, $EmailAddress);

	  if ($Api->errorCode){
	    $Sender->InformMessage(T('Subscription to our newsletter failed. Please try manually.') . T('ECODE=[') . $Api->errorCode . T('] EMSG=') . $Api->errorMessage);
	  } else {
	    $Sender->InformMessage(T('Please check our newsletter subscription confirmation email.'));
	  }	  
	}
	
	public function Setup() {}
}
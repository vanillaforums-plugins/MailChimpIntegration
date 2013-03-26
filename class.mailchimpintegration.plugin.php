<?php if (!defined('APPLICATION')) exit();

$PluginInfo['MailChimpIntegration'] = array(
	'Name' => 'Mail Chimp Integration',
	'Description' => 'Mail Chimp Integration Plugin. Autosubscribe new users.',
	'Version' => '0.1',
	'RequiredApplications' => array('Vanilla' => '2.1a1'),
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
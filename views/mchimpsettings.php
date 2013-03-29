<?php if (!defined('APPLICATION')) die(); ?>

<?php $this->ConfigurationModule->Render(); ?>

<h4><?php echo T("Bulk subscribe"); ?></h4>
<?php
echo $this->Form->Open(array('action' => Url('plugin/mailchimp/bulkSubscribe')));
echo $this->Form->Errors();
echo "<p>" . T('This will subscribe all users. If you have many users this will take some time.') . "</p>";
echo "<p>" . T('Existing users will be updated (actually nothing can be modified since only email field is sent).') . "</p>";
echo "<p>";
echo $this->Form->CheckBox('OptIn', T('Send opt-in email?'), array('checked' => 'checked'));
echo "</p><p>";
echo $this->Form->Button(T('Bulk subscribe'));
echo "</p>";
echo $this->Form->Close();
?>

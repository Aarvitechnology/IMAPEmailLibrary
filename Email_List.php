<?php

class Email_List
{

	// imap server connection
	public $conn;

	// inbox storage and inbox message count
	private $inbox;
	private $attachment_body;
	private $mailboxes;
	private $msg_cnt;


	/* email login credentials */
	// Replace <yourserver> with your server name
	private $server   = '{<yourserver>:143/novalidate-cert}';

	// Replace <email@yourserver.com> with your user email address
	private $user   = '<email@yourserver.com>';

	// Replace <Your User Email Password> with your user email address's password
	private $pass   = '<Your User Email Password>';

	private $port   = 143; // adjust according to server settings

	/* email login credentials */
	// connect to the server and get the inbox emails
	function __construct()
	{
		$this->connect();
		// $this->inbox();

	}

	// close the server connection
	function close()
	{
		$this->inbox = array();
		$this->msg_cnt = 0;

		imap_close($this->conn);
	}

	// open the server connection
	// the imap_open function parameters will need to be changed for the particular server
	// these are laid out to connect to a Dreamhost IMAP server
	function connect()
	{
		// $this->conn = imap_open('{'.$this->server.'/INBOX}', $this->user, $this->pass)or die('Cannot connect to Mail: ' . imap_last_error());
		$this->conn = imap_open($this->server, $this->user, $this->pass) or die('Cannot connect to Mail: ' . imap_last_error());
	}

	// move the message to a new folder
	function move($msg_index, $folder = 'Sent Mail.Processed')
	{
		// move on server
		imap_mail_move($this->conn, $msg_index, $folder);
		imap_expunge($this->conn);

		// re-read the inbox
		$this->inbox();
	}

	// get a specific message (1 = first email, 2 = second email, etc.)
	function get($msg_index = NULL)
	{
		if (count($this->inbox) <= 0) {
			return array();
		} elseif (!is_null($msg_index) && isset($this->inbox[$msg_index])) {
			return $this->inbox[$msg_index];
		}

		return $this->inbox[0];
	}

	// read the inbox
	function inbox()
	{
		$this->msg_cnt = imap_num_msg($this->conn);
		// echo 'Message Count'.$this->msg_cnt;
		$in = array();
		for ($i = 1; $i <= $this->msg_cnt; $i++) {
			$in[] = array(
				'index'     => $i,
				'header'    => imap_headerinfo($this->conn, $i),
				'body'      => imap_body($this->conn, $i),
				'structure' => imap_fetchstructure($this->conn, $i)
			);
		}

		$this->inbox = $in;
		return $this->inbox;
	}

	// fetch attachement
	function getAttachement($msg_index = NULL, $index)
	{

		$attachment_body = imap_fetchbody($this->conn, $msg_index, $index + 1);
		$this->attachment_body = $attachment_body;
		return $this->attachment_body;
	}

	// To get a list of mailboxes
	function getMailboxes()
	{
		$this->mailboxes = imap_list($this->conn, $this->server, '*');
		return $this->mailboxes;
	}

	//GET THE STATUS OF INBOX/RECEIVE MAILS
	function getMailboxStatus()
	{
		$status = imap_status($this->conn, "{webmail.aarvitechno.com:143/novalidate-cert}", SA_ALL);
		if ($status) {
			// SA_MESSAGES  : the number of messages in the mailbox
			echo "Messages:   " . $status->messages    . "<br />\n";
			// FOR UNREAD MESSAGES COUNT -> SA_RECENT  : number of recent messages in the mailbox
			echo "Recent:     " . $status->recent      . "<br />\n";
			// SA_UNSEEN : number of unseen (new) messages in the mailbox (NOT WORKING PROPERLY)
			echo "Unseen:     " . $status->unseen      . "<br />\n";
			// SA_UIDNEXT  : next uid to be used in the mailbox
			echo "UIDnext:    " . $status->uidnext     . "<br />\n";
			// SA_UIDVALIDITY  : a constant that changes when uids for the mailbox may no longer be valid
			echo "UIDvalidity:" . $status->uidvalidity . "<br />\n";
			// SA_ALL - set all of the above
		} else {
			echo "imap_status failed: " . imap_last_error() . "\n";
		}
	}

	/* START ==> COMPOSE MAIL*/

	function ComposeMail()
	{
		$envelope = $this->mail_envelope;
		$body = $this->mail_body;
		imap_mail_compose($envelope, $body) or die("imap_mail_compose on new mail failed: " . imap_last_error() . "<br />\n");
	}


	function build_mime_msg($to = 'panchalkrishna76@gmail.com', $subject = "My Subject", $body = "Hello MEssage Body", $from = "no-reply@aarvitechno.com")
	{
		$headers = array('from' => $from, 'to' => $to, 'subject' => $subject, 'date' => date('r'));
		$body = array(1 => array('type' => TYPETEXT, 'subtype' => 'plain', 'contents.data' => $body));
		var_dump($headers);
		var_dump($body);
		return imap_mail_compose($headers, $body) or die("imap_mail_compose on new mail failed: " . imap_last_error() . "<br />\n");
	}

	/* END ==> COMPOSE MAIL*/

	/* 	START ==> MAILBOX OPRATIONS : CREATE-RENAME-DELETE*/

	function CreatMailBox()
	{

		// $name1 = "phpnewbox";
		// $name2 = imap_utf7_encode("phpnewböx"); // phpnewb&w7Y-x

		$newname = $this->new_mailbox_name;

		echo "Newname will be '$newname'<br />\n" . $newname;
		try {
			//code...
			if ($newname != '')
				imap_createmailbox($this->conn, imap_utf7_encode("{webmail.aarvitechno.com:/novalidate-cert}INBOX/$newname")) or die("could not create new mailbox: " . implode("<br />\n", imap_errors()) . "<br />\n");
		} catch (\Throwable $th) {
			//throw $th;
			echo "could not create new mailbox: " . implode("<br />\n", imap_errors()) . "<br />\n";
		}

		// if (imap_createmailbox($this->conn, imap_utf7_encode("{webmail.aarvitechno.com:143/novalidate-cert}INBOX.$newname"))) {
		// 	$status = @imap_status($this->conn, "{webmail.aarvitechno.com:143/novalidate-cert}INBOX.$newname", SA_ALL);
		// 	if ($status) {
		// 		echo "your new mailbox '$name1' has the following status:<br />\n";
		// 		echo "Messages:   " . $status->messages    . "<br />\n";
		// 		echo "Recent:     " . $status->recent      . "<br />\n";
		// 		echo "Unseen:     " . $status->unseen      . "<br />\n";
		// 		echo "UIDnext:    " . $status->uidnext     . "<br />\n";
		// 		echo "UIDvalidity:" . $status->uidvalidity . "<br />\n";

		// 		// if (imap_renamemailbox($this->conn, "{webmail.aarvitechno.com:143/novalidate-cert}INBOX.$newname", "{webmail.aarvitechno.com:143/novalidate-cert}INBOX.$name2")) {
		// 		// 	echo "renamed new mailbox from '$name1' to '$name2'<br />\n";
		// 		// 	$newname = $name2;
		// 		// } else {
		// 		// 	echo "imap_renamemailbox on new mailbox failed: " . imap_last_error() . "<br />\n";
		// 		// }
		// } else {
		// 	echo "could not create new mailbox: " . implode("<br />\n", imap_errors()) . "<br />\n";
		// }
	}

	function RenameMailBox()
	{
		$newname = $this->new_mailbox_name;
		$oldname = $this->old_mailbox_name;
		imap_renamemailbox($this->conn, "{webmail.aarvitechno.com:143/novalidate-cert}$oldname", "{webmail.aarvitechno.com:143/novalidate-cert}$newname") or die("imap_renamemailbox on new mailbox failed: " . imap_last_error() . "<br />\n");
	}

	function DeleteMailBox()
	{

		$name = $this->mailbox_name;
		imap_deletemailbox($this->conn, "{webmail.aarvitechno.com:143/novalidate-cert}$name") or die("imap_deletemailbox on delete mailbox failed: " . imap_last_error() . "<br />\n");
	}

	/* END ==>	MAILBOX OPRATIONS : CREATE-RENAME-DELETE*/

	/* 
	
	ISSUE : https://www.php.net/manual/en/function.imap-status.php
	
	I had issues with imap_status not working correctly, while other imap functions seemed okay. I always got the ['flags']=0 response. No notes here and Google searching suggests it doesn't working properly with Exchange, so I wrote a little workaround, to at least get some information.
	*/
	function my_imap_status($stream, $mailbox = '', $info = SA_ALL)
	{
		// get current mailbox name (and info)
		$curr_obj = imap_check($stream);
		if (!$curr_obj) return false;

		// if request if for current mailbox then just return it
		if ((empty($mailbox)) || ($mailbox == $curr_obj->Mailbox)) return $curr_obj;

		// get current mailbox
		$current_mailbox = $curr_obj->Mailbox;

		//switch to new mailbox
		if (!imap_reopen($stream, $mailbox)) return false;

		// get info
		$obj = imap_check($stream);

		// switch back to original mailbox
		imap_reopen($stream, $current_mailbox);

		//return info
		return $obj;
	}
	/* ISSUE : https://www.php.net/manual/en/function.imap-status.php*/
}

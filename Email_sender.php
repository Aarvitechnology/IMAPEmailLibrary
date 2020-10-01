<?php

class Email_sender
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
	private $server   = '{<yourserver>:143/novalidate-cert}Sent Items';

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
		$this->inbox();
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
				'structure' => imap_fetchstructure($this->conn, $i),
				'header_only'    => imap_fetchheader($this->conn, $i),
				'header_rfc822'    => imap_rfc822_parse_headers($this->conn, $i)
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

	//get the status
	function getMailboxStatus()
	{
		$status = imap_status($this->conn, "{webmail.aarvitechno.com:143/novalidate-cert}", SA_ALL);
		if ($status) {
			echo "Messages:   " . $status->messages    . "<br />\n";
			echo "Recent:     " . $status->recent      . "<br />\n";
			echo "Unseen:     " . $status->unseen      . "<br />\n";
			echo "UIDnext:    " . $status->uidnext     . "<br />\n";
			echo "UIDvalidity:" . $status->uidvalidity . "<br />\n";
		} else {
			echo "imap_status failed: " . imap_last_error() . "\n";
		}
	}
}

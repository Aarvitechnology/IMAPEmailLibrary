<?php
/*
LIKE SENT BOX 
*/
// require_once('Email_sender.php');
require_once('Email_List.php');

// $EmailSenderObj = new Email_sender;
$Email_ListObj = new Email_List;
echo "<pre>";
// print_r($EmailSenderObj);

/* GET ALL MAILBOX / FOLDER LIST */

$mailboxes = $Email_ListObj->getMailboxes();
print_r($mailboxes);

/* GET ALL MAILBOX / FOLDER LIST */

/* GET STATUS OF ALL MAILBOX / FOLDER */

/*
CAN'T GET ANY COUNT IN UNSEEN MESSAGES.
FOR UNREAD MESSAGES COUNT THERE IS RECENT FLAG
*/
$mailboxes_status = $Email_ListObj->getMailboxStatus();
print_r($mailboxes_status);

/* GET STATUS OF ALL MAILBOX / FOLDER */

/* CREATE A NEW MAILBOX FOLDER */

$Email_ListObj->new_mailbox_name = 'phpnewbox1';
// $mailboxes_create_status = $Email_ListObj->CreatMailBox();
// print_r($mailboxes_create_status);

/* CREATE A NEW MAILBOX FOLDER */

/* RENAME EXSISTING MAILBOX FOLDER */

$Email_ListObj->old_mailbox_name = 'INBOX/phpnewbox1';
$Email_ListObj->new_mailbox_name = 'INBOX/phpnewbox_rename';
$mailboxes_create_status = $Email_ListObj->RenameMailBox();
// print_r($mailboxes_create_status);

/* RENAME EXSISTING MAILBOX FOLDER */
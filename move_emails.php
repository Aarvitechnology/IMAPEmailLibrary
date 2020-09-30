<?php
require_once('Email_reader.php');
$EmailReaderObj = new Email_reader;

// $mails = $EmailReaderObj->move(4,'Drafts');
/*  TO MOVE THE EMAILS BY INDEX , GIVE MAILBOX NAME FROM MAILBOX FOLDER LIST 
    IF YOU CAN'T GIVE VALID NAME THE EMAIUL WILL BE GONE WHOOP! SO CAREFULLY GIVE FOLDER NAME BEFORE MOVE A MAIL 
*/
$mails = $EmailReaderObj->move(4,'MYInbox');

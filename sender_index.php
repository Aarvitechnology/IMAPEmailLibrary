<?php
/*
LIKE SENT BOX 
*/
require_once('Email_sender.php');
require_once('Email_List.php');

$EmailSenderObj = new Email_sender;
$Email_ListObj = new Email_List;
echo "<pre>";
// print_r($EmailSenderObj);
$mails = $EmailSenderObj->inbox();
$mailboxes = $Email_ListObj->getMailboxes();
print_r($mailboxes);
$mailboxes_status = $EmailSenderObj->getMailboxStatus();
print_r($mailboxes_status);
print_r($mails);

die;
$arr_mail_response = array();
$mail_response_count = 0;
foreach ($mails as $mail_key => $mail_details) {

    /* Sender Details */
    $sender_mail = $mail_details['header']->from[0]->mailbox . $mail_details['header']->from[0]->host;
    /* Sender Details */

    $message_number = $mail_details['header']->Msgno;

    // $hdr_raw = imap_fetchheader($mbox, $mailid);
    // $hdr = imap_rfc822_parse_headers($hdr_raw);
    
    /* Attachement Details */
    $attachments = array();
    if (isset($mail_details['structure']->parts) && count($mail_details['structure']->parts)) {

        for ($i = 0; $i < count($mail_details['structure']->parts); $i++) {

            // $attachments[$i] = array(
            //     'is_attachment' => false,
            //     'filename' => '',
            //     'name' => '',
            //     'attachment' => ''
            // );

            if ($mail_details['structure']->parts[$i]->ifdparameters) {
                foreach ($mail_details['structure']->parts[$i]->dparameters as $object) {
                    if (strtolower($object->attribute) == 'filename') {
                        // echo "filename";
                        // echo "<br/>";
                        if ($object->value != '') {
                            // echo "object->value";
                            // echo "<br/>";
                            // echo $object->value;
                            // echo "<br/>";
                            // echo $i;
                            // echo "<br/>";
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
            }

            if ($mail_details['structure']->parts[$i]->ifparameters) {
                foreach ($mail_details['structure']->parts[$i]->parameters as $object) {
                    if (strtolower($object->attribute) == 'name') {
                        // echo "name";
                        // echo "<br/>";
                        // echo 'i';
                        // echo "<br/>";
                        // echo $i;
                        // echo "<br/>";
                        if ($object->value != '') {
                            // echo "object->value";
                            // echo "<br/>";
                            // echo $object->value;
                            // echo "<br/>";
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['name'] = $object->value;
                        }
                    }
                }
            }
            // echo $i;
            //     echo "<br/>";
            if ($attachments[$i]['is_attachment']) {
                // echo "is_attachment";
                // echo "<br/>";
                // echo $i;
                // echo "<br/>";
                // $attachments[$i]['attachment'] = imap_fetchbody($connection, $message_number, $i + 1);
                $attachment = $EmailSenderObj->getAttachement($message_number, $i);
                $attachments[$i]['attachment'] = $attachment;
                $attachments[$i]['encoding'] = $mail_details['structure']->parts[$i]->encoding;
                // print_r($attachment);
                // echo "<br/>";
                if ($mail_details['structure']->parts[$i]->encoding == 3) { // 3 = BASE64
                    $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                } elseif ($mail_details['structure']->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
                    $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                }
            }
        }
    }
    // print_r($attachments);
    // die;
    /* Attachement Details */

    /* Create Custom Response Array From Mailer */

    $arr_mail_response[$mail_response_count]['index'] = $mail_details['index'];

    $arr_mail_response[$mail_response_count]['sender_name'] = $mail_details['header']->fromaddress;
    $arr_mail_response[$mail_response_count]['to_name'] = $mail_details['header']->toaddress;
    $arr_mail_response[$mail_response_count]['ccaddress'] = $mail_details['header']->ccaddress;
    $arr_mail_response[$mail_response_count]['reply_toaddress'] = $mail_details['header']->reply_toaddress;

    $arr_mail_response[$mail_response_count]['sender_email'] = $sender_mail;
    $arr_mail_response[$mail_response_count]['subject'] = $mail_details['header']->Subject;
    // $arr_mail_response[$mail_response_count]['message'] = $mail_details['body'];
    $arr_mail_response[$mail_response_count]['datetime'] = $mail_details['header']->Date;

    // $arr_mail_response[$mail_response_count]['message'] = $mail_details['body'];

    // $arr_mail_response[$mail_response_count]['attachment'] = $attachments;

    $mail_response_count++;

    /* Create Custom Response Array From Mailer */
}
print_r($arr_mail_response);

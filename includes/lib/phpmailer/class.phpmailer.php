<?php


class PHPMailer {

  /////////////////////////////////////////////////
  // PROPERTIES, PUBLIC
  /////////////////////////////////////////////////

  /**
   * Email priority (1 = High, 3 = Normal, 5 = low).
   * @var int
   */
  var $Priority          = 3;

  /**
   * Sets the CharSet of the message.
   * @var string
   */
  var $CharSet           = 'iso-8859-1';

  /**
   * Sets the Content-type of the message.
   * @var string
   */
  var $ContentType        = 'text/plain';

  /**
   * Sets the Encoding of the message. Options for this are "8bit",
   * "7bit", "binary", "base64", and "quoted-printable".
   * @var string
   */
  var $Encoding          = '8bit';

  /**
   * Holds the most recent mailer error message.
   * @var string
   */
  var $ErrorInfo         = '';

  /**
   * Sets the From email address for the message.
   * @var string
   */
  var $From              = 'root@localhost';

  /**
   * Sets the From name of the message.
   * @var string
   */
  var $FromName          = 'Root User';

  /**
   * Sets the Sender email (Return-Path) of the message.  If not empty,
   * will be sent via -f to sendmail or as 'MAIL FROM' in smtp mode.
   * @var string
   */
  var $Sender            = '';

  /**
   * Sets the Subject of the message.
   * @var string
   */
  var $Subject           = '';

  /**
   * Sets the Body of the message.  This can be either an HTML or text body.
   * If HTML then run IsHTML(true).
   * @var string
   */
  var $Body              = '';

  /**
   * Sets the text-only body of the message.  This automatically sets the
   * email to multipart/alternative.  This body can be read by mail
   * clients that do not have HTML email capability such as mutt. Clients
   * that can read HTML will view the normal Body.
   * @var string
   */
  var $AltBody           = '';

  /**
   * Sets word wrapping on the body of the message to a given number of
   * characters.
   * @var int
   */
  var $WordWrap          = 0;

  /**
   * Method to send mail: ("mail", "sendmail", or "smtp").
   * @var string
   */
  var $Mailer            = 'mail';

  /**
   * Sets the path of the sendmail program.
   * @var string
   */
  var $Sendmail          = '/usr/sbin/sendmail';

  /**
   * Path to PHPMailer plugins.  This is now only useful if the SMTP class
   * is in a different directory than the PHP include path.
   * @var string
   */
  var $PluginDir         = '';

  /**
   * Holds PHPMailer version.
   * @var string
   */
  var $Version           = "2.0.2";

  /**
   * Sets the email address that a reading confirmation will be sent.
   * @var string
   */
  var $ConfirmReadingTo  = '';

  /**
   * Sets the hostname to use in Message-Id and Received headers
   * and as default HELO string. If empty, the value returned
   * by SERVER_NAME is used or 'localhost.localdomain'.
   * @var string
   */
  var $Hostname          = '';

  /**
   * Sets the message ID to be used in the Message-Id header.
   * If empty, a unique id will be generated.
   * @var string
   */
  var $MessageID         = '';

  /////////////////////////////////////////////////
  // PROPERTIES FOR SMTP
  /////////////////////////////////////////////////

  /**
   * Sets the SMTP hosts.  All hosts must be separated by a
   * semicolon.  You can also specify a different port
   * for each host by using this format: [hostname:port]
   * (e.g. "smtp1.example.com:25;smtp2.example.com").
   * Hosts will be tried in order.
   * @var string
   */
  var $Host        = 'localhost';

  /**
   * Sets the default SMTP server port.
   * @var int
   */
  var $Port        = 25;

  /**
   * Sets the SMTP HELO of the message (Default is $Hostname).
   * @var string
   */
  var $Helo        = '';

  /**
   * Sets connection prefix.
   * Options are "", "ssl" or "tls"
   * @var string
   */
  var $SMTPSecure = "";

  /**
   * Sets SMTP authentication. Utilizes the Username and Password variables.
   * @var bool
   */
  var $SMTPAuth     = false;

  /**
   * Sets SMTP username.
   * @var string
   */
  var $Username     = '';

  /**
   * Sets SMTP password.
   * @var string
   */
  var $Password     = '';

  /**
   * Sets the SMTP server timeout in seconds. This function will not
   * work with the win32 version.
   * @var int
   */
  var $Timeout      = 10;

  /**
   * Sets SMTP class debugging on or off.
   * @var bool
   */
  var $SMTPDebug    = false;

  /**
   * Prevents the SMTP connection from being closed after each mail
   * sending.  If this is set to true then to close the connection
   * requires an explicit call to SmtpClose().
   * @var bool
   */
  var $SMTPKeepAlive = false;

  /**
   * Provides the ability to have the TO field process individual
   * emails, instead of sending to entire TO addresses
   * @var bool
   */
  var $SingleTo = false;

  /////////////////////////////////////////////////
  // PROPERTIES, PRIVATE
  /////////////////////////////////////////////////

  var $smtp            = NULL;
  var $to              = array();
  var $cc              = array();
  var $bcc             = array();
  var $ReplyTo         = array();
  var $attachment      = array();
  var $CustomHeader    = array();
  var $message_type    = '';
  var $boundary        = array();
  var $language        = array();
  var $error_count     = 0;
  var $LE              = "\n";
  var $sign_key_file   = "";
  var $sign_key_pass   = "";


}

?>

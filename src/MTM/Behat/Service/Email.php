<?php
namespace MTM\Behat\Service;

class Email {

  /**
   * @property  string the email box to check.
   */
  protected $mailbox = NULL;
  protected $message_id = 0;

  public function __construct($email_box = NULL) {
    $this->mailbox = $email_box;
  }

  /**
   * Empties the mailbox.
   */
  public function _empty() {
    $fh = @fopen($this->mailbox, 'wb');
    if ($fh) {
      @fwrite($fh, '');
      fclose($fh);
    }
  }

  /**
   * Reads the contents of the next email.
   * @return array  An array with the following keys:
   *   - subject
   *   - to
   *   - from
   *   - date
   */
  public function read() {
    $cmd = "cat {$this->mailbox} | formail +{$this->message_id} -1 -s formail -X From: -X Subject: -X Date: -X To:";
    $lines = array();
    @exec($cmd, $lines);
    if (!empty($lines)) {
      $message = array();
      foreach ($lines as $line) {
        list($key, $val) = explode(':', $line);
        $key = trim(strtolower($key));
        $val = trim($val);
        $message[$key] = $val;
      }
    }
    else {
      return FALSE;
    }
    $this->message_id++;
    return $message;
  }

  public function reset() {
    $this->message_id = 0;
  }

}

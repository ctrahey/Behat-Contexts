<?php
namespace MTM\Behat\Service;

class DevelEmail extends Email {

  protected $handle = NULL;

  public function __destruct() {
    if ($this->handle) {
      @closedir($this->handle);
    }
  }

  /**
   * Empties the mailbox.
   */
  public function _empty() {
    array_map('unlink', glob($this->mailbox . '/*.txt'));
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
    if (!$this->handle) {
      $this->handle = @opendir($this->mailbox);
    }

    if (!$this->handle) {
      return FALSE;
    }

    $filename = @readdir($this->handle);
    if ($filename === '.') {
      @readdir($this->handle); // Skip . and ..
      $filename = @readdir($this->handle);
    }

    if (FALSE === $filename) {
      return FALSE;
    }

    $lines = file($this->mailbox . '/' . $filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!empty($lines)) {
      $message = array();
      $has_subject = FALSE;
      foreach ($lines as $line) {
        if (preg_match('/^([A-Za-z\-]+): .*$', $line)) {
          list($key, $val) = explode(':', $line);
          $key = trim(strtolower($key));
          $val = trim($val);
          $message[$key] = $val;
        }
        elseif (!$has_subject) {
          $message['subject'] = $line;
          $has_subject = TRUE;
          break;
        }
      }
    }
    else {
      return FALSE;
    }
    $this->message_id++;
    return $message;
  }

}

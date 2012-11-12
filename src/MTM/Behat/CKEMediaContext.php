<?php
namespace MTM\Behat;
use Behat\Behat\Context\Step\Given;
use Behat\Behat\Context\Step\When;
use Behat\Behat\Context\Step\Then;

/**
 * CKEMedia context.
 */
class CKEMediaContext extends SubContext
{
  /**
   * @When /^(?:|I )click on the media button$/
   */
  public function clickMediaButton()
  {
      $this->getSession()->wait(10000, "jQuery('.cke_button_media').length");        
      $this->getSession()->getPage()->clickLink('Add media');
  }
  
  /**
   * @When /^(?:|I )switch to the "([^"]*)" media tab$/
   */
  public function switchToMediaTab($tab) {
    $this->getSession()->switchToIFrame('mediaBrowser');
    $this->getSession()->getPage()->clickLink($tab);
  }

  /**
   * @todo either abstract the file name into parameter, or say "upload the test image" and document the file name
   * @When /^(?:|I )upload the file "test.jpg"$/
   */
  public function uploadInMedia()
  {
      $this->getSession()->switchToIFrame('mediaBrowser');
      $this->getSession()->wait(10000, "jQuery('#edit-upload-upload').length");        
      $this->attachFileToField('edit-upload-upload', 'test.jpg');
      return new When('I press "Submit"');
  }
  
  /**
   * @When /^(?:|I )submit the web upload form$/
   */
  public function SubmitTheWebUploadForm() {
    /* 
      I had to use jQuery to click this, since there are multiple #edit-submits in the
      iframe and the driver doesn't like to click on non-visible elements AND we need to click on the one in our tab! >:C
    */
    $this->getSession()->evaluateScript("jQuery('#media-tab-media_internet #edit-submit').click()");
  }


  /**
   * @Given /^(?:|I )select an image from the library$/
   */
  public function SelectAnImageFromTheLibrary() {
    $this->getSession()->evaluateScript("jQuery('#media-tab-media_default--media_browser_1 .views-row-1 .media-item').click();");
    $this->getSession()->evaluateScript("jQuery('#media-tab-media_default--media_browser_1 .fake-submit').click()");
  }

  /**
   * @When /^(?:|I )crop the image at "(?P<width>[^"]*)"x"(?P<height>[^"]*)"$/
   */
  public function cropInMedia($width,$height)
  {    
      $this->getSession()->wait(30000, "jQuery('#mediaStyleSelector').length");        
      $this->getSession()->switchToIFrame('mediaStyleSelector');
      $this->getSession()->wait(30000, "document.getElementsByClassName('enable-interface').length");        
      $this->getSession()->evaluateScript("jQuery('img.enable-interface').click()");              

      // Alas, crop's JS code won't permit this field to be empty, so 
      // the traditional fillField() method won't work here.  Also,
      // val() requires a manual trigger of change()
      $this->getSession()->evaluateScript("jQuery('#edit-crop-dimensions #edit-crop-width').val(" . $width . ").change()");              
      $this->getSession()->evaluateScript("jQuery('#edit-crop-dimensions #edit-crop-height').val(" . $height . ").change()");              
      $link = $this->getSession()->getPage()->find('css', 'a.fake-ok');
      $link->click();
  }
  
  /**              
   * @Then /^I should see the cropped "(?P<width>[^"]*)"x"(?P<height>[^"]*)" image in the wysiwyg editor$/
   */
  public function assertImageCropped($width,$height)
  {
      $this->getSession()->switchToWindow();
      $this->getSession()->switchToIFrame(0);
      $this->getSession()->wait(500);
      $this->getSession()->wait(30000, "document.getElementsByTagName('img')[0].naturalWidth");        
      assertTrue($this->getSession()->evaluateScript("return document.getElementsByTagName('img')[0].naturalWidth <= " . $width . " && document.getElementsByTagName('img')[0].naturalWidth > 0;"));              
  }
  
  
}
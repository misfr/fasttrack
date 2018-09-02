<?php
/**
 * FastTrack view
 *
 * @author Frederic BAYLE
 */

namespace FastTrack\Views;

use FastTrack\Config;
use FastTrack\Security\CsrfInjection;

/**
 * FastTrack view
 */
class View {
  /**
   * Flag that indicates wether this view is a Master
   *
   * @var bool
   */
  protected $_IsMasterView = false;

  /**
   * Output buffer (used to store the rendering)
   *
   * @var string
   */
  protected $_OutputBuffer = '';

  /**
   * Content blocks
   *
   * @var array
   */
  public $ContentBlocks = [];

  /**
   * Name of the currently captured content block
   *
   * @var string|null
   */
  protected $CurrentContentBlockCapture = null;

  /**
   * Master view (this view inherits from this Master)
   *
   * @var View
   */
  protected $MasterView = null;

  /**
   * Data binded to this view
   *
   * @var array
   */
  public $ViewBag = [];

  /**
   * Class constructor
   *
   * @param   string      $pViewFilePath  Path of the file containing the view content
   * @param   array       $pViewBag       Data to bind to this view
   * @throws \Exception
   */
  public function __construct($pViewFilePath, $pViewBag = []) {
    $ViewFilePath = Config::mapPath($pViewFilePath);

    // Check if the view file exists
    if(!file_exists($ViewFilePath)) {
      throw new \Exception("Unable to find the view file $ViewFilePath.");
    }

    // Initializsations
    $this->ViewBag = $pViewBag;

    // Create aliases of ViewBag content. ex : $ViewBag['MyData'] will become $MyData
    foreach(array_keys($this->ViewBag) as $ViewBagKey) {
      ${$ViewBagKey} = &$this->ViewBag[$ViewBagKey];
    }

    // Capture output to a buffer
    ob_start();
    include $ViewFilePath;
    $this->_OutputBuffer = ob_get_contents();
    ob_end_clean();
  }

  /**
   * Start a content block in a view file
   *
   * @param   string      $pBlockName Name of the block to start to capture
   * @throws  \Exception
   */
  public function startContentBlock($pBlockName) {
    // Check if a block capture isn't in progress
    if($this->CurrentContentBlockCapture !== null) {
      throw new \Exception("Unable to start a new block : capture of a block named {$this->CurrentContentBlockCapture} is in progress.");
    }

    // Start to capture the block
    $this->CurrentContentBlockCapture = $pBlockName;
    ob_start();
  }

  /**
   * Declare a content block in a master view file
   *
   * @param   string  $pBlockName     Name of the block to declare
   */
  public function declareContentBlock($pBlockName) {
    // Register the content block and write a metag tag that will be used to render its content
    $this->_IsMasterView = true;
    echo '${FastTrack.ContentBlock:' . $pBlockName . '}';
  }

  /**
   * End the capture of a content block in a view file
   *
   * @throws \Exception
   */
  public function endContentBlock() {
    // Check if a content block capture is in progress
    if($this->CurrentContentBlockCapture === null) {
      throw new \Exception('No content block capture is in progress');
    }

    // On termine la capture du block
    $this->ContentBlocks[$this->CurrentContentBlockCapture] = ob_get_contents();
    ob_end_clean();
    $this->CurrentContentBlockCapture = null;
  }

  /**
   * Make this view inherits from another one
   *
   * @param string $pMasterViewFilePath
   */
  public function inherits($pMasterViewFilePath) {
    $MasterViewFilePath = Config::mapPath($pMasterViewFilePath);

    // Load the master view
    $this->MasterView = new View($MasterViewFilePath, $this->ViewBag);
  }

  /**
   * Render the view
   */
  public function render() {
    echo $this->renderToString();
  }

  /**
   * Render CSRF token to a string
   *
   * @return string
   */
  public function renderCsrfTokenFieldToString() {
    return '<input type="hidden" name="__csrfToken" value="'. CsrfInjection::generateCsrfToken() . '" />';
  }

  /**
   * Render CSRF token to a string
   *
   * @return string
   */
  public function renderCsrfTokenToString() {
    return CsrfInjection::generateCsrfToken();
  }

  /**
   * Render the view to a string
   *
   * @return string
   */
  public function renderToString() {
    if($this->MasterView !== null) {
      // This view inherits from a master, render master first
      $this->_OutputBuffer = $this->MasterView->renderToString();

      // Then, render the content blocks of this view
      foreach($this->ContentBlocks as $ContentBlockKey => $BlockContent) {
        $this->_OutputBuffer = str_replace('${FastTrack.ContentBlock:' . $ContentBlockKey . '}', $BlockContent, $this->_OutputBuffer);
      }
    }

    if($this->_IsMasterView === false) {
      // This view is not a master, we must clean the content block meta tags without associated content
      $this->_OutputBuffer = preg_replace('/\$\{FastTrack\.ContentBlock:.+?\}/', '', $this->_OutputBuffer);
    }

    return $this->_OutputBuffer;
  }
}

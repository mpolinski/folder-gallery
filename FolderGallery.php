<?php
class FolderGallery {
  private $path = '';
  private $files = [];
  private $configFileName = 'config.json';
  private $templatesFolder = 'templates';

  private $config = [
    'itemAttributes' => [
      'class' => 'gallery__image align-top lazy',
      'alt' => 'content-image', 
      'title' => '', 
      'description' => '', 
      'data-author' => 'Author'
    ],
    'allowegMimeTypes' => ['image/jpeg', 'image/gif', 'image/png']
  ];

  function __construct($path) {
    if (!file_exists($path) || !is_dir($path)) {
      throw new Exception($path . 'is not a valid directory');
    }
    $this->path = $path;
    $this->read();
    $this->updateConfig();
  }

  public static function renderAttributes($attributes) {
    $html = '';
    foreach ($attributes as $key => $value) {
      if (!empty($key) && !empty($value)) {
        $html .= $key . '=' . '"' . $value . '" '; 
      }
    }
    return rtrim($html);
  }

  public static function renderSizesAttributes($imagePath) {
    if (file_exists($imagePath) && $imageSizes = getimagesize($imagePath)) {
      echo 'data-width="' . $imageSizes[0] . '" data-height="' . $imageSizes[1] . '" ';
    }

    echo '';
  }

  public function render($template = 'default') {
    $templateFile = __DIR__ . '/' . $this->templatesFolder . '/' . $template . '.php';
    if (!file_exists($templateFile)) {
      throw new Exception($templateFile . ' does not exist');
    }
    $items       = $this->config['items'];
    $imagePath   = basename($this->path);
    $galleryPath = $this->path;
    include($templateFile);
  }

  private function readConfig() {
    if ($this->isConfigPresent()) {
      return $this->config = json_decode(file_get_contents($this->getConfigFile()), true);
    }

    $this->config['items'] = [];
    return false;
  }

  private function updateConfig() {
    $this->readConfig();
    $updateConfigJson = false;

    foreach ($this->files as $file) {
      if (isset($this->config['items'][$file])) {
        $item = $this->config['items'][$file];
      } else {
        $item = [];
        $updateConfigJson = true;
      }

      foreach ($this->config['itemAttributes'] as $key => $value) {
        if (!isset($this->config['items'][$file][$key])) {
          $item[$key] = $value;
        }
      }
      $this->config['items'][$file] = $item;
    }

    if ($updateConfigJson) {
      try {
        $this->saveConfig();
      } catch (Exception $e) {
        error_log($e->getMessage());
      }
    }
  }

  private function saveConfig() {
    $configFile = $this->getConfigFile();
    if (is_writable(dirname($configFile))) {
      file_put_contents($configFile, json_encode($this->config, JSON_PRETTY_PRINT));
    } else {
      throw new Exception('Cannot write to ' . $configFile);
    }
  }

  private function getConfigFile() {
    return $this->path . '/' . $this->configFileName;
  }

  private function isConfigPresent() {
    return file_exists($this->getConfigFile()) && is_file($this->getConfigFile());
  }

  private function read() {
    $this->files = array_filter(scandir($this->path), [$this, 'isImage']);
  }

  private function isImage($item) {
    if (in_array(mime_content_type($this->path . '/' . $item), $this->config['allowegMimeTypes'])) {
      return true;
    }
    return false;
  }
}

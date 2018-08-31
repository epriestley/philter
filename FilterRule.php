<?php

final class FilterRule {

  private $classes = array();
  private $baseTypes = array();

  private $size;
  private $textColor;
  private $backgroundColor;
  private $borderColor;

  private $quality;
  private $rarity;
  private $socketGroup;
  private $hide;
  private $sockets;
  private $dropLevel;
  private $itemLevel;
  private $preserve;

  private $soundID;
  private $soundVolume;

  public function setSize($size) {
    $this->size = $size;
    return $this;
  }

  public function setTextColor($text_color) {
    $this->textColor = $text_color;
    return $this;
  }

  public function setBackgroundColor($background_color) {
    $this->backgroundColor = $background_color;
    return $this;
  }

  public function setBorderColor($border_color) {
    $this->borderColor = $border_color;
    return $this;
  }

  public function addClass($class) {
    $this->classes[] = $class;
    return $this;
  }

  public function setSound($id, $volume) {
    $this->soundID = $id;
    $this->soundVolume = $volume;
    return $this;
  }

  public function addBaseType($type) {
    $this->baseTypes[] = $type;
    return $this;
  }

  public function setBaseTypes(array $types) {
    $this->baseTypes = $types;
    return $this;
  }

  public function setQuality($quality) {
    $this->quality = $quality;
    return $this;
  }

  public function setRarity($rarity) {
    $this->rarity = $rarity;
    return $this;
  }

  public function setHide($hide, $preserve = false) {
    $this->hide = $hide;
    $this->preserve = $preserve;
    return $this;
  }

  public function setSocketGroup($socket_group) {
    $this->socketGroup = $socket_group;
    return $this;
  }

  public function setSockets($sockets) {
    $this->sockets = $sockets;
    return $this;
  }

  public function setDropLevel($drop_level) {
    $this->dropLevel = $drop_level;
    return $this;
  }

  public function setItemLevel($item_level) {
    $this->itemLevel = (array)$item_level;
    return $this;
  }

  public function renderRule() {
    $lines = array();

    if ($this->hide) {
      $lines[] = 'Hide';

      if (!$this->preserve) {
        $this->size = 18;
        $this->backgroundColor = '0 0 0 128';
        $this->borderColor = '255 255 255 128';
        $this->soundID = null;
        $this->soundVolume = null;
      }

    } else {
      $lines[] = 'Show';
    }

    if ($this->classes) {
      $classes = array();
      foreach ($this->classes as $class) {
        $classes[] = '"'.$class.'"';
      }
      $classes = implode(' ', $classes);
      $lines[] = "    Class {$classes}";
    }

    if ($this->baseTypes) {
      $types = array();
      foreach ($this->baseTypes as $type) {
        $types[] = '"'.$type.'"';
      }
      $types = implode(' ', $types);
      $lines[] = "    BaseType {$types}";
    }

    if ($this->quality !== null) {
      $lines[] = "    Quality {$this->quality}";
    }

    if ($this->rarity !== null) {
      $lines[] = "    Rarity {$this->rarity}";
    }

    if ($this->dropLevel !== null) {
      $lines[] = "    DropLevel {$this->dropLevel}";
    }

    if ($this->itemLevel !== null) {
      foreach ($this->itemLevel as $item_level) {
        $lines[] = "    ItemLevel {$item_level}";
      }
    }

    if ($this->socketGroup !== null) {
      $lines[] = "    SocketGroup {$this->socketGroup}";
    }

    if ($this->sockets !== null) {
      $lines[] = "    Sockets {$this->sockets}";
    }

    if ($this->size !== null) {
      $lines[] = "    SetFontSize {$this->size}";
    }

    if ($this->textColor !== null) {
      $lines[] = "    SetTextColor {$this->textColor}";
    }

    if ($this->backgroundColor !== null) {
      $lines[] = "    SetBackgroundColor {$this->backgroundColor}";
    }

    if ($this->borderColor !== null) {
      $lines[] = "    SetBorderColor {$this->borderColor}";
    }

    if ($this->soundID !== null) {
      $lines[] = "    PlayAlertSoundPositional {$this->soundID} {$this->soundVolume}";
    }

    return implode("\n", $lines);
  }


}

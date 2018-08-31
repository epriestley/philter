<?php

require_once 'util.php';
require_once 'FilterRule.php';

header('Content-Type: text/plain; charset=utf8');

$filter_name = null;
if (isset($_GET['filter'])) {
  $filter_name = $_GET['filter'];
}

$hide_flasks = true;
$show_scrolls = true;

switch ($filter_name) {
  case 'strict':
    $show_scrolls = false;
    break;
}

// TODO: Unique Maps.

$color_black = '0 0 0';
$color_pink = '255 45 175';
$color_gem = '27 162 155';
$color_green = '74 230 58 255';
$color_rare = '255 255 119';
$color_recipe = '75 75 75 200';
$color_maps = '44 44 44';
$color_unique = '175 96 37';
$color_currency = '170 158 130';

$size_tiny = 18;
$size_normal = 32;
$size_huge = 45;

$volume_none = 0;
$volume_medium = 90;
$volume_loud = 200;

$sound_currency = 1;
$sound_wham = 2;
$sound_echo = 4;
$sound_vibrate = 7;

$rules = array();


// -(  Unique  )---------------------------------------------------------------

$rules[] = id(new FilterRule())
  ->setRarity('= Unique')
  ->setSize($size_huge)
  ->setTextColor($color_unique)
  ->setBorderColor($color_unique)
  ->setSound($sound_vibrate, $volume_loud);


// -(  Cards  )----------------------------------------------------------------

$rules[] = id(new FilterRule())
  ->addClass('Divination')
  ->setSize($size_huge)
  ->setBorderColor($color_pink)
  ->setSound($sound_echo, $volume_medium);


// -(  Maps  )-----------------------------------------------------------------

$rules[] = id(new FilterRule())
  ->addClass('Maps')
  ->setSize($size_huge)
  ->setBackgroundColor($color_maps)
  ->setBorderColor($color_pink)
  ->setSound($sound_wham, $volume_medium);


// -(  Normal Items  )---------------------------------------------------------

$item_data = json_decode(file_get_contents('bases.json'), true);
$more_data = require_once 'more_data.php';
$item_data = array_merge($item_data, $more_data);

foreach ($item_data as $key => $item) {
  if ($item['name'] == 'Maelstrom Staff') {
    $item_data[$key]['name'] = "Maelström Staff";
  }
}

$upgrade_levels = array();
foreach ($item_data as $key => $item) {
  $type = $item['type'];
  $name = $item['name'];
  $level = $item['level'];

  $is_armour = ($item['kind'] == 'Armour');
  $is_weapon = ($item['kind'] == 'Weapon');

  $is_health = ($item['type'] == 'Life Flask');
  $is_mana = ($item['type'] == 'Mana Flask');
  $is_upgrade_flask = ($is_health || $is_mana);

  if (!$is_armour && !$is_weapon && !$is_upgrade_flask) {
    continue;
  }

  $min_level = null;
  $min_name = null;
  foreach ($item_data as $other) {
    if ($other['type'] != $type) {
      continue;
    }

    if (isset($other['str']) !== isset($item['str']) ||
        isset($other['dex']) !== isset($item['dex']) ||
        isset($other['int']) !== isset($item['int'])) {
      continue;
    }

    if ($other['level'] <= $level) {
      continue;
    }

    if ($min_level === null || ($min_level > $other['level'])) {
      $min_level = $other['level'];
      $min_name = $other['name'];
    }
  }

  if ($min_level !== null) {
    $item_data[$key]['upgrade'] = $min_level;
  }
}

$accessory_kinds = array(
  'Amulet' => true,
  'Ring' => true,
  'Belt' => true,
  'Jewel' => true,
);

$flasks = array();
$hide_rules = array();

foreach ($item_data as $item) {
  if ($item['kind'] == 'Flask') {
    $flasks[] = $item;
    continue;
  }

  $is_armour = ($item['kind'] == 'Armour');
  $is_weapon = ($item['kind'] == 'Weapon');
  $is_quiver = ($item['kind'] == 'Quiver');
  $is_shield = ($item['type'] == 'Shield');
  $is_jewel = ($item['kind'] == 'Jewel');

  $is_1h_sword =
    ($item['type'] == 'One Hand Sword') ||
    ($item['type'] == 'Thrusting One Hand Sword');

  $is_1h_mace =
    ($item['type'] == 'One Hand Mace') ||
    ($item['type'] == 'Sceptre');

  $is_1h_axe =
    ($item['type'] == 'One Hand Axe');

  $is_bow =
    ($item['type'] == 'Bow');

  $is_2h_mace =
    ($item['type'] == 'Two Hand Mace');

  $is_dagger =
    ($item['type'] == 'Dagger');

  $is_wand = ($item['type'] == 'Wand');
  $is_sceptre = ($item['type'] == 'Sceptre');

  $is_int = isset($item['int']);
  $is_dex = isset($item['dex']);
  $is_str = isset($item['str']);

  $show_rare_rule = id(new FilterRule())
    ->addBaseType($item['name']);

  if ($is_jewel) {
    $show_rare_rule->setRarity('>= Magic');
  } else {
    $show_rare_rule->setRarity('= Rare');
  }

  $is_hide = false;

  $drop_level = null;
  if ($is_weapon || $is_armour) {
    $drop_level = $item['level'];
    if ($drop_level >= 56) {
      $drop_level = null;
    } else if (isset($item['upgrade'])) {
      $drop_level = $item['upgrade'] + 7;
    }

    if ($is_armour) {
      $nice_armor = ($is_str && !$is_dex);
      $is_hide = !$nice_armor;
    }

    if ($is_weapon) {
      $nice_weapon =
        $is_2h_mace ||
        $is_bow ||
        $is_dagger ||
        $is_1h_sword ||
        $is_sceptre;

      $is_hide = !$nice_weapon;
    }
  }

  if ($drop_level) {
    $show_rare_rule->setItemLevel("<= {$drop_level}");
  }

  if ($drop_level !== false) {
    $show_rare_rule
      ->setBorderColor($color_rare)
      ->setSize($size_huge);

    if ($is_hide) {
      $show_rare_rule->setHide(true);
      $hide_rules[] = $show_rare_rule;
    } else {
      $rules[] = $show_rare_rule;
    }
  }

  $hide_rules[] = id(new FilterRule())
    ->addBaseType($item['name'])
    ->setRarity('<= Rare')
    ->setSize($size_tiny)
    ->setHide(true);
}


// -(  Flasks  )---------------------------------------------------------------

$rules[] = id(new FilterRule())
  ->addClass('Flask')
  ->setQuality('>= 1')
  ->setSize($size_huge)
  ->setBackgroundColor($color_recipe)
  ->setBorderColor($color_currency);

foreach ($flasks as $flask) {
  if (!$hide_flasks) {
    $is_health = ($flask['type'] == 'Life Flask');
    $is_mana = ($flask['type'] == 'Mana Flask');
    $is_hybrid = ($flask['type'] == 'Hybrid Flask');
    $is_upgrade_flask = ($is_health || $is_mana);

    if ($is_hybrid) {
      $upgrade_level = false;
    } else if (!$is_upgrade_flask) {
      $upgrade_level = null;
    } else {
      if (isset($flask['upgrade'])) {
        $upgrade_level = $flask['upgrade'] + 2;
      } else {
        $upgrade_level = null;
      }
    }

    if ($upgrade_level !== false) {
      $flask_rule = id(new FilterRule())
        ->addBaseType($flask['name'])
        ->setSize($size_normal);

      if ($upgrade_level !== null) {
        $flask_rule->setItemLevel("<= {$upgrade_level}");
      }

      $rules[] = $flask_rule;
    }
  }

  $rules[] = id(new FilterRule())
    ->addBaseType($flask['name'])
    ->setHide(true);
}


// -(  Gems  )-----------------------------------------------------------------

// Drop-Only Gems
$rules[] = id(new FilterRule())
  ->addClass('Gem')
  ->setBaseTypes(
    array(
      'Added Chaos Damage',
      'Detonate Mines',
      'Empower',
      'Enhance',
      'Enlighten',
      'Portal',
    ))
  ->setSize($size_huge)
  ->setTextColor($color_pink)
  ->setBorderColor($color_green)
  ->setSound($sound_vibrate, $volume_loud);

$rule_gem = id(new FilterRule())
  ->addClass('Gem')
  ->setSize($size_huge)
  ->setTextColor($color_gem)
  ->setBorderColor($color_gem)
  ->setSound($sound_wham, $volume_medium);

// Vaal Gems
$rules[] = id(clone $rule_gem)
  ->addBaseType('Vaal');

// Gems with Quality (Gemcutter Recipe)
$rules[] = id(clone $rule_gem)
  ->setQuality('>= 1');

// All Other Gems
$rules[] = id(clone $rule_gem)
  ->setHide(true);

// -(  Vendor Recipes  )-------------------------------------------------------

// Chromatic Recipe
$rules[] = id(new FilterRule())
  ->setSocketGroup('RGB')
  ->setSize($size_huge)
  ->setBackgroundColor($color_recipe)
  ->setBorderColor($color_currency)
  ->setTextColor($color_currency);

// Jewellers Recipe
$rules[] = id(new FilterRule())
  ->setSockets('= 6')
  ->setSize($size_huge)
  ->setBackgroundColor($color_recipe)
  ->setBorderColor($color_currency)
  ->setTextColor($color_currency)
  ->setSound($sound_wham, $volume_medium);

// Chaos Recipe
$rules[] = id(new FilterRule())
  ->setRarity('= Rare')
  ->setItemLevel(array('>= 60', '<= 74'))
  ->setSize($size_huge)
  ->setBackgroundColor($color_recipe)
  ->setBorderColor($color_rare)
  ->setTextColor($color_currency)
  ->setHide(true, true);

$rules[] = id(new FilterRule())
  ->addClass('Currency')
  ->setBaseTypes(
    array(
      'Simple Rope Net',
      'Reinforced Rope Net',
      'Strong Rope Net',
      'Simple Iron Net',
      'Reinforced Iron Net',
      'Strong Iron Net',
      'Simple Steel Net',
      'Reinforced Steel Net',
    ))
  ->setSound($sound_currency, $volume_none)
  ->setHide(true);

$scroll_rule = id(new FilterRule())
  ->addClass('Currency')
  ->setBaseTypes(
    array(
      'Scroll of Wisdom',
      'Portal Scroll',
    ));

if ($show_scrolls) {
  $scroll_rule->setSize($size_huge);
} else {
  $scroll_rule->setHide(true);
}

$rules[] = $scroll_rule;

$rules[] = id(new FilterRule())
  ->addClass('Currency')
  ->setBaseTypes(
    array(
      'Orb of Transmutation',
      'Orb of Alteration',
      'Orb of Augmentation',
      'Blacksmith\'s Whetstone',
      'Armourer\'s Scrap',
      'Jeweller\'s Orb',
      'Chromatic Orb',
      'Strong Steel Net',
      'Thaumaturgical Net',
    ))
  ->setSize($size_huge);

$rules[] = id(new FilterRule())
  ->addClass('Currency')
  ->addClass('Stackable Currency')
  ->setSize($size_huge)
  ->setBorderColor($color_currency)
  ->setSound($sound_currency, $volume_loud);

foreach ($hide_rules as $hide_rule) {
  $rules[] = $hide_rule;
}

$rules[] = id(new FilterRule())
  ->setSize($size_huge)
  ->setTextColor($color_black)
  ->setBackgroundColor($color_pink)
  ->setBorderColor($color_pink);

$filter = array();
foreach ($rules as $rule) {
  $filter[] = $rule->renderRule();
}

echo implode("\n\n", $filter)."\n";



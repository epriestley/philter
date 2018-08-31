<?php

$items = array();

$in = file_get_contents('armour.html');

preg_match_all(
  '(<h1 class="topBar last layoutBoxTitle">([^<]+)</h1>)',
  $in,
  $matches,
  PREG_OFFSET_CAPTURE);

$headers = array();
foreach ($matches[1] as $location) {
  $headers[$location[0]] = (int)$location[1];
}

preg_match_all(
  '(<tr[^>]*>
	<td rowspan="2"><img id="[^"]+" class="itemDataIcon" src="[^"]+" alt="Icon" data-large-image="[^"]+"/></td>
	<td rowspan="2" class="name">([^<]+)</td>
	<td>([^<]+)</td>
	<td>[^<]+</td>
	<td>[^<]+</td>
	<td>[^<]+</td>
	<td>([^<]+)</td>
	<td>([^<]+)</td>
	<td>([^<]+)</td>
</tr>)',
  $in,
  $matches,
  PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

foreach ($matches as $match) {
  $offset = $match[0][1];

  if ($headers) {
    if ($offset > reset($headers)) {
      $type = key($headers);
      array_shift($headers);
    }
  }

  $item = array(
    'name' => $match[1][0],
    'level' => (int)$match[2][0],
    'type' => $type,
    'kind' => 'Armour',
  );

  if ($match[3][0]) {
    $item['str'] = (int)$match[3][0];
  }

  if ($match[4][0]) {
    $item['dex'] = (int)$match[4][0];
  }

  if ($match[5][0]) {
    $item['int'] = (int)$match[5][0];
  }

  $items[] = $item;
}

$in = file_get_contents('weapon.html');

preg_match_all(
  '(<h1 class="topBar last layoutBoxTitle">([^<]+)</h1>)',
  $in,
  $matches,
  PREG_OFFSET_CAPTURE);

$headers = array();
foreach ($matches[1] as $location) {
  $headers[$location[0]] = (int)$location[1];
}


preg_match_all(
  '(<tr[^>]*>
	<td rowspan="2"><img id="[^"]+" class="itemDataIcon" src="[^"]+" alt="Icon" data-large-image="[^"]+"/></td>
	<td rowspan="2" class="name">([^<]+)</td>
	<td>([^<]+)</td>
	<td>[^<]+</td>
	<td>[^<]+</td>
	<td>[^<]+</td>
	<td>([^<]+)</td>
	<td>([^<]+)</td>
	<td>([^<]+)</td>
</tr>)',
  $in,
  $matches,
  PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

foreach ($matches as $match) {
  $offset = $match[0][1];

  if ($headers) {
    if ($offset > reset($headers)) {
      $type = key($headers);
      array_shift($headers);
    }
  }

  $item = array(
    'name' => $match[1][0],
    'level' => (int)$match[2][0],
    'type' => $type,
    'kind' => 'Weapon',
  );

  if ($match[3][0]) {
    $item['str'] = (int)$match[3][0];
  }

  if ($match[4][0]) {
    $item['dex'] = (int)$match[4][0];
  }

  if ($match[5][0]) {
    $item['int'] = (int)$match[5][0];
  }

  if (preg_match('/^Maelst.*Staff$/', $item['name'])) {
    $item['name'] = 'Maelstrom Staff';
  }

  $items[] = $item;
}

$count = count($items);
echo "Got {$count} items.\n";

file_put_contents('items.tmp', json_encode($items));
`cat items.tmp | json_pp > bases.json`;
`rm items.tmp`;

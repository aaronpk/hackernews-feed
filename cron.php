<?php
$seen_file = __DIR__.'/data/seen.txt';
$feed_file = __DIR__.'/data/entries.json';

if(!file_exists($seen_file)) {
  touch($seen_file);
}

$seen = file($seen_file);

echo "Fetching HN top stories\n";
$storyIDs = getJSON('https://hacker-news.firebaseio.com/v0/topstories.json');
echo "found " . count($storyIDs) . " stories\n";

$entries = json_decode(file_get_contents($feed_file), true);

for($i=0; $i<30; $i++) {
  if(!in_array($storyIDs[$i], $seen)) {
    $story = getJSON('https://hacker-news.firebaseio.com/v0/item/'.$storyIDs[$i].'.json');
    if($story && is_object($story) && property_exists($story, 'id')) {
      $post['url'] = (property_exists($story,'url') ? $story->url : false);
      $post['title'] = $story->title;
      $post['text'] = @$story->text;
      $post['author'] = $story->by;
      $post['hnurl'] = 'https://news.ycombinator.com/item?id=' . $story->id;
      $post['date'] = time();
      seenItem($story->id);
      $entries[] = $post;
    }
  }  
}

$entries = array_slice($entries, -20);
usort($entries, function($a,$b){
  return $a['date'] > $b['date'];
});

file_put_contents($feed_file, json_encode($entries));


$ch = curl_init('https://switchboard.p3k.io/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
  'hub.mode' => 'publish',
  'hub.topic' => 'http://hackernews.pin13.net/'
)));
curl_exec($ch);


function getJSON($url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_USERAGENT, "##hackernews on irc.freenode.net");
  $response = curl_exec($ch); 
  return json_decode($response);
}
function seenItem($item) {
  global $seen_file;
  $fp = fopen($seen_file, 'a');
  fwrite($fp, $item."\n");
  fclose($fp);
}
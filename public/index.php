<?php
$entry_file = '../data/entries.json';
if(!file_exists($entry_file)) {
  file_put_contents($entry_file, '[]');
}
$entries = json_decode(file_get_contents($entry_file), true);
?>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Hackernews Front Page</title>
  <link rel="stylesheet" href="/styles.css"/>
  <link rel="hub" href="https://switchboard.p3k.io/"/>
  <link rel="self" href="http://hackernews.p3k.io/"/>
  <link rel="alternate" type="application/atom+xml" href="https://waterpigs.co.uk/services/microformats-to-atom/?url=http%3A%2F%2Fhackernews.p3k.io%2F"/>
</head>
<body class="h-feed">
  <h1 class="p-name">Hackernews Front Page</h1>
  <p class="p-summary">Front-page articles from <a href="https://news.ycombinator.com">news.ycombinator.com</a>. You can subscribe to this URL in a <a href="http://indiewebcamp.com/reader">reader</a>!</p>
  <?php

  foreach($entries as $entry):
  ?>
    <div class="entry h-entry">
      
      <div class="title">
        <a class="p-name u-url" href="<?= $entry['url'] ?>"><?= $entry['title'] ?></a>
        (<?= parse_url($entry['url'], PHP_URL_HOST) ?>)
      </div>

      <div class="meta">
        by <span class="p-author h-card">
          <a href="/hackernews.png" class="u-photo"></a>
          <a href="https://news.ycombinator.com/user?id=<?= $entry['author'] ?>" class="p-name u-url"><?= $entry['author'] ?></a>
        </span>      
        <time class="dt-published" datetime="<?= date('c', $entry['date']) ?>"><?= date('F j, g:ia T', $entry['date']) ?></time>
        <a class="u-syndication" href="<?= $entry['hnurl'] ?>">view on hackernews</a>
      </div>
    </div>
  <?php
  endforeach;

?>
</body>
</html>
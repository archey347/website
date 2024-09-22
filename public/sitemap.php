<?php

header("Content-type: text/xml");


$pages = [
  [
    'url' => "https://archbar.me/index.php",
    'file' => "../templates/index.php",
    'changefreq' => 'daily',
    'priority' => '0.9'
  ],
  [
    'url' => "https://archbar.me/projects.php",
    'file' => "../templates/projects.php",
    'changefreq' => 'weekly',
    'priority' => '0.5'
  ],
  [
    'url' => "https://archbar.me/contact.php",
    'file' => "../templates/contact.php",
    'changefreq' => 'weekly',
    'priority' => '0.5'
  ]
];

?><?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php

foreach ($pages as $page) {
  print("\t<url>\n");
  print("\t\t<loc>" . $page['url'] . "</loc>\n");
  print("\t\t<lastmod>" . date ("Y-m-d", filemtime($page['file'])) . "</lastmod>\n");
  print("\t\t<changefreq>" . $page['changefreq'] . "</changefreq>\n");
  print("\t\t<priority>" . $page['priority'] . "</priority>\n");

  print("\t</url>\n");
}

?>
</urlset>

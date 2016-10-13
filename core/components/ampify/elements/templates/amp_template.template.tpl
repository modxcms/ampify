<!doctype html>
<html amp lang="en">
  <head>
    <meta charset="utf-8">
    <title>[[*pagetitle]]</title>
    <link rel="canonical" href="[[~[[*id]]? &scheme=`full`]]" />
    <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
    <script type="application/ld+json">
      {
        "@context": "http://schema.org",
        "@type": "NewsArticle",
        "headline": "[[*pagetitle]]",
        "datePublished": "[[*publishedon]]",
        "dateModified": "[[*editedon]]",
        "author": "[[++site_name]]",
        "mainEntityOfPage": "[[~[[*id]]? &scheme=`full`]]",
        "author": {
            "@type": "Person",
            "name": "Wayne Roddy"   
        }
        "publisher": {
         "@type": "Organization",
         "name": "MODX Systems",
         "logo": {
            "@type": "ImageObject",
            "width": "50",
            "height": "48",
            "url":"http://flatso.clients.modxcloud.com/assets/mx-themes/images/modx-revo-2_3-icon.png"
         }
        },
        "image": {
            "@type": "ImageObject",
            "width": "[[*page_img:imgattr=`width`:default=`50`]]",
            "height": "[[*page_img:imgattr=`height`:default=`48`]]",
            "url":"[[*page_img:default=`http://flatso.clients.modxcloud.com/assets/mx-themes/images/modx-revo-2_3-icon.png`]]"
        }
      }
    </script>
    <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
    <style amp-custom>
    body{background-color:#f2f2f2;font-family:sans-serif;}article{padding:10px;color:#111;}h1{margin:0;padding:5px;background-color:#77bccc;color:white;}amp-img{max-width:100%;}
    </style>
    <script async src="https://cdn.ampproject.org/v0.js"></script>
  </head>
  <body>
    <h1>[[*pagetitle]]</h1>
    <amp-img src="/[[*page_img]]" alt="[[*pagetitle]]" width="[[*page_img:imgattr=`width`]]" height="[[*page_img:imgattr=`height`]]" layout="responsive"></amp-img>
    <article>[[*content]]</article>
  </body>
</html>

<?php
error_reporting(1);

if(isset($_GET['pwa_data'])){
$data = unserialize(urldecode($_GET['pwa_data']));
$manifest = [
"short_name"=> $data['title'],
"name"=> $data['name'],
"background_color" => "#{$data['background_color']}",
"theme_color" => "#{$data['theme_color']}",
"display"=> "standalone",
"icons" => [
    [
      "src"=>  $data['img_144'],
      "type"=> "image/png",
      "sizes"=> "144x144",
    ],
    [
      "src"=>  $data['img_192'],
      "type"=> "image/png",
      "sizes"=> "192x192",
    ],
    [
      "src"=>  $data['img_512'],
      "type"=> "image/png",
      "sizes"=> "512x512",
    ],
    
  ],
  
  "start_url"=> '/',
  "scope"=> '/',
];

header('Content-Type: application/json');
echo json_encode($manifest);

}

// {
//     "name": "Mentorship",
//     "short_name": "Mentorship",
//     "start_url": "/",
//     "background_color": "#6777ef",
//     "description": "Mentorship",
//     "display": "fullscreen",
//     "theme_color": "#6777ef",
//     "icons": [
//         {
//             "src": "logo.png",
//             "sizes": "512x512",
//             "type": "image/png",
//             "purpose": "any maskable"
//         },{
//             "src": "apple-touch-icon.png",
//             "sizes": "150x150",
//             "type": "image/png",
//             "purpose": "any maskable"
//         }
//     ]
// }

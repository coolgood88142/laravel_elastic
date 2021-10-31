<?php
return [
 'indices' => [
     'mappings' => [
         'blog-articles' => [
             "properties"=>  [
                 "content"=>  [
                     "type"=>  "text",
                     "analyzer"=>  "ik_max_word",
                     "search_analyzer"=>  "ik_smart"
                 ],
                 "tags"=>  [
                     "type"=>  "text",
                     "analyzer"=>  "ik_max_word",
                     "search_analyzer"=>  "ik_smart"
                 ],
                 "title"=>  [
                     "type"=>  "text",
                     "analyzer"=>  "ik_max_word",
                     "search_analyzer"=>  "ik_smart"
                 ]
             ]
         ]
     ]
 ],
];
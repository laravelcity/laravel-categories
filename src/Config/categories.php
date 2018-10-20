<?php

return [

    // set categories table name
    'categories_table_name' => 'categories' ,

    // set relation category table name
    'relation_table_name' => 'categorieables' ,

    // add new fields to categories table
    'other_fields' => [

        [
            'column' => 'user_id' ,
            'type' => 'integer' ,
            'default' => 0 ,
            'nullable' => false ,

            /* 'values'=>null => form enum filed */
        ]

    ] ,

    // for pagination number
    'perPage' => 20 ,

    // restrict depth for make new category
    'depth' => 3 ,

    // category prefix url
    'prefix' => '/category/' ,

    // default category
    'default_category' => [
        'title' => 'default' ,
        'slug' => 'default'
    ]

];
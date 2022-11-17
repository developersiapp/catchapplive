<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 17/6/19
 * Time: 11:45 AM
 */
$css_arr = [
    [
        'name' => 'bootstrap',
        'path' => 'assets/bootstrap-3/css/bootstrap.min.css',
        'required' => 1
    ],
    [
        'name' => 'font-awesome',
        'path' => 'assets/node_modules/font-awesome/css/font-awesome.min.css',
        'required' => 1
    ],
    [
        'name' => 'ionicons',
        'path' => 'assets/node_modules/ionicons/dist/css/ionicons.min.css',
        'required' => 1
    ],
    [
        'name' => 'jquery-ui',
        'path' => 'assets/node_modules/jquery-ui/themes/base/all.css',
        'required' => 0
    ],
    [
        'name' => 'sweetalert',
        'path' => 'assets/node_modules/sweetalert2/dist/sweetalert2.min.css',
        'required' => 1
    ],
    [
        'name' => 'bootstrap-file-input',
        'path' => 'assets/node_modules/bootstrap-fileinput/css/fileinput.min.css',
        'required' => 1
    ],
    [
        'name' => 'summernote',
        'path' => 'assets/node_modules/summernote/dist/summernote.css',
        'required' => 0
    ],
    [
        'name' => 'adminlte',
        'path' => 'backend/css/AdminLTE.min.css',
        'required' => 1
    ],
    [
        'name' => 'adminlte-skin',
        'path' => 'backend/css/skins/_all-skins.min.css',
        'required' => 1
    ],

    [
        'name' => 'select2',
        'path' => 'assets/node_modules/select2/dist/css/select2.min.css',
        'required' => 0
    ],


    [
        'name' => 'slick-carousel',
        'path' => 'assets/node_modules/slick-carousel/slick/slick.css',
        'required' => 0
    ],
    [
        'name' => 'slick-carousel-theme',
        'path' => 'assets/node_modules/slick-carousel/slick/slick-theme.css',
        'required' => 0,
        'dependency' => 'slick-carousel'
    ],

    [
        'name' => 'bootstrap-datetimepicker',
        'path' => 'assets/node_modules/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css'
    ],

    [
        'name' => 'common',
        'path' => 'backend/css/common.css',
        'required' => 1
    ]
];

//@import url("common.css");
//@import url("header.css");
//@import url("testimonails.css");
//@import url("institutes.css");
//@import url("blog.css");
//@import url("review.css");
//@import url("slider.css");
//@import url("footer.css");


\App\Libraries\AssetsLib::getInstance()->setPathCss($css_arr)->getCSSHTML();

$js_arr = [
    [
        'name' => 'jquery',
        'path' => 'assets/node_modules/jquery/dist/jquery.min.js',
        'required' => 1
    ]
];

\App\Libraries\AssetsLib::getInstance()->setPathJs($js_arr)->getJSHTML();

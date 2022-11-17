<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 17/6/19
 * Time: 11:46 AM
 */

$js_arr = [
    [
        'name' => 'popper',
        'path' => 'assets/node_modules/popper.js/dist/umd/popper.min.js',
        'required' => 0
    ],
    [
        'name' => 'bootstrap',
        'path' => 'assets/bootstrap-3/js/bootstrap.min.js',
        'required' => 1
    ],
    [
        'name' => 'momentjs',
        'path' => 'assets/node_modules/moment/min/moment.min.js',
        'required' => 0
    ],

    [
        'name' => 'jquery-ui-widget',
        'path' => 'assets/node_modules/jquery-ui/ui/widget.js',
        'required' => 0
    ],
    [
        'name' => 'jquery-ui-scroll-parent',
        'path' => 'assets/node_modules/jquery-ui/ui/scroll-parent.js',
        'required' => 0
    ],
    [
        'name' => 'jquery-ui-data',
        'path' => 'assets/node_modules/jquery-ui/ui/data.js',
        'required' => 0
    ],


    [
        'name' => 'jquery-ui-mouse',
        'path' => 'assets/node_modules/jquery-ui/ui/widgets/mouse.js',
        'required' => 0,
        ''
    ],
    [
        'name' => 'jquery-ui-draggable',
        'path' => 'assets/node_modules/jquery-ui/ui/widgets/draggable.js',
        'required' => 0
    ],

    [
        'name' => 'jquery-ui-sortable',
        'path' => 'assets/node_modules/jquery-ui/ui/widgets/sortable.js',
        'required' => 0,
        'dependency' => ['jquery-ui-mouse', 'jquery-ui-widget', 'jquery-ui-data', 'jquery-ui-scroll-parent']
    ],
    [
        'name' => 'sweetalert',
        'path' => 'assets/node_modules/sweetalert2/dist/sweetalert2.min.js',
        'required' => 1
    ],

    [
        'name' => 'select2',
        'path' => 'assets/node_modules/select2/dist/js/select2.full.min.js',
        'required' => 0
    ],
    [
        'name' => 'bootstrap-file-input',
        'path' => 'assets/node_modules/bootstrap-fileinput/js/fileinput.min.js',
        'required' => 1
    ],
    [
        'name' => 'summernote',
        'path' => 'assets/node_modules/summernote/dist/summernote.min.js',
        'required' => 0
    ],

    [
        'name' => 'bootstrap-datetimepicker',
        'path' => 'assets/node_modules/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js',
        'dependency' => ['momentjs']
    ],
    [
        'name' => 'sortable',
        'path' => 'assets/node_modules/Nestable/jquery.nestable.js'
    ],

//    [
//        'name' => 'adminlte',
//        'path' => 'backend/js/adminlte.min.js',
//        'required' => 1
//    ],

    [
        'name' => 'form-ajax',
        'path' => 'js/ajax.js',
        'required' => 1
    ],

//    [
//        'name' => 'custom',
//        'path' => 'backend/js/custom.js',
//        'required' => 1
//    ],
];

\App\Libraries\AssetsLib::getInstance()->setPathJs($js_arr)->getJSHTML();
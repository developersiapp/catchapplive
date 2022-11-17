<?php
/**
 * Created by PhpStorm.
 * User: iapp
 * Date: 17/6/19
 * Time: 11:47 AM
 */

namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AssetsLib
{
    public $all_js;
    public $all_css;
    public $js_arr;
    public $css_arr;

    public $minifyJs;
    public $minifyCss;

    /**
     * @var AssetsLib $instance
     */
    public static $instance;

    public function __construct()
    {
        self::$instance = $this;
        $this->all_js = array();
        $this->all_css = array();
        $this->js_arr = array();
        $this->css_arr = array();
//        $this->minifyJs = config('constants.minifyJs', true);
        if (isset($_GET['nojscache'])) {
            $this->minifyJs = false;
        }
//        $this->minifyCss = config('constants.minifyCss', true);
        if (isset($_GET['nocsscache'])) {
            $this->minifyCss = false;
        }
        return $this;
    }

    /**
     * @return AssetsLib
     */
    public static function getInstance()
    {
        if (self::$instance != null) {
            return self::$instance;
        }
        return new AssetsLib();
    }

    public static function loadCss(...$names)
    {
        foreach ($names as $name) :
            array_push(self::getInstance()->css_arr, $name);
        endforeach;
    }

    public static function loadJs(...$names)
    {
        foreach ($names as $name) :
            array_push(self::getInstance()->js_arr, $name);
        endforeach;
    }

    public static function load(...$names)
    {
        self::loadJs(...$names);
        self::loadCss(...$names);
    }

    public function setPathCss($param)
    {
        $this->all_css = [];
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $this->all_css[$value['name']] = $value;
                if (isset($value['required']) && $value['required']) {
                    self::loadCss($value['name']);
                }
            }
        }
        return $this;
    }

    public function setPathJs($param)
    {
        $this->all_js = [];
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $this->all_js[$value['name']] = $value;
                if (isset($value['required']) && $value['required']) {
                    self::loadJs($value['name']);
                }
            }
        }
        return $this;
    }

    public function appendPathJs($param)
    {
        if (is_array($param)) {
            foreach ($param as $key => $value) {
                $this->all_js[$value['name']] = $value;
            }
        }
    }

    public function getCSS()
    {
        $narr = [];
        foreach ($this->all_css as $key => $value) {
            if (in_array($key, $this->css_arr)) {
                $narr[$key] = $value;
            }
        }
        return $narr;
    }

    public function getJS()
    {
        $narr = [];
//        if (isset($_GET['test'])) {
        $allJs = Collection::make($this->all_js)->keyBy('name');
        foreach ($this->js_arr as $js_name) {
            $js = $allJs->get($js_name, false);
            if ($js) {
                if (isset($js['dependency']) && is_array($js['dependency'])) {
                    foreach ($js['dependency'] as $dependency) {
                        $this->js_arr[] = $dependency;
                    }
                }
            }
        }
        foreach ($allJs as $key => $value) {
            if (in_array($key, $this->js_arr)) {
                $narr[$key] = $value;
//                print_r($value['dependency']??'');
            }
        }
//            print_r($narr);
        return $narr;
//        }
        /*foreach ($this->all_js as $key => $value) {
            if (in_array($key, $this->js_arr)) {
                $narr[$key] = $value;
            }
        }*/

        return $narr;
    }

    public function getCacheDirectory()
    {
        $cachedisk = Storage::disk('cache');
        $path = $cachedisk->getDriver()->getAdapter()->getPathPrefix();
        if (!file_exists($path)) {
            mkdir($path);
        }
        return $cachedisk;
    }

    public function getJSHTML()
    {
        $assetsdisk = Storage::disk('assets');
        $cachedisk = $this->getCacheDirectory();
        $js = $this->getJS();
        if ($this->minifyJs) {
            $newfilename = $this->getFileName($js) . '.js';

            if (isset($_GET['clearjscache'])) {
                //echo $newfilepath;
                $cachedisk->delete($newfilename);
            }

            if ($cachedisk->exists($newfilename)) {
                $lastModified = Carbon::createFromTimestamp($cachedisk->lastModified($newfilename));
                $diff = $lastModified->diffInMinutes(Carbon::now());
                if ($diff < 120) {
                    ?>
                    <!-- Loaded combined JS from cache  (<?= $lastModified->toDateTimeString() ?>) -->
                    <!-- Created <?= $diff ?> minutes ago -->
                    <script src="<?= url("cache/" . $newfilename) ?>?v=<?= config('constants.ver_js') ?>"
                            type="text/javascript"></script>
                    <?php
                    return;
                }
            }

            $this->minify_js($js, $newfilename);
            ?>
            <!-- Loaded Refreshed Combined JS  -->
            <script src="<?= url("cache/" . $newfilename) ?>?v=<?= config('constants.ver_js') ?>"
                    type="text/javascript"></script>
            <?php
        } else {
            $newfilename = $this->getFileName($js) . '.js';
            $cachedisk->delete($newfilename);

            foreach ($js as $key => $value) {
                ?>
                <script src="<?= url($value['path']) ?>?sadadfs=<?= time() ?>" type="text/javascript"></script>
                <?php
            }
        }
    }

    private function minify_js($js, $newfilepath)
    {
        $minifier = new JS();
        $path = $this->getCacheDirectory()->getAdapter()->getPathPrefix();
        foreach ($js as $key => $value) {
            if (Storage::disk('assets')->exists($value['path'])) {
                $file = Storage::disk('assets')->get($value['path']);
                $minifier->add($file);
            } else {
                \Illuminate\Support\Facades\Log::info("Js File not found:" . json_encode($value));
            }
        }
        $minifier->minify($path . $newfilepath);
//        $minifier->gzip($path . $newfilepath);
    }

    public function getCSSHTML()
    {
        $assetsdisk = Storage::disk('assets');
        $cachedisk = $this->getCacheDirectory();
        $css = $this->getCSS();
        if ($this->minifyCss) {
            $newfilename = $this->getFileName($css) . '.css';

            if (isset($_GET['clearcsscache'])) {
                //echo $newfilepath;
                $cachedisk->delete($newfilename);
            }

            if ($cachedisk->exists($newfilename)) {
                $lastModified = Carbon::createFromTimestamp($cachedisk->lastModified($newfilename));
                $diff = $lastModified->diffInMinutes(Carbon::now());
                if ($diff < 120) {
                    ?>
                    <!-- Loaded combined CSS from cache  (<?= $lastModified->toDateTimeString() ?>) -->
                    <!-- Created <?= $diff ?> minutes ago -->
                    <link href="<?= url("cache/" . $newfilename) ?>?v=<?= config('constants.ver_css') ?>"
                          rel="stylesheet" type="text/css">
                    <?php
                    return;
                }
            }

            $this->minify_css($css, $newfilename);
            ?>
            <!-- Loaded Refreshed Combined JS  -->
            <link href="<?= url("cache/" . $newfilename) ?>?v=<?= config('constants.ver_css') ?>"
                  rel="stylesheet" type="text/css">
            <?php
        } else {
            $newfilename = $this->getFileName($css) . '.css';
            $cachedisk->delete($newfilename);

            foreach ($css as $key => $value) {
                ?>
                <link href="<?= url($value['path']) ?>?sadadfs=<?= time() ?>" rel="stylesheet" type="text/css">
                <?php
            }
        }
    }

    private function minify_css($css, $newfilepath)
    {
        $minifier = new CSS();
        $path = $this->getCacheDirectory()->getAdapter()->getPathPrefix();
        $assetspath = Storage::disk('assets')->getAdapter()->getPathPrefix();
        foreach ($css as $key => $value) {
            if (Storage::disk('assets')->exists($value['path'])) {
//                $file = Storage::disk('assets')->get($value['path']);
                $file = $assetspath . $value['path'];
                $minifier->add($file);
            } else {
                \Illuminate\Support\Facades\Log::info("Css File not found:" . json_encode($value));
            }
        }
        $minifier->minify($path . $newfilepath);
    }

    private function getFileName($arr)
    {
        $newfilename = "" . config('constants.ver_css') . config('constants.ver_js');
        foreach ($arr as $key => $value) {
            $newfilename .= basename($value['path']);
        }
        return md5($newfilename);
    }
}
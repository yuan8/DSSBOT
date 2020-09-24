<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class Layout 
{
   
     static $config=[];

     static public $tahun_ac=2020;

     public static function layout2(){
       
     }


     static function ll($d){
        enval('static::'.$d.'()');
     }
   
   static function conAll(){
    return static::$config;
   }

    public function __construct($args)
    {

        static::$config = config('adminlte');

        foreach ($args as $key => $value) {
            # code...
            static::$config[$key]=$value;
        }

        if(isset($args['menuBuild'])){

            switch($args['menuBuild']){
                case 'RKPD':
                static::$config['sidebar_collapse']=true;
                static::$config['layout_topnav']=false;
                static::$config['menu']=[

                    [
                        'text' =>'DASHBOARD',
                        'class'=>'',
                        'url'=>'',

                        'href'=>route('sipd.rkpd.handle',['tahun'=>static::$config['tahun']]),
                        'icon'=>'nav-icon fas fa-tachometer-alt'
                        // 'topnav' => true,
                    ],
                    [
                        'text' =>'LIST RKPD',
                        'class'=>'',
                        'url'=>'',

                        'icon'=>'nav-icon fas fa-file',
                        'href'=>route('sipd.rkpd',['tahun'=> static::$config['tahun']]),

                    ],
                    [
                        'text' => 'RKPD DOKUMEN',
                        'url'=>'',
                        'class'=>'',
                        'icon'=>"fas fa-circle nav-icon",
                        'href'=>''
                        // 'topnav' => true,
                    ],
                    [
                        'text' => 'RKPD TAMBAHAN',
                        'url'=>'',
                        'class'=>'',
                        'href'=>'',
                        'icon'=>'fas fa-circle nav-icon'
                        // 'topnav' => true,
                    ],
                 ];


                break;

            }


        }else{
            static::$config['sidebar_collapse']=false;
            static::$config['layout_topnav']=true;
            static::$config['menu']=[
                [
                        'text' => 'SIPD RKPD '.date('Y'),
                        'url'=>'',
                        'class'=>'',
                        'href'=>''
                        // 'topnav' => true,
                    ],
                 ];
        }
    }


    static function getConfig($data,$def=''){
        return isset(static::$config[$data])?(empty(static::$config[$data])?$def:static::$config[$data]):$def;
    }


    /**
     * Set of tokens related to screen sizes.
     *
     * @var array
     */
    protected static $screenSizes = ['xs', 'sm', 'md', 'lg', 'xl'];

    /**
     * Check if layout topnav is enabled.
     *
     * @return bool
     */
    public static function isLayoutTopnavEnabled()
    {
        return static::getConfig('layout_topnav') || View::getSection('layout_topnav');
    }

    /**
     * Check if layout boxed is enabled.
     *
     * @return bool
     */
    public static function isLayoutBoxedEnabled()
    {
        return static::getConfig('layout_boxed') || View::getSection('layout_boxed');
    }

    /**
     * Make and return the set of classes related to the body tag.
     *
     * @return string
     */
    public static function makeBodyClasses()
    {
        $classes = [];

        $classes = array_merge($classes, self::makeLayoutClasses());
        $classes = array_merge($classes, self::makeSidebarClasses());
        $classes = array_merge($classes, self::makeRightSidebarClasses());
        $classes = array_merge($classes, self::makeCustomBodyClasses());

        return trim(implode(' ', $classes));
    }

    /**
     * Make and return the set of data attributes related to the body tag.
     *
     * @return string
     */
    public static function makeBodyData()
    {
        $data = [];

        // Add data related to the "sidebar_scrollbar_theme" configuration.

        $sb_theme_cfg = static::getConfig('sidebar_scrollbar_theme', 'os-theme-light');

        if ($sb_theme_cfg != 'os-theme-light') {
            $data[] = 'data-scrollbar-theme='.$sb_theme_cfg;
        }

        // Add data related to the "sidebar_scrollbar_auto_hide" configuration.

        $sb_auto_hide = static::getConfig('sidebar_scrollbar_auto_hide', 'l');

        if ($sb_auto_hide != 'l') {
            $data[] = 'data-scrollbar-auto-hide='.$sb_auto_hide;
        }

        return trim(implode(' ', $data));
    }

    /**
     * Make and return the set of classes related to the layout configuration.
     *
     * @return array
     */
    private static function makeLayoutClasses()
    {
        $classes = [];

        // Add classes related to the "layout_topnav" configuration.

        if (self::isLayoutTopnavEnabled()) {
            $classes[] = 'layout-top-nav';
        }

        // Add classes related to the "layout_boxed" configuration.

        if (self::isLayoutBoxedEnabled()) {
            $classes[] = 'layout-boxed';
        }

        // Add classes related to fixed sidebar layout configuration. The fixed
        // sidebar is not compatible with layout topnav.

        if (! self::isLayoutTopnavEnabled() && static::getConfig('layout_fixed_sidebar')) {
            $classes[] = 'layout-fixed';
        }

        // Add classes related to fixed navbar/footer configuration. The fixed
        // navbar/footer is not compatible with layout boxed.

        if (! self::isLayoutBoxedEnabled()) {
            $classes = array_merge($classes, self::makeFixedResponsiveClasses('navbar'));
            $classes = array_merge($classes, self::makeFixedResponsiveClasses('footer'));
        }

        return $classes;
    }

    /**
     * Make the set of classes related to a fixed responsive configuration.
     *
     * @param string $section (navbar or footer)
     * @return array
     */
    private static function makeFixedResponsiveClasses($section)
    {
        $classes = [];
        $cfg = static::getConfig('layout_fixed_'.$section);

        if ($cfg === true) {
            $cfg = ['xs' => true];
        }

        // At this point the config should be an array.

        if (! is_array($cfg)) {
            return $classes;
        }

        // Make the set of responsive classes in relation to the config.

        foreach ($cfg as $size => $enabled) {
            if (in_array($size, self::$screenSizes)) {
                $size = ($size === 'xs') ? $section : "{$size}-{$section}";
                $fixed = $enabled ? 'fixed' : 'not-fixed';
                $classes[] = "layout-{$size}-{$fixed}";
            }
        }

        return $classes;
    }

    /**
     * Make the set of classes related to the left sidebar configuration.
     *
     * @return array
     */
    private static function makeSidebarClasses()
    {
        $classes = [];

        // Add classes related to the "sidebar_mini" configuration.

        if (static::getConfig('sidebar_mini', true) === true) {
            $classes[] = 'sidebar-mini';
        } elseif (static::getConfig('sidebar_mini', true) == 'md') {
            $classes[] = 'sidebar-mini sidebar-mini-md';
        }

        // Add classes related to the "sidebar_collapse" configuration.

        if (static::getConfig('sidebar_collapse') || View::getSection('sidebar_collapse')) {
            $classes[] = 'sidebar-collapse';
        }

        return $classes;
    }

    /**
     * Make the set of classes related to the right sidebar configuration.
     *
     * @return array
     */
    private static function makeRightSidebarClasses()
    {
        $classes = [];

        // Add classes related to the "right_sidebar" configuration.

        if (static::getConfig('right_sidebar') && static::getConfig('right_sidebar_push')) {
            $classes[] = 'control-sidebar-push';
        }

        return $classes;
    }

    /**
     * Make the set of classes related to custom body classes configuration.
     *
     * @return array
     */
    private static function makeCustomBodyClasses()
    {
        $classes = [];
        $cfg = static::getConfig('classes_body', '');

        if (is_string($cfg) && $cfg) {
            $classes[] = $cfg;
        }

        return $classes;
    }
}

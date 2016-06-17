<?php
/**
 * Created by PhpStorm.
 * User: wuwentao
 * Date: 2016/6/17
 * Time: 13:55
 */

namespace DataPool\Utils;


use Symfony\Component\ClassLoader\Psr4ClassLoader;

class Loader
{

    private static $register_path =[
        ['DataPool', 'src', true],
    ];

    /**
     * @return Psr4ClassLoader
     */
    public static function getLoader()
    {
        return self::loadClass(self::$register_path);
    }

    /**
     * @param array $dirs [[prefix, path, <recursive>], ...]
     * @return Psr4ClassLoader
     */
    public static function loadClass(array $dirs)
    {
        $loader = new Psr4ClassLoader();
        $root = realpath(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']));
        $paths = [];
        foreach ($dirs as $d) {
            if (is_array($d) && count($d) > 1) {
                $prefix = $d[0];
                $path = $d[1];
                $r = false;
                if (count($d) > 2 && $d[2]) {
                    $r = true;
                }
                $p = self::getClassPath($root, $prefix, $path, $r);
                $paths = array_merge($paths, $p);
            }
        }
        foreach ($paths as $p) {
            $loader->addPrefix($p['prefix'], $p['path']);
        }
        $loader->register();
        return $loader;
    }

    /**
     * @param string $root
     * @param string $prefix
     * @param string $dir
     * @param bool $recursive
     * @return array
     */
    private static function getClassPath($root, $prefix, $dir, $recursive = true)
    {
        $paths = [];
        if ($recursive) {
            $p = self::getPrefixPath($prefix, $root . DIRECTORY_SEPARATOR . $dir);
            $paths = array_merge($paths, $p);
        } else {
            array_push($paths, ['prefix'=>$prefix, 'path'=>$dir]);
        }
        return $paths;
    }

    /**
     * @param $prefix
     * @param $realPath
     * @return array
     */
    private static function getPrefixPath($prefix, $realPath)
    {
        $pp = [];
        array_push($pp, ['prefix'=>$prefix, 'path'=>$realPath]);
        $di = new \DirectoryIterator($realPath);
        foreach($di as $f) {
            if ($f->isDot()) continue;
            if ($f->isFile()) continue;
            if (strpos(basename($f), '.') === 0) continue;
            if ($f->isDir()) {
                //load if first letter is upper case
                $fc = substr($f->getBasename(), 0, 1);
                if ($fc > 64 && $fc < 91) {
                    $pp = array_merge($pp, self::getPrefixPath($prefix . '\\' . $f, $realPath . DIRECTORY_SEPARATOR . $f));
                }
            }
        }
        return $pp;
    }
}
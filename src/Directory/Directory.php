<?php

namespace SlimFramework\Directory;

use Generator;

class Directory
{
    /**
     * @param $nameSpacePath
     * @param $namespace
     * @param array $excludeFiles
     * @param array $excludePaths
     * @return array
     */
    public static function turnNameSpacePathIntoArray(
        $nameSpacePath,
        $namespace,
        array $excludeFiles = [],
        array $excludePaths = []
    ): array {
        $items = [];

        $pathsToExclude = ['.', '..'];

        foreach ($excludePaths as $path) {
            $pathsToExclude[] = $path;
        }

        $files = scandir($nameSpacePath);

        if (empty($files)) {
            return $items;
        }

        foreach ($files as $class) {
            $isExcludePath = in_array($class, $pathsToExclude);
            $isExcludeFile = in_array($class, $excludeFiles);

            if (!$isExcludePath && !$isExcludeFile) {
                $possibleDirectory = $nameSpacePath . "/{$class}";

                if (!is_dir($possibleDirectory)) {
                    $items[] = $namespace . str_replace('.php', '', $class);
                } else {
                    $newNameSpace = ($namespace . $class . "\\");

                    $items = array_merge($items, self::turnNameSpacePathIntoArray(
                        $possibleDirectory,
                        $newNameSpace,
                        $excludeFiles,
                        $excludePaths
                    ));
                }
            }
        }

        return $items;
    }

    /**
     * @param $files
     * @return Generator
     */
    public static function getIterator($files): Generator
    {
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }

            yield $file;
        }
    }

    /**
     * @param string $path
     * @return string
     */
    public static function turnPathIntoNameSpace(string $path): string
    {
        $nameSpaceSplit = explode('src', $path);

        $nameSpaceSufix = $nameSpaceSplit[1];

        $nameSpaceSufix = str_replace("/", "\\", $nameSpaceSufix);

        return "App" . $nameSpaceSufix . '\\';
    }

    /**
     * @param $path
     * @return array
     */
    public static function getFiles($path)
    {
        $files = [];

        foreach (Directory::getIterator(scandir($path)) as $file) {
            $files[] = $file;
        }

        return $files;
    }
}

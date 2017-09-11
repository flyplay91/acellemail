<?php

/**
 * UpgradeManager class.
 *
 * Tool for upgrading the entire system source
 *
 * LICENSE: This product includes software developed at
 * the Acelle Co., Ltd. (http://acellemail.com/).
 *
 * @category   Acelle Library
 *
 * @author     N. Pham <n.pham@acellemail.com>
 * @author     L. Pham <l.pham@acellemail.com>
 * @copyright  Acelle Co., Ltd
 * @license    Acelle Co., Ltd
 *
 * @version    1.0
 *
 * @link       http://acellemail.com
 */

namespace Acelle\Library;

use ZipArchive;
use Illuminate\Support\Facades\Log as LaravelLog; // something wrong, cannot use the default name Log

class UpgradeManager
{
    protected $source;
    protected $target;

    const META_FILE = 'meta.json';

    /**
     * Constructor, specify the source, target and load the meta information
     *
     */
    function __construct()
    {
        $this->source = storage_path('tmp/patch');
        $this->target = base_path();
    }

    /**
     * Constructor, specify the source, target and load the meta information
     *
     */
    public function load($path)
    {
        // Check WRITE permission
        $this->cleanup();

        try {
            // Extract the zip file
            $old = umask(0);
            $zip = new ZipArchive();
            $res = $zip->open($path);
            if ($res === true) {
                $zip->extractTo($this->source);
                $zip->close();
                umask($old);

                // test the patch, throw an exception in case meta.json does not exist
                $this->validate();
            } else {
                umask($old);
                throw new \Exception("Invalid upgrade package");
            }
        } catch (\Exception $e) {
            $this->rm($this->source);
            throw $e;
        }

    }

    /**
     * Read the meta data from a patch package
     *
     */
    public function getNewVersion()
    {
        if ($this->isNewVersionAvailable()) {
            return $this->getMetaInfo()['version'];
        } else {
            return null;
        }
    }

    /**
     * Get last supported version for upgrade
     *
     */
    public function getLastSupportedVersion()
    {
        if ($this->isNewVersionAvailable()) {
            return $this->getMetaInfo()['last_supported'];
        } else {
            return null;
        }
    }

    /**
     * Read the meta data from a patch package
     *
     */
    public function cleanup()
    {
        // Check WRITE permission
        if (!$this->isWritable($this->source)) {
            throw new \Exception("Cannot write to folder {$this->source}");
        } else {
            // Clean up the target folder
            $this->rm($this->source);
        }
    }

    /**
     * Read the meta data from a patch package
     *
     */
    public function validate()
    {
        $meta = $this->getMetaInfo();
        if (version_compare($this->getNewVersion(), $this->getCurrentVersion(), '=')) {
            throw new \Exception(sprintf('The version you uploaded is the same as the current one (%s)', $this->getNewVersion()));
        }

        if (version_compare($this->getNewVersion(), $this->getCurrentVersion(), '<')) {
            throw new \Exception(sprintf('The version you uploaded (%s) is older than the current one (%s)', $this->getNewVersion(), $this->getCurrentVersion()));
        }

        if (version_compare($this->getLastSupportedVersion(), $this->getCurrentVersion(), '>')) {
            throw new \Exception(sprintf('You are on a version that is not supported by this update, last supported version is %s', $this->getLastSupportedVersion() ));
        }

        // DRYRUN to see if there is any error
        $this->test();
    }

    /**
     * Read the meta data from a patch package
     *
     */
    private function getMetaInfo()
    {
        $path = join_paths($this->source, self::META_FILE);
        if (!file_exists($path)) {
            throw new \Exception("Unknown package format");
        }

        return json_decode(file_get_contents($path), true);
    }

    /**
     * Actually run the upgrade process
     *
     */
    public function run($test = false)
    {
        $old = umask(0);

        try {
            if ($test) {
                LaravelLog::info('Start upgrading (test)');
            } else {
                LaravelLog::info('Start upgrading');
            }

            if (!$test) {
                // set umask(0) and back up the current umask
                $old = umask(0);
            }

            $errors = [];
            $meta = $this->getMetaInfo();
            $updates = $meta['updated'];
            $deletes = $meta['deleted'];
            $packages = $meta['packages'];
            $dirs = $meta['dirs'];
            $langFiles = [];

            // new or updated files
            foreach($updates as $file) {
                $source = join_paths($this->source, $file);
                $target = join_paths($this->target, $file);

                if ($test) {
                    if ($this->isLanguageFile($source)) {
                        $languages = \Acelle\Model\Language::all();
                        foreach ($languages as $lang) {
                            $target = join_paths($lang->languageDir(), pathinfo($file)['basename']);
                            if (!$this->isWritable($target)) {
                                $errors[] = $target;
                            }
                        }
                    } else {
                        if (!$this->isWritable($target)) {
                            $errors[] = $target;
                        }
                    }
                } else {
                    LaravelLog::info("Replacing {$target}");

                    if ($this->isEnFile($source)) {
                        $langFiles[] = $source;
                    } elseif ($this->isLanguageFile($source)) {
                        $this->mergeLanguageFile($source, $target);
                    } else {
                        $this->copy($source, $target);
                    }
                }
            }

            // process language files after all the other files have been processed
            // in case a new language is added
            foreach ($langFiles as $source) {
                LaravelLog::info("Upgrade language file {$source}");
                $this->upgradeLanguageFiles($source);
            }

            // deleted files
            foreach($deletes as $file) {
                $target = join_paths($this->target, $file);

                if ($test) {
                    if (!$this->isWritable($target)) {
                        $errors[] = $target;
                    }
                } else {
                    LaravelLog::info("Deleting {$target}");
                    @unlink($target);
                }

            }

            // new or updated packages
            foreach($packages as $dir) {
                $source = join_paths($this->source, 'vendor', $dir);
                $target = join_paths($this->target, 'vendor', $dir);
                if ($test) {
                    if (!$this->isWritable($target)) {
                        $errors[] = $target;
                    }
                } else {
                    LaravelLog::info("Replacing {$target}");
                    $this->rm($target);
                    $this->copy($source, $target);
                }
            }

            // new or updated entire directories
            foreach($dirs as $dir) {
                $source = join_paths($this->source, $dir);
                $target = join_paths($this->target, $dir);
                if ($test) {
                    if (!$this->isWritable($target)) {
                        $errors[] = $target;
                    }
                } else {
                    LaravelLog::info("Replacing {$target}");
                    $this->rm($target);
                    $this->copy($source, $target);
                }
            }

            // just finish for test mode
            if ($test) {
                return $errors;
            }

            // cleanup
            LaravelLog::info("Cleaning up");
            $this->cleanup();

            // reload the config & run migration
            LaravelLog::info("Start caching & migrating");
            artisan_config_cache();
            artisan_migrate();
            LaravelLog::info("All done!");

            if (!$test) {
                // restore the umask
                umask($old);
            }

            return true;
        } catch (\Exception $e) {
            if (!$test) {
                // restore the umask
                umask($old);
            }
            throw $e;
        }
    }

    /**
     * Actually run the upgrade process
     *
     */
    public function upgradeTranslationFiles()
    {
        $dir = base_path('resources/lang/');

    }

    /**
     * Test the upgrade process (DRY-RUN)
     *
     */
    public function test()
    {
        return $this->run(true);
    }

    /**
     * Get current app version
     *
     */
    public function getCurrentVersion()
    {
        return trim(file_get_contents(base_path('VERSION')));
    }

    /**
     * Check if new version is available
     *
     */
    public function isNewVersionAvailable()
    {
        return file_exists($this->source);
    }

    /**
     * Check if an existing file is writable or a new path can be created
     *
     * @input string file path
     * @output boolean
     */
    private function isWritable($path)
    {
        if (is_writable($path)) {
            return true;
        } elseif (!file_exists($path) && $this->canCreateFile($path)) {
            return true;
        } else {
            // file exists but not writable
            // file not exist nor creatable
            return false;
        }
    }

    /**
     * Check if the specified path can be created
     *
     * @output boolean
     */
    private function canCreateFile($path)
    {
        $a = explode(DIRECTORY_SEPARATOR, $path);
        $parent = null;
        for ($i = 0; $i < sizeof($a); $i += 1) {
            $tmppath = implode(DIRECTORY_SEPARATOR, array_slice($a, 0, $i));

            if (empty($tmppath)) {
                continue;
            }

            if (!file_exists($tmppath)) {
                break;
            } else {
                $parent = $tmppath;
            }
        }

        return is_writable($parent);
    }

    /**
     * Delete a directory recursively
     *
     */
    private function rm($src)
    {
        if (!file_exists($src)) {
            return;
        }

        if (!is_dir($src)) {
            unlink($src);
            return;
        }

        $dir = opendir($src);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                $full = $src . '/' . $file;
                if ( is_dir($full) ) {
                    $this->rm($full);
                }
                else {
                    unlink($full);
                }
            }
        }
        closedir($dir);
        rmdir($src);
    }

    /**
     * Copy a directory recursively
     *
     */
    private function copy($src, $dst)
    {
        // create the base directory for the destination if it does not exist yet
        $this->createBaseDir($dst);

        // if source is a file, just copy it
        if (!is_dir($src)) {
            copy($src, $dst);
            return;
        }

        // if source is a directory, copy recursively all its child files/directories
        $dir = opendir($src);
        mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir(join_paths($src, $file)) ) {
                    $this->copy(join_paths($src,  $file), join_paths($dst, $file));
                } else {
                    copy(join_paths($src, $file), join_paths($dst,  $file));
                }
            }
        }
        closedir($dir);
    }

    /**
     * Create base directory for a given path
     *
     */
    public function createBaseDir($path)
    {
        $dirname = pathinfo($path)['dirname'];
        $a = explode(DIRECTORY_SEPARATOR, $dirname);
        for ($i = 0; $i <= sizeof($a); $i += 1) { // @important: use <= here
            $tmppath = implode(DIRECTORY_SEPARATOR, array_slice($a, 0, $i));
            if (empty($tmppath)) {
                continue;
            }

            if (!file_exists($tmppath)) {
                mkdir($tmppath);
            }
        }
    }

    /**
     * Check if the provided file is a language file
     *
     */
    public function isLanguageFile($path)
    {
        return preg_match('/resources\/lang\/[a-z0-9]{2}\/[a-z0-9]+\.php$/', $path);
    }

    /**
     * Check if the provided file is a language file and is EN
     *
     */
    public function isEnFile($path)
    {
        return preg_match('/resources\/lang\/en\/[a-z0-9]+\.php$/', $path);
    }

    /**
     * Upgrade all existing language packages using the provided file
     * For example: $manager->upgradeLanguageFiles('/home/new/messages.php')
     */
    public function upgradeLanguageFiles($sourcePath)
    {
        $source = include $sourcePath;

        $languages = \Acelle\Model\Language::all();
        foreach ($languages as $lang) {
            // if for any reason the language directory does not exist, just ignore it
            if (!file_exists($lang->languageDir())) {
                LaravelLog::warning(sprintf("Language directory %s does not exist", $lang->languageDir()));
                continue;
            }

            $target = include join_paths($lang->languageDir(), pathinfo($sourcePath)['basename']);
            $merged = array_diff_key($target + $source, array_diff_key($target, $source));
            $lang->updateFromYaml(pathinfo($sourcePath)['filename'], \Yaml::dump($merged));
        }
    }

    /**
     * Merge the language file to retain user's change
     */
    public function mergeLanguageFile($sourcePath, $targetPath)
    {
        // if the language files does not exist yet, just create it
        if (!file_exists($targetPath)) {
            $this->copy($sourcePath, $targetPath);
        }
        $source = include $sourcePath;
        $target = include $targetPath;
        $merged = array_diff_key($target + $source, array_diff_key($target, $source));

        $out = '<?php return ' . var_export(\Yaml::parse(\Yaml::dump($merged)), true) . ' ?>';
        \File::put($targetPath, $out);
    }
}

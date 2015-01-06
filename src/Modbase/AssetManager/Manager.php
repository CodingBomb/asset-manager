<?php namespace Modbase\AssetManager;

use \Illuminate\Filesystem\Filesystem as Filesystem;
use \Illuminate\Support\Facades\Config as Config;
use \Illuminate\Support\Facades\HTML;

class Manager {

    /**
     * Decoded contents of the JSON file
     * 
     * @var array
     */
    protected $data;
    
    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;
    
    /**
     * Create a new manager
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Get HTML for stylesheet of the bundle
     *
     * @param        $bundle
     * @param string $folder
     * @param array  $option ['index' => 0, 'name'=>'style.name']
     *                       Important: name should not contain '-'.
     *
     * @return string stylesheet html
     */
    public function styles($bundle, $folder = 'css', $option = ['index' => 0])
    {
        // If we didn't parse the file before, then do it now
        if (!$this->data)
        {
            $this->parseVersionsFile();
        }
        // Parser style file with bundle
        $style = '';
        if (array_key_exists('index', $option)) {
            $index = intval($option['index']);
            $filename = $this->data[$bundle.'.styles'][$index];
            $style = HTML::style($folder.'/'.$filename);
        }
        else if (array_key_exists('name', $option)) {
            $name = strval($option['name']);
            foreach ($this->data[$bundle.'.styles'] as $filename) {
                $pieces = explode('-', $filename);
                if ($name == $pieces[0]) {
                    $style = HTML::style($folder.'/'.$filename);
                    break;
                }
            }
        }
        else {
            $style = HTML::style($folder.'/'.$this->data[$bundle.'.styles'][0]);
        }

        return $style.PHP_EOL;
    }

    /**
     * Get HTML for javascript of the bundle
     * @param        $bundle
     * @param string $folder
     * @param array  $option ['index' => 0, 'name'=>'script.name']
     *                       Important: name should not contain '-'.
     *
     * @return string javascript html
     */
    public function scripts($bundle, $folder = 'js', $option = ['index' => 0])
    {
        // If we didn't parse the file before, then do it now
        if (!$this->data)
        {
            $this->parseVersionsFile();
        }

        // Parser script file with bundle
        $script = '';
        if (array_key_exists('index', $option)) {
            $index = intval($option['index']);
            $filename = $this->data[$bundle.'.scripts'][$index];
            $script = HTML::script($folder.'/'.$filename);
        }
        else if (array_key_exists('name', $option)) {
            $name = strval($option['name']);
            foreach ($this->data[$bundle.'.scripts'] as $filename) {
                $pieces = explode('-', $filename);
                if ($name == $pieces[0]) {
                    $script = HTML::script($folder.'/'.$filename);
                    break;
                }
            }
        }
        else {
            $script = HTML::script($folder.'/'.$this->data[$bundle.'.scripts'][0]);
        }

        return $script.PHP_EOL;
    }

    /**
     * Get the assets file and contents.
     *
     * @return array
     */
    protected function getVersionsFile()
    {
        return $this->files->get(base_path().'/'.Config::get('asset-manager::config.file'));
    }

    /**
     * JSON decode the assets file
     * 
     * @return array
     */
    protected function parseVersionsFile()
    {
        $this->data = json_decode($this->getVersionsFile(), true);
    }
}
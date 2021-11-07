<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2021/10/21
 * Time: 23:17
 */

namespace pcj\temp_replace;


use pcj\temp_replace\Exception\TempReplaceException;
use Symfony\Component\Filesystem\Filesystem;

class TempReplace
{
    //字符替换map
    protected $string_replace_map=[];
    //源文件
    protected $original_file=null;
    //生成目标文件
    protected $new_file_name=null;

    /**
     * @param array $string_replace_map
     */
    public function setStringReplaceMap($string_replace_map)
    {
        $this->string_replace_map = $string_replace_map;
        return $this;
    }

    /**
     * @param null $original_file
     */
    public function setOriginalFile($original_file)
    {
        $this->original_file = $original_file;
        return $this;
    }

    /**
     * @param null $new_file_name
     */
    public function setNewFileName($new_file_name)
    {
        $this->new_file_name = $new_file_name;
        return $this;
    }

    protected function check(){
        if($this->original_file===null){
            throw new TempReplaceException("未甚至源文件");
        }

    }

    public function generate()
    {
        $o_file=new \SplFileInfo($this->original_file);
        $content =file_get_contents($o_file->getRealPath());
        $content=str_replace(array_keys($this->string_replace_map),array_values($this->string_replace_map),$content);
        if($this->new_file_name!==null){
            $fs=new Filesystem();
            $fs->copy($o_file->getRealPath(),$this->new_file_name,true);
            $new_file=new \SplFileInfo($this->new_file_name);
            $file=fopen($new_file->getRealPath(),"w");
        }else{
            $file=fopen($o_file->getRealPath(),"w");
        }
        fwrite($file,$content);
        fclose($file);
    }
    
}
<?php

namespace Laravelcity\Categories\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes,HtmlMethods;

    protected $guarded=[];
    protected $modelType;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table=config('categories.categories_table_name');
        $this->perPage=config('categories.perPage');
    }

    /**
     * set model_type for any model
     * @param string $model
     * @return $this
    */
    public function setModelType($model){
        $this->modelType=$model;
        return $this;
    }

    /**
     * get all categories
     * @return Collection
    */
    function allCategories(){
        return self::where('model_type',$this->modelType)->paginate();
    }

    /**
    * get root categories
     * @return Collection
    */
    function rootCategories(){
        return self::whereIn('model_type',[$this->modelType,'NONE'])
            ->orderby('id','desc')
            ->where('parent',0)->paginate();
    }

    /**
    * check category exist
    * @param $title => string
     * @return bool
    */
    function categoryExist($title){
        if(self::where('title',$title)->where('model_type',$this->modelType)->first())
            return true;

        return false;
    }

    /**
    * get category child with depth
    * @params $depth
     * @return Collection
    */
    function child($depth=null){
        if($depth==null)
            return self::where('parent',$this->attributes['id'])->get();
        else
            return self::where('parent',$this->attributes['id'])->where('depth',$depth)->get();
    }

    /**
     * get category parent
     * @return Categories
     */
    function parent(){
        return self::where('id',$this->attributes['parent'])->first();
    }

    /**
     * get category parent
     * @return Categories
     */
    function parents(){
        return self::where('id',$this->attributes['parent'])->get();
    }

    /**
     * get category with id
     * @param $id
     * @param $type
     * @return Categories
     */
    function getCategory($id,$type=null){
        if(is_numeric($id))
            $model=self::where('id',$id)->select();
        else
            $model=self::where('slug',$id)->select();

        if($type)
            $model->where('model_type',$type);

        $cat=$model->first();

        return $cat;
    }

    /**
     * get category url
     * @return string
     */
    function url(){
        return config('Categories.prefix').$this->attributes['slug'];
    }


}

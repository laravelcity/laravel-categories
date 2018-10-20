<?php

namespace Laravelcity\Categories\Lib;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Laravelcity\Categories\Models\Categories;

class Repository
{

    protected $category;
    protected $modelType;

    public function __construct ()
    {
        $this->category = new Categories();
        $this->category->setModelType($this->modelType);
    }

    /**
     * create category with model_type
     * @params $modelType => string
     * @return $this
     */
    public function newCollection ($model , $extClass = null)
    {
        $this->modelType = $model;
        if ($extClass)
            $this->category = new $extClass;

        $this->category->setModelType($model);
        return $this;
    }

    /**
     * get all categories
     * @return Collection
     */
    public function all ()
    {
        return $this->category->allCategories();
    }

    /**
     * get root categories that parent=0
     * @return Collection
     */
    public function rootCategories ()
    {
        return $this->category->rootCategories();
    }

    /**
     * get deleted categories
     * @return Collection
     */
    public function trash ()
    {
        return $this->category->onlyTrashed()->paginate();
    }

    /**
     * get category with id
     * @param $id
     * @return Categories
     */
    public function find ($id)
    {
        if ($model = $this->category->getCategory($id))
            return $model;

        else
            return false;
    }

    /**
     * get id list array with id
     * @param $id
     * @return Collection
     */
    public function findModels ($id = [])
    {
        return $this->category->whereIn('id' , $id)->get();
    }

    /**
     * get first category
     * @return Categories
     */
    public function first ()
    {
        return $this->category->first();
    }

    /**
     * insert category
     * @param Request $request
     * @return Categories
     */
    public function insert (Request $request)
    {
        if ($this->category->categoryExist($request->input('title') , $request->input('model_type')))
            throw new CategoryException(trans('Categories::category.error-message.categoryExist'));

        $category = $this->category->newInstance($request->except('_token'));
        $slug = new Slugger($this->modelType);
        $category->slug = $slug->storeSlug($request->input('slug') , $request->input('title'));
        $category->model_type = $this->modelType;
        $category->save();

        return $category;

    }

    /**
     * update category
     * @param int $id
     * @param Request $request
     * @return Categories
     */
    public function update (Request $request , $id)
    {
        if ($category = $this->category->where('id' , $id)->first()) {

            foreach ($request->except(['_token']) as $key => $val) {
                if (Schema::hasColumn('categories' , $key))
                    $category->{$key} = $val;
            }

            $slug = new Slugger($this->modelType);
            $slug = $slug->updateSlug($category , $request->input('slug') , $request->input('title'));
            $category->slug = $slug;

            $category->save();
            return $category;
        } else
            throw new CategoryException(trans('Categories::category.error-message.categoryNotExist'));

    }

    /**
     * delete category with id
     * @param $id => int
     */
    public function delete ($id)
    {
        $this->category->where('id' , $id)->delete();
    }

    /**
     * destroy category with id
     * @param $id => int
     */
    public function destroy ($id)
    {
        $this->category->where('id' , $id)->forceDelete();
    }

    /**
     * run actions : restore , delete , trash
     */
    public function runActions ()
    {
        $action = \request()->input('action');
        $selection = \request()->input('selection');

        if ($action == '' || $selection == null)
            throw new CategoryException(trans('Categories::category.error-message.sendDataError'));

        switch ($action) {
            case "delete":
                $this->category->whereIn('id' , $selection)->forceDelete();
                break;
            case "restore":
                $this->category->whereIn('id' , $selection)->restore();
                break;
            case "destroy":
                $this->category->whereIn('id' , $selection)->delete();
                break;
        }
    }

    /**
     * get html select of categories
     * @param $attributes => array
     * @param $select => boolean
     */
    public function htmlSelectList ($attributes = null , $value = null , $select = true)
    {
        return $this->category->getOptionListCategories($value , $attributes , $select);
    }

    /**
     * get html ul of categories
     * @param $attributes => array
     * @param $select => boolean
     * @return string
     */
    public function htmlUlList ($attributes = null , $ul = true)
    {
        return $this->category->getUlListCategories($attributes , $ul);
    }

    /**
     * get array list of categories
     * @return array
     */
    public function arrayList ()
    {
        return $this->category->categoryLists();
    }

    /**
     * get category depth
     * @param $category => collect
     * @return int
     */
    function depth ($category)
    {
        return $this->category->getDepth($category->parent);
    }

    /**
     * make line for category show depth
     * @param $category => collect
     * @param $lineStyle => string
     * @return string
     */
    function beforeLineTitle ($category , $lineStyle = ' __ ')
    {
        return $this->category->makeLineForDepth($this->depth($category) , $lineStyle);
    }

}
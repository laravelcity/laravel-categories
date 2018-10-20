<?php

namespace Laravelcity\Categories\Models;

trait HtmlMethods
{

    /**
     * get root categories => parent=0
     * @return Collection
     */
    public function getRootCategories ()
    {
        return self::where('parent' , 0)
            ->where('slug' , '<>' , config('Categories::category.default_category.slug'))
            ->orderby('id' , 'desc')
            ->where('model_type' , $this->modelType)
            ->get();
    }

    /**
     * get array list of categories with child
     * @param Collection $categories
     * @return array
     */
    public function categoryLists ()
    {
        $data = [];
        foreach ($this->rootCategories() as $category) {
            $data[] = [
                'id' => $category->id ,
                'title' => $category->title ,
                'slug' => $category->slug ,
                'parent' => $category->parent ,
                'children' => $this->CategoryLists($category->child()) ,
            ];
        }
        return $data;
    }

    /**
     * get html option with child
     * @param Collection $categories
     * @return string
     */
    public function categoryOption ($categories , $value = null)
    {
        $options = "";
        if ($categories) {
            foreach ($categories as $category) {
                $line = $this->makeLineForDepth($this->getDepth($category->parent));
                $selected = $value == $category->id ? 'selected' : '';
                $options .= "<option $selected value='{$category->id}'>$line {$category->title}</option>";
                $options .= $this->categoryOption($category->child());
            }
        }
        return $options;
    }

    /**
     * get html li (list)  with child
     * @param Collection $categories
     * @return string
     */
    public function categoryLi ($categories , $child = false)
    {
        $list = "";
        if ($categories) {
            if ($child && count($categories) > 0) {
                $list .= "<ul>";
                foreach ($categories as $category) {
                    $list .= "<li > <a href='" . $category->url() . "'>{$category->title}</a></li>";
                    $list .= $this->categoryLi($category->child() , true);
                }
                $list .= "</ul>";

            } else {
                foreach ($categories as $category) {
                    $list .= "<li > <a href='" . $category->url() . "'>{$category->title}</a></li>";
                    $list .= $this->categoryLi($category->child() , true);
                }
            }
        }
        return $list;
    }

    /**
     * get html select or options
     * @param string $model_type
     * @param array $attributes
     * @param boolean $select
     * @return string
     */
    public function getOptionListCategories ($value , $attributes = null , $select = true)
    {
        $options = $this->categoryOption($this->getRootCategories($this->modelType) , $value);
        $attrs = $this->makeAttributesForOption($attributes);
        $newOption = "<option selected value='0'>" . trans('Categories::category.no-parent-category') . " </option>" . $options;
        if ($select == true)
            return "<select $attrs>$newOption</select>";
        else
            return $newOption;
    }

    /**
     * get html ul or li (list)
     * @param string $model_type
     * @param array $attributes
     * @param boolean $select
     * @return string
     */
    public function getUlListCategories ($attributes = null , $ul = false)
    {
        $lists = $this->categoryLi($this->getRootCategories($this->modelType));
        $attrs = $this->makeAttributesForOption($attributes);

        if ($ul == true)
            return "<ul $attrs>$lists</ul>";
        else
            return $lists;
    }

    /**
     * make line style with depth for before title
     * @param int $depth
     * @param string $symbol
     * @return string
     */
    public function makeLineForDepth ($depth = 0 , $symbol = ' - ')
    {
        $line = '';
        if ($depth > 0) {
            for ($i = 1; $i <= $depth; $i++)
                $line .= $symbol;
        }
        return $line;
    }

    /**
     * make attr for html select
     * @param array $attributes
     * @return string
     */
    public function makeAttributesForOption ($attributes)
    {
        $attrs = '';
        if ($attributes) {
            foreach ($attributes as $key => $value) {
                $attrs .= "$key='" . $value . "'";
            }
        }
        return $attrs;
    }

    /**
     * get category depth
     * @param $parent => int
     * @return int
     */
    public function getDepth ($parent = 0)
    {
        $counter = 0;
        $r = true;

        while ($r) {
            $category = self::where('id' , $parent)->first();
            if ($category) {
                $counter++;
                $parent = $category->parent;
                if ($category->parent == 0) {
                    break;
                }
            } else
                break;
        }
        return $counter;
    }
}
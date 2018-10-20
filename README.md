# Laravel Categories

You can create a separate **categories** for any **models** .

## Install laravel categories

#### STEP1:

Run below statements on your terminal :

``
    composer require laravelcity/laravel-categories
``

#### STEP2:

add **provider** and **facade** in **config/app.php**


```
    providers => [
      ...
      Laravelcity\Categories\CategoriesServiceProvider::class,
    ],


    aliases => [
      ...
      'Category'=> \Laravelcity\Categories\Facade\Category::class,
    ]
```

#### STEP3:

``
    php artisan vendor:publish --provider=Laravelcity\Categories\CategoriesServiceProvider
``
or
``
    php artisan vendor:publish 
``

Now you can customize your config in file `config/categories.php`

#### STEP4:

``
    php artisan migrate
``

## How to manage categories for each model 

For example, we want to create categories for posts

```

    class PostCategoriesController extends Model
    {
    
        protected $category;

        public function __construct()
        {
            // set model type for categories
            $this->category=Category::newCollection('Post'); 
        }
        
       // return all categories
       public function index()
       {
          $categories=$this->category->rootCategories();
          return view('view-name')->with(['categories'=>$categories]);
       }

        // insert category
        public function store(Request $request)
        {
            $request->validate(['title'=>'required','parent'=>'required']);

            try{
                 $this->category->insert($request);
                 return redirect()->back()->withSuccess('insert success');

            } catch (CategoryException $e){
                return redirect()->back()->withErrors($e->getMessage());
            }
        }

        // edit category
        public function edit($id)
        {
            try {
                $category=$this->category->find($category_id);
                .
                .
                .
                
            } catch (CategoryException $e){
                return redirect()->back()->withErrors($e->getMessage());
            }
        }

        // update category
        public function update(Request $request, $category_id)
        {
            try {
                $this->category->update($request,$category_id);
                .
                .
                .
              
            } catch (CategoryException $e){
                return redirect()->back()->withErrors($e->getMessage());
            }
        }

        // run actions to categories
        public function actions()
        {
            try {
                $this->category->runActions();
                .
                .
                .

            } catch (CategoryException $e){
                return redirect()->back()->withErrors($e->getMessage());
            }
        }

       // destroy category
        public function destroy($category_id)
        {
            try {
                $this->category->destroy($category_id);
                .
                .
                .

            }catch (CategoryException $e){
                return redirect()->back()->withErrors($e->getMessage());
            }
        }

       // return trashed categories
        public function trash()
        {
            try {
                $categories= $this->category->trash(); //return collection
                .
                .
                .

            }catch (CategoryException $e){
                return redirect()->back()->withErrors($e->getMessage());
            }
        }

    }
    
```

`$category->runActions();` It has three operators

- destroy
- delete
- restore

To run these methods, send the following parameters


- **action**
 
    This is a parameter to save selected operator. 
    paramert is string and only => 'destroy','delete','restore'  
 
- **selection** 
 
    This is a parameter to save selected categories to execute operations on them
    parametr is array,for example => [2,5,8,9]

## other methods 

- `$this->category->rootCategories()`
   return root categories
    
- `$this->category->htmlSelectList(['class'=>'form-control','name'=>'parent'],null,true)`
    return html select of categories 
    
- `$this->category->htmlUlList(['class'=>'form-control','name'=>'parent'])`
    return html ul (list) of categories 
    
- `$this->category->all()`
    return all categories
    
- `$this->category->findModels(array $id)`
   Return array list of models  
   
- `$this->category->arrayList()`
   Return the array list of categories 
   
- `$this->category->depth(Categories $category)`
   Return category depth

## how to use of categories    
    
##### 1- Create a model with name Categories and extends `\Laravelcity\Categories\Models\Categories` ,and build your relationships as follows.

```
    namespace App\Models;
        
    class Categories extends \Laravelcity\Categories\Models\Categories
    {
        public function posts()
        {
            return $this->morphedByMany(Post::class, 'categorieable');
        }
    }

```

Create all relationships in this way and just change the name (`posts()`) and model (`Post::class`) of the relationship

And we use the following example

```

    namespace App\Models;

    class Post extends Model
    {
    
        public function categories()
        {
            return $this->morphToMany(App\Models\Categories::class, 'categorieable');
        }

    }
    
```


##### 2- set categories for model items

```
    public function __construct(Post $post)
    {
        $this->category = Category::newCollection('Post','App\Models\Categories::class');
        // The second parameter is to identify the model that we created for the relationship
        $this->post = $post;
    }
    
    public function store(Request $request){
    
        $post=$this->post->create($request->except(['_token']));
        
        if(\request()->input('category_id',null)){
            $categories=$this->category->findModels(\request()->input('category_id'));
            // category_id must have an array
            $post->category()->sync($categories);
        }
         
    }
    
```


##### 2- get categories of post 

```
    public function show($request){
    
         $post=Post::find($id);
         $categories=$post->categories; // return Categories collect
        
    }
     
```


##### 2- get posts of Category

```

     // $category_id it can be `string` or `number` 
     $category=$this->category->find($category_id);
     return $category->posts; // return Categories collect
    
```


Note that you can create multiple categories with different models
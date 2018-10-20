<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up ()
    {

        // categories table
        Schema::create(config('categories.categories_table_name') , function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->increments('id');
            $table->string('title' , 200);
            $table->string('slug' , 250);
            $table->string('description' , 180)->nullable();
            $table->string('model_type' , 200);
            $table->integer('count')->default(0);
            $table->integer('parent')->default(0);
            $table->text('meta')->nullable(); // for custom fields
            $this->makeOtherFields($table);
            $table->softDeletes();
            $table->timestamps();
        });

        // relation table
        Schema::create(config('categories.relation_table_name') , function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->integer('categorieable_id');
            $table->string('categorieable_type' , 200);
            $table->integer('categories_id')->unsigned()->index();
            $table->foreign('categories_id')->references('id')->on('categories')->onDelete('cascade');
        });

        // insert default category when migrating
        \Illuminate\Support\Facades\DB::table('categories')->insert([
            'id' => 1 ,
            'title' => config('categories.default_category.title') ,
            'slug' => config('categories.default_category.slug') ,
            'model_type' => 'NONE'
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down ()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('categories');
        Schema::dropIfExists('categorieables');
        Schema::enableForeignKeyConstraints();
    }

    /**
     * set new fields to category table
     */
    public function makeOtherFields (Blueprint $table)
    {
        if (count(config('categories.other_fields')) > 0) {
            foreach (config('categories.other_fields') as $filed) {

                if (isset($filed['values']) && $filed['values'] != null) {

                    if (isset($filed['nullable']) && $filed['nullable'] != false)
                        $table->{$filed['type']}($filed['column'] , $filed['values'])->default($filed['default'])->nullable();
                    else
                        $table->{$filed['type']}($filed['column'] , $filed['values'])->default($filed['default']);

                } else {

                    if (isset($filed['nullable']) && $filed['nullable'] != false)
                        $table->{$filed['type']}($filed['column'])->default($filed['default'])->nullable();
                    else
                        $table->{$filed['type']}($filed['column'])->default($filed['default']);

                }
            }
        }
    }
}

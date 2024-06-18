<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BrandCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id('brand_id');
            $table->string('brand_name', 100);
            $table->string('brand_logo', 100);
            $table->text('brand_description');
            $table->string('brand_country', 100);
            $table->string('brand_website', 100);
            $table->boolean('brand_is_active');
            $table->timestamp('brand_created_at')->useCurrent();
            $table->timestamp('brand_updated_at')->useCurrent();
            $table->timestamp('brand_deleted_at')->nullable();
        });
// write sql query to create brands table with columns
// brand_id, brand_name, brand_logo, brand_description, brand_country, brand_website, brand_is_active, brand_created_at, brand_updated_at, brand_deleted_at
// brand_id should be primary key
// brand_created_at, brand_updated_at, brand_deleted_at should be timestamp
// brand_deleted_at should be nullable
// brand_name, brand_logo, brand_description, brand_country, brand_website should be string

        // create brands table with columns brand_id, brand_name, brand_logo, brand_description, brand_country, brand_website, brand_is_active, brand_created_at, brand_updated_at, brand_deleted_at


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->uuid('id');
            $table->string('url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('module_id')->nullable();
            $table->string('module_name')->nullable();
            $table->string('image_type')->nullable();
            $table->string('extension')->nullable();
            $table->string('mime_type')->nullable();
            $table->double('size')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}

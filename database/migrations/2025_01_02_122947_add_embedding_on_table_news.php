<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE news ADD COLUMN embedding vector(384);");
    }
    
    public function down()
    {
        DB::statement("ALTER TABLE news DROP COLUMN embedding;");
    }
    
};

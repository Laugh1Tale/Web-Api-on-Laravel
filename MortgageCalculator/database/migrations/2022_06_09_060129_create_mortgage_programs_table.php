<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mortgage_programs', function (Blueprint $table) {
            $table->id();
            $table->string('bank name', 50);
            $table->string('program name', 50);
            $table->string('realty type', 25);
            $table->decimal('interest rate');
            $table->decimal('minimum payment');
            $table->decimal('maximum amount', 17, 2);
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
        Schema::dropIfExists('mortgage_programs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('shops', function (Blueprint $table) {
        // Saldo awal 0, tipe decimal biar presisi untuk uang
        $table->decimal('balance', 15, 2)->default(0)->after('description');
    });
}

public function down()
{
    Schema::table('shops', function (Blueprint $table) {
        $table->dropColumn('balance');
    });
}
};

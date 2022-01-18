<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeatureFlagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->string('type');
        });

        $permissionsId=DB::table('permissions')->insertGetId(
            ['name' => 'feature-flag.index', 'display_name' => 'feature-flag.index', 'description' => 'feature-flag.index']
        );
        DB::table('permission_role')->insert(
            ['permission_id' => $permissionsId, 'role_id' => 1]
        );


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permission=DB::table('permissions')->where(
            ['name' => 'feature-flag.index']
        )->first();
        if($permission) {
            $permissionRole = DB::table('permission_role')->where(
                ['permission_id' => $permission->id, 'role_id' => 1]
            )->delete();
        }

        $permission=DB::table('permissions')->where(
            ['name' => 'feature-flag.index']
        )->delete();

        Schema::dropIfExists('feature_flags');
    }
}

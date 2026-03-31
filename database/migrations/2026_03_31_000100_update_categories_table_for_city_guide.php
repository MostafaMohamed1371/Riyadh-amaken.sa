<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (! Schema::hasColumn('categories', 'name') && ! Schema::hasColumn('categories', 'title')) {
                $table->string('name')->nullable();
            }

            if (! Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable();
            }

            if (! Schema::hasColumn('categories', 'icon')) {
                $table->string('icon')->nullable();
            }

            if (! Schema::hasColumn('categories', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (! Schema::hasColumn('categories', 'sort_order')) {
                $table->integer('sort_order')->default(0);
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'title') && ! Schema::hasColumn('categories', 'name')) {
                $table->renameColumn('title', 'name');
            }

            if (Schema::hasColumn('categories', 'image')) {
                $table->dropColumn('image');
            }

            if (Schema::hasColumn('categories', 'no_places')) {
                $table->dropColumn('no_places');
            }

            if (Schema::hasColumn('categories', 'type')) {
                $table->dropColumn('type');
            }
        });

        if (Schema::hasColumn('categories', 'name') && Schema::hasColumn('categories', 'slug')) {
            $categories = DB::table('categories')->select('id', 'name', 'slug')->get();

            foreach ($categories as $category) {
                $name = $category->name ?: 'category-'.$category->id;
                $slug = $category->slug ?: Str::slug($name).'-'.$category->id;

                DB::table('categories')
                    ->where('id', $category->id)
                    ->update([
                        'name' => $name,
                        'slug' => $slug,
                    ]);
            }
        }

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'name')) {
                $table->string('name')->nullable(false)->change();
            }

            if (Schema::hasColumn('categories', 'slug')) {
                $table->string('slug')->nullable(false)->change();
                $table->unique('slug');
            }
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'slug')) {
                $table->dropUnique(['slug']);
            }

            if (Schema::hasColumn('categories', 'sort_order')) {
                $table->dropColumn('sort_order');
            }

            if (Schema::hasColumn('categories', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('categories', 'icon')) {
                $table->dropColumn('icon');
            }

            if (Schema::hasColumn('categories', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};

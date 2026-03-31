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
        Schema::table('events', function (Blueprint $table) {
            if (! Schema::hasColumn('events', 'slug')) {
                $table->string('slug')->nullable();
            }

            if (! Schema::hasColumn('events', 'short_description')) {
                $table->string('short_description')->nullable();
            }

            if (! Schema::hasColumn('events', 'full_description')) {
                $table->longText('full_description')->nullable();
            }

            if (! Schema::hasColumn('events', 'start_date')) {
                $table->date('start_date')->nullable();
            }

            if (! Schema::hasColumn('events', 'end_date')) {
                $table->date('end_date')->nullable();
            }

            if (! Schema::hasColumn('events', 'start_time')) {
                $table->time('start_time')->nullable();
            }

            if (! Schema::hasColumn('events', 'end_time')) {
                $table->time('end_time')->nullable();
            }

            if (! Schema::hasColumn('events', 'ticket_price')) {
                $table->decimal('ticket_price', 10, 2)->nullable();
            }

            if (! Schema::hasColumn('events', 'booking_url')) {
                $table->string('booking_url')->nullable();
            }

            if (! Schema::hasColumn('events', 'image')) {
                $table->string('image')->nullable();
            }

            if (! Schema::hasColumn('events', 'gallery')) {
                $table->json('gallery')->nullable();
            }

            if (! Schema::hasColumn('events', 'organizer')) {
                $table->string('organizer')->nullable();
            }

            if (! Schema::hasColumn('events', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }

            if (! Schema::hasColumn('events', 'is_active')) {
                $table->boolean('is_active')->default(true);
            }

            if (! Schema::hasColumn('events', 'meta_title')) {
                $table->string('meta_title')->nullable();
            }

            if (! Schema::hasColumn('events', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
        });

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('events', 'date')) {
                $table->dropColumn('date');
            }

            if (Schema::hasColumn('events', 'time')) {
                $table->dropColumn('time');
            }
        });

        if (Schema::hasColumn('events', 'title') && Schema::hasColumn('events', 'slug')) {
            $events = DB::table('events')->select('id', 'title', 'slug', 'start_date')->get();

            foreach ($events as $event) {
                $title = $event->title ?: 'event-'.$event->id;
                $slug = $event->slug ?: Str::slug($title).'-'.$event->id;
                $startDate = $event->start_date ?: now()->toDateString();

                DB::table('events')
                    ->where('id', $event->id)
                    ->update([
                        'slug' => $slug,
                        'start_date' => $startDate,
                    ]);
            }
        }

        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'slug')) {
                $table->string('slug')->nullable(false)->change();
                $table->unique('slug');
            }

            if (Schema::hasColumn('events', 'start_date')) {
                $table->date('start_date')->nullable(false)->change();
            }

            $table->index(['is_active', 'is_featured']);
            $table->index('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'slug')) {
                $table->dropUnique(['slug']);
            }

            if (Schema::hasColumn('events', 'meta_description')) {
                $table->dropColumn('meta_description');
            }

            if (Schema::hasColumn('events', 'meta_title')) {
                $table->dropColumn('meta_title');
            }

            if (Schema::hasColumn('events', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('events', 'is_featured')) {
                $table->dropColumn('is_featured');
            }

            if (Schema::hasColumn('events', 'organizer')) {
                $table->dropColumn('organizer');
            }

            if (Schema::hasColumn('events', 'gallery')) {
                $table->dropColumn('gallery');
            }

            if (Schema::hasColumn('events', 'image')) {
                $table->dropColumn('image');
            }

            if (Schema::hasColumn('events', 'booking_url')) {
                $table->dropColumn('booking_url');
            }

            if (Schema::hasColumn('events', 'ticket_price')) {
                $table->dropColumn('ticket_price');
            }

            if (Schema::hasColumn('events', 'end_time')) {
                $table->dropColumn('end_time');
            }

            if (Schema::hasColumn('events', 'start_time')) {
                $table->dropColumn('start_time');
            }

            if (Schema::hasColumn('events', 'end_date')) {
                $table->dropColumn('end_date');
            }

            if (Schema::hasColumn('events', 'start_date')) {
                $table->dropColumn('start_date');
            }

            if (Schema::hasColumn('events', 'full_description')) {
                $table->dropColumn('full_description');
            }

            if (Schema::hasColumn('events', 'short_description')) {
                $table->dropColumn('short_description');
            }

            if (Schema::hasColumn('events', 'slug')) {
                $table->dropColumn('slug');
            }
        });
    }
};

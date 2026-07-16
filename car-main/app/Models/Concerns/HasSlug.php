<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the trait.
     */
    protected static function bootHasSlug()
    {
        static::creating(function ($model) {
            $model->generateUniqueSlug();
        });

        static::updating(function ($model) {
            $nameField = $model->getSlugSourceField();
            if ($model->isDirty($nameField)) {
                $model->generateUniqueSlug();
            }
        });
    }

    /**
     * Generate a unique slug for the model.
     */
    protected function generateUniqueSlug()
    {
        $nameField = $this->getSlugSourceField();
        $slugField = $this->getSlugField();
        
        $baseSlug = Str::slug($this->{$nameField});
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $counter++;
            $slug = "{$baseSlug}-{$counter}";
        }

        $this->{$slugField} = $slug;
    }

    /**
     * Check if a slug already exists for another record of this model.
     */
    protected function slugExists(string $slug): bool
    {
        $query = static::where($this->getSlugField(), $slug);

        if ($this->exists) {
            $query->where($this->getKeyName(), '!=', $this->getKey());
        }

        if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(static::class))) {
            $query->withTrashed();
        }

        return $query->exists();
    }

    /**
     * Get the name of the field to generate the slug from.
     */
    protected function getSlugSourceField(): string
    {
        return 'name'; // Default for most, override in model if different (e.g. 'title')
    }

    /**
     * Get the name of the slug field.
     */
    protected function getSlugField(): string
    {
        return 'slug';
    }
}

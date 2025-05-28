<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewRequest extends Model
{
    protected $fillable = [
        'user_id',
        'action_type',
        'model_type',
        'model_id',
        'data',
        'status'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getModelClass()
    {
        return '\\App\\Models\\' . $this->model_type;
    }

    public function executeAction()
    {
        $modelClass = $this->getModelClass();

        switch ($this->action_type) {
            case 'create':
                return $modelClass::create($this->data);
            
            case 'update':
                $model = $modelClass::findOrFail($this->model_id);
                return $model->update($this->data);
            
            case 'delete':
                $model = $modelClass::findOrFail($this->model_id);
                return $model->delete();
        }
    }
} 
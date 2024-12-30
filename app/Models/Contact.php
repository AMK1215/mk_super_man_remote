<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = ['link', 'contact_type_id', 'agent_id'];

    public function contact_type()
    {
        return $this->belongsTo(ContactType::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class);
    }

    public function contactAgents()
    {
        return $this->hasMany(ContactAgent::class);
    }

    public function scopeAgent($query)
    {
        return $query->whereHas('contactAgents', function ($query) {
            $query->where('agent_id', Auth::id());
        });
    }

    public function scopeAgentPlayer($query)
    {
        return $query->whereHas('contactAgents', function ($query) {
            $query->where('agent_id', Auth::user()->agent_id);
        });
    }

    public function scopeMaster($query)
    {
        $agents = User::find(auth()->user()->id)->agents()->pluck('id')->toArray();

        return $query->whereHas('contactAgents', function ($query) use ($agents) {
            $query->whereIn('agent_id', $agents);
        });
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactAgent;
use App\Models\ContactType;
use App\Traits\AuthorizedCheck;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizedCheck;

    public function index()
    {
        $auth = auth()->user();
        $this->MasterAgentRoleCheck();
        $contacts = $auth->hasPermission('master_access') ? Contact::query()->master()->latest()->get() : Contact::query()->agent()->latest()->get();

        return view('admin.contact.index', compact('contacts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->MasterAgentRoleCheck();
        $contact_types = ContactType::all();

        return view('admin.contact.create', compact('contact_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');

        // Validate the request
        $request->validate([
            'link' => 'required',
            'contact_type_id' => 'required|exists:contact_types,id',
            'type' => $isMaster ? 'required' : 'nullable',
            'agent_id' => ($isMaster && $request->type === 'single') ? 'required|exists:users,id' : 'nullable',
        ]);

        $type = $request->type ?? 'single';
        try {
            $contact = Contact::create([
                'link' => $request->link,
                'contact_type_id' => $request->contact_type_id,
            ]);
            if ($type === 'single') {
                $agentId = $isMaster ? $request->agent_id : $user->id;
                $this->FeaturePermission($agentId);
                ContactAgent::create([
                    'contact_id' => $contact->id,
                    'agent_id' => $agentId
                ]);
            } elseif ($type === 'all') {
                foreach ($user->agents as $agent) {
                    ContactAgent::create([
                        'contact_id' => $contact->id,
                        'agent_id' => $agent->id
                    ]);
                }
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        return redirect()->route('admin.contact.index')->with('success', 'Contact created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        $this->MasterAgentRoleCheck();
        $contact_types = ContactType::all();
        if (! $contact) {
            return redirect()->back()->with('error', 'Contact Not Found');
        }

        return view('admin.contact.edit', compact('contact', 'contact_types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $this->MasterAgentRoleCheck();
        $user = Auth::user();
        $isMaster = $user->hasRole('Master');

        if (! $contact) {
            return redirect()->back()->with('error', 'Banner Text Not Found');
        }
        $data = $request->validate([
            'link' => 'required',
            'contact_type_id' => 'required|exists:contact_types,id',
        ]);

        DB::transaction(function () use ($contact, $data, $request, $isMaster, $user) {
            $contact->update($data);
            $contact->contactAgents()->delete();

            if ($request->type === "single") {
                $agentId = $isMaster ? $request->agent_id : $user->id;
                $contact->contactAgents()->create(['agent_id' => $agentId, 'contact_id' => $contact->id]);
            } elseif ($request->type === "all") {
                foreach ($user->agents as $agent) {
                    $contact->contactAgents()->create(['agent_id' => $agent->id, 'contact_id' => $contact->id]);
                }
            }
        });

        return redirect()->route('admin.contact.index')->with('success', 'Contact updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $this->MasterAgentRoleCheck();
        if (! $contact) {
            return redirect()->back()->with('error', 'Contact Not Found');
        }
        $this->FeaturePermission($contact->agent_id);
        $contact->delete();

        return redirect()->back()->with('success', 'Contact Deleted Successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\User;
use App\Models\Merk;
use App\Models\UsersMerks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use hisorange\BrowserDetect\Facade as Browser;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::select(
                'users.id',
                'users.id_customer',
                'users.name',
                'users.email',
                'users.phone',
                'users.role_as',
                DB::raw("COALESCE(GROUP_CONCAT(merks.name SEPARATOR ', '), '-') as brands")
            )
            ->leftJoin('users_merks', 'users.id_customer', '=', 'users_merks.id_customer')
            ->leftJoin('merks', 'users_merks.id_merks', '=', 'merks.id')
            ->groupBy('users.id', 'users.id_customer', 'users.name', 'users.email', 'users.phone', 'users.role_as');

        // Filter berdasarkan role (jika perlu)
        if (!$request->has('show_all')) {
            $query->where('users.role_as', 0); // hanya non-admin
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', '%' . $search . '%')
                  ->orWhere('users.id_customer', 'like', '%' . $search . '%')
                  ->orWhere('users.email', 'like', '%' . $search . '%')
                  ->orWhere('users.phone', 'like', '%' . $search . '%');
            });
        }

        $merks = Merk::orderBy('name')->get();

        $users = $query
            ->orderBy('users.name', 'ASC')
            ->paginate(10)
            ->appends(['search' => $request->search]);

        if ($request->ajax()) {
            return view('users.partials.table', compact('users'))->render();
        }

        return view('users.index', compact('users', 'merks'));
    }

    /**
     * Store a newly created user.
     */
    public function store(UserFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();

            // Create user
            $user = new User();
            $user->id_customer = $validatedData['id_customer'];
            $user->name = $validatedData['name'];
            $user->password = Hash::make($validatedData['password']);
            $user->role_as = 0; // default user role
            $user->save();

            // Attach merks to user
            if ($request->has('merks') && is_array($request->merks)) {
                foreach ($request->merks as $merkId) {
                    UsersMerks::create([
                        'id_customer' => $user->id_customer,
                        'id_merks' => $merkId,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    /**
     * Show user data for editing.
     */
    public function edit($id)
    {
        $user = User::with(['merks' => function($query) {
            $query->select('merks.id', 'merks.name');
        }])->findOrFail($id);

        $userMerks = $user->merks->pluck('id')->toArray();
        
        return response()->json([
            'id' => $user->id,
            'id_customer' => $user->id_customer,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'merks' => $userMerks,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UserFormRequest $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $validatedData = $request->validated();

            // Update user data
            $user->id_customer = $validatedData['id_customer'];
            $user->name = $validatedData['name'];
            
            // Update password if provided
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
            }
            
            $user->save();

            // Sync merks
            if ($request->has('merks')) {
                // Delete existing merks
                UsersMerks::where('id_customer', $user->id_customer)->delete();
                
                // Add new merks
                foreach ($request->merks as $merkId) {
                    UsersMerks::create([
                        'id_customer' => $user->id_customer,
                        'id_merks' => $merkId,
                    ]);
                }
            } else {
                // If no merks selected, remove all
                UsersMerks::where('id_customer', $user->id_customer)->delete();
            }

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $idCustomer = $user->id_customer;
            
            // Delete related merks first
            UsersMerks::where('id_customer', $idCustomer)->delete();
            
            // Delete user
            $user->delete();

            DB::commit();

            return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Toggle user status (aktif/nonaktif)
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status = $user->status == 1 ? 0 : 1;
            $user->save();

            $status = $user->status == 1 ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()->back()->with('success', "Status user berhasil {$status}");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengubah status user');
        }
    }
}
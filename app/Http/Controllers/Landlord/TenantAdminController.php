<?php

    namespace App\Http\Controllers\Landlord;

    use App\Http\Controllers\Controller;
    use App\Models\Tenant;
    use App\Services\TenantProvisioner;
    use Illuminate\Http\Request;

    class TenantAdminController extends Controller
    {
        public function index()
        {
            $tenants = Tenant::query()->orderBy('id')->get();
            return view('landlord.tenants.index', compact('tenants'));
        }

        public function create()
        {
            return view('landlord.tenants.create');
        }

        public function store(Request $request)
        {
            $data = $request->validate([
                'id'       => ['required','alpha_dash','max:50','unique:tenants,id'],
                'name'     => ['required','string','max:255'],
                'db_name'  => ['required','string','max:191'],
                'db_user'  => ['nullable','string','max:191'],
                'db_pass'  => ['nullable','string','max:191'],
                'is_active'=> ['nullable','boolean'],
                'admin_email'    => ['nullable','email'],
                'admin_password'  => ['nullable','string','min:6'],
            ]);

            $tenant = Tenant::create([
                'id'        => $data['id'],
                'name'      => $data['name'],
                'db_name'   => $data['db_name'],
                'db_user'   => $data['db_user'] ?? env('TENANT_DB_USERNAME'),
                'db_pass'   => $data['db_pass'] ?? env('TENANT_DB_PASSWORD', ''),
                'is_active' => $data['is_active'] ?? true,
            ]);

            TenantProvisioner::make()->provision($tenant, [
                'create_database' => true,
                'migrate'         => true,
                'seed_admin'      => filled($data['admin_email'] ?? null),
                'admin_email'     => $data['admin_email'] ?? null,
                'admin_password'  => $data['admin_password'] ?? 'password',
            ]);

            return redirect()->route('landlord.tenants.index')
                ->with('success', "Tenant {$tenant->id} skapad och provisionerad.");
        }

        public function provision(Tenant $tenant)
        {
            TenantProvisioner::make()->provision($tenant, [
                'create_database' => true,
                'migrate'         => true,
            ]);

            return back()->with('success', "Tenant {$tenant->id} provisionerad.");
        }
    }

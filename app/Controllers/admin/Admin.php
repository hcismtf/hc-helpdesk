<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\RequestTypeModel;
use App\Models\PermissionsModel;
use App\Models\TiketTransactionsModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use DateTime;

class Admin extends BaseController
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    public function login()
    {
        if (session('isLoggedIn')) {
            return redirect()->to('/admin/dashboard');
        }
        return view('admin/login');
    }

    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Superadmin login (config)
        $superadminConfig = new \Config\Superadmin();
        if (
            $username === $superadminConfig->username &&
            $password === $superadminConfig->password_plain
        ) {
            session()->set([
                'isLoggedIn' => true,
                'role' => 'superadmin',
                'username' => $username,
                'user_permissions' => ['dashboard', 'tickets', 'user_management', 'system_settings'] // full access
            ]);
            return redirect()->to('/admin/dashboard');
        }

        // User login dari database
        $userModel = new \App\Models\UserModel();
        $user = $userModel
            ->where('email', $username)
            ->orWhere('name', $username)
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            // Ambil permission dari role
            $roleDetailModel = new \App\Models\RoleDetailModel();
            $rolePermissionsModel = new \App\Models\RolePermissionsModel();
            $permissionsModel = new PermissionsModel();

            $roleDetail = $roleDetailModel->where('user_id', $user['id'])->first();
            $roleId = $roleDetail['role_id'] ?? null;

            $rolePerms = $rolePermissionsModel->where('role_id', $roleId)->findAll();
            $userPermissions = [];
            foreach ($rolePerms as $rp) {
                $perm = $permissionsModel->find($rp['permission_id']);
                if ($perm) $userPermissions[] = $perm['code'];
            }

            $userModel->update($user['id'], [
                'last_login_time' => date('Y-m-d H:i:s')
            ]);
            session()->set([
                'isLoggedIn' => true,
                'role' => 'user',
                'username' => $user['name'],
                'user_id' => $user['id'],
                'user_permissions' => $userPermissions
            ]);
            return redirect()->to('/admin/dashboard');
        }

        // Jika gagal login
        return redirect()->back()->with('error', 'Username atau password salah');
    }

    public function dashboard()
    {
        // Cek sudah login
        if (!session('isLoggedIn')) {
            return redirect()->to('/admin/login');
        }

        $ticketModel = new \App\Models\TicketModel();
        $slaModel = new \App\Models\SlaModel();

        // Ambil filter dari GET
        $type = $this->request->getGet('type');
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;

        // Query builder: ambil semua tiket yang status-nya bukan closed
        $builder = $ticketModel->where('ticket_status !=', 'closed');
        if ($type) $builder->where('req_type', $type);
        if ($start) $builder->where('created_date >=', $start . ' 00:00:00');
        if ($end) $builder->where('created_date <=', $end . ' 23:59:59');

        // Pagination
        $openTickets = $builder->orderBy('created_date', 'DESC')->paginate($perPage, 'tickets', $page);
        $pager = $ticketModel->pager;

        // Statistik status
        $openCount = $ticketModel->where('ticket_status', 'open')->countAllResults();
        $inProgressCount = $ticketModel->where('ticket_status', 'in_progress')->countAllResults();
        $doneCount = $ticketModel->where('ticket_status', 'closed')->countAllResults();
        $totalCount = $ticketModel->countAllResults();

        // Ambil semua SLA untuk mapping (by priority & request_type jika ada)
        $slaList = $slaModel->findAll();
        $slaMap = [];
        foreach ($slaList as $sla) {
            $slaMap[$sla['priority']] = $sla;
        }

        // Inject SLA ke tiket dan hitung due_date jika belum ada
        foreach ($openTickets as &$ticket) {
            $priority = $ticket['ticket_priority'];
            $sla = $slaMap[$priority] ?? null;
            if ($sla) {
                $ticket['sla_response_time'] = $sla['response_time'];
                $ticket['sla_resolution_time'] = $sla['resolution_time'];
                // Hitung due_date jika belum ada
                if (empty($ticket['due_date'])) {
                    $created = new DateTime($ticket['created_date']);
                    $created->modify('+' . $sla['resolution_time'] . ' hours');
                    $ticket['due_date'] = $created->format('Y-m-d H:i:s');
                }
            } else {
                $ticket['sla_response_time'] = 24;
                $ticket['sla_resolution_time'] = 24;
                if (empty($ticket['due_date'])) {
                    $created = new DateTime($ticket['created_date']);
                    $created->modify('+24 hours');
                    $ticket['due_date'] = $created->format('Y-m-d H:i:s');
                }
            }
        }
        unset($ticket);

        // Untuk dropdown type
        $types = $ticketModel->select('req_type')->distinct()->findAll();

        // Ambil semua tiket yang sudah closed untuk statistik
        $closedTickets = $ticketModel->where('ticket_status', 'closed')->findAll();

        $totalResponse = 0;
        $totalResolution = 0;
        $slaCompliant = 0;
        $countClosed = count($closedTickets);

        foreach ($closedTickets as $t) {
            // Hitung response time (first_response_at - created_date)
            if (!empty($t['first_response_at']) && !empty($t['created_date'])) {
                $responseTime = strtotime($t['first_response_at']) - strtotime($t['created_date']);
                $totalResponse += $responseTime;
            }
            // Hitung resolution time (finish_date - created_date)
            if (!empty($t['finish_date']) && !empty($t['created_date'])) {
                $resolutionTime = strtotime($t['finish_date']) - strtotime($t['created_date']);
                $totalResolution += $resolutionTime;
                // SLA Compliance: finish_date <= due_date
                if (!empty($t['due_date']) && strtotime($t['finish_date']) <= strtotime($t['due_date'])) {
                    $slaCompliant++;
                }
            }
        }

        // AVG Response Time
        $avgResponse = $countClosed ? $totalResponse / $countClosed : 0;
        // AVG Resolution Time
        $avgResolution = $countClosed ? $totalResolution / $countClosed : 0;
        // SLA Compliance Rate
        $slaRate = $countClosed ? round(($slaCompliant / $countClosed) * 100) : 0;

        // Helper untuk format detik ke d h m
        function formatDuration($seconds) {
            $d = floor($seconds / 86400);
            $h = floor(($seconds % 86400) / 3600);
            $m = floor(($seconds % 3600) / 60);
            return sprintf('%02d d %02d h %02d m', $d, $h, $m);
        }

        $avgResponseStr = formatDuration($avgResponse);
        $avgResolutionStr = formatDuration($avgResolution);


        return view('admin/dashboard', [
            'openCount' => $openCount,
            'inProgressCount' => $inProgressCount,
            'doneCount' => $doneCount,
            'totalCount' => $totalCount,
            'openTickets' => $openTickets,
            'pager' => $pager,
            'types' => $types,
            'perPage' => $perPage,
            'page' => $page,
            'type' => $type,
            'start' => $start,
            'end' => $end,
            'username' => session('username'),
            'role' => session('role'),
            'avgResponseStr' => $avgResponseStr,
            'avgResolutionStr' => $avgResolutionStr,
            'slaRate' => $slaRate
        ]);
    }

    public function Ticket_dashboard()
    {
        $model = new \App\Models\TicketModel();
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');

        // Query builder: filter berdasarkan tanggal created_date & priority
        $priority = $this->request->getGet('priority');
        $builder = $model;
        if ($start) $builder = $builder->where('created_date >=', $start . ' 00:00:00');
        if ($end) $builder = $builder->where('created_date <=', $end . ' 23:59:59');
        if ($priority) $builder = $builder->where('ticket_priority', $priority);

        $ticketsRaw = $builder->orderBy('created_date', 'DESC')->paginate($perPage, 'tickets', $page);

        $encrypter = \Config\Services::encrypter();
        $tickets = [];
        foreach ($ticketsRaw as $ticket) {
            $nip_decrypted = '';
            if (!empty($ticket['nip_encrypted'])) {
                try {
                    $nip_decrypted = $encrypter->decrypt(hex2bin($ticket['nip_encrypted']));
                } catch (\Exception $e) {
                    $nip_decrypted = '[Invalid]';
                }
            }
            $ticket['emp_nip'] = $nip_decrypted;
            $tickets[] = $ticket;
        }

        $pager = $model->pager;

        return view('admin/Ticket_dashboard', [
            'tickets' => $tickets,
            'pager' => $pager,
            'perPage' => $perPage,
            'active' => 'tickets',
            'start' => $start,
            'end' => $end,
            'priority' => $priority
        ]);
    }
    public function Ticket_detail($id)
    {
        $model = new \App\Models\TicketModel();
        $ticket = $model->find($id);
        $userModel = new \App\Models\UserModel();
        $users = $userModel->where('status', 'active')->findAll();
        // Ticket Attachment
        $attModel = new \App\Models\TicketAttModel();
        $attachmentsRaw = $attModel->where('tiket_trx_id', $id)->findAll();
        $attachments = [];

        // Decrypt NIP jika perlu
        $encrypter = \Config\Services::encrypter();
        if (!empty($ticket['nip_encrypted'])) {
            try {
                $ticket['emp_nip'] = $encrypter->decrypt(hex2bin($ticket['nip_encrypted']));
            } catch (\Exception $e) {
                $ticket['emp_nip'] = '[Invalid]';
            }
        }

        foreach ($attachmentsRaw as $att) {
            try {
                $file_name = $encrypter->decrypt(hex2bin($att['file_name']));
                $file_path = $encrypter->decrypt(hex2bin($att['file_path']));
            } catch (\Exception $e) {
                $file_name = '[Invalid]';
                $file_path = '';
            }
            $attachments[] = [
                'file_name' => $file_name,
                'file_path' => $file_path
            ];
        }

        // Ambil reply terakhir dari tiket_transactions untuk assigned_to
        $trxModel = new TiketTransactionsModel();
        $lastTrx = $trxModel->where('tiket_trx_id', $id)->orderBy('created_at', 'desc')->first();

        $assignedName = '-';
        if (!empty($lastTrx['assigned_to'])) {
            foreach ($users as $u) {
                if ($u['id'] == $lastTrx['assigned_to']) {
                    $assignedName = $u['name'];
                    break;
                }
            }
        }

        // Ambil semua replies
        $repliesRaw = $trxModel->where('tiket_trx_id', $id)->orderBy('created_at', 'asc')->findAll();
        $replies = [];
        foreach ($repliesRaw as $r) {
            $author = '';
            foreach ($users as $u) {
                if ($u['id'] == $r['user_id']) {
                    $author = $u['name'];
                    break;
                }
            }
            $replies[] = [
                'author'     => $author ?: 'User',
                'created_at' => $r['created_at'],
                'text'       => $r['reply']
            ];
        }
        $hasReply = count($replies) > 0;

        return view('admin/Ticket_detail', [
            'ticket'       => $ticket,
            'users'        => $users,
            'replies'      => $replies,
            'attachments'  => $attachments,
            'hasReply'     => $hasReply,
            'assignedName' => $assignedName // <-- dari tiket_transactions terakhir
        ]);
    }
    public function system_settings()
    {
        $faqModel = new \App\Models\FaqModel();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll(10);

        $requestTypeModel = new RequestTypeModel();
        $requestTypes = $requestTypeModel->findAll();

        $slaModel = new \App\Models\SlaModel();
        $usedRequestTypeIds = array_column($slaModel->findAll(), 'request_type_id');

        $permissionsModel = new PermissionsModel();
        $permissions = $permissionsModel->orderBy('name', 'asc')->findAll();

        // Tambahkan variabel page dan totalPages jika view butuh
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $total = $faqModel->countAll();
        $totalPages = ceil($total / $perPage);

        $editFaq = null;
        $editId = $this->request->getGet('edit_faq_id');
        if ($editId) {
            $editFaq = $faqModel->find($editId);
        }

        $data = [
            'username' => session('username') ?? '[user name]',
            'role' => session('role') ?? 'Superadmin',
            'faqs' => $faqs,
            'editFaq' => $editFaq,
            'permissions' => $permissions,
            'requestTypes' => $requestTypes,
            'usedRequestTypeIds' => $usedRequestTypeIds,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages
        ];
        return view('admin/System_settings', $data);
    }
    public function add_faq()
    {
        $question = $this->request->getPost('question');
        $answer = $this->request->getPost('answer');
        $created_by = session('username') ?? 'admin';

        $faqModel = new \App\Models\FaqModel();
        $faqModel->insert([
            'question' => $question,
            'answer' => $answer,
            'created_by' => $created_by,
            'created_date' => date('Y-m-d H:i:s')
        ]);
        return $this->response->setJSON(['success' => true]);
    }
    public function edit_faq()
    {
        $id = $this->request->getPost('id');
        $question = $this->request->getPost('question');
        $answer = $this->request->getPost('answer');
        $modified_by = session('username') ?? 'admin';
        $modified_date = date('Y-m-d H:i:s');

        $faqModel = new \App\Models\FaqModel();
        $faqModel->update($id, [
            'question' => $question,
            'answer' => $answer,
            'modified_by' => $modified_by,
            'modified_date' => $modified_date
        ]);
        return $this->response->setJSON(['success' => true]);
    }
    public function get_faq_list()
    {
        $faqModel = new \App\Models\FaqModel();
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;

        $total = $faqModel->countAll();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll($perPage, ($page-1)*$perPage);
        $totalPages = ceil($total / $perPage);

        return view('admin/faq_list', [
            'faqs' => $faqs,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage
        ]);
    }
    public function delete_faq()
    {
        $id = $this->request->getPost('id');
        $faqModel = new \App\Models\FaqModel();
        $faqModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
    
    public function add_user_role()
    {
        $name = $this->request->getPost('name');
        $permissions = $this->request->getPost('permissions'); // array of permission_id
        $created_by = session('username') ?? 'admin';
        $created_date = date('Y-m-d H:i:s');

        $roleModel = new \App\Models\RoleModel();
        $rolePermissionsModel = new \App\Models\RolePermissionsModel();
        $encrypter = \Config\Services::encrypter();

        // Simpan role dulu
        $roleId = $roleModel->insert([
            'name' => $name,
            'created_by' => $created_by,
            'created_date' => $created_date
        ]);

        // Simpan ke role_permissions (bisa banyak permission)
        if (is_array($permissions)) {
            foreach ($permissions as $permissionId) {
                if ($permissionId) {
                    // Simpan ID asli (integer), BUKAN hasil enkripsi!
                    $rolePermissionsModel->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionId
                    ]);
                }
            }
        }

        return $this->response->setJSON(['success' => true]);
    }
    public function edit_user_role()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $permissions = $this->request->getPost('permissions'); // array of permission_id

        $roleModel = new \App\Models\RoleModel();
        $rolePermissionsModel = new \App\Models\RolePermissionsModel();

        // Update nama role
        $roleModel->update($id, [
            'name' => $name,
            'modified_by' => session('username'),
            'modified_date' => date('Y-m-d H:i:s')
        ]);

        // Hapus semua permission lama
        $rolePermissionsModel->where('role_id', $id)->delete();

        // Insert permission baru
        if (is_array($permissions)) {
            foreach ($permissions as $permissionId) {
                if ($permissionId) {
                    $rolePermissionsModel->insert([
                        'role_id' => $id,
                        'permission_id' => $permissionId
                    ]);
                }
            }
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function delete_user_role()
    {
        $id = $this->request->getPost('id');
        $roleModel = new \App\Models\RoleModel();
        $rolePermissionsModel = new \App\Models\RolePermissionsModel();

        // Hapus relasi permissions dulu
        $rolePermissionsModel->where('role_id', $id)->delete();

        // Hapus role
        $deleted = $roleModel->delete($id);

        return $this->response->setJSON(['success' => (bool)$deleted]);
    }    
    public function get_user_role_list()
    {
        $roleModel = new \App\Models\RoleModel();
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;

        $total = $roleModel->countAll();
        $roles = $roleModel->orderBy('id', 'desc')->findAll($perPage, ($page-1)*$perPage);
        $totalPages = ceil($total / $perPage);

        $roleDetailModel = new \App\Models\RoleDetailModel();
        $rolePermissionsModel = new \App\Models\RolePermissionsModel();
        $permissionsModel = new PermissionsModel();

        foreach ($roles as &$role) {
            $details = $roleDetailModel->where('role_id', $role['id'])->findAll();
            $role['users'] = $details;

            // Ambil permission untuk role ini
            $rolePerms = $rolePermissionsModel->where('role_id', $role['id'])->findAll();
            $permNames = [];
            foreach ($rolePerms as $rp) {
                $perm = $permissionsModel->find($rp['permission_id']);
                if ($perm) $permNames[] = $perm['name'];
            }
            $role['menu_access'] = implode(', ', $permNames);
        }

        return view('admin/user_role_list', [
            'roles' => $roles,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage
        ]);
    }
    public function get_request_type_list()
    {
        $requestTypeModel = new RequestTypeModel;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;

        $total = $requestTypeModel->countAll();
        $types = $requestTypeModel->orderBy('id', 'desc')->findAll($perPage, ($page-1)*$perPage);
        $totalPages = ceil($total / $perPage);

        return view('admin/request_type', [
            'types' => $types,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage
        ]);
    }
    public function get_request_type_detail($id)
    {
        $model = new RequestTypeModel();
        $type = $model->find($id);
        if ($type) {
            return $this->response->setJSON(['success' => true, 'data' => $type]);
        }
        return $this->response->setJSON(['success' => false]);
    }

 
    public function add_request_type()
    {
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');
        $created_by = session('username') ?? 'admin';

        $requestTypeModel = new RequestTypeModel;
        $requestTypeModel->insert([
            'name' => $name,
            'description' => $description,
            'status' => $status, // enum: 'Active' atau 'In Active'
            'created_by' => $created_by,
            'created_date' => date('Y-m-d H:i:s'),
            'modified_by' => $created_by,
            'modified_date' => date('Y-m-d H:i:s')
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function delete_request_type()
    {
        $id = $this->request->getPost('id');
        // $slaModel = new \App\Models\SlaModel();

        // Hapus pengecekan ini jika kolom tidak ada:
        // $used = $slaModel->where('request_type_id', $id)->countAllResults();

        // Langsung hapus request type
        $requestTypeModel = new RequestTypeModel();
        $requestTypeModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
    public function edit_request_type()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');
        $modified_by = session('username') ?? 'admin';
        $modified_date = date('Y-m-d H:i:s');

        $model = new RequestTypeModel();
        $model->update($id, [
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'modified_by' => $modified_by,
            'modified_date' => $modified_date
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function update_request_type()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');
        $status = $this->request->getPost('status');

        $requestTypeModel = new RequestTypeModel();
        $requestTypeModel->update($id, [
            'name' => $name,
            'description' => $description,
            'status' => $status
        ]);
        // Redirect ke frame Request Type di System_settings
        return redirect()->to(base_url('admin/System_settings?tab=request-type'));
    }
    public function add_sla()
    {
        // $request_type_id = $this->request->getPost('request_type_id');
        $priority = $this->request->getPost('priority');
        $response_time = $this->request->getPost('response_time');
        $resolution_time = $this->request->getPost('resolution_time');
        $created_by = session('username') ?? 'admin';
        $created_date = date('Y-m-d H:i:s');

        $slaModel = new \App\Models\SlaModel();
        $slaModel->insert([
            // 'request_type_id' => $request_type_id,
            'priority' => $priority,
            'response_time' => $response_time,
            'resolution_time' => $resolution_time,
            'created_by' => $created_by,
            'created_date' => $created_date
        ]);
        return $this->response->setJSON(['success' => true]);
    }
    public function get_sla_list()
    {
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 10;
        $slaModel = new \App\Models\SlaModel();
        $slas = $slaModel->paginate($perPage, 'default', $page);
        $totalPages = $slaModel->pager->getPageCount();

        return view('admin/sla_settings', [
            'slas' => $slas,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages
        ]);
    }
    public function edit_sla()
    {
        $id = $this->request->getPost('id');
        $priority = $this->request->getPost('priority');
        $response_time = $this->request->getPost('response_time');
        $resolution_time = $this->request->getPost('resolution_time');
        $modified_by = session('username') ?? 'admin';
        $modified_date = date('Y-m-d H:i:s');

        $slaModel = new \App\Models\SlaModel();
        $slaModel->update($id, [
            'priority' => $priority,
            'response_time' => $response_time,
            'resolution_time' => $resolution_time,
            'modified_by' => $modified_by,
            'modified_date' => $modified_date
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function delete_sla()
    {
        $id = $this->request->getPost('id');
        $slaModel = new \App\Models\SlaModel();
        $slaModel->delete($id);
        return $this->response->setJSON(['success' => true]);
    }
    // public function get_used_request_types()
    // {
    //     $slaModel = new \App\Models\SlaModel();
    //     $usedRequestTypeIds = array_column($slaModel->findAll(), 'request_type_id');
    //     return $this->response->setJSON(['used' => $usedRequestTypeIds]);
    // }

    public function user_mgt()
    {
        $roleModel = new \App\Models\RoleModel();
        $roles = $roleModel->findAll();

        $userModel = new \App\Models\UserModel();
        // JOIN ke role_detail dan role untuk dapat nama role
        $users = $userModel
            ->select('users.*, role.name as role_name')
            ->join('role_detail', 'role_detail.user_id = users.id', 'left')
            ->join('role', 'role.id = role_detail.role_id', 'left')
            ->findAll();
        
        $permissionsModel = new PermissionsModel();
        $permissions = $permissionsModel->orderBy('id', 'desc')->findAll();
        return view('admin/user_mgt', [
            'active' => 'user_mgt',
            'roles' => $roles,
            'users' => $users,
            'permissions' => $permissions
        ]);
    }
    public function add_user()
    {
        $userModel = new \App\Models\UserModel();
        $roleDetailModel = new \App\Models\RoleDetailModel();

        $userData = [
            'name'        => $this->request->getPost('name'),
            'email'       => $this->request->getPost('email'),
            'password'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'status'      => $this->request->getPost('status'),
            'role_id'     => $this->request->getPost('role'), // simpan role_id langsung di tabel users
            'created_by'  => session('username') ?? 'system',
            'created_date'=> date('Y-m-d H:i:s'),
        ];

        $userId = $userModel->insert($userData);

        // Simpan ke role_detail
        $roleId = $this->request->getPost('role');
        if ($roleId) {
            $roleDetailModel->insert([
                'role_id' => $roleId,
                'user_id' => $userId,
                'created_by' => session('username') ?? 'system',
                'created_date' => date('Y-m-d H:i:s')
            ]);
        }

        // Ambil data user baru untuk update list di frontend
        $user = $userModel->find($userId);

        return $this->response->setJSON(['success' => true, 'user' => $user]);
    }
    public function delete_user()
    {
        $id = $this->request->getPost('id');
        $userModel = new \App\Models\UserModel();
        $roleDetailModel = new \App\Models\RoleDetailModel();

        // Hapus role_detail dulu
        $roleDetailModel->where('user_id', $id)->delete();

        // Hapus user
        $userModel->delete($id);

        return $this->response->setJSON(['success' => true]);
    }
    public function edit_user()
    {
        $id = $this->request->getPost('id');
        $username = $this->request->getPost('username');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $roleId = $this->request->getPost('role');
        $status = $this->request->getPost('status');
        $password = $this->request->getPost('password');

        $userModel = new \App\Models\UserModel();
        $roleDetailModel = new \App\Models\RoleDetailModel();

        $updateData = [
            'name' => $name,
            'email' => $email,
            'status' => $status,
            'modified_by' => session('username') ?? 'system',
            'modified_date' => date('Y-m-d H:i:s'),
        ];
        if (!empty($password)) {
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        if (!empty($username)) {
            $updateData['username'] = $username;
        }

        $userModel->update($id, $updateData);

        // Update role_detail
        $roleDetail = $roleDetailModel->where('user_id', $id)->first();
        if ($roleDetail) {
            $roleDetailModel->update($roleDetail['id'], [
                'role_id' => $roleId,
                'modified_by' => session('username') ?? 'system',
                'modified_date' => date('Y-m-d H:i:s')
            ]);
        } else {
            $roleDetailModel->insert([
                'role_id' => $roleId,
                'user_id' => $id,
                'created_by' => session('username') ?? 'system',
                'created_date' => date('Y-m-d H:i:s')
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }
    public function get_user()
    {
        $id = $this->request->getGet('id');
        $userModel = new \App\Models\UserModel();
        $roleDetailModel = new \App\Models\RoleDetailModel();

        $user = $userModel->where('id', $id)->first();
        $roleDetail = $roleDetailModel->where('user_id', $id)->first();

        if ($user) {
            $user['role_id'] = $roleDetail['role_id'] ?? '';
            return $this->response->setJSON(['success' => true, 'user' => $user]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }
    public function add_permission()
    {
        $name = $this->request->getPost('name');
        $code = $this->request->getPost('code');
        $model = new PermissionsModel();

        $data = [
            'name' => $name,
            'code' => $code,
            'created_by' => session('username'),
            'created_date' => date('Y-m-d H:i:s')
        ];
        $id = $model->insert($data);
        if ($id) {
            $permission = $model->find($id);
            return $this->response->setJSON(['success' => true, 'permission' => $permission]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    public function get_permission()
    {
        $id = $this->request->getGet('id');
        $model = new PermissionsModel();
        $permission = $model->find($id);
        if ($permission) {
            return $this->response->setJSON(['success' => true, 'permission' => $permission]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    public function edit_permission()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $code = $this->request->getPost('code');
        $model = new PermissionsModel();

        $data = [
            'name' => $name,
            'code' => $code,
            'modified_by' => session('username'),
            'modified_date' => date('Y-m-d H:i:s')
        ];
        $model->update($id, $data);
        $permission = $model->find($id);
        if ($permission) {
            return $this->response->setJSON(['success' => true, 'permission' => $permission]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }

    public function delete_permission()
    {
        $id = $this->request->getPost('id');
        $model = new PermissionsModel();
        if ($model->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false]);
        }
    }
    public function report_user()
    {
        $username = session('username') ?? '';
        $role = session('role') ?? '';

        // Data dummy
        $reportTypes = [
            ['id' => 1, 'name' => 'Monthly'],
            ['id' => 2, 'name' => 'Incident'],
            ['id' => 3, 'name' => 'Summary'],
        ];
        $reports = [
            [
                'type_name' => 'Monthly',
                'created_date' => '28/08/2025 15:43:23'
            ],
            [
                'type_name' => 'Incident',
                'created_date' => '27/08/2025 10:12:00'
            ]
        ];
        $page = 1;
        $perPage = 10;
        $totalPages = 3;

        // Tambahkan variabel $active
        return view('admin/report_user', [
            'username'    => $username,
            'role'        => $role,
            'reportTypes' => $reportTypes,
            'reports'     => $reports,
            'page'        => $page,
            'perPage'     => $perPage,
            'totalPages'  => $totalPages,
            'active'      => 'reports' // <--- ini penting untuk navbar
        ]);
    }
    public function send_reply($ticketId)
    {
        $replyText   = $this->request->getPost('reply');
        $status      = $this->request->getPost('status');
        $priority    = $this->request->getPost('priority');
        $assignedTo  = $this->request->getPost('assigned_to');

        // Jika assigned_to tidak dikirim dari form, ambil dari transaksi terakhir
        if (empty($assignedTo)) {
            $trxModel = new TiketTransactionsModel();
            $lastTrx = $trxModel->where('tiket_trx_id', $ticketId)->orderBy('created_at', 'desc')->first();
            $assignedTo = !empty($lastTrx['assigned_to']) ? $lastTrx['assigned_to'] : null;
        }

        $userId   = session('user_id');
        $role     = session('role');
        $username = session('username') ?? 'system';

        if ($role === 'superadmin') {
            $userId = 9999;
            $username = 'superadmin';
        }

        // Ambil SLA sesuai priority
        $slaModel = new \App\Models\SlaModel();
        $sla = $slaModel->where('priority', $priority)->first();
        $resolutionTime = $sla ? (int)$sla['resolution_time'] : 24; // default 24 jam jika tidak ada

        // Ambil ticket lama
        $ticketModel = new \App\Models\TicketModel();
        $ticket = $ticketModel->find($ticketId);

        // Hitung due_date baru
        $createdDate = $ticket['created_date'] ?? date('Y-m-d H:i:s');
        $dueDate = (new DateTime($createdDate))->modify('+' . $resolutionTime . ' hours')->format('Y-m-d H:i:s');

        // Isi first_response_at jika belum ada
        $firstResponseAt = $ticket['first_response_at'];
        if (empty($firstResponseAt)) {
            $firstResponseAt = date('Y-m-d H:i:s');
        }

        // Jika status closed, isi finish_date
        $finishDate = null;
        if ($status === 'closed') {
            $finishDate = date('Y-m-d H:i:s');
        }

        // Insert reply
        $trxModel = new TiketTransactionsModel();
        $trxModel->insert([
            'tiket_trx_id' => $ticketId,
            'user_id'      => $userId,
            'submitted_by' => $userId,
            'status'       => $status,
            'priority'     => $priority,
            'assigned_to'  => $assignedTo,
            'reply'        => $replyText,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        // Update ticket
        $updateData = [
            'ticket_status'     => $status,
            'ticket_priority'   => $priority,
            'assigned_to'       => $assignedTo,
            'due_date'          => $dueDate,
            'first_response_at' => $firstResponseAt,
            'modified_date'     => date('Y-m-d H:i:s'),
            'modified_by'       => $username
        ];
        if ($finishDate) {
            $updateData['finish_date'] = $finishDate;
        }
        $ticketModel->update($ticketId, $updateData);

        // --- KIRIM EMAIL ---
        // Ambil nama assigned_to
        $assignedName = '-';
        if ($assignedTo) {
            $userModel = new \App\Models\UserModel();
            $assignedUser = $userModel->find($assignedTo);
            if ($assignedUser) $assignedName = $assignedUser['name'];
        }

        // Template email
        if ($status === 'closed') {
            $emailBody = "
                Dear, {$ticket['emp_name']}<br><br>
                Terima kasih telah menunggu, tiket anda telah <b>selesai</b> dikerjakan, berikut adalah detail ticket anda:<br><br>
                <b>Request Type:</b> {$ticket['req_type']}<br>
                <b>Original Message:</b> {$ticket['message']}<br>
                <b>Feedback Admin:</b> {$replyText}<br><br>
                Jika masih ada yang ingin ditanyakan, silakan hubungi kami melalui email <b>muhammad.farras@mtf.co.id</b>.<br><br>
                Hormat kami,<br>
                Human Capital Division
            ";
        } else {
            $emailBody = "
                Dear, {$ticket['emp_name']}<br><br>
                Terima kasih telah mengajukan ticket di HC Helpdesk, berikut adalah detail ticket anda:<br><br>
                <b>Request Type:</b> {$ticket['req_type']}<br>
                <b>Original Message:</b> {$ticket['message']}<br>
                <b>Feedback Admin:</b> {$replyText}<br><br>
                Ticket anda telah di assign kepada <b>{$assignedName}</b>. Mohon ditunggu untuk update berikutnya.<br><br>
                Hormat kami,<br>
                Human Capital Division
            ";
        }

        $toEmail = $ticket['email'] ?? '';
        if ($toEmail) {
            // Pastikan PHPMailer sudah diinstall via composer
            require_once(ROOTPATH . 'vendor/phpmailer/phpmailer/src/PHPMailer.php');
            require_once(ROOTPATH . 'vendor/phpmailer/phpmailer/src/Exception.php');
            require_once(ROOTPATH . 'vendor/phpmailer/phpmailer/src/SMTP.php');

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = "smtp-mail.outlook.com";
                $mail->SMTPAuth = true;
                $mail->Username = "muhammad.farras@mtf.co.id";
                $mail->Password = "Tun4\$F1n@nc32025@#!-"; // isi password email Anda
                $mail->SMTPSecure = "tls";
                $mail->Port = 587;

                $mail->setFrom("muhammad.farras@mtf.co.id", "HC Helpdesk");
                $mail->addAddress($toEmail);

                $mail->Subject = "Ticket #" . $ticketId . " - HC Helpdesk";
                $mail->isHTML(true);
                $mail->Body = $emailBody;

                $mail->send();
            } catch (Exception $e) {
                // error_log('Mailer Error: ' . $mail->ErrorInfo);
            }
        }

        return redirect()->to('admin/Ticket_detail/' . $ticketId);
    }
}
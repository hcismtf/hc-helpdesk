<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\RequestTypeModel;

class Admin extends BaseController
{
    public function login()
    {
        return view('admin/login');
    }

    public function authenticate()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Cek superadmin pakai password_plain (sementara)
        if (
            $username === $this->superadmin->username &&
            $password === $this->superadmin->password_plain
        ) {
            session()->set([
                'isLoggedIn' => true,
                'role' => 'superadmin',
                'username' => $username
            ]);
            return redirect()->to('/admin/dashboard');
        }

        // Jika gagal login
        return redirect()->back()->with('error', 'Username atau password salah');
    }

    public function dashboard()
    {
        if (session('isLoggedIn') && session('role') === 'superadmin') {
            $ticketModel = new \App\Models\TicketModel();

            // Ambil filter dari GET
            $type = $this->request->getGet('type');
            $start = $this->request->getGet('start');
            $end = $this->request->getGet('end');
            $perPage = $this->request->getGet('per_page') ?? 10;
            $page = $this->request->getGet('page') ?? 1;

            // Query builder
            $builder = $ticketModel->where('ticket_status', 'open');
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

            // Untuk dropdown type
            $types = $ticketModel->select('req_type')->distinct()->findAll();

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
            ]);
        }
        return redirect()->to('/admin/login');
    }

    public function Ticket_dashboard()
    {
        $model = new \App\Models\TicketModel();
        $perPage = $this->request->getGet('per_page') ?? 10;
        $page = $this->request->getGet('page') ?? 1;
        
        $ticketsRaw = $model->orderBy('created_date', 'DESC')->paginate($perPage, 'tickets', $page);

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
        ]);
    }
    public function Ticket_detail($id)
    {
        $model = new \App\Models\TicketModel();
        $ticket = $model->find($id);

        // Decrypt NIP jika perlu
        $encrypter = \Config\Services::encrypter();
        if (!empty($ticket['nip_encrypted'])) {
            try {
                $ticket['emp_nip'] = $encrypter->decrypt(hex2bin($ticket['nip_encrypted']));
            } catch (\Exception $e) {
                $ticket['emp_nip'] = '[Invalid]';
            }
        }

        return view('admin/Ticket_detail', [
            'ticket' => $ticket,
        ]);
    }

    public function Ticket_update_status($id)
    {
        $status = $this->request->getPost('status');
        $priority = $this->request->getPost('priority');

        $ticketModel = new \App\Models\TicketModel();

        // Update status dan priority
        $ticketModel->update($id, [
            'ticket_status' => $status,
            'ticket_priority' => $priority,
            'modified_date' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('admin/Ticket_detail/' . $id);
    }

    public function system_settings()
    {
        $faqModel = new \App\Models\FaqModel();
        $faqs = $faqModel->orderBy('id', 'desc')->findAll(10); // Ambil 10 FAQ terbaru
        // Ganti 'Superadmin' dan '[user name]' dengan data session jika ingin dinamis
        $requestTypeModel = new RequestTypeModel();
        $requestTypes = $requestTypeModel->findAll();
        $slaModel = new \App\Models\SlaModel();
        $usedRequestTypeIds = array_column($slaModel->findAll(), 'request_type_id');
        $editFaq = null;
        $editId = $this->request->getGet('edit_faq_id');
        if ($editId) {
            $editFaq = $faqModel->find($editId);
        }
        $data = [
            'username' => session('username') ?? '[user name]',
            'role' => session('role') ?? 'Superadmin',
            'faqs' => $faqs,
            'editFaq' => $editFaq
        ];
        return view('admin/System_settings', ['requestTypes' => $requestTypes, 'usedRequestTypeIds' => $usedRequestTypeIds], $data);
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
        $permissions = $this->request->getPost('permissions'); // array
        $created_by = session('username') ?? 'admin';
        $created_date = date('Y-m-d H:i:s');

        // Simpan permission sebagai string (misal: "Tickets,System settings")
        $menu_access = is_array($permissions) ? implode(',', $permissions) : '';

        $roleModel = new \App\Models\RoleModel();
        $roleModel->insert([
            'name' => $name,
            'menu_access' => $menu_access,
            'created_by' => $created_by,
            'created_date' => $created_date
        ]);

        return $this->response->setJSON(['success' => true]);
    }
    public function edit_user_role()
    {
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $permissions = $this->request->getPost('permissions');
        $modified_by = session('username') ?? 'admin';
        $modified_date = date('Y-m-d H:i:s');
        $menu_access = is_array($permissions) ? implode(',', $permissions) : '';
        $roleModel = new \App\Models\RoleModel();
        $roleModel->update($id, [
            'name' => $name,
            'menu_access' => $menu_access,
            'modified_by' => $modified_by,
            'modified_date' => $modified_date
        ]);
        return $this->response->setJSON(['success' => true]);
    }

    public function delete_user_role()
    {
        $id = $this->request->getPost('id');
        $roleModel = new \App\Models\RoleModel();
        $roleModel->delete($id);
        return $this->response->setJSON(['success' => true]);
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
        foreach ($roles as &$role) {
            $details = $roleDetailModel->where('role_id', $role['id'])->findAll();
            $role['users'] = $details;
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
        $slaModel = new \App\Models\SlaModel();

        // Cek apakah request type sudah dipakai di SLA
        $used = $slaModel->where('request_type_id', $id)->countAllResults();
        if ($used > 0) {
            return $this->response->setJSON([
                'success' => false,
                'invalid' => true,
                'message' => 'Request Type sudah dipakai di SLA dan tidak bisa dihapus.'
            ]);
        }

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
}
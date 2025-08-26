<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

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
    

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}
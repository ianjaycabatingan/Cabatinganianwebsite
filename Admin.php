<?php
class Admin {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }public function authenticate($username, $password) {
        $result = $this->db->callProcedureSingle('AuthenticateAdmin', [$username]);
        $admin = $result[0] ?? null;
        
        if ($admin && $admin['password'] === $password) {
            // Remove password from array before returning
            unset($admin['password']);
            return $admin;
        }
        return false;
    }
    
    public function getDashboardStats() {
        $results = $this->db->callProcedureMulti('GetDashboardStats');
        return [
            'total_products' => $results[0][0]['total_products'] ?? 0,
            'total_transactions' => $results[1][0]['total_transactions'] ?? 0,
            'low_stock_count' => $results[2][0]['low_stock_count'] ?? 0
        ];
    }
}

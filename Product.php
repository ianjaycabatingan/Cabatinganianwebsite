<?php
class Product {
    private $db;
    private $id;
    private $name;
    private $description;
    private $category;
    private $price;
    private $stock_quantity;
    private $cost_price;
    private $expiry_date;
    private $status;

    public function __construct(Database $db) {
        $this->db = $db;
    }public function getAllProducts() {
        return $this->db->callProcedureSingle('GetAllProducts');
    }

    public function getProductById($id) {
        $result = $this->db->callProcedureSingle('GetProductById', [$id]);
        return $result[0] ?? null;
    }

    public function addProduct($name, $description, $category, $price, $stock, $cost_price, $expiry_date) {
        $result = $this->db->callProcedureSingle('AddProduct', [
            $name, $description, $category, $price, $stock, $cost_price, $expiry_date
        ]);
        return $result[0] ?? null;
    }

    public function updateProduct($id, $name, $description, $category, $price, $stock, $cost_price, $expiry_date) {
        $result = $this->db->callProcedureSingle('UpdateProduct', [
            $id, $name, $description, $category, $price, $stock, $cost_price, $expiry_date
        ]);
        return $result[0] ?? null;
    }

    public function deleteProduct($id) {
        $result = $this->db->callProcedureSingle('DeleteProduct', [$id]);
        return $result[0] ?? null;
    }
}

<?php
class InventoryMovement {
    private $db;
    private $id;
    private $product_id;
    private $movement_type;
    private $quantity;
    private $reference_type;
    private $reference_id;
    private $notes;

    public function __construct($db) {
        $this->db = $db;
    }

    public function logMovement($product_id, $movement_type, $quantity, $reference_type, $reference_id = null, $notes = '') {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO inventory_movements 
                (product_id, movement_type, quantity, reference_type, reference_id, notes)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$product_id, $movement_type, $quantity, $reference_type, $reference_id, $notes]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception("Error logging inventory movement: " . $e->getMessage());
        }
    }

    public function getProductMovements($product_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM inventory_movements 
                WHERE product_id = ? 
                ORDER BY created_at DESC
            ");
            $stmt->execute([$product_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error getting inventory movements: " . $e->getMessage());
        }
    }

    public function getLowStockProducts($threshold) {
        try {
            $stmt = $this->db->prepare("CALL GetLowStockProducts(?)");
            $stmt->execute([$threshold]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Error getting low stock products: " . $e->getMessage());
        }
    }
}

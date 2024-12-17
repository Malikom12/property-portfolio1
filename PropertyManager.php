<?php
class PropertyManager {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function addProperty($userId, $data) {
        $stmt = $this->pdo->prepare("INSERT INTO properties (user_id, title, address, price, description, latitude, longitude, status) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $userId,
            $data['title'],
            $data['address'],
            $data['price'],
            $data['description'],
            $data['latitude'],
            $data['longitude'],
            $data['status']
        ]);
    }
    
    public function getProperties($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getProperty($id, $userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM properties WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        return $stmt->fetch();
    }
}
?>
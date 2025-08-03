<?php
class UsuarioModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Autentica un usuario
     */
    public function autenticar(string $usuario, string $password): ?array {
        $query = "SELECT id, nombre, password_hash, rol, club_id FROM usuarios WHERE usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            unset($usuario['password_hash']); // No devolver el hash
            return $usuario;
        }

        return null;
    }

    /**
     * Obtiene un usuario por ID
     */
    public function obtenerPorId(int $id): ?array {
        $query = "SELECT id, nombre, usuario, rol, club_id FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
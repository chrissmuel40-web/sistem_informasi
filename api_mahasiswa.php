<?php
header('Content-Type: application/json');
require 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];
$reference = $database->getReference('presensi');

// MENGAMBIL DATA DARI FIREBASE (TAMPIL DI TABEL)
if ($method === 'GET') {
    $snapshot = $reference->getSnapshot();
    $data = $snapshot->getValue();
    echo json_encode($data ? $data : []);
} 
// MENAMBAH, MENGEDIT, ATAU MENGHAPUS DATA KE FIREBASE
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (isset($input['action']) && $input['action'] == 'delete') {
        $id = $input['id'];
        $reference->getChild($id)->remove();
        echo json_encode(['status' => 'success', 'message' => 'Data terhapus']);
    } elseif (isset($input['id'])) {
        $id = $input['id'];
        unset($input['id']);
        $reference->getChild($id)->set($input);
        echo json_encode(['status' => 'success', 'message' => 'Data diupdate']);
    } else {
        $reference->push($input);
        echo json_encode(['status' => 'success', 'message' => 'Data ditambahkan']);
    }
}
?>
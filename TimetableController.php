<?php
class TimetableController {


    // Function to fetch faculties from the database
    public function getFaculties() {
        $db = $this->connectDB();
        $query = $db->query("SELECT * FROM faculties");
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Function to fetch timetable based on module codes
    public function getTimetable($moduleCodes) {
        $db = $this->connectDB();
        $placeholders = rtrim(str_repeat('?,', count($moduleCodes)), ',');
        $query = $db->prepare("SELECT m.module_code, m.module_name, t.day_of_week, t.start_time, t.end_time, t.venue 
                               FROM modules m 
                               JOIN timetable t ON m.id = t.module_id 
                               WHERE m.module_code IN ($placeholders)");
        $query->execute($moduleCodes);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkModuleExists($moduleCode) {
        $db = $this->connectDB();
        $query = $db->prepare("SELECT COUNT(*) FROM modules WHERE UPPER(module_code) = ?"); // Use UPPER to ensure case insensitivity
        $query->execute([trim($moduleCode)]); // Trim any spaces
        return $query->fetchColumn() > 0; // Return true if exists
    }
    

    // Database connection (PDO)
    private function connectDB() {
        try {
            return new PDO("mysql:host=localhost;dbname=timetable", "root", "");  // Change credentials as needed
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
}

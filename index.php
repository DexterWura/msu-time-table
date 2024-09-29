<?php
require_once 'config/database.php'; // Adjust path to the new config file
require_once 'TimetableController.php'; // Ensure path is correct

$timetableController = new TimetableController();
$faculties = $timetableController->getFaculties();
$timetable = [];
$invalidModules = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Normalize and clean module codes
    $moduleCodes = array_map('trim', explode(',', strtoupper($_POST['module_codes'])));

    // Check for valid module codes
    $validModules = [];
    foreach ($moduleCodes as $code) {
        $moduleExists = $timetableController->checkModuleExists($code); // Check if module exists
        if ($moduleExists) {
            $validModules[] = $code; // Only add valid module codes
        } else {
            $invalidModules[] = $code; // Collect invalid module codes
        }
    }

    if (!empty($validModules)) {
        $timetable = $timetableController->getTimetable($validModules);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Timetable</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>


    <div class="container mt-5">

     <!-- Centered logo at the top -->
     <div class="text-center mb-4">
            <img src="assets/images/mymsu.png" alt="MSU Logo" class="img-fluid" style="max-width: 200px;">
        </div>

        <p><b>FACULTY OF BUSINESS SCIENCES - CDPs</b> (All Levels - 4TH QUARTER MODULES ONLY: 30 SEP - 08 NOVEMBER 2024 TIMETABLE )
            Accounting Sciences(AS), Economic Sciences(ES), Information & Marketing Sciences(IMS), Supply Chain, Insurance & Risk Sciences(SCIRS),
            Management Sciences(MS), and Tourism, Hospitality and Leisure Sciences(THLS)
        </p>
        
        <form method="POST" class="bg-light p-4 shadow-sm rounded">
            <div class="form-group">
                <label for="faculty">Select Faculty:</label>
                <select name="faculty" id="faculty" class="form-control" required>
                    <?php foreach ($faculties as $faculty): ?>
                        <option value="<?= $faculty['id'] ?>"><?= $faculty['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="module_codes">Enter Module Codes (comma separated):</label>
                <input type="text" name="module_codes" id="module_codes" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-primary">Get Timetable</button>
        </form>

        <?php if (!empty($invalidModules)): ?>
            <div class="alert alert-danger">
                The following module codes are invalid: <?= implode(', ', $invalidModules); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($timetable)): ?>
            <h2>Your Timetable</h2>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Module Code</th>
                        <th>Module Name</th>
                        <th>Day</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Venue</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($timetable as $row): ?>
                        <tr>
                            <td><?= $row['module_code'] ?></td>
                            <td><?= $row['module_name'] ?></td>
                            <td><?= $row['day_of_week'] ?></td>
                            <td><?= $row['start_time'] ?></td>
                            <td><?= $row['end_time'] ?></td>
                            <td><?= $row['venue'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <form action="generate_ics.php" method="POST">
                <input type="hidden" name="module_codes" value="<?= htmlspecialchars($_POST['module_codes']) ?>">
                <button type="submit" class="btn-primary">Add to Calendar</button>
            </form>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="text-center mt-5">
        <p>Developed by <a href="https://www.linkedin.com/in/dexterity-wurayayi-967a64230?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank">Dexterwura</a></p>
    </footer>

</body>
</html>


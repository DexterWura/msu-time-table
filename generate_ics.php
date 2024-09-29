<?php
require_once 'config/database.php'; // Adjust path to the new config file
require_once 'TimetableController.php'; // Ensure path is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $moduleCodes = explode(',', $_POST['module_codes']);
    $timetableController = new TimetableController();
    $timetable = $timetableController->getTimetable($moduleCodes);

    // Start ICS File
    $ics_content = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//YourApp//NONSGML v1.0//EN\r\nCALSCALE:GREGORIAN\r\nMETHOD:PUBLISH\r\n";
    
    foreach ($timetable as $event) {
        $start = new DateTime("2024-09-30 " . $event['start_time'], new DateTimeZone('Africa/Harare'));
        $end = new DateTime("2024-09-30 " . $event['end_time'], new DateTimeZone('Africa/Harare'));
        $day = strtoupper(substr($event['day_of_week'], 0, 2)); // e.g., 'MO' for Monday

        $ics_content .= "BEGIN:VEVENT\r\n";
        $ics_content .= "DTSTART;TZID=Africa/Harare:" . $start->format('Ymd\THis') . "\r\n";
        $ics_content .= "DTEND;TZID=Africa/Harare:" . $end->format('Ymd\THis') . "\r\n";
        $ics_content .= "RRULE:FREQ=WEEKLY;BYDAY=$day;UNTIL=20241108T235959Z\r\n";
        $ics_content .= "SUMMARY:{$event['module_code']} {$event['module_name']}\r\n";
        $ics_content .= "LOCATION:{$event['venue']}\r\n";
        $ics_content .= "END:VEVENT\r\n";
    }

    $ics_content .= "END:VCALENDAR\r\n";

    // Set headers for file download
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="timetable.ics"');
    
    echo $ics_content;
    exit;
}

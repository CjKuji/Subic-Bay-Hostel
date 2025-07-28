<?php
// Connect to MySQL database
$mysqli = new mysqli("localhost", "root", "", "subic_hostel_db");

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Define room sections and room numbers
$rooms = [
    ['section' => 'A-2F-a', 'room_type_id' => 2, 'room_numbers' => [1001, 1002, 1003, 1004, 1005, 1006, 1007, 1008]],
    ['section' => 'A-2F-b', 'room_type_id' => 2, 'room_numbers' => [1014, 1013, 1012, 1011, 1018, 1017, 1016, 1015]],
    ['section' => 'A-2F-c', 'room_type_id' => 2, 'room_numbers' => [1021, 1022, 1023, 1024, 1025, 1026, 1027, 1028]],
    ['section' => 'A-2F-d', 'room_type_id' => 2, 'room_numbers' => [1034, 1033, 1032, 1031, 1038, 1037, 1036, 1035]],
    ['section' => 'A-2F-e', 'room_type_id' => 1, 'room_numbers' => [3019, 3017, 3015, 3013, 3011, 3020, 3018, 3016, 3014, 3012, 3010, 3008, 3006, 3004, 3002, 3009, 3007, 3005, 3003, 3001]],
    ['section' => 'A-2F-h', 'room_type_id' => 1, 'room_numbers' => [3050, 3048, 3046, 3044, 3042, 3049, 3047, 3045, 3043, 3041, 3060, 3058, 3056, 3054, 3052, 3059, 3057, 3055, 3053, 3051]],
    ['section' => 'A-2F-g', 'room_type_id' => 1, 'room_numbers' => [3021, 3023, 3025, 3027, 3029, 3024, 3022, 3026, 3028, 3030, 3032, 3034, 3036, 3038, 3040, 3031, 3033, 3035, 3037, 3039]],
    ['section' => 'A-2F-k', 'room_type_id' => 1, 'room_numbers' => [3089, 3087, 3085, 3083, 3081, 3090, 3088, 3086, 3084, 3082, 3100, 3098, 3096, 3094, 3092, 3099, 3097, 3095, 3093, 3091]],
    ['section' => 'A-2F-j', 'room_type_id' => 1, 'room_numbers' => [3061, 3063, 3065, 3067, 3069, 3062, 3064, 3066, 3068, 3070, 3072, 3074, 3076, 3078, 3080, 3071, 3073, 3075, 3077, 3079]],
    ['section' => 'A-2F-m', 'room_type_id' => 1, 'room_numbers' => [3119, 3117, 3115, 3113, 3111, 3120, 3118, 3116, 3114, 3112, 3110, 3108, 3106, 3104, 3102, 3109, 3107, 3105, 3103, 3101]],
    ['section' => 'A-2F-n', 'room_type_id' => 1, 'room_numbers' => [3139, 3137, 3135, 3133, 3131, 3140, 3138, 3136, 3134, 3132, 3130, 3128, 3126, 3124, 3122, 3129, 3127, 3125, 3123, 3121]],
    ['section' => 'A-3F-a', 'room_type_id' => 2, 'room_numbers' => [1001, 1002, 1003, 1004, 1005, 1006, 1007, 1008]],
    ['section' => 'A-3F-b', 'room_type_id' => 2, 'room_numbers' => [1014, 1013, 1012, 1011, 1018, 1017, 1016, 1015]],
    ['section' => 'A-3F-c', 'room_type_id' => 2, 'room_numbers' => [1021, 1022, 1023, 1024, 1025, 1026, 1027, 1028]],
    ['section' => 'A-3F-d', 'room_type_id' => 2, 'room_numbers' => [1034, 1033, 1032, 1031, 1038, 1037, 1036, 1035]],
    ['section' => 'A-3F-e', 'room_type_id' => 1, 'room_numbers' => [3019, 3017, 3015, 3013, 3011, 3020, 3018, 3016, 3014, 3012, 3010, 3008, 3006, 3004, 3002, 3009, 3007, 3005, 3003, 3001]],
    ['section' => 'A-3F-h', 'room_type_id' => 1, 'room_numbers' => [3050, 3048, 3046, 3044, 3042, 3049, 3047, 3045, 3043, 3041, 3060, 3058, 3056, 3054, 3052, 3059, 3057, 3055, 3053, 3051]],
    ['section' => 'A-3F-g', 'room_type_id' => 1, 'room_numbers' => [3021, 3023, 3025, 3027, 3029, 3022, 3024, 3026, 3028, 3030, 3032, 3034, 3036, 3038, 3040, 3031, 3033, 3035, 3037, 3039]],
    ['section' => 'A-3F-k', 'room_type_id' => 1, 'room_numbers' => [3089, 3087, 3085, 3083, 3081, 3090, 3088, 3086, 3084, 3082, 3100, 3098, 3096, 3094, 3092, 3099, 3097, 3095, 3093, 3091]],
    ['section' => 'A-3F-j', 'room_type_id' => 1, 'room_numbers' => [3061, 3063, 3065, 3067, 3069, 3062, 3064, 3066, 3068, 3070, 3072, 3074, 3076, 3078, 3080, 3071, 3073, 3075, 3077, 3079]],
    ['section' => 'A-3F-m', 'room_type_id' => 1, 'room_numbers' => [3119, 3117, 3115, 3113, 3111, 3120, 3118, 3116, 3114, 3112, 3110, 3108, 3106, 3104, 3102, 3109, 3107, 3105, 3103, 3101]],
    ['section' => 'A-3F-n', 'room_type_id' => 1, 'room_numbers' => [3139, 3137, 3135, 3133, 3131, 3140, 3138, 3136, 3134, 3132, 3130, 3128, 3126, 3124, 3122, 3129, 3127, 3125, 3123, 3121]],
];

// Prepare the insert statement for rooms
$stmt_insert_room = $mysqli->prepare("INSERT INTO rooms (room_number, floor_id, room_type_id, is_occupied, created_at) VALUES (?, ?, ?, 0, NOW())");

// Prepare the floor query with a prepared statement to prevent SQL injection
$stmt_floor = $mysqli->prepare("SELECT id FROM floors WHERE section_name = ?");

// Loop through each section and insert rooms
foreach ($rooms as $room_section) {
    $section = $room_section['section'];
    $room_type_id = $room_section['room_type_id'];
    $room_numbers = $room_section['room_numbers'];

    // Fetch floor_id using prepared statement
    $stmt_floor->bind_param("s", $section);
    $stmt_floor->execute();
    $result = $stmt_floor->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        $floor_id = $row['id'];

        // Delete existing rooms for this section (optional depending on your use case)
        $mysqli->query("DELETE FROM rooms WHERE floor_id = $floor_id");

        // Insert rooms
        foreach ($room_numbers as $room_number) {
            $stmt_insert_room->bind_param("sii", $room_number, $floor_id, $room_type_id);
            if (!$stmt_insert_room->execute()) {
                echo "❌ Error inserting room $room_number in section $section: " . $stmt_insert_room->error . "<br>";
            }
        }

        echo "✅ Refreshed rooms for section $section<br>";
    } else {
        echo "⚠️ Section '$section' not found in 'floors' table. Skipped.<br>";
    }
}

// Close statements and connection
$stmt_insert_room->close();
$stmt_floor->close();
$mysqli->close();

echo "<br>🎉 Room insertion (with cleanup) complete.";
?>

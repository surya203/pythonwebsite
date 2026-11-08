<?php

header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");


/*
|--------------------------------------------------------------------------
| RECENT ACTIVITY DATA
|--------------------------------------------------------------------------
| Keep your activity data here.
| This file stays on the server.
|--------------------------------------------------------------------------
*/

$activities = [

    [
        "name" => "Rahul",
        "location" => "Hyderabad, India",
        "course" => "FullStack Python Development with AI",
        "time" => "2 minutes ago"
    ],

    [
        "name" => "Priya",
        "location" => "Vijayawada, India",
        "course" => "Data Analytics with AI",
        "time" => "4 minutes ago"
    ],

    [
        "name" => "Sai",
        "location" => "Bengaluru, India",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "6 minutes ago"
    ],

    [
        "name" => "Anjali",
        "location" => "Visakhapatnam, India",
        "course" => "Graphic Design & Video Editing with AI",
        "time" => "8 minutes ago"
    ],

    [
        "name" => "Karthik",
        "location" => "Warangal, India",
        "course" => "Basic to Advanced Java Programming with AI",
        "time" => "10 minutes ago"
    ],

    [
        "name" => "Suresh",
        "location" => "Chennai, India",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "12 minutes ago"
    ],

    [
        "name" => "Divya",
        "location" => "Mumbai, India",
        "course" => "Data Analytics with AI",
        "time" => "14 minutes ago"
    ],

    [
        "name" => "Arjun",
        "location" => "Pune, India",
        "course" => "FullStack Python Development with AI",
        "time" => "16 minutes ago"
    ],

    [
        "name" => "Sneha",
        "location" => "Delhi, India",
        "course" => "Graphic Design & Video Editing with AI",
        "time" => "18 minutes ago"
    ],

    [
        "name" => "Vamsi",
        "location" => "Guntur, India",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "20 minutes ago"
    ],

    [
        "name" => "Pooja",
        "location" => "Kochi, India",
        "course" => "Data Analytics with AI",
        "time" => "22 minutes ago"
    ],

    [
        "name" => "Nikhil",
        "location" => "Mysuru, India",
        "course" => "Basic to Advanced Java Programming with AI",
        "time" => "24 minutes ago"
    ],

    [
        "name" => "Harsha",
        "location" => "Tirupati, India",
        "course" => "FullStack Python Development with AI",
        "time" => "26 minutes ago"
    ],

    [
        "name" => "Keerthi",
        "location" => "Nellore, India",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "28 minutes ago"
    ],

    [
        "name" => "Rohit",
        "location" => "Ahmedabad, India",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "30 minutes ago"
    ],

    [
        "name" => "Akhil",
        "location" => "Dubai, UAE",
        "course" => "FullStack Python Development with AI",
        "time" => "32 minutes ago"
    ],

    [
        "name" => "Meghana",
        "location" => "Abu Dhabi, UAE",
        "course" => "Data Analytics with AI",
        "time" => "34 minutes ago"
    ],

    [
        "name" => "Varun",
        "location" => "Singapore",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "36 minutes ago"
    ],

    [
        "name" => "Riya",
        "location" => "London, UK",
        "course" => "Graphic Design & Video Editing with AI",
        "time" => "38 minutes ago"
    ],

    [
        "name" => "Aditya",
        "location" => "Toronto, Canada",
        "course" => "FullStack Python Development with AI",
        "time" => "40 minutes ago"
    ],

    [
        "name" => "Swathi",
        "location" => "Melbourne, Australia",
        "course" => "Data Analytics with AI",
        "time" => "42 minutes ago"
    ],

    [
        "name" => "Manoj",
        "location" => "Dallas, USA",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "44 minutes ago"
    ],

    [
        "name" => "Lakshmi",
        "location" => "New Jersey, USA",
        "course" => "FullStack Python Development with AI",
        "time" => "46 minutes ago"
    ],

    [
        "name" => "Abhishek",
        "location" => "San Francisco, USA",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "48 minutes ago"
    ],

    [
        "name" => "Neha",
        "location" => "Berlin, Germany",
        "course" => "Data Analytics with AI",
        "time" => "50 minutes ago"
    ],

    [
        "name" => "Rakesh",
        "location" => "Amsterdam, Netherlands",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "52 minutes ago"
    ],

    [
        "name" => "Sanjana",
        "location" => "Dublin, Ireland",
        "course" => "Graphic Design & Video Editing with AI",
        "time" => "54 minutes ago"
    ],

    [
        "name" => "Tarun",
        "location" => "Melbourne, Australia",
        "course" => "Basic to Advanced Java Programming with AI",
        "time" => "56 minutes ago"
    ],

    [
        "name" => "Naveen",
        "location" => "Doha, Qatar",
        "course" => "FullStack Python Development with AI",
        "time" => "58 minutes ago"
    ],

    [
        "name" => "Isha",
        "location" => "Muscat, Oman",
        "course" => "Data Analytics with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Deepak",
        "location" => "Riyadh, Saudi Arabia",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Shreya",
        "location" => "Kuwait City, Kuwait",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Ajay",
        "location" => "Auckland, New Zealand",
        "course" => "FullStack Python Development with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Nandini",
        "location" => "Johannesburg, South Africa",
        "course" => "Data Analytics with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Vivek",
        "location" => "Kuala Lumpur, Malaysia",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Tejas",
        "location" => "Tokyo, Japan",
        "course" => "Basic to Advanced Java Programming with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Madhuri",
        "location" => "Doha, Qatar",
        "course" => "Graphic Design & Video Editing with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Yash",
        "location" => "New York, USA",
        "course" => "FullStack Python Development with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Aparna",
        "location" => "London, UK",
        "course" => "Data Analytics with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Siddharth",
        "location" => "Toronto, Canada",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Bhavana",
        "location" => "Singapore",
        "course" => "Graphic Design & Video Editing with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Teja",
        "location" => "Secunderabad, India",
        "course" => "FullStack Python Development with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Sowmya",
        "location" => "Birmingham, UK",
        "course" => "Data Analytics with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Pranav",
        "location" => "Melbourne, Australia",
        "course" => "Basic to Advanced Java Programming with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Harini",
        "location" => "Washington, USA",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Ravi",
        "location" => "Sharjah, UAE",
        "course" => "DevOps & DevSecOps with MultiCloud & AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Kavya",
        "location" => "Toronto, Canada",
        "course" => "Graphic Design & Video Editing with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Mohan",
        "location" => "Hyderabad, India",
        "course" => "FullStack Python Development with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Pavani",
        "location" => "Vijayawada, India",
        "course" => "Data Analytics with AI",
        "time" => "1 hour ago"
    ],

    [
        "name" => "Surya",
        "location" => "Dubai, UAE",
        "course" => "FullStack AI with Generative AI & Agentic AI",
        "time" => "1 hour ago"
    ]

];


/*
|--------------------------------------------------------------------------
| RANDOM RECORD
|--------------------------------------------------------------------------
*/

if (empty($activities)) {

    echo json_encode([
        "success" => false
    ]);

    exit;
}


$randomIndex =
    array_rand($activities);


echo json_encode([
    "success" => true,
    "data" => $activities[$randomIndex]
]);

?>
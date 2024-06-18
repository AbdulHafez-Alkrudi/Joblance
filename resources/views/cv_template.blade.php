Sarah Tlass, [17/06/2024 05:03 م]
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Template</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .cv-container {
            background-color: #ffffff;
            width: 90%;
            max-width: 1200px;
            box-shadow: 0 4px 8px rgba(62, 3, 69, 0.1);
            border-radius: 8px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .cv-header {
            background-color: #402c50;
            color: #ffffff;
            text-align: center;
            padding: 20px 30px;
        }

        .cv-header h1 {
            margin: 0;
            font-size: 2.5em;
        }

        .cv-header p {
            margin: 5px 0 0;
            font-size: 1.2em;
        }

        .cv-content {
            display: flex;
            flex-wrap: wrap;
        }

        .cv-left {
            background-color: #402c50;
            color: #ffffff;
            width: 100%;
            max-width: 40%;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            box-sizing: border-box;
        }

        .cv-left img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin-bottom: 20px;
            border: 5px solid #ffffff;
        }

        .cv-left h2 {
            font-size: 1.8em;
            margin: 0;
            text-align: center;
        }

        .cv-left p {
            font-size: 1.2em;
            margin: 5px 0;
            text-align: center;
        }

        .cv-left .contact-info, .cv-left .language, .cv-left .skills {
            margin-top: 30px;
            text-align: center;
        }

        .cv-left .section-title {
            font-size: 1.5em;
            margin-bottom: 10px;
            border-bottom: 2px solid #ffffff;
            padding-bottom: 5px;
        }

        .cv-left ul {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }

        .cv-left ul li {
            margin: 5px 0;
            font-size: 1em;
        }

        .cv-right {
            width: 100%;
            max-width: 60%;
            padding: 30px;
            box-sizing: border-box;
        }

        .cv-right .section {
            margin-bottom: 20px;
        }

        .cv-right .section h2 {
            font-size: 1.8em;
            color: #773b85;
            border-bottom: 2px solid #502c4d;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }

        .cv-right .section p, .cv-right .section ul {
            margin: 10px 0;
            font-size: 1em;
            color: #555555;
        }

        .cv-right .section ul {
            list-style: none;
            padding: 0;
        }

        .cv-right .section ul li {
            margin: 10px 0;
        }

        .cv-right .section ul li strong {
            display: inline-block;
            width: 150px;
        }

        .cv-right .section ul li span {
            color: #555555;
        }

        .cv-footer {
            background-color: #402c50;
            color: #ffffff;
            text-align: center;
            padding: 10px 30px;
            font-size: 0.9em;
        }

        @media (max-width: 1024px) {
            .cv-left, .cv-right {
                width: 100%;
                max-width: 100%;
            }

            .cv-left img {
                width: 120px;
                height: 120px;
            }

            .cv-left h2 {
                font-size: 1.6em;
            }

            .cv-left p, .cv-left .section-title {
                font-size: 1.2em;
            }

            .cv-right .section h2 {
                font-size: 1.6em;
            }
        }

        @media (max-width: 768px) {
            .cv-content {
                flex-direction: column;
            }

            .cv-left {
                max-width: 100%;
                padding: 20px;
            }

            .cv-left img {
                width: 100px;
                height: 100px;
            }

            .cv-left h2 {
                font-size: 1.4em;
            }

            .cv-left p, .cv-left .section-title {
                font-size: 1em;
            }

            .cv-right {
                max-width: 100%;
                padding: 20px;
            }

            .cv-right .section h2 {
                font-size: 1.4em;
            }
        }

        @media (max-width: 480px) {
            .cv-left {
                padding: 15px;
            }

            .cv-left img {
                width: 80px;
                height: 80px;
            }

            .cv-left h2 {
                font-size: 1.2em;
            }

            .cv-left p, .cv-left .section-title {
                font-size: 0.9em;
            }

            .cv-right {
                padding: 15px;
            }

            .cv-right .section h2 {
                font-size: 1.2em;
            }
        }
    </style>
</head>
<body>
    <div class="cv-container">
        <div class="cv-header">
            <h1>Olivia Wilson</h1>
            <p>Marketing Manager</p>
        </div>
        <div class="cv-content">
            <div class="cv-left">
                <img src="https://via.placeholder.com/150" alt="Profile Photo">
                <h2>About Me</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum sit amet quam rhoncus, egestas dui eget, malesuada justo.</p>
                <div class="contact-info">
                    <div class="section-title">Contact Info</div>
                    <ul>
                        <li>📞 123-456-7890</li>
                        <li>📧 hello@reallygreatsite.com</li>
                        <li>📍 123 Anywhere St., Any City</li>
                    </ul>
                </div>
                <div class="language">
                    <div class="section-title">Language</div>
                    <ul>
                        <li>English</li>
                        <li>German (basic)</li>
                        <li>Spanish (basic)</li>
                    </ul>
                </div>
                <div class="skills">
                    <div class="section-title">Skills</div>
                    <ul>
                        <li>Management Skills</li>
                        <li>Creativity</li>
                        <li>Digital Marketing</li>
                        <li>Negotiation</li>
                        <li>Critical Thinking</li>
                        <li>Leadership</li>
                    </ul>
                </div>
            </div>
            <div class="cv-right">
                <div class="section">
                    <h2>Experience</h2>
                    <ul>
                        <li>
                            <strong>2020 - Present</strong>
                            <span>Business Consultant at Ginyard International Co.<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pharetra in lorem at laoreet. Donec hendrerit libero eget est tempor, quis tempus arcu elementum. In elementum elit ac dui tristique feugiat.</span>
                        </li>
                        <li>
                            <strong>2015 - 2020</strong>
                            <span>Junior/Business Consultant at Ingoude Company<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pharetra in lorem at laoreet. Donec hendrerit libero eget est tempor, quis tempus arcu elementum. In elementum elit ac dui tristique feugiat.</span>
                        </li>
                        <li>
                            <strong>2012 - 2015</strong>
                            <span>Junior/Business Consultant at Timmerman Industries<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pharetra in lorem at laoreet. Donec hendrerit libero eget est tempor, quis tempus arcu elementum. In elementum elit ac dui tristique feugiat.</span>
                        </li>
                        <li>
                            <strong>2010 - 2012</strong>
                            <span>Junior/Business Consultant at Ingoude Company<br>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam pharetra in lorem at laoreet. Donec hendrerit libero eget est tempor, quis tempus arcu elementum. In elementum elit ac dui tristique feugiat.</span>
                        </li>
                    </ul>
                </div>
                <div class="section">
                    <h2>Education</h2>
                    <ul>
                        <li>
                            <strong>2006 - 2008</strong>
                            <span>Borcelle Business School<br>Bachelor of Business Management</span>
                        </li>
                        <li>
                            <strong>2006 - 2008</strong>
                            <span>Larana Business School<br>Certificate in Digital Marketing</span>
                        </li>
                    </ul>
                </div>
                <div class="section">
                    <h2>Skills</h2>
                    <ul>
                        <li>
                            <strong>2006 - 2008</strong>
                            <span>Borcelle Business School<br>Bachelor of Business Management</span>
                        </li>
                        <li>
                            <strong>2006 - 2008</strong>
                            <span>Larana Business School<br>Certificate in Digital Marketing</span>
                        </li>
                    </ul>
                </div>
                <div class="section">
                    <h2>References</h2>
                    <ul>
                        <li>
                            <strong>Bailey Dupont</strong><br>
                            <span>Wardiere Inc. / CEO<br>📞 123-456-7890<br>📧 hello@reallygreatsite.com</span>
                        </li>
                        <li>
                            <strong>Harumi Kobayashi</strong><br>
                            <span>Wardiere Inc. / CEO<br>📞 123-456-7890<br>📧 hello@reallygreatsite.com</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="cv-footer">
            <p>&copy; 2024 Olivia Wilson. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resume — Cedric James Leala</title>
    <style>
        @page {
            margin: 28pt 36pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            font-size: 9.5pt;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .name {
            font-size: 18pt;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin: 0;
        }
        .title {
            font-size: 10.5pt;
            font-weight: bold;
            color: #0284c7;
            margin-top: 3px;
            margin-bottom: 6px;
        }
        .contact-info {
            font-size: 8.5pt;
            color: #475569;
        }
        .contact-info span {
            margin: 0 4px;
        }
        .section-title {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            border-bottom: 1.5px solid #cbd5e1;
            padding-bottom: 2px;
            margin-top: 12px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }
        .summary-text {
            text-align: justify;
            margin-bottom: 8px;
        }
        .skills-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .skills-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .skills-label {
            font-weight: bold;
            width: 155px;
            color: #334155;
        }
        .job-item {
            margin-bottom: 10px;
        }
        .job-header {
            width: 100%;
            margin-bottom: 2px;
        }
        .job-title {
            font-weight: bold;
            color: #0f172a;
            font-size: 10pt;
        }
        .job-company {
            color: #0284c7;
            font-weight: bold;
        }
        .job-date {
            float: right;
            font-weight: bold;
            color: #64748b;
            font-size: 8.5pt;
        }
        .job-desc {
            margin-top: 2px;
            text-align: justify;
        }
        ul.bullet-list {
            margin: 3px 0 6px 16px;
            padding: 0;
        }
        ul.bullet-list li {
            margin-bottom: 2px;
        }
        .personal-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        .personal-grid td {
            width: 50%;
            padding: 2px 0;
            vertical-align: top;
        }
        .ref-grid {
            width: 100%;
            border-collapse: collapse;
        }
        .ref-grid td {
            width: 50%;
            vertical-align: top;
            padding: 2px 0;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div class="header">
        <h1 class="name">Cedric James Leala</h1>
        <div class="title">Full Stack Developer | AI-Assisted Development (Claude Code)</div>
        <div class="contact-info">
            Sitio Gulod Muzon, Taytay, Rizal 1920 <span>|</span> 0938-854-2688 <span>|</span> cedricjamesleala128@gmail.com <span>|</span> github.com/cdrc1280
        </div>
    </div>

    <!-- Professional Summary -->
    <div class="section-title">Professional Summary</div>
    <div class="summary-text">
        Full Stack Developer with three years of experience shipping and maintaining six concurrently running production systems for TESDA, owning each end to end — schema, API, and UI. Core stack is Laravel, Filament, Vue.js, and MySQL, with additional front-end experience in React, TypeScript, and Inertia.js from concurrent freelance work. Uses Claude Code and other AI coding assistants as part of a daily build-test-ship workflow to move fast without sacrificing maintainability, and is comfortable taking a scoped problem from requirements to working, production-ready software independently.
    </div>

    <!-- Technical Skills -->
    <div class="section-title">Technical Skills</div>
    <table class="skills-table">
        <tr>
            <td class="skills-label">AI-Assisted Development:</td>
            <td>Hands-on daily use of Claude Code for scaffolding, debugging, and iterating on production code; also use ChatGPT and Gemini for code review and documentation.</td>
        </tr>
        <tr>
            <td class="skills-label">Proficient:</td>
            <td>HTML, CSS, SCSS, JavaScript, Laravel, Laravel Filament, Vue.js, Tailwind CSS, Bootstrap, Git, MySQL.</td>
        </tr>
        <tr>
            <td class="skills-label">Familiar:</td>
            <td>Inertia.js, TypeScript, React.js, REST API Integration, Livewire, Alpine.js.</td>
        </tr>
    </table>

    <!-- Work Experience -->
    <div class="section-title">Work Experience</div>

    <div class="job-item">
        <div class="job-header">
            <span class="job-title">IT MIS Developer (Full Stack)</span> — <span class="job-company">TESDA Taguig</span>
            <span class="job-date">Jul 2023 – Jun 2026</span>
            <div class="clear"></div>
        </div>
        <div class="job-desc">
            Shipped and maintained six concurrently running national production systems, owning each system end to end — schema, API, and UI:
        </div>
        <ul class="bullet-list">
            <li><strong>TESDA Profiling MIS</strong> (mis.tesda-clgeo.com): National profiling system with multi-agency access.</li>
            <li><strong>CBTVETO Landing Site</strong> (tesda-clgeo.com): Public LMS, TESDA directory, and document verification portal.</li>
            <li><strong>National Directory for CTECs</strong> (ctec.tesda-clgeo.com): Trainers' profiles and achievement tracker.</li>
            <li><strong>CBTVET LMS</strong> (lms.cbtveto.com): Student enrollment and learning materials portal.</li>
            <li><strong>Document Tracking System DTS</strong> (dts.tesda-clgeo.com): CLGEO document workflow monitoring.</li>
            <li><strong>Legislative Info System LIS</strong> (staging-lis.tesda.gov.ph): Tracks legislative bills and budget monitoring.</li>
        </ul>
    </div>

    <div class="job-item">
        <div class="job-header">
            <span class="job-title">Front-End Developer (Freelance)</span> — <span class="job-company">Keen Eye Software Solutions</span>
            <span class="job-date">Oct 2024 – Mar 2025</span>
            <div class="clear"></div>
        </div>
        <div class="job-desc">
            Developed responsive web interfaces using React.js, TypeScript, Vue.js, and Inertia.js bridge integration for client projects.
        </div>
    </div>

    <div class="job-item">
        <div class="job-header">
            <span class="job-title">Web Developer</span> — <span class="job-company">Point One Integrated Technologies Inc.</span>
            <span class="job-date">Sep 2022 – Mar 2023</span>
            <div class="clear"></div>
        </div>
        <div class="job-desc">
            Maintained internal web portals, integrated RESTful API endpoints, optimized MySQL database queries, and implemented UI bug fixes.
        </div>
    </div>

    <!-- Education -->
    <div class="section-title">Education</div>
    <div class="job-item">
        <div class="job-header">
            <span class="job-title">BS in Information & Communication Systems</span> — <span class="job-company">AMA Computer Learning Center (ACLC)</span>
            <span class="job-date">2018 – 2022</span>
            <div class="clear"></div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="section-title">Personal Information</div>
    <table class="personal-grid">
        <tr>
            <td><strong>Birthdate:</strong> Jan 28, 2000</td>
            <td><strong>Civil Status:</strong> Single</td>
        </tr>
        <tr>
            <td><strong>Age:</strong> 26 Years Old</td>
            <td><strong>Religion:</strong> Roman Catholic</td>
        </tr>
        <tr>
            <td><strong>Nationality:</strong> Filipino</td>
            <td><strong>Location:</strong> Sitio Gulod Muzon, Taytay, Rizal 1920</td>
        </tr>
    </table>

    <!-- Character References -->
    <div class="section-title">Character References</div>
    <table class="ref-grid">
        <tr>
            <td>
                <strong>Ramil R. Bengco</strong><br>
                Data Science Analyst, IQVIA Solutions<br>
                Email: ramil.bengco@gmail.com | Phone: 0907-198-4728
            </td>
            <td>
                <strong>John Jeremie Jacinto</strong><br>
                IT MIS Developer, TESDA<br>
                Email: jeremiejacinto101@gmail.com | Phone: 0967-319-3601
            </td>
        </tr>
    </table>

</body>
</html>

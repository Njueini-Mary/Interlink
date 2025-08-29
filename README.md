# Centralized Internship & Attachment Management System

## Overview

This project is a centralized web-based platform designed to connect university students with verified work-based (WBL) and service-based (SBL) learning opportunities. It streamlines the process of discovering, applying for, and tracking internships and attachments, benefiting students, organizations, and academic institutions.

## Background

Securing internships and attachments is a critical part of university education, but the process is often fragmented, manual, and inefficient. Students struggle to find relevant opportunities, organizations lack direct channels to qualified candidates, and institutions face challenges in tracking placements and outcomes. This system addresses these issues by providing a unified, digital solution.

## Problem Statement

- Students rely on informal, decentralized methods to find placements, leading to missed opportunities and poor matches.
- Organizations cannot efficiently reach or filter qualified students.
- Institutions lack tools for tracking, reporting, and quality assurance of student placements.

## Objectives

- **General:** Develop a centralized web platform for managing student internships and attachments.
- **Specific:**
  1. Assess current placement processes and tools.
  2. Evaluate and improve existing information systems.
  3. Design and implement a user-friendly, centralized platform for SBL and WBL.
  4. Test the system’s effectiveness in improving access and management of placements.

## Justification

Current systems are outdated and fragmented. This project modernizes the process, ensuring timely access to opportunities, better matching, and improved institutional oversight.

## Scope

- **Included:** Student registration, opportunity listings, application submission, smart filtering, admin dashboards, and feedback.
- **Excluded:** Salary negotiation, legal contracting, third-party job board integration.

## Limitations & Delimitations

- Institutional policy differences and lack of real-time external listings may affect accuracy.
- The system is designed for flexibility and tested with sample data from multiple sources.

## Literature Review & Related Work

- **Handshake:** US-based, effective but limited to partner institutions.
- **LinkedIn:** Global reach, but not tailored for academic internships.
- **Internshala:** Student-focused, but India-centric.
- **Kenyan Platforms:** Fragmented, lack real-time tracking and comprehensive coverage.

**Gap:** No existing platform supports both WBL and SBL, real-time dashboards, and local needs in one place.

## Technology Stack

| Tool        | Function                        | Reason                                  |
|-------------|---------------------------------|-----------------------------------------|
| HTML/CSS    | Frontend                        | Easy to use, widely supported           |
| JavaScript  | Frontend interactivity          | Powerful, many frameworks available     |
| PHP         | Backend logic                   | Good technical knowhow, robust          |
| MySQL       | Database                        | Fast setup, reliable                    |
| Python      | Smart filtering/data handling   | Advanced logic integration              |
| VS Code     | Development environment         | Productivity, extensions                |
| GitHub      | Version control                 | Collaboration, tracking                 |

## System Features

- Student and organization registration
- Opportunity listing and smart filtering
- Application submission and real-time tracking
- Admin oversight and reporting
- Feedback collection and analysis

## System Architecture

- **Three-tier architecture:** Presentation (web), Logic (PHP/Python), Data (MySQL)
- **Modules:** Registration, Filtering, Application, Admin, Dashboard, Feedback

## Development Methodology

- **Prototyping Model:** Iterative development with user feedback at each stage.
- **Object-Oriented Analysis & Design (OOAD):** Modular, reusable, and scalable.

## Testing

- Unit, integration, and system testing to ensure reliability and quality.

## Setup & Installation

1. **Clone the repository:**
	```bash
	git clone https://github.com/<your-org>/interlink.git
	cd interlink
	```
2. **Install dependencies:**
	```bash
	composer install
	npm install
	```
3. **Configure environment:**
	- Copy `.env.example` to `.env` and set your database credentials.
	- Generate application key:
	  ```bash
	  php artisan key:generate
	  ```
4. **Run migrations and seeders:**
	```bash
	php artisan migrate --seed
	```
5. **Start the development server:**
	```bash
	php artisan serve
	```

## Contribution

Contributions are welcome! Please fork the repository and submit a pull request.

## License

This project is licensed under the MIT License.
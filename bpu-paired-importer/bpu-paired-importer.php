<?php
/**
 * Plugin Name: BPU PAIRED Importer
 * Description: One-shot migration from old PAIRED platform database. Delete after running.
 * Version: 1.0
 * Author: BPU Tech
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// ============================================================
// EMBEDDED DATA — parsed from paired_dump.sql
// ============================================================

$PAIRED_USERS = [
    [ 'id' => '1', 'name' => null, 'email' => 'pairedbybpu@gmail.com', 'role' => 'admin', 'about_me' => null, 'expertise_industry' => null, 'other_industry' => null, 'current_role' => null, 'designation' => null, 'company' => null, 'experience_year' => null, 'skills_normalized' => '', 'linkedin_profile' => null, 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => null, 'mentorship_availability' => null, 'mentorship_requirements' => null, 'mentees_at_once' => null, 'career_goals' => null, 'employment_status' => null, 'is_active' => '0', 'status' => '1', 'created_at' => '2025-01-21 11:33:58', 'bp_network' => null, 'gender' => null, 'phone' => null, 'image' => null, 'thumb' => null, 'has_bookings' => '0' ],
    [ 'id' => '11', 'name' => 'Temitope Akintayo       ', 'email' => 'sayrichardbriggs@gmail.com', 'role' => 'user', 'about_me' => 'I am a web technologist with 10+ years of experience creating and managing web assets for corporations and non-governmental organisations. I am looking to mentor the next generation of web techs and help them become better at what they do and secure the job of their choice.', 'expertise_industry' => '', 'other_industry' => 'Industry Disruption', 'current_role' => 'Marketer', 'designation' => 'Web Technologist', 'company' => 'Black Professionals United Kingdom', 'experience_year' => '11', 'skills_normalized' => 'Wordpress,HTML,Javascript', 'linkedin_profile' => 'https://www.linkedin.com/in/temitope-akintayo-4043b3305/', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Lagos', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '-Mentees should have basic expertise and knowledge.
-Mentees should have listening and communication skills.
-Mentees should be able to take and work on corrective feedback.
-Mentees should be growth-focused and adaptable.', 'mentees_at_once' => '1', 'career_goals' => '', 'employment_status' => 'Self-employed', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-02-12 18:07:00', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '08135251884', 'image' => 'uploads/medium/aaaec49d8af4afa232d0c5f63bb0c794_medium-518x612.jpg', 'thumb' => 'uploads/thumbnail/aaaec49d8af4afa232d0c5f63bb0c794_thumb-126x150.jpg', 'has_bookings' => '1' ],
    [ 'id' => '71', 'name' => 'Benny Nmeholam         ', 'email' => 'partnerships@blackprofessionals.uk', 'role' => 'user', 'about_me' => 'I’m Benny Nmeholam, an experienced Senior Client Relationship Manager with a strong background in strategic partnerships, customer success, and vendor relations. With over a decade of experience across various industries, I specialize in building and maintaining long-term client relationships that drive business growth.  

Currently, I work as a Partnerships Relationship Manager at Black Professionals UK, where I focus on fostering meaningful collaborations that create opportunities for Black professionals. My expertise includes marketing, procurement, and leveraging data-driven insights to enhance client engagement.  

Beyond work, I’m passionate about Diversity & Inclusion and helping others navigate their professional journeys. I look forward to sharing insights and learning together!', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'Relationship Manager', 'designation' => '', 'company' => 'Black Professionals United Kingdom', 'experience_year' => '12', 'skills_normalized' => 'Time mgt', 'linkedin_profile' => '', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Falkirk', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'A suitable mentee should be committed to their growth, have clear goals, and be proactive in their learning journey. They should be open to feedback, willing to take initiative, and able to follow through on advice. While some level of experience or exposure in their field is beneficial, the key is having a strong willingness to learn and adapt. A growth mindset, accountability, and active engagement in the mentorship process are essential for making the most of the experience.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => '', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-02-26 12:30:30', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/06128234717763c0c69a22e64977fb76_medium-446x477.png', 'thumb' => 'uploads/thumbnail/06128234717763c0c69a22e64977fb76_thumb-140x150.png', 'has_bookings' => '1' ],
    [ 'id' => '72', 'name' => 'Joy Alonge', 'email' => 'joyalonge01@gmail.com', 'role' => 'mentee', 'about_me' => 'What about ne', 'expertise_industry' => 'Engineering', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'LinkedIn.com', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Lagos', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Don’t even ask', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-02-26 19:30:42', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '73', 'name' => 'Opeyemi Grace Afolabi', 'email' => 'opeyemi.afolabi@hotmail.com', 'role' => 'mentee', 'about_me' => 'Daughter of a king!', 'expertise_industry' => 'Healthcare', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '7', 'skills_normalized' => '', 'linkedin_profile' => 'linkedin.com/ oloriyemie', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Abuja', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Shine bright like a diamond.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-02-27 08:47:45', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '74', 'name' => 'Anthony Kings', 'email' => 'kingsanthonyn@gmail.com', 'role' => 'mentee', 'about_me' => 'NA', 'expertise_industry' => 'Other', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '4', 'skills_normalized' => '', 'linkedin_profile' => 'NA', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Uyo', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'NA', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-02-27 09:40:33', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '76', 'name' => 'Lilian Dike ', 'email' => 'ldike@blackprofessionals.uk', 'role' => 'mentee', 'about_me' => 'I am a dedicated and ambitious professional with a strong interest in HR.  I am passionate about continuous learning, career development, and expanding my skill set', 'expertise_industry' => 'Other', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'Linkedin.com/in/lilian-dike-acipm-53729259', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My goal is to build a successful career in Human Resources, focusing on strategic talent management, employee development, and HR innovation. I aspire to advance into a strategic HR leadership role where I can influence organizational direction and drive initiatives that align talent management with business objectives.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-02-27 11:38:22', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '77', 'name' => 'Chinyere Moneke', 'email' => 'ceemonx@gmail.com', 'role' => 'mentee', 'about_me' => 'Looking to get employment', 'expertise_industry' => 'Media', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/temitope-akintayo-4043b3305', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Aberdeen', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'To kickstart my career in a reputable organisation.', 'employment_status' => 'Student', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-02-27 14:25:14', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '78', 'name' => 'Akua Tsetsewa Yawson', 'email' => 'ayawson@blackprofessionals.uk', 'role' => 'mentee', 'about_me' => 'Maame Akua is a Design Experience and Communications Specialist at the Impact Hub Accra, well-versed in conducting creative, proactive media outreach and pitching ideas. She has played a pivotal role in communicating with a broad range of local and international stakeholders, such as the US State Department, Fondation Botnar and the US Embassy media team. As the Communications, Brand Management and Public Relations expert, Maame Akua has designed, led and implemented social media strategies to align with Impact Hub Accra\'s business goals, cultivating relationships with key stakeholders, vendors and industry influencers across fields to create positive brand experiences.
She has collaborated with the International Climate Initiative (IKI) and Siemens Stiftung to curate two campaigns under the Net Zero Accra initiative, A Race to Net Zero and Charge 2022, to discuss and understand the vision for electric mobility in Ghana.', 'expertise_industry' => 'Advertising/Public Relations', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '5', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/akua-tsetsewaa-yawson-44b767a0/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Accra', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I am working towards specialising in international communications and relations, social impact spaces, VC funding and venture support landscapes, and real estate. ', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-02-28 11:31:57', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '80', 'name' => 'Blessing      ', 'email' => 'blessinglonge001@gmail.com', 'role' => 'mentee', 'about_me' => 'Growth oriented. Would appreciate a mentor who is passionate about growth.', 'expertise_industry' => 'Arts/Creative/Entertainment', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2+ years ', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/blessing-longe-6a235a245', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Enugu', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => '✓Build a stronger professional network.
✓Gain more skills.
', 'employment_status' => 'Employed Part-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-03-03 16:21:38', 'bp_network' => '', 'gender' => 'Female', 'phone' => '09022659585', 'image' => 'uploads/medium/7b04ba6622ce01fa53a4a18984397dc8_medium-1200x1045.jpg', 'thumb' => 'uploads/thumbnail/7b04ba6622ce01fa53a4a18984397dc8_thumb-150x130.jpg', 'has_bookings' => '0' ],
    [ 'id' => '89', 'name' => 'Akinwale Fayemiro  ', 'email' => 'akinwalefaye@gmail.com', 'role' => 'user', 'about_me' => 'A dedicated marketing professional with a passion for storytelling, music, and the arts, with related experience in broadcast media production, digital marketing, and content editing. 

Additionally, a member of the "Arts Marketing Association," an organization for those working in the arts, culture, and heritage sectors in the United Kingdom.', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'Marketing Coordinator ', 'designation' => 'Marketing Co-ordinator', 'company' => 'BBC Scotland ', 'experience_year' => '3', 'skills_normalized' => 'Media ,marketing,content,music,production', 'linkedin_profile' => 'https://www.linkedin.com/in/akinwale-fayemiro/', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Stirling', 'mentorship_availability' => 'Once in 2 Months', 'mentorship_requirements' => 'Dedicated attitude ', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-04-14 11:16:07', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/b4db92003e358d8c76fa1272513c5149_medium-799x1200.jpg', 'thumb' => 'uploads/thumbnail/b4db92003e358d8c76fa1272513c5149_thumb-99x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '90', 'name' => 'Simi Taiwo', 'email' => 'simitaiwo4@gmail.com', 'role' => 'mentee', 'about_me' => 'My name is Simi Taiwo, I am a product designer from Nigeria, with 3 years of professional experience. I hold a  masters degree in User Experience and interaction design from Glasgow Caledonian university and a bachelor’s degree from Covenant university in Nigeria. I am passionate about creativity and pushing for empathy and accessibility in tech solutions. Asides my career interests I also enjoy exploring different hobbies. I love being in spaces that allow me share and explore my creativity. ', 'expertise_industry' => 'Information Technology', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '3', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/shalom-taiwo-40371b163?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow ', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My current career goals are:
- Taking the next step to grow in my field 
- Finding a Job role 
- Becoming more comfortable in my skills and tackling my imposter syndrome ', 'employment_status' => 'Prefer Not to Answer', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-04-14 11:29:44', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '91', 'name' => 'Omoregba Loveth', 'email' => 'lovethomoregba@gmail.com', 'role' => 'mentee', 'about_me' => 'I have a string background in customer service, sales and operations coordination. I currently study International Business Management at masters level and I am looking to navigate through the career path as an Operations Analyst/executive.', 'expertise_industry' => 'Other', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'http://linkedin.com/in/omoregba-loveth', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Build valuable skills to grow as an Operations Executive to Operations Manager/ Business Development Consultant. ', 'employment_status' => 'Student', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-04-14 11:50:02', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '92', 'name' => 'Joshua Babalola', 'email' => 'bojimajoshua@gmail.com', 'role' => 'mentee', 'about_me' => 'Currently in a Basic level phase, learning,  training as I set to acquire hands on experiences.', 'expertise_industry' => 'Information Technology', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '1', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/joshua-babalola?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Be able to transition from Health care into Cybersecurity.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-04-20 01:00:40', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '93', 'name' => 'Scott Gunderson', 'email' => 'scott.gunderson@lloydsbanking.com', 'role' => 'user', 'about_me' => 'I have worked in Financial Services for around 25 years and currently lead a large Customer Service team at Lloyds Banking Group (LBG). My team help customers save for their retirement. I am actively involved in leading our Diversity Equity and Inclusion work within our Insurance Business at LBG and have a passion for getting the absolute best out of people inside and outside of work. I\'ve worked in partnership with BPU for 4 years now and would be delighted to meet members who are looking for support. ', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'Senior Customer Service Manager', 'designation' => null, 'company' => 'Lloyds Banking Group', 'experience_year' => '', 'skills_normalized' => 'Collaboration,networking,customer services,leadership,Management,Business Development,Pensions,Financial Services,Relationship Management,Change Management,Project Delivery', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Broxburn', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'There is no set criteria but I\'d like the mentee to be very clear in their requirements around what they are looking for in a mentor, what do you want to get out of the relationship? Are you looking to establish or further your career in Financial Services? 
I would also love to learn from my mentees, reverse mentorship would work well for me too, so I can continue my learning as I look to build a more diverse team and be the best leader I can be.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '2', 'created_at' => '2025-04-29 08:16:05', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '94', 'name' => 'STEPHEN KAYODE BABATUNDE    ', 'email' => 'Stephenb2010@hotmail.com', 'role' => 'user', 'about_me' => 'I am a regulated mortgage and insurance broker. Before switching to finance, i worked as Process Engineer in Manufacturing & Renewable industry and as Project Manager in Construction Sector.

I am Director at Vivid Financial Solutions, a company who are experts in providing tailored mortgage and insurance advise. 

I serve as a community engagement lead for West Lothian area

I Serves as a member on the Board of Trustees at Citizens Advice Bureau.

I am an ambassador at Black Professionals United Kingdom', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'DIRECTOR', 'designation' => '', 'company' => 'Vivid Financial Solutions Ltd', 'experience_year' => '10', 'skills_normalized' => 'Financial Intermidiary,Mortgage Broker,Project management,Financial Education', 'linkedin_profile' => 'https://www.linkedin.com/in/stephen-babatunde-cemap-331533102/', 'facebook_profile' => '', 'instagram_profile' => 'https://www.instagram.com/steveb101/', 'x_profile' => '', 'residence' => 'LIVINGSTON', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'Be yourself and Be open', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Self-employed', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-05-07 19:37:14', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/e32d01d828da19a959edd0a346f4e5cb_medium-1200x966.jpg', 'thumb' => 'uploads/thumbnail/e32d01d828da19a959edd0a346f4e5cb_thumb-150x120.jpg', 'has_bookings' => '0' ],
    [ 'id' => '97', 'name' => 'Enoch Adeyemi  ', 'email' => 'eadeyemi@blackprofessionals.uk', 'role' => 'user', 'about_me' => 'Enoch Adeyemi identifies himself first as human. Moreover, he holds the distinction of being a Fellow of the Association of Chartered Certified Accountants (ACCA) and specializes in consulting within the financial services industry. Driven by a strong commitment to positive change, Enoch has founded several organizations, including Black Professionals United Kingdom - a rapidly growing entity that offers support to professionals, students, and corporate partners. Additionally, he contributes his expertise in leadership, finance, diversity, equity, and inclusion (DEI), as well as entrepreneurship, across multiple advisory boards.', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'Founder & CEO', 'designation' => '', 'company' => 'Black Professionals United Kingdom', 'experience_year' => '19', 'skills_normalized' => 'Leadership,Financial Consulting,Entrepreneurship,Diversity and Inclusion', 'linkedin_profile' => 'https://www.linkedin.com/in/enochadeyemifcca/', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Falkirk', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'A suitable mentee should demonstrate commitment, a clear goal or area they’re seeking guidance in, and a willingness to take action on feedback.
', 'mentees_at_once' => '1', 'career_goals' => '', 'employment_status' => 'Prefer Not to Answer', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-05-08 12:28:42', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/ddfa1dda1b13f757c40b91fc04e36a57_medium-1200x1161.jpg', 'thumb' => 'uploads/thumbnail/ddfa1dda1b13f757c40b91fc04e36a57_thumb-150x145.jpg', 'has_bookings' => '0' ],
    [ 'id' => '99', 'name' => 'Michael Jackson ', 'email' => 'mail2emjay@yahoo.com', 'role' => 'user', 'about_me' => 'Michael is a skillful collaborator with a growth mindset. He is an effective communicator and skilled at communicating with influence to ensure buy-in from varying range of stakeholders groups.

Michael is passionate about coaching and mentoring and he has helped scores of people in different parts of the world to achieve great things including but not limited to coaching, mentoring, CV review, interview preparation and set them up on the path to success.

Michael has a TikTok and YouTube channels where he shares his thoughts and inspirations about life, happiness and the world of Project Management.
He is an ardent believer in continuous improvement (kaizen) and small incremental changes. 

Michael has been married for over a decade with 2 lovely daughters. In his spare time, Michael runs/jogs to keep fit.

Michael is happy to mentor and support individuals across all fields to reach their potential through the awareness of how human skills (popularly called soft skills) can be built continuously and consciously as these skills underpin everything we do as human beings across all walks of life.', 'expertise_industry' => '20', 'other_industry' => '', 'current_role' => 'Technology and Agile Project Management Professional ', 'designation' => null, 'company' => 'NatWest Group', 'experience_year' => '', 'skills_normalized' => 'Communication,Project management,Interview Preparation,Stakeholder engagement ,People Management,Taking Action', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Scotland', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'I expect mentees to be open minded and have a growth mindset.', 'mentees_at_once' => '4', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '2', 'created_at' => '2025-05-08 14:28:54', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '100', 'name' => 'Akosua Asabea Ohemeng Owusu  ', 'email' => 'aowusu@blackprofessionals.uk', 'role' => 'user', 'about_me' => 'I currently work as a senior management consultant at Capco.  Beyond my professional role, I\'m deeply committed to fostering diversity and inclusion within the professional community.  As the Director of Communications for Black Professionals Scotland, I have also had the honor of co-hosting the Black Scottish Awards, celebrating the achievements of Black professionals across Scotland.  My journey has been enriched by my prior working experience in Ghana, and I am drawing from my diverse background to inspire and support the next generation of professionals. ', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'Senior Management Consultant', 'designation' => '', 'company' => 'Capco', 'experience_year' => '11', 'skills_normalized' => 'business analysis,change management,project management,stakeholder management,consulting,financial analysis,agile', 'linkedin_profile' => 'https://www.linkedin.com/in/akosua-asabea-ohemeng-owusu-54a11230/', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Edinburgh', 'mentorship_availability' => 'Once in 2 Months', 'mentorship_requirements' => 'University student, recent graduate or an early careers individual.
I also require someone with a positive attitude, effective communication and an open mind.', 'mentees_at_once' => '3', 'career_goals' => '', 'employment_status' => '', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-05-12 13:01:16', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/355a195633580a2f2b49ea3d02ca80fc_medium-800x1200.jpg', 'thumb' => 'uploads/thumbnail/355a195633580a2f2b49ea3d02ca80fc_thumb-100x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '102', 'name' => 'Tosin Ogunlesi ', 'email' => 'togunlesi@blackprofessionals.uk', 'role' => 'user', 'about_me' => 'I am an experienced technology change and transformation expert with 15+ years working with global teams in multiple sectors including Financial services, Consulting and Retail. I am responsible for guiding business and technology teams through times of transformational change. 

I maximise the business value produced by the data and technology teams I work with. I ensure change decisions are data driven and also provide an enabling culture where teams can deliver solutions with clarity and focus.

A public speaker and events host; I am  energised by advocating for black ethnic minority career opportunities. I currently serve as the head of technology in the Black Professionals UK team. 

I also lead a digital product team looking to digitise and improve how secondary school students access mentors globally.', 'expertise_industry' => '20', 'other_industry' => '', 'current_role' => 'Snr business analyst ', 'designation' => null, 'company' => 'Lloyd\'s banking group', 'experience_year' => '', 'skills_normalized' => 'Change management', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'Clear understanding of what I can help with 

Respect and professionalism', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '2', 'created_at' => '2025-05-12 13:22:22', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '104', 'name' => 'Austin Uche   ', 'email' => 'austinu.nls@gmail.com', 'role' => 'user', 'about_me' => 'Passionate About Service, Culture, and Driving Meaningful Impact

With a deep-rooted commitment to service and transformation, I dedicate my career to initiatives that empower individuals, foster professional growth, and drive societal change.

Project & Program Management
Having led and contributed to over 40 transformative projects and programmes, I thrive on collaborations that create lasting impact. Partnering with organisations such as BlackRock, TSB, and JP Morgan, I have co-driven initiatives focused on recruitment, networking, and community engagement. In 2023 alone, our efforts resulted in over 20 Black professionals securing permanent roles in top organisations, with a success rate of 90% in attendance and measurable impact.

Customer Success & Engagement
With a strong foundation in customer success and engagement, I have helped thousands of individuals access career and culture-enhancing opportunities. My experience includes consulting, screening, and onboarding, ensuring clients receive the best support to achieve their goals.

I believe that service breeds impact, and through strategic collaboration, we can build solutions that empower communities and professionals alike.

Let’s connect and explore opportunities to create meaningful change together.', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'Program Coordinator, Events', 'designation' => 'Sales Consultant II Program Coordinator', 'company' => 'Black Professionals United Kingdom', 'experience_year' => '10', 'skills_normalized' => 'Event Management,International Sales,Business Development,Program Coordination', 'linkedin_profile' => 'https://www.linkedin.com/in/austinuche/', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Glasgow', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'A mentee who is ready to unlearn and relearn', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => '', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-05-19 13:23:17', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/9e29d6a38ba580c9cf4888dceab1ded5_medium-640x640.jpg', 'thumb' => 'uploads/thumbnail/9e29d6a38ba580c9cf4888dceab1ded5_thumb-150x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '105', 'name' => 'Taofeek Giwa', 'email' => 'tgiwa@blackprofessionals.uk', 'role' => 'mentee', 'about_me' => 'Taofeek is a Data Professional with over 12 years working experience within the Financial Services industry in the UK. He is the COO of Black Professionals UK, a fast growing organisation that supports Black Professionals, students and corporate partners. He is also the Co-Founder of VisitNigeriaNow. ', 'expertise_industry' => 'Banking & Financial Services', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '13 ', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com/in/ taofeekgiwa', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Denny', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'To keep being the best version of me to turn up each day', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-05-20 22:39:14', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '106', 'name' => 'Ololade Giwa', 'email' => 'lollipizai01@gmail.com', 'role' => 'user', 'about_me' => '⸻

Ololade [Surname] is a Doctor of Philosophy in Nursing and an NMC Registered Nurse with over 15 years of experience within the healthcare and care industry in the UK. Her career spans clinical, managerial, and entrepreneurial roles, reflecting her deep commitment to quality care and leadership in nursing.

Ololade began her journey in healthcare after completing her BSc, MSc, and PhD in Nursing in the UK. She progressed steadily through frontline and leadership roles, gaining hands-on experience within community settings and care environments. Her previous roles include Clinical Unit Manager and Deputy Home Manager, where she was responsible for overseeing nursing care, staff management, and compliance with care standards.

Currently, Ololade is the Managing Director of 360 Medline and 360 Staffing Agency. Through 360 Medline, she continues to work as an agency nurse, primarily within NHS South Lanarkshire hospitals across various clinical settings. At 360 Staffing, she recruits and supports nurses and carers to deliver high-quality care in residential and nursing homes across the region.

Ololade is highly skilled in PEG feed administration in acute settings, insulin administration and management, catheterisation, wound care, and end-of-life care. She has extensive experience providing care for individuals living with dementia and learning disabilities.

Throughout her career, Ololade has built strong collaborative relationships with healthcare professionals and community stakeholders. She is known for her clinical expertise, compassionate care, and ability to lead and motivate multidisciplinary teams. Her passion for quality improvement and staff development continues to influence the standard of care provided under her leadership', 'expertise_industry' => '26', 'other_industry' => '', 'current_role' => 'Nursing Manager', 'designation' => null, 'company' => '360 Medline ', 'experience_year' => '', 'skills_normalized' => 'Nursing,Public health', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Stirling', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'Be ready to learn ', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '2', 'created_at' => '2025-05-21 14:25:41', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '107', 'name' => 'Ifeyinwa Dada', 'email' => 'ifydada@theholisticwellbeingsummit.org', 'role' => 'user', 'about_me' => '
Ifeyinwa Dada (Ify) AIEMA 
Is the Chief Executive Officer of SUMMIT.g Edinburgh Makeup Brand, Urban Summit Apartments and The Holistic Wellbeing Summit.  

Ify has a Double Honours Bachelor of Science degree in Biology & Education and a Post graduate Diploma in Occupational & Environmental Health and Safety management (university of Portsmouth)

An expert in turning her ‘Passions into Profit’- experience in starting, growing and scaling diverse small businesses in multicultural communities acquired during 2 decades Expat journey in three (3)  continents.

Global Expertise and Achievements:• 
25 years of purchasing and is management experience in markets such as the Philippines, Malaysia, Singapore, Hong Kong, China, Vietnam, and Turkey

• Specializes in identifying high-potential products, international sourcing, and ensuring compliance with import standards.

• Founder of “SUMMIT.g Edinburgh” Makeup brand which have been featured in Vogue UK and The Sun, with several products achieving the ‘Amazon Choice’.

• 2 decades in Estate & Property Management: Short and long stay accommodation with Urban Summit Apartments in Aberdeen, UK. Achieving Excellent ratings on online Travel channels.

Community Engagement and Mentorship:
Passionate about youth development, Ify dedicates her time to mentoring and coaching teenagers, empowering them to reach their full potential. She also serves as a Sunday school teacher and teen mentor at her local church.

Professional Affiliations:
• Associate Member of the Institute of Environmental Management & Assessment (IEMA)
• Member of the Edinburgh Chamber of Commerce
• Member of Fife Women in Business
• Volunteer Delegate, UN Women UK CSW68 & CSW69
• Certified Makeup & Beauty Consultant

Her latest project The Holistic Wellbeing Summit - A platform that addresses women’s holistic needs and fosters empowerment.

At the Holistic Wellbeing Summit, 
Our mission is to redefine what it means to care for women by creating a transformative experience that caters to their holistic needs in ways that are inclusive, empowering, and deeply nourishing.
', 'expertise_industry' => '35', 'other_industry' => '', 'current_role' => 'Chief Executive Officers- The Holistic Wellbeing Summit; Summit-Gate Ltd & The Summit Apartments.', 'designation' => null, 'company' => 'The Holistic Wellbeing Summit ', 'experience_year' => '', 'skills_normalized' => 'Event Planning & Programme Delivery,Strategic Leadership,Advocacy & Community Engagement ,Cross-sector Collaboration,Empowerment and Leadership Development', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Fife', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '“Come as you are” is my belief. 

I’m keen to work with any one who is humble enough to believe they need others to support their journey of growth & development and equally know they have sufficient resources to work with.
', 'mentees_at_once' => '3', 'career_goals' => '', 'employment_status' => 'Self-employed', 'is_active' => '0', 'status' => '2', 'created_at' => '2025-05-23 12:25:14', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '109', 'name' => 'Obo Idornigie ', 'email' => 'oidornigie@gmail.com', 'role' => 'user', 'about_me' => 'I am an oil and gas research analyst. I have a background in Geology and oil and d gas economics.  My focus at the moment is corporate analysis,  M&A and floating facility analysis.', 'expertise_industry' => '9', 'other_industry' => '', 'current_role' => 'SVP - Energy Trends ans Analysis', 'designation' => null, 'company' => 'Welligence ', 'experience_year' => '', 'skills_normalized' => 'Oil and Gas research/consulting', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'United Kingdom ', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'Focused and and at leat a Bsc or close to getting a Bsc.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '2', 'created_at' => '2025-05-23 20:41:50', 'bp_network' => 'Black Professionals Europe', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '113', 'name' => 'Katie Shaikh   ', 'email' => 'katie.shaikh@freeagent.com', 'role' => 'user', 'about_me' => 'I manage the Talent team (hiring and recruitment) at FreeAgent. We\'re based in Edinburgh and our product is a much loved cloud accounting app for small businesses. Our differentiators include how easy the product is to use and our friendly support team. 

I have worked at FreeAgent for over 8 years and love helping managers hire the right people. I have a background in teaching English, agency recruitment, publishing and an MSc in Human Resources too. ', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'Senior Talent Team Manager', 'designation' => '', 'company' => 'FreeAgent', 'experience_year' => '20', 'skills_normalized' => 'Job applications,Interviewing', 'linkedin_profile' => '', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Edinburgh', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'I\'m here to provide helpful context for anyone looking to apply for our Support Accountant vacancies. 
If you have accounting experience or qualifications, and enjoy customer support then this could be a good opportunity. I am here if anyone would like to better understand the recruitment and selection process for these roles before submitting an application.
At the minimum I\'d expect a mentee to have researched what FreeAgent does and have an interest in these roles.', 'mentees_at_once' => '3', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-06-03 16:01:37', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/f102b17157d09dc5c18c70f36142eef6_medium-1144x1144.png', 'thumb' => 'uploads/thumbnail/f102b17157d09dc5c18c70f36142eef6_thumb-150x150.png', 'has_bookings' => '0' ],
    [ 'id' => '114', 'name' => 'Andrew Leaver      ', 'email' => 'andrew.leaver@crl.com', 'role' => 'user', 'about_me' => 'I currently work within Life Sciences for Charles River Laboratories, a contract Research Organisation, which is a global company focused on the development of medical treatments to enhance human and animal welfare. My remit covers all levels and professionals disciplines from Lab Science roles to all support functions including Admin, Finance, HR, IT, and Sales to name but a few. 

I have been with CRL since Nov 2021 and was promoted into my ciurrent role in March 2025. 
I lead the Education Committee for Scotland, co-ordinate the Academic engagement across the UK & Ireland and lead on a number of key DE&I initiatives. 

Prior to my current tenure I have worked in: 
Hospitality - Operations / HR / H&S roles in properties from 3 star to 5 Red star.
HR Consultancy (Business Owner) - Working in all three sectors (Public, Private and Third) and across multiple industries. (Finance, Retail, Construction, Leisure, Estate Agency, Manufacturing, etc..)

I have led teams from two to twenty two. Recruited across continents and am a voracious networker. 

As an Talent Acquisition professional with an international remit I tend to specialise in supporting early and mid career development and would be honoured to assist if YOU believe I have the correct background to help you turn your career page on to your next chapter. 

You can find me on LinkedIn here: https://www.linkedin.com/in/andrew-j-p-leaver/ ', 'expertise_industry' => '30', 'other_industry' => '', 'current_role' => 'Associate Director - Talent Acquisition UK & Ireland', 'designation' => null, 'company' => 'Charles River Laboratories', 'experience_year' => '', 'skills_normalized' => 'Recruitment / Talent Acquisition,Human Resources,Health & Safety,Stakeholder Management,Interview Preparation,Networking,DE&I Advocacy', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Coldstream', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'I have no preconceived ideas about what position you might be in. You could be at the very start  of your career with little or no experience, you could have amazing qualifications but struggling to break through the selection processes or have hit a wall and thinking about a complete Career change. Everyone is unique, and everyone has their own challenges so I\'ll be happy to have a conversation and take each request on it\'s merits. 

Even as a mature (I\'ve avoided the other word), middle aged white male I\'ve experienced career challenges of my own - happy to share if need be. Half the solution lies in starting a conversation and seeing that action alone as a positive step. After all none of us have all the answers ourselves. 

In the end, if I can\'t give you help you need, maybe I know someone else who can. ', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-06-04 12:59:54', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/8a2d381795b622edef664d25acf44876_medium-323x359.jpg', 'thumb' => 'uploads/thumbnail/8a2d381795b622edef664d25acf44876_thumb-134x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '117', 'name' => 'Omotoyosi Adunmo  ', 'email' => 'omotoyosiadunmo@gmail.com', 'role' => 'mentee', 'about_me' => 'I hold a Master’s degree in International business management and I am actively working to build a career in business and project management. Currently, I volunteer as a Project Support Officer to gain hands-on experience in project delivery and coordination, while applying and strengthening my organizational and communication skills.

Recently, I successfully completed the PRINCE2 Foundation certification and am now preparing for the Practitioner level to deepen my understanding of structured project management methodologies. Throughout my career, I have consistently leveraged transferable skills gained from diverse professional roles, including adaptability, stakeholder engagement, and problem-solving.

I am committed to continuous learning and professional development and am keen to gain practical insights and guidance from experienced professionals in the field to help shape my path forward in business and project management.', 'expertise_industry' => 'Other', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2 years ', 'skills_normalized' => '', 'linkedin_profile' => 'http://linkedin.com/in/adunmo-omotoyosi-702346206', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow ', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My long term career goal is to become an IT project manager ', 'employment_status' => 'Employed Part-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-06-16 13:29:52', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/5aedfecea1eab9581022e951c000d864_medium-675x1200.jpeg', 'thumb' => 'uploads/thumbnail/5aedfecea1eab9581022e951c000d864_thumb-84x150.jpeg', 'has_bookings' => '0' ],
    [ 'id' => '118', 'name' => 'Gabriel Adeyeye', 'email' => 'science4men1@gmail.com', 'role' => 'user', 'about_me' => 'An experienced Customer Acquistion Specialist with over five (5+) years of hands-on experience helping businesses attract the right customers and grow smarter. I specialize in Up-to-date Customer Acquisition techniques and Account-Based Marketing (ABM), blending strategy with creativity to build campaigns and marketing plans that truly connect.', 'expertise_industry' => '1', 'other_industry' => '', 'current_role' => 'Digital marketing manager ', 'designation' => null, 'company' => '', 'experience_year' => '', 'skills_normalized' => 'Marketing,Digital marketing ,Customer Acquisition', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Romford', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'Upskilling, network', 'mentees_at_once' => '1', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-16 13:33:29', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '119', 'name' => 'Risi Agbaje', 'email' => 'adefunke.agbaje@gmail.com', 'role' => 'user', 'about_me' => 'Senior Business Analyst | BA Coach & Mentor | Founder, InsightEdge

???? Certified BA (IIBA-CCBA) with 10+ years of experience leading change and transformation in Financial Services & Specialty Insurance
???? Founder & Lead Instructor at InsightEdge – a specialist business analysis training institute empowering professionals through practical, real-world learning
???? Business Analysis Coach & Mentor, dedicated to developing the next generation of impactful BAs
???? Expertise in Agile & Waterfall delivery, regulatory compliance, delegated authority, and system integration 
???? Known for simplifying complexity, fostering stakeholder collaboration, and aligning solutions with strategic business goals', 'expertise_industry' => '5', 'other_industry' => '', 'current_role' => 'Senior Consultant Business Analyst ', 'designation' => null, 'company' => 'Capco', 'experience_year' => '', 'skills_normalized' => 'Business Analysis Soft skills,System integration ,Stakeholder Management ,Negotiation', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Manchester ', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '1.  Set objectives 
2. Serious Minded
3. Self motivated 
4. Committed
', 'mentees_at_once' => '1', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-16 15:59:55', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '120', 'name' => 'Maryamu Yaroson', 'email' => 'myaroson@yahoo.com', 'role' => 'user', 'about_me' => 'I am a passionate HR professional with around four years of experience in the field. I’m also a foodie, a keen networker, and an easy-going person.

I’m currently an HR Generalist with Sorenson UK, where I’ve spent the last two years supporting a wide range of HR operations. My background includes experience in recruitment, payroll, employee relations, and organisational change; all of which have deepened my passion for people and inclusive workplaces.

As a Black woman in HR, I’m especially passionate about helping minority ethnic professionals rise, thrive, and make a real impact in their careers. I enjoy supporting others with interview preparation, personal branding, and navigating the realities of the workplace with confidence, while also learning from every experience.

For me, mentorship is about lifting as you climb; creating space for others to grow, lead, and leave their own mark.
', 'expertise_industry' => '31', 'other_industry' => '', 'current_role' => 'HR Generalist ', 'designation' => null, 'company' => 'Sorenson ', 'experience_year' => '', 'skills_normalized' => 'Interview prep ,Personal branding ,HR operations', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow ', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'I’m looking to mentor individuals who are open-minded, reflective, and committed to their personal and professional growth. A suitable mentee should have a clear desire to develop their career ; whether that’s stepping into their first role, progressing in their current path, or navigating workplace challenges with more confidence.

They don’t need to have everything figured out, but I do appreciate mentees who are willing to engage honestly, take & give feedback, and take action. I’m especially keen to support early-career professionals or students looking for their first opportunity ; those who want to grow their influence, build their confidence, and take practical steps toward their next chapter.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-16 16:18:19', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '121', 'name' => 'Eva Osei-Owusu ', 'email' => 'eoowusu2@gmail.com', 'role' => 'mentee', 'about_me' => 'Highly motivated and experienced professional with 13+ years in customer service and a graduate background in Behavioural Science. Skilled in analyzing consumer choices, biases, and behavioral influences. Utilizing expertise to design evidence-based policy interventions that empower individuals to make informed, optimal decisions 
Passionate About:*

1. Improving decision-making processes through behavioural science.
2. Enhancing customer experiences and satisfaction.
3. Developing inclusive, evidence-based policies.
4. Contributing to a better world through informed, optimal choices.', 'expertise_industry' => 'Banking & Financial Services', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '15years', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com/in/ eva-osei-owusu-123', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Stirling ', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'To become a Behavioural Scientist or Analyst in the Health Sector ', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-16 22:32:40', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '122', 'name' => 'Mohammed Salihu   ', 'email' => 'mcsalihu@gmail.com', 'role' => 'mentee', 'about_me' => 'Client-focused professional with experience in financial services and utilities, specialising in complaint resolution, bereavement case management, and regulatory compliance. Skilled in Salesforce, CRM, UI5, and CWT, with a track record of resolving complex cases with empathy, accuracy, and professionalism. Recognised for delivering high-quality outcomes in fast-paced, regulated environments - and as an FCA Innovation Award winner.

Also a published poet and author (Voices from the Soil), with work featured by UNICEF, Brittle Paper, and the Paisley Book Festival. My creative work explores themes of identity, migration, and spirituality through both written and visual storytelling.', 'expertise_industry' => 'Banking & Financial Services', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/mohammedsalihu', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Paisley', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My short-term goal is to gain experience in communications or marketing within the creative sector, where I can bring together my background in finance and my passion for storytelling and community engagement.

Long term, I hope to build a career that allows me to contribute to meaningful narratives in arts, culture, or social impact - ideally leading creative projects that amplify diverse voices, including those of immigrants like myself. I’m especially drawn to roles where I can blend analytical thinking with authentic, inclusive storytelling.

', 'employment_status' => '', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-06-17 00:04:40', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/d9e20f6504908837fded919b633a8599_medium-1200x800.jpg', 'thumb' => 'uploads/thumbnail/d9e20f6504908837fded919b633a8599_thumb-150x100.jpg', 'has_bookings' => '0' ],
    [ 'id' => '123', 'name' => 'Rachael Enumah ', 'email' => 'rachaelenumah72@gmail.com', 'role' => 'mentee', 'about_me' => 'I am an aspiring architect in the making, currently at Part 2 architectural Assistant level here in the UK. However, before coming into the country for my masters program, I have acquired industry experience for over 2 years. I currently work on personal projects but looking forward to join a design team.
I am an enthusiastic learner, dedicated professional with friendly personality that can persevere through challenges. I truly believe \'I learn everyday\'.  ', 'expertise_industry' => 'Construction', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '3', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com/in/rachael-enumah-470493160', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Once in 2 Months', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => '- To become a British registered architect.
- To branch into construction project management.
- To create a design and building entity/company that improves the construction scene in the UK and abroad.
- To lead graciously.
- To have a good time while working. I believe work is about providing solutions and being rewarded for it.', 'employment_status' => 'Self-employed', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-17 00:40:35', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '124', 'name' => 'Theresa Abegunde ', 'email' => 'Tesabegs1@gmail.com', 'role' => 'user', 'about_me' => 'I have about four years of experience working across startups and global teams, with a focus on user-centered design, accessibility, and inclusive product thinking. I\'m especially passionate about using design to solve real-world problems and advocate for underrepresented users. I’m also currently building my own startup, where I’m applying these principles to create more thoughtful and inclusive digital products. Outside of work, I enjoy exploring how emerging technologies like AI can empower creatives and improve user experiences.', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'User Experience Designer', 'designation' => '', 'company' => 'Publicis Sapient', 'experience_year' => '4', 'skills_normalized' => 'Product Design,UI/UX Designer', 'linkedin_profile' => 'https://www.linkedin.com/in/tessey-abegunde/', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'London', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'I’m open to mentoring people who are new to design or looking to transition into the field. 
I’m happy to support them with guidance, resources, and feedback wherever they may need it. I just ask that they have demonstrated some level of commitment such as completing a course, bootcamp, or working on personal projects so there’s a clear interest and foundation we can build on together.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Not employed and not looking for work', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-06-17 00:49:26', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/dc76915940ace93b431a8b767d01e3fc_medium-1125x1200.jpg', 'thumb' => 'uploads/thumbnail/dc76915940ace93b431a8b767d01e3fc_thumb-140x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '125', 'name' => 'Mufthau Olawale Adebayo', 'email' => 'olawalemadebayo@gmail.com', 'role' => 'mentee', 'about_me' => 'My name is Mufthau Olawale (Mo) Adebayo. I am a qualified civil engineer with over 10 years of experience delivering infrastructure projects in Nigeria, including roads, drainage, water treatment, and flood control. Since moving to the UK, I have completed a work placement with Morrison Construction and currently work as a healthcare support worker with NHS Scotland while continuing to look for engineering roles. I enjoy learning, solving problems, and working with others. I have strong site supervision, project planning, and data analysis skills. I’m always ready to adapt, grow, and support a team. I believe in hard work and staying positive even when facing challenges. I’m looking for guidance and a community that will help me take the next step in my career.', 'expertise_industry' => 'Construction', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '13 years', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/mufthau-adebayo-b1b557265', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My goal is to return fully into civil engineering and build a long-term career in the UK’s built environment. I’m interested in roles such as project planner, site engineer, or assistant project manager, and I’m open to entry-level opportunities that give me room to grow. I want to work on meaningful infrastructure or housing projects that improve people’s lives. I also want to gain UK-recognised experience that helps me complete my ICE Initial Professional Development and become chartered in the future. I’m eager to understand how the UK construction industry works, build a strong professional network, and keep learning. Having a mentor would give me the direction and support I need to reach these goals faster.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-17 06:30:29', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '126', 'name' => 'Ayanda', 'email' => 'agrippamamba@gmail.com', 'role' => 'user', 'about_me' => 'I am currently a Marketing Strategist with expertise in sustainable finance, entrepreneurship, fintech, and SME development. I bring a background in banking, innovation, and consultancy, with a passion for mentoring emerging talent and supporting inclusive economic growth through responsible financial solutions and entrepreneurial empowerment.

', 'expertise_industry' => '5', 'other_industry' => '', 'current_role' => 'Digital Factory', 'designation' => null, 'company' => 'Marketing Strategist', 'experience_year' => '', 'skills_normalized' => 'Marketing ,Finance,Strategy,Entrepreneurship,Innovation and Technology,Leadership,Business Planning,Fintechs', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'My mentees should be curious, open-minded, eager to learn, committed to growth, and willing to take initiative and ask questions.

', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-06-17 07:15:22', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '127', 'name' => 'Funmi Oluduro  ', 'email' => 'funmistudi@gmail.com', 'role' => 'user', 'about_me' => 'I am known for my visionary leadership and dedication to driving innovation. This has been a transformative force all through my professional career. My relentless pursuit of excellence has resulted in ground-breaking developments within organizations, influencing not only the tech sector but also inspiring the next generation of women in software and product development. I have been nominated and won several tech-based awards in UK and currently building 3 digital products. ', 'expertise_industry' => '', 'other_industry' => '', 'current_role' => 'CINO', 'designation' => 'Chief Innovation Officer', 'company' => 'Maufin', 'experience_year' => '15', 'skills_normalized' => 'AI,Strategy ,Project Management ,Product Development,Entrepreneurship', 'linkedin_profile' => 'https://www.linkedin.com/in/funmi-oluduro-mba-spm-ambcs-019b6118/', 'facebook_profile' => '', 'instagram_profile' => '', 'x_profile' => '', 'residence' => 'Edinburgh', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'SMART Goals, Idea/Concept Note, 3-4 key career or business points for me to help with.', 'mentees_at_once' => '5', 'career_goals' => '', 'employment_status' => 'Employed Part-Time', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-06-17 07:23:06', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/650f53a086615d8a0aa9fa35b4877241_medium-913x920.jpg', 'thumb' => 'uploads/thumbnail/650f53a086615d8a0aa9fa35b4877241_thumb-148x150.jpg', 'has_bookings' => '1' ],
    [ 'id' => '128', 'name' => 'Aminat Olaseni Ogunlola', 'email' => 'olaseniogunlola@yahoo.com', 'role' => 'user', 'about_me' => 'As a Scientist, Artist, Tech Enthusiast & Serial Volunteer, I am a Multi-skilled & Multitalented Professional with a career focused on protecting and improving health outcome for the vulnerable population. At the centre of my passion lies a very deep desire to support young individuals on their journey of excellence. Imbibing values that help to shape their lives and society positively, as well as protecting and improving their mental health outcome.

Experience/Portfolio
-Currently work as a Quality Assurance Specialist within the Pharma industry in UK. Recently transformed the Automation of QMS metrics Excel Spreadsheet for my department and helped organisation channel time & effort conserved into highly prioritised activities.
-Working on personal Tech projects, recently explored with building an AI chatbot & AI model.
-Community Ambassador with Diabetes UK on Health Advocacy & campaigns to raise awareness about Diabetes risk factors and resources available. Featured on official website during Black history month 2023.
-Life Coach & Mentor for young people -personal volunteering project. Recently working on a major project with more structured support.
-Career Mentor for Glasgow Caledonian University final year students.
•Mentor at Royal Soceity of Chemistry. 
•Past Course Representative for 250+ Masters student on Public Health program at Glasgow Caledonian University 2022/23. Star Award winner in 2023.

Skills/Qualifications
•Bsc. Chemistry 2010 (Lagos, Nigeria)
•MSc. Public Health 2023 (Glasgow, Scotland UK)
•AI Engineering/Data Analytics internship 2025
•Member, Royal Soceity of Chemistry 2025
•Certified Lean Six Sigma White Belt, CLSSWB, 2025 - (Quality & Continuous improvement Specialist)

Others: A talented Creative - Realist Portrait Artist and more.', 'expertise_industry' => '25', 'other_industry' => '', 'current_role' => 'Quality Assurance Specialist ', 'designation' => null, 'company' => 'Target Healthcare Group', 'experience_year' => '', 'skills_normalized' => 'Multiskilled', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow ', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'My Mentees should at least have:

-A burning desire to step out of Mediocrity & ready to Thrive.
-Willingness to learn and adapt
-Readiness in taking responsibility & commitment of time for personal growth & development ', 'mentees_at_once' => '1', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '2', 'created_at' => '2025-06-17 08:48:38', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '129', 'name' => 'Ajoke Ehimiyein', 'email' => 'ajoke.ehimiyein@crl.com', 'role' => 'user', 'about_me' => 'Ajoke Ehimiyein, a Nigerian trained veterinarian, graduated from University of Ibadan in 2002. Worked at the Ahmadu Bello University, Zaria, as a lecturer, clinician and researcher. I had the opportunity to do my MSc research at the CDC, Rabies Section, Atlanta in 2009 where I worked on evaluation of the different diagnostic techniques use for rabies diagnosis especially for use in Nigeria. My PhD was on therapeutic intervention including antioxidant in the treatment of canine babesiosis. Had her MSc and PhD from ABU, Zaria and a post-doc at the University of Edinburgh (TIBA), a collaboration with African Academy of Science. I was opportune to be one of the five to be selected from Africa for TIBA post doc study to help tackle some diseases affecting Africa. I worked on histopathology affecting intestinal mucosae of rat infected with malaria parasite. Currently working with CRL as a Senior Scientist/Study director in General Toxicology, Safety Assessment, Charles River Laboratories where I lead the African Ancestry Employee Resource Group. I’m passionate about science and solving health related issues affecting humans and animals.  
', 'expertise_industry' => '30', 'other_industry' => '', 'current_role' => 'Senior Research Scientist/Study Director', 'designation' => null, 'company' => 'Charles River Laboratories', 'experience_year' => '', 'skills_normalized' => 'Good listener,Ability to inspire,Ability to motivate', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'Mentees should be proactive, communicate openly about their goals and challenges and take ownership for their own development. They must be able to follow through on commitments and agreed-upon actions. Mutual respect should be maintained.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-06-17 10:30:32', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '130', 'name' => 'Ekhueorohan Timothy  ', 'email' => 'timmyosa@gmail.com', 'role' => 'user', 'about_me' => 'Timothy presently works with the civil service in the United Kingdom. He is currently an executive officer with the Department for Work and Pensions, where he contributes to investigations and decision-making. 
As a customer service advisor, he has also worked for His Majesty Revenue and Customs (HMRC) and the Lloyds Banking Group. 

He has a master\'s degree in media and communication from the esteemed University of Strathclyde in Glasgow, United Kingdom, and has extensive experience in journalism, public relations, and advertising. He has over the years worked as an editor, reporter, and administrator at several media outlets and NGOs, where he was required to work with little supervision.

His desire to succeed in his career has driven him to participate in a number of volunteer activities. This has aided him in developing his professional competencies, skills, and knowledge in a company that values hard work and entrusts him with responsibilities and challenges. Additionally, he has a national diploma in mass communication from Auchi Polytechnic and a Bachelor of Arts from the esteemed University of Benin. 

He has mentored hundreds of people both locally and internationally in order for them to reach their goals in life. He is the CEO of Sir Tim-Osa Media, a company with an impressive staff that has produced champions in several industries locally as well as internationally.
He is happily married to Precious Timothy Ekhueorohan ', 'expertise_industry' => '27', 'other_industry' => '', 'current_role' => 'Decision Maker', 'designation' => null, 'company' => 'DWP', 'experience_year' => '', 'skills_normalized' => 'Analytical Thinking,Sound Judgment,Communication skills,Attention to details ,Customer Service Orientation,Integrity and Confidentiality,Resilience and Accountability,IT proficiency,Good time manager', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow, Scotland ', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'As a mentor, I focus on helping individuals discover purpose, navigate professional and spiritual growth, and develop the courage to lead in their chosen fields. My approach is structured, value-based, and driven by faith, excellence, and empathy."

The only requirements needed is to come with the right mindset to learn, unlearn and re-learn.', 'mentees_at_once' => '4', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-06-17 11:08:35', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/db6ef888e98ee3ade0a2b429f2da07a9_medium-900x1200.jpg', 'thumb' => 'uploads/thumbnail/db6ef888e98ee3ade0a2b429f2da07a9_thumb-112x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '132', 'name' => 'Frama Aboh ', 'email' => 'frama.aboh@gmail.com', 'role' => 'user', 'about_me' => 'I am a Senior Vice President reporting directly to the CEO of the UK entity of BNY. My primary focus is on Risks and Controls from a first-line perspective for the Bank. Additionally, I hold several other leadership positions, including Co-Site Head of the Edinburgh office and Co-Chair of our EMEA-wide Multicultural Network.

My first degree and background are in Law, which has provided me with a strong foundation in my technical skills and financial services. Beyond this, I have extensive experience in leadership, managing stakeholders, public speaking and advocacy, as well as community impact engagement', 'expertise_industry' => '5', 'other_industry' => '', 'current_role' => 'Senior Vice President- Legal Entity Risk and Controls', 'designation' => null, 'company' => 'Bank of New York (BNY)', 'experience_year' => '', 'skills_normalized' => 'Risk management,Leadership,Stakeholder Management,Advocacy,Problem solving,Analytical skills,Communication,Public speaking', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Armadale', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '
To be prepared and with an idea of what they want to get out of the mentoring relationship
', 'mentees_at_once' => '1', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-06-17 17:43:44', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/00dd60ff4524740d33ac5efc683b1463_medium-1200x799.jpg', 'thumb' => 'uploads/thumbnail/00dd60ff4524740d33ac5efc683b1463_thumb-150x99.jpg', 'has_bookings' => '0' ],
    [ 'id' => '133', 'name' => 'Helene KOUCHIKA', 'email' => 'kulchicbeauty@gmail.com', 'role' => 'mentee', 'about_me' => 'I’m a dedicated and results-oriented Project Management professional with over five years of experience as a Desk-Based Project Manager, specializing in structured cabling projects. I have a proven track record of coordinating cross-functional teams, managing budgets, and ensuring the successful delivery of projects on time and within scope.

My strengths lie in organisation, problem-solving, and effective stakeholder communication, which have enabled me to navigate complex project environments with confidence and clarity. I take pride in my ability to keep projects aligned with strategic goals while maintaining a focus on quality and cost-efficiency.

I’m now seeking new opportunities where I can leverage my experience in project execution and leadership, ideally in a dynamic organisation or even within a new industry, where I can continue to grow and make a meaningful impact.', 'expertise_industry' => 'Information Technology', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '10', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/helene-kouchika-16208665/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'CHELMSFORD', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I’m looking to further my career in project management or any role that involves leading and coordinating projects. I’m also open to exploring opportunities in a different industry, where I can apply and grow my skills in a new context', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-18 10:18:07', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '134', 'name' => 'ORUMADE ISRAEL ', 'email' => 'orumaizzy@gmail.com', 'role' => 'mentee', 'about_me' => 'I am a Creative Director and Media Specialist with over 15 years of experience in the film, broadcasting, and digital content space. I lead as the Founder and Creative Director of Virgos 3DX, a media and visual production company focused on storytelling that inspires, informs, and elevates.

In my career, I’ve had the privilege of working across various mediums—documentaries, television shows, feature films, branded campaigns, and social media content—managing projects from ideation to final cut. My work is rooted in both artistry and strategy, combining strong visual aesthetics with clear messaging.

I recently completed a Master of Science in Digital Campaigning and Content Creation at Queen Margaret University, where I sharpened my ability to craft purposeful campaigns using modern digital tools and platforms. My undergraduate foundation in Fine and Applied Arts (Graphics, Media & Advertising) from Nnamdi Azikiwe University laid the groundwork for my unique blend of visual storytelling and brand development.

Through Virgos 3DX and other collaborations, I help brands and organizations create compelling visual experiences that drive engagement, spark conversations, and build connection.

I believe media is not just content—it’s culture. And I’m here to shape it with purpose.

Let’s connect, collaborate, and create something extraordinary.', 'expertise_industry' => 'Media', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '16', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com/in/israyelorumade', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Wilkieston', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My career goal is to become a globally recognized Creative Leader and Media Strategist, driving impactful storytelling through film, digital campaigns, and immersive content. I aim to scale into a leading creative powerhouse producing purpose-driven media that influences culture, empowers communities, and elevates brands.

I am passionate about merging creativity with strategy, and I aspire to collaborate with global organizations, non-profits, and forward-thinking brands to craft content that addresses real-world challenges social, political, spiritual, and environmental.

In the long term, I envision building a creative academy or mentorship platform to train and empower young creatives in Africa and beyond, equipping them with skills in digital media, branding, and content creation to become change-makers in their own right.

', 'employment_status' => 'Employed Part-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-19 08:59:18', 'bp_network' => '', 'gender' => 'Male', 'phone' => '07887901980', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '136', 'name' => 'Geoffrey Idun', 'email' => 'hello@luxia.uk', 'role' => 'mentee', 'about_me' => 'With 25 years of experience spanning roles as a Graphic Designer, Brand Manager, Creative Director, a PIEMA level Sustainability Consultant and more recently an AI Agents implementation specialist, I bring a unique blend of creativity, strategic vision, and environmental consciousness to every project. 

Throughout my career, I have led innovative design initiatives that not only captivate audiences but also prioritise sustainability and social responsibility. From developing impactful branding strategies to implementing eco-friendly design practices, I am committed to driving positive change through creative excellence. ', 'expertise_industry' => 'Arts/Creative/Entertainment', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '25', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/geoffidun/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Cardiff', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My goal now is to setup and run a successful AI agents consultancy and implementation agency that helps Shopify owners to fundamentally transform how their business operates. Using the capabilities of AI agents we aim to drive faster growth, increase sales, improve customer engagement, and enhance efficiency for Shopify stores by implementing personalisation and upselling, optimised pricing, automated customer support, inventory management and demand forecasting, data analytics and much more.', 'employment_status' => 'Self-employed', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-23 10:37:52', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '137', 'name' => 'Getrude', 'email' => 'getrudetendaichipfuwamuti@gmail.com', 'role' => 'mentee', 'about_me' => 'I am a Project Manager with hands-on experience delivering digital and website projects. I have worked with cross-functional teams including developers, business analysts, and QA testers to manage timelines, define requirements, and deliver solutions that align with business goals.
Over the past year, I have led Agile ceremonies, coordinated testing phases, and supported budget tracking for tech and marketing-driven initiatives. I have also developed and executed event and marketing strategies that helped build community engagement and brand awareness for non-profit and start-up organisations.
I enjoy working on meaningful projects, keeping things organised, and ensuring smooth communication between teams and stakeholders.', 'expertise_industry' => 'Telecommunications', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '3 years', 'skills_normalized' => '', 'linkedin_profile' => 'http://linkedin.com/in/getrude-t-c-a590b417a', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Dunfermline ', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Secure a role in Scotland with Visa Sponsorship. 

Willing to partake in volunteering roles to build more experience. 

', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-23 20:57:51', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '138', 'name' => 'Brittany Lynn Martin ', 'email' => 'byblynn@gmail.com', 'role' => 'mentee', 'about_me' => 'I’m an accomplished Event and Venue Operations professional with over a decade of experience leading complex, large-scale events across Canada. With a strong foundation in venue management, I’ve overseen the operations of six premier event spaces in Toronto, managing logistics, staffing, and execution while leading a team of 40+.

Throughout my career, I’ve worked with top-tier agencies to deliver national brand experiences for clients such as Shopify and Hershey Chocolate. I’ve also managed venue operations for high-profile events hosted by brands like Nike, Netflix, and Apple. My background includes hands-on experience in event décor, rentals, and vendor management, with a focus on operational excellence and seamless execution.

Recognized for my leadership, adaptability, and collaborative approach, I specialize in crafting high-impact event experiences, from planning to delivery. I’m currently based in Toronto and plan to relocate to the UK in Fall 2025 under the Youth Mobility Scheme, where I aim to contribute my expertise to a dynamic team in London or across the UK.', 'expertise_industry' => 'Hospitality', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '10', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/blynnmartin/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Mississauga', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'In the short term, my goal is to secure a senior-level role in London where I can gain hands-on experience working with a reputable brand, music or entertainment venue, or producing high-profile events. I\'m also open to project management roles within the event space that will challenge me and allow me to expand my skill set. As part of the Youth Mobility Scheme, I will be based in the UK for two years, and I’m eager to gain invaluable experience that I might not have had access to in Canada.

I see this opportunity as a way to take my career to new heights, broaden my international perspective, and build meaningful industry connections. I’m seeking mentorship from professionals in the UK events space—someone who can offer insight into the local market, provide feedback, and help guide me toward the best opportunities to grow and make an impact.

In the long term, my ambition is to step into a Director-level position and beyond. Ultimately, I aim to invest in or operate my own business in the event industry, combining my years of hands-on experience with a passion for creative, large-scale event production and leadership.', 'employment_status' => 'Not employed but looking for work', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-06-24 18:12:51', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/f266ead77edd11eb88a1f578888fcf3d_medium-941x941.jpg', 'thumb' => 'uploads/thumbnail/f266ead77edd11eb88a1f578888fcf3d_thumb-150x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '139', 'name' => 'Mariam Mosuro', 'email' => 'folaky2001@yahoo.com', 'role' => 'mentee', 'about_me' => 'I am a student in high school,  I am interested in working with a mentor who would support me, help me build my confidence, speaking out and presentation. And guide me as I grow. ', 'expertise_industry' => 'Energy', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '0', 'skills_normalized' => '', 'linkedin_profile' => 'Non', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow ', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I am interested in Legal and Tech.. am still deciding ', 'employment_status' => 'Student', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-25 06:16:59', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '140', 'name' => 'Chinwendu Mesharch ', 'email' => 'austinemeshach@gmail.com', 'role' => 'mentee', 'about_me' => 'I am an experienced engineer with 10 years experience in the power generation industry from Nigeria. Currently rounding up my masters in Reliability Engineering and Asset Management.', 'expertise_industry' => 'Energy', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '10', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/chinwendu-mesharch-cmrp-14260427?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Manchester ', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I am looking for roles in Reliability/Maintenance Engineering within the energy or process industry ', 'employment_status' => 'Not employed but looking for work', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-06-27 19:11:12', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '141', 'name' => 'Olayinka Tajudeen Balogun ', 'email' => 'yinka.balogun.t@gmail.com', 'role' => 'mentee', 'about_me' => 'I am Olayinka Balogun, I moved to the UK (Croydon, London) this earlier this month from Nigeria on a spousal visa with right to work. I have about 5 years of experience, 4+years of that was with KPMG and EY Deals Advisory team.

I worked on financial due diligence, financial modelling, financial planning & analysis and valuations project while working with KPMG and EY.

I have done lots of applications and a couple of some interviews but with feedback around concerns about not having UK experience. I keep pushing and believe having a mentor would also guide and aid my push and applications.   ', 'expertise_industry' => 'Other', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '5', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/balogun-olayinka/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Croydon', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My career goal is to become a transformative leader in the finance and investment space, with a particular focus on helping businesses—especially in emerging markets like Nigeria—access the capital and strategic support they need to thrive. Having worked across financial modeling, valuation, due diligence, and strategic advisory in both local and international contexts.

In the short term, I aim to deepen my expertise in corporate finance and M&A by working on complex transactions that span industries and borders. I want to be involved in deals from start to finish—conducting analysis, structuring capital, managing risk, and ultimately unlocking value. 

In the long term, I aspire to be a catalyst for economic growth in Nigeria and across Africa. I intend to build a platform that supports small and growing businesses through capital provision, strategic advice, and operational guidance. Additionally, I hope to return to the fashion industry—not just as a legacy, but as an entrepreneur who applies financial discipline and innovation to create a globally competitive brand.

Ultimately, I want to bridge global capital with African opportunity, using finance as a tool for sustainable development and inclusive growth.
', 'employment_status' => 'Not employed but looking for work', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-06-27 19:45:13', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/e63dc77848cce1f76f3bdc53a38aab7f_medium-800x800.jpg', 'thumb' => 'uploads/thumbnail/e63dc77848cce1f76f3bdc53a38aab7f_thumb-150x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '142', 'name' => 'Judy Wanjiku  ', 'email' => 'wanjikujudy395@gmail.com', 'role' => 'mentee', 'about_me' => 'I am a recent business analytics postgraduate with a soft spot for project management. That passion has inspired me to earn a few certifications like Scrum Master, Agile Project Management (APM) Foundation et cetera, because I find the intersection of technology and project coordination super fascinating.
 
But life is not all certificates and TED Talks, I enjoy spending time listening to music, trying new workouts and reading autobiographies. It is all about balancing a curious mind with a curious life.', 'expertise_industry' => 'Information Technology', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2 years', 'skills_normalized' => '', 'linkedin_profile' => 'n/a', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My career goal is to be  an IT Project Manager', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-06-28 18:06:53', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '143', 'name' => 'Titi Saidu ', 'email' => 'saidutitilayo@gmail.com', 'role' => 'user', 'about_me' => 'I\'m currently a Vice President at J.P. Morgan Chase, leading efforts in Technology Risk and Controls. With a strong background in governance, compliance, and operational risk, I\'ve worked across complex environments to build resilient control frameworks and support safe technology delivery. I\'m particularly passionate about empowering others to navigate ambiguity with confidence, and I enjoy mentoring around career transitions, strategic influence, and professional presence — especially for underrepresented voices in corporate spaces.', 'expertise_industry' => '20', 'other_industry' => '', 'current_role' => 'VP- Technology Risk and Controls Lead', 'designation' => null, 'company' => 'JP Morgan Chase ', 'experience_year' => '', 'skills_normalized' => 'Cybersecurity, AI Risk & Governance,technology risk & controls', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Bournemouth ', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'Passion to succeed and grow', 'mentees_at_once' => '4', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-07-01 12:19:33', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '144', 'name' => 'Elizabeth Oyelowo', 'email' => 'emojioyelowo@gmail.com', 'role' => 'mentee', 'about_me' => 'I am a finance professional with varied background in accounting, audit, tax and recently developing skills in sustainability.', 'expertise_industry' => 'Banking & Financial Services', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '10+', 'skills_normalized' => '', 'linkedin_profile' => 'linkedin.com/in/elizabethoyelowo', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => ' I am looking to garner my varied knowledge, skills, and interests into a fulfilling career here in the UK.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-07-04 14:51:29', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '145', 'name' => 'the juggular', 'email' => 'heyhellobluu@gmail.com', 'role' => 'mentee', 'about_me' => 'The Rain', 'expertise_industry' => 'Arts/Creative/Entertainment', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '4', 'skills_normalized' => '', 'linkedin_profile' => '#', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Guzappe', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'The Sunshine', 'employment_status' => 'Not employed and not looking for work', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-07-11 17:20:44', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '146', 'name' => 'Boluwatife Oluwaseun', 'email' => 'boluwatifeoluwaseunfaith@gmail.com', 'role' => 'mentee', 'about_me' => 'I am Tife, an MSc business analytics graduate with a diverse background in marketing analytics, customer insight analytics, and web analytics. Currently working in the Insurance sector as a Customer Experience Coordinator.', 'expertise_industry' => 'Information Technology', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/boluwatife-oluwaseun/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Gedling, Nottingham', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Fully transition into a data analyst or data scientist role', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-07-12 04:45:03', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '147', 'name' => 'Monde Porter', 'email' => 'qaunettantuli@gmail.com', 'role' => 'user', 'about_me' => 'I’m a results-driven marketing leader with over 12 years of experience growing challenger and legacy brands across FMCG categories in the UK and Africa. With a foundation in formulation science and a passion for brand storytelling, I’ve built a career on driving commercially impactful strategies that resonate culturally and emotionally.

Currently, I serve as International Marketing Manager at Vimto, leading brand, innovation, and channel strategy across global markets. My focus is on unlocking growth in underleveraged territories, building culturally relevant campaigns, and expanding the presence of Red Vimto in diverse consumer spaces. From portfolio planning to go-to-market execution, I thrive on making global brands feel locally.', 'expertise_industry' => '7', 'other_industry' => '', 'current_role' => 'International Marketing Manager ', 'designation' => null, 'company' => 'Nichols plc', 'experience_year' => '', 'skills_normalized' => 'Marketing,Public speaking  ,P&L Management  ,Brand Strategy', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'East Kilbride', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'I would like my mentee to be anyone who is willing to make it an mutual exchange. Preferably a female based in Scotland, to allow for face to face engagement. ', 'mentees_at_once' => '1', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-07-16 14:59:39', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '148', 'name' => 'Faith Lamba', 'email' => 'fzlamba1@gmail.com', 'role' => 'mentee', 'about_me' => 'I have a background in Economics for my bachelor’s and Energy and Economics for my master’s program. I am interested in understanding how economic principles can influence or help solve real-world problems. I consider myself an open-minded person, and I am exploring different areas of economics, including finance, human resource management, and policy, to see where I can make the most impact. I am looking forward to learning from experienced professionals through this mentorship program and gaining more clarity about my career direction.', 'expertise_industry' => 'Professionals Services', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '1', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/faith-lamba-56b34532b/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I am still exploring the specific path I want to take, but my goal is to build a career where I can apply economic knowledge to real-world situations whether in finance, policy, human resources, or energy. I am especially interested in roles that involve problem-solving, working with people, and contributing to meaningful change. Through this mentorship program, I hope to learn more about the different opportunities out there, gain practical insights, and grow both personally and professionally.', 'employment_status' => 'Student', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-07-21 23:55:05', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '149', 'name' => 'Ajoke Ehimiyein', 'email' => 'ajokeeo@gmail.com', 'role' => 'user', 'about_me' => 'Ajoke Ehimiyein, a Nigerian trained veterinarian, graduated from University of Ibadan in 2002. Worked at the Ahmadu Bello University, Zaria, as a lecturer, clinician and researcher. I had the opportunity to do my MSc research at the CDC, Rabies Section, Atlanta in 2009 where I worked on evaluation of the different diagnostic techniques use for rabies diagnosis especially for use in Nigeria. My PhD was on therapeutic intervention including antioxidant in the treatment of canine babesiosis. Had her MSc and PhD from ABU, Zaria and a post-doc at the University of Edinburgh (TIBA), a collaboration with African Academy of Science. I was opportune to be one of the five to be selected from Africa for TIBA post doc study to help tackle some diseases affecting Africa. I worked on histopathology affecting intestinal mucosae of rat infected with malaria parasite. Currently working with CRL as a Study director in Toxicology and the lead for African Ancestry Employee Resource Group in Charles River.', 'expertise_industry' => '30', 'other_industry' => '', 'current_role' => 'Senior Research Scientist', 'designation' => null, 'company' => 'Charles River Laboratories', 'experience_year' => '', 'skills_normalized' => 'Good listener, Build trust', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'The mentee should be transparent, truthful and ready to work on any assigned task. ', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-07-28 08:15:20', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '150', 'name' => 'Tendai ', 'email' => 'tschitsamatanga@gmail.com', 'role' => 'mentee', 'about_me' => 'I have a background in Actuarial Science and currently work as a Risk analyst withing credit risk. I am currently in my first professional role and figuring out my next career steps. ', 'expertise_industry' => 'Banking & Financial Services', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/tendai-chitsamatanga', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'PAISLEY', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Through this mentorship opportunity, I am looking for guidance and how to grow in my career and develop a stringer sense of confidence in my skills. I also want to improve my critical thinking and strategic planning abilities so I can approach problems with a more structured abs forward thinking mindset. Beyond technical skills I am eager to learn how to navigate my career progression, building meaningful professional relationships, and position myself for future opportunities. I am open to advice, new perspectives, and actionable steps that will help me grow both professionally and personally. ', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-07-28 16:05:08', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '151', 'name' => 'Shakirat Sadiq', 'email' => 'gbolagadeshakirat@gmail.com', 'role' => 'mentee', 'about_me' => 'I am a dedicated HR  professional from Nigeria, with  a strong track record in driving people-focused initiatives. My experience spans across talent management, training and development, HR policy advisory, and fostering positive workplace culture. 

Relocating to the UK has expanded my perspective, and I\'m currently  navigating a career transition, and I am sourcing for opportunities to learn, network and grow.
', 'expertise_industry' => 'Other', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '5', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com/in/shakirat-gbolagade', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Aberdeen', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My career goal is to secure a HR role or any role where i can leverage my expertise in people operations, talent acquisition, to deliver measurable business impact.

I am currently  focused on roles that allow me  to influence change, optimize processes, and contribute to organizational growth and personal growth at the same time. My ultimate aim is to be a strategic HR leader who drives transformation, innovation, and positive workplace experiences globally.', 'employment_status' => 'Not employed but looking for work', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-08-14 00:32:39', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '152', 'name' => 'Ikenna Onuoha', 'email' => 'ikenna4000@yahoo.com', 'role' => 'mentee', 'about_me' => 'I am a business and finance analyst  seeking a role in finance and tech. ', 'expertise_industry' => 'Information Technology', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '5', 'skills_normalized' => '', 'linkedin_profile' => 'Ikenna onuoha', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I hope to work in finance and tech as an entrepreneur and develop an app', 'employment_status' => 'Prefer Not to Answer', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-08-27 13:53:22', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '153', 'name' => 'Kayode Adedigba ', 'email' => 'kadedigba@gmail.com', 'role' => 'user', 'about_me' => 'Bio – Kayode Adedigba, CBAP

Kayode Adedigba is a Solutions Architect at o9 Solutions with nearly 20 years’ experience in supply chain, business analysis, and digital transformation across Africa and Europe. He also serves as Communities Director (North & Scotland) for IIBA UK and volunteers as a Children’s Champion with AFRUCA. Passionate about mentoring, Kayode enjoys guiding others in building purposeful careers, growing digital skills, and developing leadership mindsets that create real impact.', 'expertise_industry' => '20', 'other_industry' => '', 'current_role' => 'Solutions Architect', 'designation' => null, 'company' => 'o9 Solutions UK Limited', 'experience_year' => '', 'skills_normalized' => 'Business Analysis,Business Architecture,Solution Architecture,Supply Chain Management,Integrated Business Planning,Revenue Growth Management', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Middlesbrough', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'A suitable mentee should:

1. Have clear goals or areas of growth in mind
2. Be committed and consistent with follow-through
3. Stay open to feedback and learning
4. Show curiosity and initiative
5. Respect time and communication in the relationship

In short: intentional, open-minded, and proactive.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-09-01 14:14:14', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'uploads/medium/d21d100f1f7287336368bad1e0212bf1_medium-1200x1200.jpg', 'thumb' => 'uploads/thumbnail/d21d100f1f7287336368bad1e0212bf1_thumb-150x150.jpg', 'has_bookings' => '0' ],
    [ 'id' => '154', 'name' => 'Folawemi', 'email' => 'folawemi01@gmail.com', 'role' => 'mentee', 'about_me' => 'PROFESSIONAL SUMMARY
I am a highly motivated and detail-oriented professional with a passion for environmental sustainability and regulatory compliance. With hands-on experience in the power-generation and infrastructure sectors, I’ve successfully supported cross-functional projects that align business operations with net-zero goals and sustainable growth. My expertise spans across project planning, stakeholder engagement, GHG accounting (Scope 1,2 and 3), carbon footprint analysis and lifecycle assessment. I have a proven ability to collect, validate and interpret environmental data to drive informed decision-making and ensure adherence to UK and global sustainability standards.', 'expertise_industry' => 'Energy', 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'linkedin.com/in/folawemi-olubiyo-8987461b8', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Short-term Career goals; Secure a role in environmental management, sustainability strategy development or project management where i can apply my expertise in Carbon accounting (Scope 1, 2 & 3), Lifecycle assessments, Environmental and Stakeholder engagement. Also expand my professional network within the sustainability and energy transition sectors across the UK.
Long-term Career goals: Lead sustainability initiatives that integrate regulatory compliance, carbon management, and innovative project delivery, Drive impact in the energy and infrastructure sectors by contributing to net-zero targets and climate resilience and Position myself as a strategic advisor or sustainability leader, influencing policy, procurement, and design decisions across major infrastructure projects.', 'employment_status' => 'Not employed but looking for work', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-09-02 13:06:51', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '157', 'name' => 'John Doe', 'email' => 'md.mhhn@gmail.com', 'role' => 'user', 'about_me' => 'test', 'expertise_industry' => '2', 'other_industry' => '', 'current_role' => 'admin', 'designation' => null, 'company' => '', 'experience_year' => '', 'skills_normalized' => 'sdaf', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'asdf', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'test', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '1', 'created_at' => '2025-09-20 06:26:25', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '158', 'name' => 'Benny', 'email' => 'bnmeholam@blackprofessionals.uk', 'role' => 'mentee', 'about_me' => 'hhhhgggg', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '7', 'skills_normalized' => '', 'linkedin_profile' => 'hhhhhhh', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Falkirk', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'sddddddd', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-09-22 13:42:27', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '159', 'name' => 'wazed', 'email' => 'wazedm325@gmail.com', 'role' => 'mentee', 'about_me' => 'dsfdsf', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '5 year', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Dhaka', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'sdfdsf', 'employment_status' => 'Employed Part-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-09-24 08:07:14', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '160', 'name' => 'Johanna Nkwanyuo ', 'email' => 'nkwanyuojohanna@gmail.com', 'role' => 'mentee', 'about_me' => 'I am a first-year student studying Biomedical Science at Glasgow Caledonian University, and I aspire to be a biomedical scientist. However, on this journey, I am aware of a plethora of directions I can take and the contributions I can make to private organisations, hospitals, and the field of biology and medicine at large . As I study and volunteer in the near future I am aware I will be able to find my niceh and particular field I would like to specialize in.
', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '0', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com/in/johanna-nkwanyuo', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow, Glasgow', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => '-Volunteer / work as an intern in a scientific or hospital laboratory
-Industry Placement for 3rd year of my course
-Become a registered HCPC Biomedical Scientist
-Work in an environment actively engaged in research (specifically cancer) and developing new diagnostic and treatment methods for patients in real time  ', 'employment_status' => 'Student', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-09-28 23:02:33', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/e114128ad4938d266a25d16737a6b455_medium-1200x900.jpg', 'thumb' => 'uploads/thumbnail/e114128ad4938d266a25d16737a6b455_thumb-150x112.jpg', 'has_bookings' => '0' ],
    [ 'id' => '161', 'name' => 'erhu kome', 'email' => 'thirty.gravity-5b@icloud.com', 'role' => 'user', 'about_me' => 'Share a bit about yourself to help mentees get to know you better! Include your current role, professional background, and any unique skills or interests that could guide and inspire others. Feel free to keep it short and focused.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => 'Director', 'designation' => null, 'company' => '', 'experience_year' => '', 'skills_normalized' => 'Writing', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Isle of Man', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => 'Share a bit about yourself to help mentees get to know you better! Include your current role, professional background, and any unique skills or interests that could guide and inspire others. Feel free to keep it short and focused.', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Part-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-09-29 12:09:27', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '162', 'name' => 'Juliajat', 'email' => 'cinkincaid@gmail.com', 'role' => 'mentee', 'about_me' => 'Hello! We are interested in promising projects for investment. If you have an innovative idea or business model but lack the funding to implement it, we invite you to discuss support options. <a> Text me on Whatsapp for quick communication</a>', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '1981', 'skills_normalized' => '', 'linkedin_profile' => 'http://wa.me/+79602868073]', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Nur-Sultan', 'mentorship_availability' => 'Once in 2 Months', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'Hello! We are interested in promising projects for investment. If you have an innovative idea or business model but lack the funding to implement it, we invite you to discuss support options. <a> Text me on Whatsapp for quick communication</a>', 'employment_status' => 'Homemaker', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-09-30 05:04:05', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '163', 'name' => 'Benny NM ', 'email' => 'bennynmeholam@gmail.com', 'role' => 'user', 'about_me' => 'hiiiiii', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => 'Manager', 'designation' => null, 'company' => 'BPU', 'experience_year' => '', 'skills_normalized' => 'Mgt', 'linkedin_profile' => '', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Scotland', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => 'Balance', 'mentees_at_once' => '2', 'career_goals' => '', 'employment_status' => 'Employed Full-Time', 'is_active' => '1', 'status' => '1', 'created_at' => '2025-10-06 16:45:37', 'bp_network' => 'Black Professionals United Kingdom', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '1' ],
    [ 'id' => '164', 'name' => 'tank top', 'email' => 'tjakintayo@gmail.com', 'role' => 'mentee', 'about_me' => 'hey love', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '4', 'skills_normalized' => '', 'linkedin_profile' => '#', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'yes', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'wow', 'employment_status' => 'Self-employed', 'is_active' => '1', 'status' => '0', 'created_at' => '2025-10-07 11:18:27', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '165', 'name' => 'Musa Amanda Madanhire  ', 'email' => 'musa.madanhire1@icloud.com', 'role' => 'mentee', 'about_me' => 'I’m a detail-oriented professional transitioning from a 10-year teaching career into project management. With experience coordinating teams, managing deadlines, and improving systems, I bring strong organisational and communication skills to every project. I’ve completed PRINCE2® and Agile training and recently led a cross-functional team to deliver a successful product launch on schedule. I’m now seeking opportunities in project management or communications where I can apply my leadership and problem-solving skills to drive meaningful results.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '10', 'skills_normalized' => '', 'linkedin_profile' => 'www.linkedin.com/in/musa-amanda-madanhire-b8a5a2125', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Aylesbury', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I’m an experienced educator with a strong background in coordinating projects, managing deadlines, and delivering outcomes through effective teamwork and clear communication. My experience in curriculum planning and stakeholder collaboration has strengthened my ability to organise complex tasks, analyse data, and adapt messages for different audiences. I’m passionate about improving systems, streamlining processes, and supporting teams to achieve shared goals. I’m now looking to transition into a project management or communications role where I can apply my organisational, analytical, and interpersonal skills to create meaningful impact.', 'employment_status' => '', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-10-07 21:25:31', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'uploads/medium/9516949ada5bcdd6ea4c00318dfc2a1e_medium-810x728.jpeg', 'thumb' => 'uploads/thumbnail/9516949ada5bcdd6ea4c00318dfc2a1e_thumb-150x134.jpeg', 'has_bookings' => '0' ],
    [ 'id' => '166', 'name' => 'Francis ogufere', 'email' => 'francisogufere@gmail.com', 'role' => 'mentee', 'about_me' => 'I have a first degree in Electrical/Electronics engineering. I worked as a project engineer for 5 years . I completed a masters in Sustainable engineering and presently work as a Smart Grid Engineer in a Mitsubishi Subsidiary in Glasgow. I wrote prince 2 in 2024 and passed the practitioner and I’m looking to pivot to project management ', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '15 ', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/francis-ogufere-4a358710a?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow ', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'To become a project manager in the Uk company ', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-10-20 10:44:47', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '167', 'name' => 'Cynthia Njogu', 'email' => 'wambuicynthia670@gmail.com', 'role' => 'mentee', 'about_me' => 'I completed my MSc in Business Management at Edinburgh Napier University, after previously earning a BSc in Industrial Mathematics from Jomo Kenyatta University of Agriculture and Technology. I’m currently working at Lloyds Banking Group as a Senior Pensions Administrator, where I handle pension cases, help customers, and support the team with day-to-day operations. My background blends analytical skills with business knowledge, and I’m now looking to grow further within the banking and financial services space.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/cynthia-njogu-a6a87023a', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I’m currently working to progress into an analyst role within finance, ideally in areas like data analysis, financial analysis, or risk. I want to build on my analytical background and my experience at Lloyds by moving into a role where I can work more with data, support decision-making, and contribute to improving processes and controls. My focus right now is developing the skills and experience needed to step into an analyst position as my next career move.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-11-14 07:33:24', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '168', 'name' => 'Claire Rowayinor  ', 'email' => 'clairerowayinor@icloud.com', 'role' => 'mentee', 'about_me' => 'Motivated and organised professional with experience across property management and financial services.

Skilled at building strong relationships, driving efficient operations, and delivering practical solutions. Confident working with data, systems, and technology (Microsoft Office and Apple platforms) while maintaining a clear, people-focused communication style. 

Passionate about advancing diversity, equity, and inclusion, upholding ethical business practices, contributing to philanthropic efforts, and staying active through sports.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '1', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/clairerowayinor/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh City', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I describe myself as a visionary and an innovator. My main subjects of interest are Legal Studies, Business Management and Finance (Life and Asset Management).

', 'employment_status' => 'Not employed but looking for work', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-12-08 15:39:05', 'bp_network' => '', 'gender' => 'Female', 'phone' => '07878469552', 'image' => 'uploads/medium/97017d05f7290f8732ba8d69d6180883_medium-900x1200.jpeg', 'thumb' => 'uploads/thumbnail/97017d05f7290f8732ba8d69d6180883_thumb-112x150.jpeg', 'has_bookings' => '0' ],
    [ 'id' => '169', 'name' => 'Godswill omonkhodion ', 'email' => 'Godswill.Bincom@gmail.com', 'role' => 'mentee', 'about_me' => '​I am a highly motivated Junior Software Engineer with a two-year record of professional experience in full-stack development.
​My expertise includes two years specializing in front-end technologies, coupled with one year of experience utilizing Java Spring Boot for back-end development.
​I am a Nigerian citizen currently based in Scotland, United Kingdom.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'linkedin.com/in/omonkhodion', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => '​My primary career objective is to become a seasoned, full-stack software developer with deep and broad expertise across the modern software development ecosystem.
​This comprehensive technical proficiency will encompass:
​Solution Architecture: The ability to design scalable, robust, and strategic enterprise systems.
​Cloud Computing: Advanced knowledge and practical application of platforms such as AWS.
​DevOps and Containerization: Mastery of essential technologies like Docker for deployment and system management.
​This accelerated development trajectory is strategically aimed at preparing me for a leadership role, specifically targeting an Assistant Vice President (AVP) position, within the next three years.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2025-12-13 20:29:27', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '170', 'name' => 'Samson Ofoesuwa', 'email' => 'samsonofoesuwa@gmail.com', 'role' => 'mentee', 'about_me' => 'I am seeking a mentorship to help me move into a career path that offers strong financial progression, long-term relevance, and meaningful responsibility. I have experience in operations, process management, and running my own business, and I’m motivated to develop skills that place me closer to decision-making and commercial impact. I’m particularly interested in learning how to leverage operational, analytical, and strategic thinking to build a sustainable and well-paid career, while avoiding roles with limited growth or impact. A mentor would help me gain clarity, challenge my thinking, and make smarter career moves with intention rather than trial and error.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '4', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/samson-o-b83246150/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'London, UK, London, Greater London, United Kingdom, SE15 4UG, United Kingdom', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My career goal is to build a financially rewarding, future-proof career where I can use my operational, analytical, and problem-solving skills to drive real commercial impact. I want to move into roles with ownership, decision-making responsibility, and clear progression, rather than long-term support positions. I am motivated by high standards, accountability, and continuous learning, and I’m seeking opportunities that align my skills with meaningful outcomes and long-term growth.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2026-01-18 23:02:30', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '171', 'name' => 'Chito Obiefune', 'email' => 'Ckasobi@gmail.com', 'role' => 'mentee', 'about_me' => 'Energetic, meticulous, focused. Passionate about project management and operations across areas of real estate, infrastructure, technology and product. 3+ years of experience managing projects that balance vision with execution – working across people, processes, and technology to deliver outcomes aligned with stakeholder expectations. Certified Workflow Specialist with a practical delivery driven approach to process optimisation. 

My background spans project management, real estate, and information technology. Certified in Negotiation Mastery by Harvard Business School Online with a masters in Real Estate Management (RICS accredited) from the University of the West of England. Currently studying for my second masters in Strategic Project Management + PRINCE2 Practitioner cert.

Focus areas: Project Management | Project Delivery | RE and Infrastructure PM | Manufacturing PM | Corporate Strategy | Workflow Development | Property Management and Negotiations | Product Management | Sustainability (Built Environment).

Software tools: Jira, Asana, Trello, Visio, ClickUp, Vebra Alto, Excel, MS 365, Automation tools (Make, N8N, etc. for workflow design, task automation and reporting)

Please reach out: chitoconnect@gmail.com', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/chitoo?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Edinburgh', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I am currently to gain hands-on experience in project management, especially in areas of real estate and infrastructure, technology, and sustainability.', 'employment_status' => 'Student', 'is_active' => '0', 'status' => '0', 'created_at' => '2026-01-23 02:43:49', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '172', 'name' => 'Tami Olarinoye', 'email' => 'tamiolarinoye@gmail.com', 'role' => 'mentee', 'about_me' => 'I’m an early-career investment professional with around three years’ experience across wealth management and portfolio support roles. I’ve worked closely with senior advisers and portfolio managers, supporting investment analysis, portfolio implementation, and client outcomes within regulated environments. I’ll be starting a new job in investment management next week, and I’m keen to deepen my technical knowledge, build conviction in decision-making, and accelerate my development as an investor. I value mentorship, accountability, and learning from those who have navigated similar paths.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '3', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/tamiolarinoye/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Newham, London', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My goal is to progress as far as possible within investment management. In the near term, I want to sharpen my investment judgement, research skills, and confidence in portfolio decision-making. Over the longer term, I aim to grow into a well-rounded investment professional with strong technical expertise, sound judgement, and the ability to add real value to clients and teams.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2026-02-10 18:31:25', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '173', 'name' => 'Damilola Ogungbemi', 'email' => 'd.e.ogungbemi@leeds.ac.uk', 'role' => 'mentee', 'about_me' => 'I am a Data Scientist with a background in Microbiology and Biotechnology, currently working at the Leeds Institute for Data Analytics (LIDA). My work focuses on applying data science to real-world challenges, particularly in areas that serve the public good. I am interested in responsible data use, data ethics, and using analytics to inform better decision-making.

Before transitioning fully into data science, my academic and research experience involved scientific analysis, problem solving, and working with complex datasets. I enjoy helping others navigate the path into data and research careers, especially those coming from non-traditional or scientific backgrounds.

Beyond my technical work, I am passionate about service, community impact, and personal development. I value discipline, integrity, and intentional growth, and I am keen to learn from experienced professionals and gain guidance that will help me navigate the field more effectively.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/damilola-ogungbemi/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Leeds', 'mentorship_availability' => 'Twice a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'My long-term goal is to build a career in health data science, where I can apply analytical methods to improve health outcomes and support evidence-based decision-making. I am particularly interested in areas such as population health, health informatics, and the use of data to address public health challenges.

Through mentorship, I hope to gain insight into the health data science landscape, understand the skills and experiences needed to progress in the field, and receive guidance on how to strengthen my technical and research profile. I am also keen to learn how professionals in the field approach health data problems and translate the analysis into meaningful impact.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2026-03-16 15:42:54', 'bp_network' => '', 'gender' => 'Female', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '174', 'name' => 'Samson udoaka ', 'email' => 'samsonudoaka@gmail.com', 'role' => 'mentee', 'about_me' => 'I was a project engineer for a construction company in nigeria.  my role focuses on work coordination and management of financial expenses. I relocated to uk to study and complete a masters degree in engineering project management. I have completed PRINCE 2 Foundation in project management and did some project management voluntary roles. I strongly desire to get back into project management in the UK energy sector.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2', 'skills_normalized' => '', 'linkedin_profile' => 'Not yet created', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'scotland', 'mentorship_availability' => 'Once a month', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I aim to be a project engineer in the energy sector in 6months', 'employment_status' => 'Prefer Not to Answer', 'is_active' => '0', 'status' => '0', 'created_at' => '2026-04-14 18:57:44', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
    [ 'id' => '175', 'name' => 'Caleb Achile', 'email' => 'caleb.achile@outlook.com', 'role' => 'mentee', 'about_me' => 'I am a young professional currently working as a risk analyst in financial services. I have an undergraduated degree in Economics and a masters in Risk Management with about 3.5 years total work experience. Currently reside in the United Kingdom.', 'expertise_industry' => null, 'other_industry' => '', 'current_role' => '', 'designation' => null, 'company' => '', 'experience_year' => '2.5', 'skills_normalized' => '', 'linkedin_profile' => 'https://www.linkedin.com/in/caleb-achile-a4643422b/', 'facebook_profile' => null, 'instagram_profile' => null, 'x_profile' => null, 'residence' => 'Glasgow', 'mentorship_availability' => 'Once in 2 Months', 'mentorship_requirements' => '', 'mentees_at_once' => '', 'career_goals' => 'I look to further my career and gain relevant skills to elevate to strategic management positions. Currently working as an analyst and closer to the business, I am looking to further my skills to well equip me for management roles.', 'employment_status' => 'Employed Full-Time', 'is_active' => '0', 'status' => '0', 'created_at' => '2026-05-05 14:21:58', 'bp_network' => '', 'gender' => 'Male', 'phone' => '0', 'image' => 'assets/images/no-photo-sm.png', 'thumb' => 'assets/images/no-photo-sm.png', 'has_bookings' => '0' ],
];

$PAIRED_BOOKINGS = [
    [ 'id' => '1', 'user_id' => '11', 'mentee_id' => '3', 'session_id' => '1', 'booking_number' => '74250916', 'date' => '2025-02-16', 'time' => '00:00-01:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-02-12 18:25:44', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '2', 'user_id' => '11', 'mentee_id' => '3', 'session_id' => '1', 'booking_number' => '16379285', 'date' => '2025-02-16', 'time' => '00:00-01:00', 'note' => null, 'status' => '1', 'duration' => '30', 'created_at' => '2025-02-12 18:27:49', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '3', 'user_id' => '11', 'mentee_id' => '70', 'session_id' => '1', 'booking_number' => '78516290', 'date' => '2025-03-02', 'time' => '00:00-01:00', 'note' => null, 'status' => '1', 'duration' => '30', 'created_at' => '2025-02-26 10:37:49', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '4', 'user_id' => '71', 'mentee_id' => '70', 'session_id' => '3', 'booking_number' => '86230945', 'date' => '2025-03-21', 'time' => '19:00-00:00', 'note' => null, 'status' => '2', 'duration' => '30', 'created_at' => '2025-03-19 17:20:16', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '5', 'user_id' => '79', 'mentee_id' => '70', 'session_id' => '2', 'booking_number' => '56348921', 'date' => '2025-03-22', 'time' => '12:00-14:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-03-19 17:20:54', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '6', 'user_id' => '11', 'mentee_id' => '70', 'session_id' => '5', 'booking_number' => '30257814', 'date' => '2025-03-26', 'time' => '20:00-21:00', 'note' => null, 'status' => '3', 'duration' => '30', 'created_at' => '2025-03-21 12:12:16', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '7', 'user_id' => '71', 'mentee_id' => '70', 'session_id' => '3', 'booking_number' => '2634159', 'date' => '2025-03-26', 'time' => '15:00-00:00', 'note' => null, 'status' => '3', 'duration' => '30', 'created_at' => '2025-03-25 12:36:43', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '8', 'user_id' => '71', 'mentee_id' => '70', 'session_id' => '4', 'booking_number' => '9813245', 'date' => '2025-03-26', 'time' => '18:00-18:30', 'note' => null, 'status' => '0', 'duration' => '60', 'created_at' => '2025-03-25 12:37:06', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '9', 'user_id' => '71', 'mentee_id' => '70', 'session_id' => '8', 'booking_number' => '16093785', 'date' => '2025-03-26', 'time' => '15:00-00:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-03-25 12:46:09', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '10', 'user_id' => '79', 'mentee_id' => '98', 'session_id' => '2', 'booking_number' => '72149638', 'date' => '2025-05-10', 'time' => '12:00-14:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-05-08 14:15:01', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '11', 'user_id' => '71', 'mentee_id' => '70', 'session_id' => '3', 'booking_number' => '48369270', 'date' => '2025-05-20', 'time' => '', 'note' => 'Testing', 'status' => '2', 'duration' => '30', 'created_at' => '2025-05-19 13:15:43', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '12', 'user_id' => '71', 'mentee_id' => '11', 'session_id' => '17', 'booking_number' => '20964187', 'date' => '2025-05-26', 'time' => '19:00-19:30', 'note' => null, 'status' => '1', 'duration' => '30', 'created_at' => '2025-05-21 22:19:28', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '13', 'user_id' => '71', 'mentee_id' => '1', 'session_id' => '3', 'booking_number' => '72314560', 'date' => '2025-06-13', 'time' => '19:00-00:00', 'note' => null, 'status' => '1', 'duration' => '30', 'created_at' => '2025-06-12 11:50:46', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '14', 'user_id' => '71', 'mentee_id' => '137', 'session_id' => '20', 'booking_number' => '27619538', 'date' => '2025-07-08', 'time' => '00:00-00:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-06-23 21:10:31', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '15', 'user_id' => '127', 'mentee_id' => '122', 'session_id' => '33', 'booking_number' => '96835721', 'date' => '2025-07-04', 'time' => '19:00-20:00', 'note' => null, 'status' => '0', 'duration' => '60', 'created_at' => '2025-07-02 08:41:56', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '16', 'user_id' => '71', 'mentee_id' => '158', 'session_id' => '3', 'booking_number' => '34815670', 'date' => '2025-09-24', 'time' => '15:00-00:00', 'note' => null, 'status' => '0', 'duration' => '240', 'created_at' => '2025-09-22 13:45:26', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '17', 'user_id' => '11', 'mentee_id' => '161', 'session_id' => '5', 'booking_number' => '46305879', 'date' => '2025-10-13', 'time' => '18:00-20:00', 'note' => null, 'status' => '3', 'duration' => '30', 'created_at' => '2025-09-30 14:39:36', 'is_completed' => '0', 'is_recurring' => '1' ],
    [ 'id' => '18', 'user_id' => '71', 'mentee_id' => '158', 'session_id' => '8', 'booking_number' => '27831650', 'date' => '2025-10-03', 'time' => '19:00-00:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-10-02 10:00:41', 'is_completed' => '0', 'is_recurring' => '1' ],
    [ 'id' => '19', 'user_id' => '11', 'mentee_id' => '158', 'session_id' => '5', 'booking_number' => '65370218', 'date' => '2025-10-06', 'time' => '18:00-20:00', 'note' => null, 'status' => '2', 'duration' => '30', 'created_at' => '2025-10-03 11:15:26', 'is_completed' => '0', 'is_recurring' => '1' ],
    [ 'id' => '20', 'user_id' => '71', 'mentee_id' => '158', 'session_id' => '9', 'booking_number' => '13869570', 'date' => '2025-10-07', 'time' => '16:00-00:00', 'note' => null, 'status' => '1', 'duration' => '30', 'created_at' => '2025-10-06 16:16:55', 'is_completed' => '0', 'is_recurring' => '1' ],
    [ 'id' => '21', 'user_id' => '163', 'mentee_id' => '158', 'session_id' => '37', 'booking_number' => '70928136', 'date' => '2025-10-07', 'time' => '13:00-13:30', 'note' => null, 'status' => '1', 'duration' => '30', 'created_at' => '2025-10-06 17:11:47', 'is_completed' => '0', 'is_recurring' => '0' ],
    [ 'id' => '22', 'user_id' => '11', 'mentee_id' => '164', 'session_id' => '5', 'booking_number' => '62908531', 'date' => '2025-10-20', 'time' => '18:00-20:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-10-07 11:25:46', 'is_completed' => '0', 'is_recurring' => '1' ],
    [ 'id' => '23', 'user_id' => '71', 'mentee_id' => '168', 'session_id' => '9', 'booking_number' => '7135264', 'date' => '2025-12-09', 'time' => '16:00-00:00', 'note' => null, 'status' => '0', 'duration' => '30', 'created_at' => '2025-12-08 15:40:31', 'is_completed' => '0', 'is_recurring' => '1' ],
];

$PAIRED_EXPERIENCES = [
    [ 'id' => '1', 'user_id' => '11', 'title' => 'Founder', 'company' => 'Bluu Interactive', 'start_date' => '23/05/2022', 'end_date' => '', 'is_present' => '1', 'contribution' => 'I contributed a lot to the organisation.' ],
    [ 'id' => '2', 'user_id' => '11', 'title' => 'Web Developer', 'company' => 'Pay Finance', 'start_date' => '2020', 'end_date' => '2023', 'is_present' => '0', 'contribution' => '' ],
    [ 'id' => '3', 'user_id' => '116', 'title' => 'Lead Developer', 'company' => 'FyreApp', 'start_date' => 'January 2022', 'end_date' => 'October 2023', 'is_present' => '0', 'contribution' => 'I did this. I did that.' ],
    [ 'id' => '4', 'user_id' => '71', 'title' => 'Relationship Manager', 'company' => 'Black Professionals UK', 'start_date' => '', 'end_date' => '', 'is_present' => '0', 'contribution' => '' ],
];

$PAIRED_EDUCATIONS = [
    [ 'id' => '1', 'user_id' => '11', 'institute' => 'Coursera', 'degree' => 'Advanced Web Development', 'start_year' => '2022', 'end_year' => '2024' ],
];

$PAIRED_MESSAGES = [
    [ 'id' => '1', 'user_id' => '70', 'receiver_id' => '71', 'message' => 'Hi Benny.. happy to make your acquaintance .. Will you kindly do me the honours of teaching me your award winning people skills lol', 'created_at' => '2025-03-18 16:02:15', 'is_read' => '1' ],
    [ 'id' => '2', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Hi dear', 'created_at' => '2025-03-19 15:41:13', 'is_read' => '0' ],
    [ 'id' => '3', 'user_id' => '11', 'receiver_id' => '79', 'message' => 'Hello Austin. I will like to connect with you.', 'created_at' => '2025-03-20 14:02:55', 'is_read' => '0' ],
    [ 'id' => '4', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thanks for coming!', 'created_at' => '2025-03-25 10:54:14', 'is_read' => '0' ],
    [ 'id' => '5', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Again thank you.', 'created_at' => '2025-03-25 12:54:18', 'is_read' => '0' ],
    [ 'id' => '6', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Again thank you.', 'created_at' => '2025-05-19 12:40:36', 'is_read' => '0' ],
    [ 'id' => '7', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thank you Dami', 'created_at' => '2025-05-21 11:19:32', 'is_read' => '0' ],
    [ 'id' => '8', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thanks for coming!', 'created_at' => '2025-05-21 13:21:44', 'is_read' => '0' ],
    [ 'id' => '9', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thanks for coming!', 'created_at' => '2025-05-22 12:26:16', 'is_read' => '0' ],
    [ 'id' => '10', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thank you Dami', 'created_at' => '2025-05-28 09:56:28', 'is_read' => '0' ],
    [ 'id' => '11', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Again thank you.', 'created_at' => '2025-05-28 11:46:53', 'is_read' => '0' ],
    [ 'id' => '12', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thank you Dami', 'created_at' => '2025-05-30 11:50:48', 'is_read' => '0' ],
    [ 'id' => '13', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thank you Dami', 'created_at' => '2025-06-11 12:24:14', 'is_read' => '0' ],
    [ 'id' => '14', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Again thank you.', 'created_at' => '2025-06-12 10:18:00', 'is_read' => '0' ],
    [ 'id' => '15', 'user_id' => '71', 'receiver_id' => '70', 'message' => 'Thank you Dami', 'created_at' => '2025-06-16 10:17:38', 'is_read' => '0' ],
    [ 'id' => '16', 'user_id' => '139', 'receiver_id' => '132', 'message' => 'Hello Frama<br>
<br>
I wil like to work with you as a Mentee.<br>
<br>
Thank you', 'created_at' => '2025-06-25 06:25:46', 'is_read' => '0' ],
    [ 'id' => '17', 'user_id' => '122', 'receiver_id' => '89', 'message' => 'Hi Akinwale. I hope you’re well. I’m Mohammed - an accounting graduate and emerging poet currently transitioning into communications and arts marketing.<br>
<br>
I really admire your work at the intersection of marketing and the arts. As someone navigating this shift (and building a career in the UK as a recent graduate and immigrant), I’d be grateful for any advice or a quick chat if you’re open to it.<br>
<br>
Thanks so much for your time!<br>
', 'created_at' => '2025-06-27 00:33:55', 'is_read' => '0' ],
    [ 'id' => '18', 'user_id' => '141', 'receiver_id' => '97', 'message' => 'Dear Enoch,<br>
<br>
Good afternoon. I hope this message finds you well.<br>
<br>
My name is Olayinka Balogun. I recently relocated to the UK (Croydon, London) from Nigeria earlier this month on a spousal visa with full right to work.<br>
<br>
I have about 5 years of experience in finance, with 4.5 of those years spent in Deals Advisory at KPMG and EY. I’m currently in the process of applying and interviewing for finance roles in the UK.<br>
<br>
I would be truly honored if you could be my mentor. Your extensive experience—particularly as a Fellow of the ACCA, your consulting work within financial services, and your leadership across initiatives like Black Professionals United Kingdom—deeply resonates with my own aspirations. I believe there is so much I can learn from your journey, especially as I navigate the UK job market and work to establish myself professionally in this new environment.<br>
<br>
Thank you in advance for considering my request.<br>
<br>
Warm regards,<br>
Olayinka Balogun', 'created_at' => '2025-06-27 19:55:35', 'is_read' => '0' ],
    [ 'id' => '19', 'user_id' => '142', 'receiver_id' => '100', 'message' => 'Hi Akosua,<br>
<br>
My name is Judy. I have just joined the PAIRED community.  I read your bio and background and we share several similar skills. I would be thrilled to learn from you, and I was wondering if you would be open to guiding me as a mentor. <br>
<br>
Judy', 'created_at' => '2025-06-28 18:35:46', 'is_read' => '0' ],
    [ 'id' => '20', 'user_id' => '122', 'receiver_id' => '127', 'message' => '', 'created_at' => '2025-07-02 04:42:15', 'is_read' => '1' ],
    [ 'id' => '21', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'Hi Funmi,  I’m Mohammed, currently pivoting into product development from a non-technical background. I’m exploring bootcamps and self-learning, and keen to grow in this space as an immigrant.  Given your leadership in innovation and product, I’d really appreciate the chance to learn from you. Would you be open to connecting?', 'created_at' => '2025-07-02 03:45:17', 'is_read' => '1' ],
    [ 'id' => '22', 'user_id' => '127', 'receiver_id' => '122', 'message' => 'Hi Mohammed,', 'created_at' => '2025-07-02 08:13:13', 'is_read' => '1' ],
    [ 'id' => '23', 'user_id' => '127', 'receiver_id' => '122', 'message' => 'Thanks for connecting and subscribing. I\'ll be honored to help you on your journey. So your immediate goals is to join a relevant and practical learning based bootcamp. You did mention a pivot, can you share: 1) A general list of 5 -10 bootcamps/ self-learning options you\'ve explored (2) End goal of your pivot (I don\'t want to assume its to change jobs) (3) Timeline and Budget (if any) for the process. This will help us make the best of use of mentorship time. Thanks and do have a great day!', 'created_at' => '2025-07-02 08:19:07', 'is_read' => '1' ],
    [ 'id' => '24', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'Hi Funmi', 'created_at' => '2025-07-04 17:59:09', 'is_read' => '0' ],
    [ 'id' => '25', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'Apologies for the delayed response.  I didn\'t receive any notification about the message. I had assumed there was going to be a live session today ', 'created_at' => '2025-07-04 17:59:44', 'is_read' => '0' ],
    [ 'id' => '26', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'I am struggling to join the session, please can you share the link?', 'created_at' => '2025-07-04 18:05:53', 'is_read' => '0' ],
    [ 'id' => '27', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'thanks again for your support. To be honest, I moved here initially for accountancy and hoped to get chartered, but with the immigration changes and some self-reflection, I’ve been thinking more seriously about pivoting into product development, which aligns with my interest in innovation and global opportunities.', 'created_at' => '2025-07-04 18:28:20', 'is_read' => '0' ],
    [ 'id' => '28', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'I’m still exploring bootcamps. I’ve come across ones like Coursera (Google PM cert), Avado. I’ll do more in-depth research soon', 'created_at' => '2025-07-04 18:29:27', 'is_read' => '0' ],
    [ 'id' => '29', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'My end goal isn’t just a job switch, but to gain practical, globally useful skills I can use to solve real problems. I’m more of a hands-on learner, not very exam-focused.  Timeline: 6–9 months Budget: Around £500–£900 max (open to free/scholarship options)  Also thinking about long-term routes like the Innovator Founder Visa.', 'created_at' => '2025-07-04 18:29:51', 'is_read' => '0' ],
    [ 'id' => '30', 'user_id' => '122', 'receiver_id' => '127', 'message' => 'I just graduated, so I’m prioritizing cost-effective options like self-learning, which I see as more practical than doing a Master’s. I’m open to options above £900 if they’re strong.', 'created_at' => '2025-07-04 18:36:28', 'is_read' => '0' ],
    [ 'id' => '31', 'user_id' => '142', 'receiver_id' => '127', 'message' => 'Hi Funmi <br>
<br>
My name is Judy. I have just joined the PAIRED community. I read your bio and background and we share several similar passions. I would be thrilled to learn from you, and I was wondering if you would be open to guiding me as a mentor. Looking forward to your response.<br>
<br>
Judy', 'created_at' => '2025-07-07 12:46:23', 'is_read' => '0' ],
    [ 'id' => '32', 'user_id' => '146', 'receiver_id' => '100', 'message' => 'Hello, my name is Tife. I work as a customer experience coordinator at Allianz. I want to transition fully into a data analyst/business analyst/data scientist role, and I would love for you to mentor me. ', 'created_at' => '2025-07-12 04:52:57', 'is_read' => '0' ],
    [ 'id' => '33', 'user_id' => '148', 'receiver_id' => '94', 'message' => 'Hello Mr. Stephen Babatunde, <br>
<br>
I came across your profile from the Paired Mentorship Programme by Black Professionals UK and was inspired by your journey, especially how you transitioned from engineering and project management into finance, and now lead your own company. It encouraged me, as someone still figuring out where I fit within the professional services industry. <br>
I have a background in Economics, and I am exploring opportunities across finance, administration, policies and community-focused work.  I am keen to learn from your experience and gain guidance as I navigate the early stages of my career.<br>
<br>
Thank you for considering my request.<br>
<br>
Best regards,<br>
Faith Lamba.', 'created_at' => '2025-07-22 01:17:59', 'is_read' => '0' ],
    [ 'id' => '34', 'user_id' => '150', 'receiver_id' => '100', 'message' => ' Hi Akosua,<br>
<br>
I hope you’re doing well. My name is Tendai, we’ve met a few times at BPUK events.<br>
<br>
I was wondering if you might be open to catching up sometime soon. I’m currently looking for a mentor, and given your actuarial background and experience, I thought you’d be a great person to speak with as I navigate the next steps in my career.<br>
<br>
Please let me know if you’d be happy to connect, I’d really appreciate it.<br>
<br>
Regards,<br>
<br>
Tendai Chitsamatanga', 'created_at' => '2025-07-28 16:18:04', 'is_read' => '0' ],
    [ 'id' => '35', 'user_id' => '158', 'receiver_id' => '71', 'message' => 'Hiiiiii', 'created_at' => '2025-09-22 13:51:26', 'is_read' => '1' ],
    [ 'id' => '36', 'user_id' => '160', 'receiver_id' => '129', 'message' => 'Hello Ajoke<br>
I am really inspired by your profile and your research, especially looking into our ancestory. What are your thoughts on cancer research and what would you say a first year student like myself should look forward to specialising in your pov of biosciences in 2025?<br>
Thank you and looking forward to your reply<br>
', 'created_at' => '2025-09-28 23:25:54', 'is_read' => '0' ],
    [ 'id' => '37', 'user_id' => '160', 'receiver_id' => '114', 'message' => 'Hello Andrew<br>
I am  a first year student at Glasgow Caledonian studying Biomedical Science.<br>
What are your thoughts on cancer research and what other fields/careers would you recceomend specialising in as of 2025 in your point of view of the bioscience world ?<br>
Thank you and looking forward to your reply.<br>
', 'created_at' => '2025-09-28 23:36:26', 'is_read' => '0' ],
    [ 'id' => '38', 'user_id' => '158', 'receiver_id' => '71', 'message' => 'Hi', 'created_at' => '2025-10-02 09:59:24', 'is_read' => '1' ],
    [ 'id' => '39', 'user_id' => '11', 'receiver_id' => '0', 'message' => 'hey', 'created_at' => '2025-10-03 10:38:08', 'is_read' => '0' ],
    [ 'id' => '40', 'user_id' => '11', 'receiver_id' => '0', 'message' => 'hey', 'created_at' => '2025-10-03 10:40:19', 'is_read' => '0' ],
    [ 'id' => '41', 'user_id' => '158', 'receiver_id' => '71', 'message' => 'Helllo', 'created_at' => '2025-10-06 16:15:10', 'is_read' => '1' ],
    [ 'id' => '42', 'user_id' => '158', 'receiver_id' => '71', 'message' => 'Hiiiiii', 'created_at' => '2025-10-06 16:15:53', 'is_read' => '1' ],
    [ 'id' => '43', 'user_id' => '164', 'receiver_id' => '11', 'message' => 'hey', 'created_at' => '2025-10-07 11:23:07', 'is_read' => '1' ],
    [ 'id' => '44', 'user_id' => '164', 'receiver_id' => '11', 'message' => 'hello', 'created_at' => '2025-10-07 10:28:54', 'is_read' => '1' ],
    [ 'id' => '45', 'user_id' => '11', 'receiver_id' => '164', 'message' => 'Hello', 'created_at' => '2025-10-20 08:44:21', 'is_read' => '1' ],
    [ 'id' => '46', 'user_id' => '164', 'receiver_id' => '11', 'message' => 'How can I help?', 'created_at' => '2025-10-20 08:50:31', 'is_read' => '1' ],
    [ 'id' => '47', 'user_id' => '164', 'receiver_id' => '11', 'message' => 'Hello', 'created_at' => '2025-10-21 09:43:49', 'is_read' => '1' ],
    [ 'id' => '48', 'user_id' => '11', 'receiver_id' => '164', 'message' => 'What do you want?', 'created_at' => '2025-10-21 10:36:06', 'is_read' => '1' ],
    [ 'id' => '49', 'user_id' => '164', 'receiver_id' => '11', 'message' => 'not much', 'created_at' => '2025-10-21 11:52:00', 'is_read' => '0' ],
    [ 'id' => '50', 'user_id' => '159', 'receiver_id' => '157', 'message' => 'dsfsdfdsfs', 'created_at' => '2025-10-21 15:23:15', 'is_read' => '0' ],
    [ 'id' => '51', 'user_id' => '159', 'receiver_id' => '157', 'message' => 'dsfsdfdsfs', 'created_at' => '2025-10-21 15:23:52', 'is_read' => '0' ],
    [ 'id' => '52', 'user_id' => '159', 'receiver_id' => '157', 'message' => 'dsfsdfdsfs', 'created_at' => '2025-10-21 15:26:45', 'is_read' => '0' ],
    [ 'id' => '53', 'user_id' => '159', 'receiver_id' => '157', 'message' => 'dsfsdfdsfs', 'created_at' => '2025-10-21 15:28:05', 'is_read' => '0' ],
    [ 'id' => '54', 'user_id' => '161', 'receiver_id' => '11', 'message' => 'Can we work?', 'created_at' => '2025-10-22 08:44:45', 'is_read' => '1' ],
    [ 'id' => '55', 'user_id' => '11', 'receiver_id' => '161', 'message' => 'sure, what do you want us to do?', 'created_at' => '2025-10-22 07:54:21', 'is_read' => '1' ],
    [ 'id' => '56', 'user_id' => '161', 'receiver_id' => '11', 'message' => 'good', 'created_at' => '2025-10-22 07:56:00', 'is_read' => '1' ],
    [ 'id' => '57', 'user_id' => '161', 'receiver_id' => '11', 'message' => 'hello', 'created_at' => '2025-10-22 12:44:50', 'is_read' => '1' ],
    [ 'id' => '58', 'user_id' => '161', 'receiver_id' => '11', 'message' => 'are we still together', 'created_at' => '2025-10-22 12:44:57', 'is_read' => '1' ],
    [ 'id' => '59', 'user_id' => '159', 'receiver_id' => '157', 'message' => 'hello', 'created_at' => '2025-10-23 15:23:58', 'is_read' => '0' ],
    [ 'id' => '60', 'user_id' => '161', 'receiver_id' => '11', 'message' => 'hello bro', 'created_at' => '2025-10-23 15:24:58', 'is_read' => '1' ],
    [ 'id' => '61', 'user_id' => '159', 'receiver_id' => '157', 'message' => 'hiiii', 'created_at' => '2025-10-23 15:25:03', 'is_read' => '0' ],
    [ 'id' => '62', 'user_id' => '11', 'receiver_id' => '161', 'message' => 'I am with you', 'created_at' => '2025-10-23 15:26:29', 'is_read' => '1' ],
    [ 'id' => '63', 'user_id' => '161', 'receiver_id' => '11', 'message' => 'hi', 'created_at' => '2025-10-23 15:36:02', 'is_read' => '1' ],
    [ 'id' => '64', 'user_id' => '71', 'receiver_id' => '158', 'message' => 'Hi', 'created_at' => '2025-10-24 10:03:38', 'is_read' => '1' ],
    [ 'id' => '65', 'user_id' => '158', 'receiver_id' => '163', 'message' => 'Hii', 'created_at' => '2025-10-24 11:05:55', 'is_read' => '1' ],
    [ 'id' => '66', 'user_id' => '163', 'receiver_id' => '158', 'message' => 'Hello', 'created_at' => '2025-10-24 10:07:14', 'is_read' => '0' ],
    [ 'id' => '67', 'user_id' => '167', 'receiver_id' => '124', 'message' => 'Good morning Theresa <br>
I hope you are well<br>
I wanted to share a bit about my background and the direction I’m aiming for in my career. I completed my MSc in Business Management at Edinburgh Napier University, and my undergraduate degree is a BSc in Industrial Mathematics from Jomo Kenyatta University of Agriculture and Technology. I’m currently working at Lloyds Banking Group as a Senior Pensions Administrator.<br>
<br>
I’m now actively working towards progressing into an analyst role within finance, especially in areas like data analysis, financial analysis, or risk. I’m keen to move into a position where I can apply my analytical skills more deeply, work with data, and contribute to decision-making and process improvements.<br>
<br>
I’d really appreciate any advice or guidance you can offer on the best way to prepare for this transition or any areas you think I should focus on.<br>
<br>
Thank you again for your support.<br>
Cynthia ', 'created_at' => '2025-11-14 07:38:11', 'is_read' => '1' ],
    [ 'id' => '68', 'user_id' => '167', 'receiver_id' => '124', 'message' => 'Good morning Theresa <br>
I hope you are well<br>
I wanted to share a bit about my background and the direction I’m aiming for in my career. I completed my MSc in Business Management at Edinburgh Napier University, and my undergraduate degree is a BSc in Industrial Mathematics from Jomo Kenyatta University of Agriculture and Technology. I’m currently working at Lloyds Banking Group as a Senior Pensions Administrator.<br>
<br>
I’m now actively working towards progressing into an analyst role within finance, especially in areas like data analysis, financial analysis, or risk. I’m keen to move into a position where I can apply my analytical skills more deeply, work with data, and contribute to decision-making and process improvements.<br>
<br>
I’d really appreciate any advice or guidance you can offer on the best way to prepare for this transition or any areas you think I should focus on.<br>
<br>
Thank you again for your support.<br>
Cynthia ', 'created_at' => '2025-11-14 07:38:12', 'is_read' => '1' ],
    [ 'id' => '69', 'user_id' => '167', 'receiver_id' => '104', 'message' => 'Good morning Austin<br>
I wanted to share a bit about my background and the direction I’m aiming for in my career. I completed my MSc in Business Management at Edinburgh Napier University, and my undergraduate degree is a BSc in Industrial Mathematics from Jomo Kenyatta University of Agriculture and Technology. I’m currently working at Lloyds Banking Group as a Senior Pensions Administrator.<br>
<br>
I’m now actively working towards progressing into an analyst role within finance, especially in areas like data analysis, financial analysis, or risk. I’m keen to move into a position where I can apply my analytical skills more deeply, work with data, and contribute to decision-making and process improvements.<br>
<br>
I’d really appreciate any advice or guidance you can offer on the best way to prepare for this transition or any areas you think I should focus on.<br>
<br>
Thank you again for your support.<br>
Regards <br>
Cynthia ', 'created_at' => '2025-11-14 07:39:30', 'is_read' => '0' ],
    [ 'id' => '70', 'user_id' => '124', 'receiver_id' => '167', 'message' => 'Hi Cynthia,', 'created_at' => '2025-11-14 23:00:17', 'is_read' => '1' ],
    [ 'id' => '71', 'user_id' => '124', 'receiver_id' => '167', 'message' => 'Thanks for reaching out. Unfortunately I am not sure I can be of much help because the new path you are trying to follow is not in my area of specialisation. ', 'created_at' => '2025-11-14 23:01:53', 'is_read' => '1' ],
    [ 'id' => '72', 'user_id' => '167', 'receiver_id' => '124', 'message' => 'Good morning ', 'created_at' => '2025-11-18 07:38:35', 'is_read' => '1' ],
    [ 'id' => '73', 'user_id' => '167', 'receiver_id' => '124', 'message' => 'Thankyou for confirming ', 'created_at' => '2025-11-18 07:38:46', 'is_read' => '1' ],
];


// ============================================================
// CORE IMPORT LOGIC (shared by admin page and WP-CLI)
// ============================================================

function bpu_paired_meta_map( $u ) {
    $industry         = ! empty( $u['expertise_industry'] ) ? $u['expertise_industry'] : ( $u['other_industry'] ?? '' );
    $current_role_val = ! empty( $u['current_role'] ) ? $u['current_role'] : ( $u['designation'] ?? '' );
    return [
        'user_bio'                   => $u['about_me'] ?? '',
        'industry'                   => $industry,
        'industryfield_of_expertise' => $u['expertise_industry'] ?? '',
        'current_role'               => $current_role_val,
        'company'                    => $u['company'] ?? '',
        'years_of_experience'        => $u['experience_year'] ?? '',
        'skills_separate'            => $u['skills_normalized'] ?? '',
        'linkedin_profile'           => $u['linkedin_profile'] ?? '',
        'facebook_profile'           => $u['facebook_profile'] ?? '',
        'instagram_profile'          => $u['instagram_profile'] ?? '',
        'x_profile'                  => $u['x_profile'] ?? '',
        'mentorship_availability'    => $u['mentorship_availability'] ?? '',
        'mentorship_requirements'    => $u['mentorship_requirements'] ?? '',
        'mentees_at_once'            => $u['mentees_at_once'] ?? '',
        'career_goals'               => $u['career_goals'] ?? '',
        'employment_status'          => $u['employment_status'] ?? '',
        'gender'                     => $u['gender'] ?? '',
        'bp_network'                 => $u['bp_network'] ?? '',
        'residence'                  => $u['residence'] ?? '',
        'phone'                      => $u['phone'] ?? '',
        'paired_image_path'          => $u['image'] ?? '',
        'paired_thumb_path'          => $u['thumb'] ?? '',
        'paired_is_active'           => $u['is_active'] ?? '',
        'paired_status'              => $u['status'] ?? '',
        'paired_created_at'          => $u['created_at'] ?? '',
    ];
}

function bpu_paired_set_user_meta( $wp_user_id, $u ) {
    foreach ( bpu_paired_meta_map( $u ) as $key => $val ) {
        update_user_meta( $wp_user_id, $key, $val );
    }
}

function bpu_paired_merge_user_meta( $wp_user_id, $u ) {
    foreach ( bpu_paired_meta_map( $u ) as $key => $val ) {
        if ( empty( get_user_meta( $wp_user_id, $key, true ) ) ) {
            update_user_meta( $wp_user_id, $key, $val );
        }
    }
}

/**
 * Run the full import. Returns ['stats'=>[...], 'log'=>[...], 'resets'=>[...]].
 */
function bpu_paired_run_import() {
    global $PAIRED_USERS, $PAIRED_BOOKINGS, $PAIRED_EXPERIENCES, $PAIRED_EDUCATIONS;

    $stats = [
        'users_created'    => 0,
        'users_merged'     => 0,
        'users_skipped'    => 0,
        'users_errors'     => 0,
        'bookings_created' => 0,
        'bookings_skipped' => 0,
        'bookings_errors'  => 0,
        'exp_records'      => 0,
        'exp_users'        => 0,
        'edu_records'      => 0,
        'edu_users'        => 0,
    ];

    $log                = [];
    $old_to_new_user_id = [];
    $password_resets    = [];

    // ── 1. Users ──────────────────────────────────────────────
    $log[] = '=== Importing Users ===';

    foreach ( $PAIRED_USERS as $u ) {
        $old_id = $u['id'];
        $email  = trim( $u['email'] ?? '' );
        $name   = trim( $u['name'] ?? '' );
        $role   = $u['role'] ?? 'user';

        if ( empty( $email ) ) {
            $log[] = "  SKIP  user old_id={$old_id} — no email";
            $stats['users_skipped']++;
            continue;
        }

        if ( $role === 'admin' ) {
            $wp_role = 'administrator';
        } elseif ( $role === 'mentee' ) {
            $wp_role = 'subscriber';
        } else {
            $wp_role = ( $u['has_bookings'] === '1' ) ? 'bpu_mentor' : 'subscriber';
        }

        $display_name = ! empty( $name ) ? $name : $email;
        $user_login   = sanitize_user( strtolower( preg_replace( '/[^a-zA-Z0-9._\-]/', '', $email ) ), true );
        if ( empty( $user_login ) ) {
            $user_login = 'user_' . $old_id;
        }

        $existing = get_user_by( 'email', $email );

        if ( $existing ) {
            $new_wp_id                     = $existing->ID;
            $old_to_new_user_id[ $old_id ] = $new_wp_id;
            update_user_meta( $new_wp_id, '_paired_old_id', $old_id );
            bpu_paired_merge_user_meta( $new_wp_id, $u );
            $log[] = "  MERGE user: {$email} → existing WP ID {$new_wp_id} (empty meta filled)";
            $stats['users_merged']++;
            continue;
        }

        $user_data = [
            'user_login'   => $user_login,
            'user_email'   => $email,
            'display_name' => $display_name,
            'role'         => $wp_role,
            'user_pass'    => wp_generate_password( 16 ),
        ];

        $new_wp_id = wp_insert_user( $user_data );
        if ( is_wp_error( $new_wp_id ) ) {
            $user_data['user_login'] = $user_login . '_' . $old_id;
            $new_wp_id = wp_insert_user( $user_data );
        }
        if ( is_wp_error( $new_wp_id ) ) {
            $log[] = "  ERROR user {$email}: " . $new_wp_id->get_error_message();
            $stats['users_errors']++;
            continue;
        }

        update_user_meta( $new_wp_id, '_paired_old_id', $old_id );
        bpu_paired_set_user_meta( $new_wp_id, $u );
        $old_to_new_user_id[ $old_id ] = $new_wp_id;

        $role_label        = $wp_role === 'bpu_mentor' ? 'Mentor' : ( $wp_role === 'administrator' ? 'Admin' : 'Mentee' );
        $password_resets[] = [ 'email' => $email, 'role' => $role_label, 'name' => $display_name ];
        $log[] = "  CREATE user: {$email} as {$role_label} (WP ID {$new_wp_id})";
        $stats['users_created']++;
    }

    $log[] = "Users done: created={$stats['users_created']} merged={$stats['users_merged']} skipped={$stats['users_skipped']} errors={$stats['users_errors']}";

    // ── 2. Bookings ───────────────────────────────────────────
    $log[] = '';
    $log[] = '=== Importing Bookings ===';

    $status_map = [ '0' => 'pending', '1' => 'confirmed', '2' => 'completed', '3' => 'cancelled' ];

    foreach ( $PAIRED_BOOKINGS as $b ) {
        $old_booking_id = $b['id'];
        $booking_number = $b['booking_number'] ?? '';
        $old_mentor_id  = $b['user_id'];
        $old_mentee_id  = $b['mentee_id'];

        $existing_posts = get_posts( [
            'post_type'   => 'mentorship_booking',
            'post_status' => 'any',
            'meta_query'  => [ [ 'key' => '_paired_old_booking_id', 'value' => $old_booking_id ] ],
            'numberposts' => 1,
        ] );
        if ( ! empty( $existing_posts ) ) {
            $log[] = "  SKIP  booking old_id={$old_booking_id} (already imported)";
            $stats['bookings_skipped']++;
            continue;
        }

        $mentor_wp_id = $old_to_new_user_id[ $old_mentor_id ] ?? null;
        if ( ! $mentor_wp_id ) {
            $found        = get_users( [ 'meta_key' => '_paired_old_id', 'meta_value' => $old_mentor_id, 'number' => 1 ] );
            $mentor_wp_id = ! empty( $found ) ? $found[0]->ID : null;
        }
        if ( ! $mentor_wp_id ) {
            $log[] = "  WARN  booking {$old_booking_id}: mentor old_id={$old_mentor_id} not found — skipped";
            $stats['bookings_errors']++;
            continue;
        }

        $mentee_wp_id = $old_to_new_user_id[ $old_mentee_id ] ?? null;
        if ( ! $mentee_wp_id ) {
            $found        = get_users( [ 'meta_key' => '_paired_old_id', 'meta_value' => $old_mentee_id, 'number' => 1 ] );
            $mentee_wp_id = ! empty( $found ) ? $found[0]->ID : null;
        }
        if ( ! $mentee_wp_id ) {
            $log[] = "  WARN  booking {$old_booking_id}: mentee old_id={$old_mentee_id} not found — skipped";
            $stats['bookings_errors']++;
            continue;
        }

        $status_str = $status_map[ (string) ( $b['status'] ?? '0' ) ] ?? 'pending';

        $post_id = wp_insert_post( [
            'post_type'   => 'mentorship_booking',
            'post_status' => 'publish',
            'post_title'  => 'Session #' . $booking_number,
            'post_date'   => $b['created_at'] ?? current_time( 'mysql' ),
        ] );

        if ( is_wp_error( $post_id ) ) {
            $log[] = "  ERROR booking old_id={$old_booking_id}: " . $post_id->get_error_message();
            $stats['bookings_errors']++;
            continue;
        }

        update_post_meta( $post_id, 'booking_mentor_id',      $mentor_wp_id );
        update_post_meta( $post_id, 'booking_mentee_id',      $mentee_wp_id );
        update_post_meta( $post_id, 'booking_date',           $b['date'] ?? '' );
        update_post_meta( $post_id, 'booking_time_slot',      $b['time'] ?? '' );
        update_post_meta( $post_id, 'booking_notes',          $b['note'] ?? '' );
        update_post_meta( $post_id, 'booking_status',         $status_str );
        update_post_meta( $post_id, 'booking_duration',       $b['duration'] ?? '' );
        update_post_meta( $post_id, 'booking_is_completed',   $b['is_completed'] ?? '0' );
        update_post_meta( $post_id, 'booking_is_recurring',   $b['is_recurring'] ?? '0' );
        update_post_meta( $post_id, '_paired_old_booking_id', $old_booking_id );

        $log[] = "  CREATE booking old_id={$old_booking_id} → post_id={$post_id} mentor={$mentor_wp_id} mentee={$mentee_wp_id} [{$status_str}]";
        $stats['bookings_created']++;
    }

    $log[] = "Bookings done: created={$stats['bookings_created']} skipped={$stats['bookings_skipped']} errors={$stats['bookings_errors']}";

    // ── 3. Experiences ────────────────────────────────────────
    $log[] = '';
    $log[] = '=== Importing Experiences ===';

    $exp_by_user = [];
    foreach ( $PAIRED_EXPERIENCES as $e ) {
        $exp_by_user[ $e['user_id'] ][] = [
            'title'        => $e['title'] ?? '',
            'company'      => $e['company'] ?? '',
            'start_date'   => $e['start_date'] ?? '',
            'end_date'     => $e['end_date'] ?? '',
            'is_present'   => $e['is_present'] ?? '0',
            'contribution' => $e['contribution'] ?? '',
        ];
    }
    foreach ( $exp_by_user as $old_uid => $exp_list ) {
        $wp_uid = $old_to_new_user_id[ $old_uid ] ?? null;
        if ( ! $wp_uid ) {
            $found  = get_users( [ 'meta_key' => '_paired_old_id', 'meta_value' => $old_uid, 'number' => 1 ] );
            $wp_uid = ! empty( $found ) ? $found[0]->ID : null;
        }
        if ( ! $wp_uid ) { $log[] = "  WARN  experiences: old_id={$old_uid} not found — skipped"; continue; }
        update_user_meta( $wp_uid, 'bpu_experiences', $exp_list );
        $cnt = count( $exp_list );
        $stats['exp_records'] += $cnt;
        $stats['exp_users']++;
        $log[] = "  Stored {$cnt} experience(s) for WP user {$wp_uid}";
    }
    $log[] = "Experiences done: {$stats['exp_records']} records for {$stats['exp_users']} users";

    // ── 4. Educations ─────────────────────────────────────────
    $log[] = '';
    $log[] = '=== Importing Educations ===';

    $edu_by_user = [];
    foreach ( $PAIRED_EDUCATIONS as $e ) {
        $edu_by_user[ $e['user_id'] ][] = [
            'institute'  => $e['institute'] ?? '',
            'degree'     => $e['degree'] ?? '',
            'start_year' => $e['start_year'] ?? '',
            'end_year'   => $e['end_year'] ?? '',
        ];
    }
    foreach ( $edu_by_user as $old_uid => $edu_list ) {
        $wp_uid = $old_to_new_user_id[ $old_uid ] ?? null;
        if ( ! $wp_uid ) {
            $found  = get_users( [ 'meta_key' => '_paired_old_id', 'meta_value' => $old_uid, 'number' => 1 ] );
            $wp_uid = ! empty( $found ) ? $found[0]->ID : null;
        }
        if ( ! $wp_uid ) { $log[] = "  WARN  educations: old_id={$old_uid} not found — skipped"; continue; }
        update_user_meta( $wp_uid, 'bpu_educations', $edu_list );
        $cnt = count( $edu_list );
        $stats['edu_records'] += $cnt;
        $stats['edu_users']++;
        $log[] = "  Stored {$cnt} education(s) for WP user {$wp_uid}";
    }
    $log[] = "Educations done: {$stats['edu_records']} records for {$stats['edu_users']} users";

    return [ 'stats' => $stats, 'log' => $log, 'resets' => $password_resets ];
}

// ============================================================
// WORDPRESS ADMIN PAGE (Tools → PAIRED Importer)
// ============================================================

add_action( 'admin_menu', function () {
    add_management_page(
        'PAIRED Importer',
        'PAIRED Importer',
        'manage_options',
        'bpu-paired-importer',
        'bpu_paired_admin_page'
    );
} );

function bpu_paired_admin_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Not allowed.' );
    }

    $result = null;

    if (
        isset( $_POST['bpu_run_import'] ) &&
        check_admin_referer( 'bpu_paired_import_action', 'bpu_paired_nonce' )
    ) {
        set_time_limit( 300 );
        $result = bpu_paired_run_import();
    }

    ?>
    <div class="wrap">
        <h1>BPU PAIRED Importer</h1>
        <p>Imports all users, bookings, experiences and educations from the old PAIRED platform into WordPress.</p>
        <ul style="list-style:disc;padding-left:20px;">
            <li><strong>Existing users</strong> (matched by email) — password, role and display name are untouched; only empty profile fields are filled.</li>
            <li><strong>New users</strong> — created with a random password. A password-reset table is shown at the end.</li>
            <li><strong>Safe to re-run</strong> — already-imported records are skipped automatically.</li>
        </ul>

        <?php if ( ! $result ) : ?>
            <form method="post">
                <?php wp_nonce_field( 'bpu_paired_import_action', 'bpu_paired_nonce' ); ?>
                <p>
                    <input type="submit" name="bpu_run_import" class="button button-primary button-large"
                           value="Run Import Now"
                           onclick="return confirm('This will write data into your live WordPress database. Continue?');">
                </p>
            </form>

        <?php else : ?>
            <?php $s = $result['stats']; ?>
            <div class="notice notice-success" style="padding:12px 16px;">
                <p><strong>Import complete.</strong></p>
                <p>Users: <strong>created <?php echo (int) $s['users_created']; ?></strong>
                   | merged <?php echo (int) $s['users_merged']; ?>
                   | skipped <?php echo (int) $s['users_skipped']; ?>
                   | errors <?php echo (int) $s['users_errors']; ?></p>
                <p>Bookings: <strong>created <?php echo (int) $s['bookings_created']; ?></strong>
                   | skipped <?php echo (int) $s['bookings_skipped']; ?>
                   | errors <?php echo (int) $s['bookings_errors']; ?></p>
                <p>Experiences: <?php echo (int) $s['exp_records']; ?> records for <?php echo (int) $s['exp_users']; ?> users
                   &nbsp;|&nbsp;
                   Educations: <?php echo (int) $s['edu_records']; ?> records for <?php echo (int) $s['edu_users']; ?> users</p>
            </div>

            <h2>Full log</h2>
            <pre style="background:#1e1e1e;color:#d4d4d4;padding:16px;overflow:auto;max-height:500px;font-size:12px;border-radius:4px;"><?php
                foreach ( $result['log'] as $line ) {
                    echo esc_html( $line ) . "\n";
                }
            ?></pre>

            <?php if ( ! empty( $result['resets'] ) ) : ?>
                <h2>Password reset needed</h2>
                <p>These accounts were created with a random password. Use the reset links below, or go to
                   <a href="<?php echo esc_url( admin_url( 'users.php' ) ); ?>">Users</a> to send bulk resets.</p>
                <table class="widefat striped" style="max-width:750px;">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Reset link</th></tr></thead>
                    <tbody>
                    <?php foreach ( $result['resets'] as $pr ) :
                        $wp_user = get_user_by( 'email', $pr['email'] );
                    ?>
                        <tr>
                            <td><?php echo esc_html( $pr['name'] ); ?></td>
                            <td><?php echo esc_html( $pr['email'] ); ?></td>
                            <td><?php echo esc_html( $pr['role'] ); ?></td>
                            <td><?php
                                if ( $wp_user ) {
                                    $key = get_password_reset_key( $wp_user );
                                    if ( ! is_wp_error( $key ) ) {
                                        $link = network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $wp_user->user_login ), 'login' );
                                        echo '<a href="' . esc_url( $link ) . '" target="_blank">Copy reset link</a>';
                                    }
                                }
                            ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <hr>
            <p><strong>Done.</strong> Once you have verified the imported data, please deactivate and delete this plugin.</p>
        <?php endif; ?>
    </div>
    <?php
}

// ============================================================
// WP-CLI COMMAND (bonus — works if terminal access is available)
// ============================================================

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    WP_CLI::add_command( 'bpu-import', new class {
        /** @when after_wp_load */
        public function paired( $args, $assoc_args ) {
            $result = bpu_paired_run_import();
            foreach ( $result['log'] as $line ) {
                WP_CLI::log( $line );
            }
            WP_CLI::log( '' );
            WP_CLI::log( 'Password reset needed for:' );
            foreach ( $result['resets'] as $pr ) {
                WP_CLI::log( "  - {$pr['email']} ({$pr['role']}) [{$pr['name']}]" );
            }
            WP_CLI::success( 'Import complete. Please delete this plugin after verifying the data.' );
        }
    } );
}

<?php

session_start();
if (empty($_SESSION['logged_in'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Additional Enrollment Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .form-section-title { font-weight: 600; margin-top: 1.5rem; }
        .muted-note { font-size: .95rem; white-space: pre-line; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row min-vh-100">
            <aside class="col-12 col-md-3 col-lg-2 bg-white border-end p-3 d-flex flex-column">
                <div class="d-flex align-items-center mb-4">
                    <span class="fs-5 fw-semibold">Menu</span>
                </div>
                <div class="list-group list-group-flush">
                    <a href="dashboard.php#dashboard" class="list-group-item list-group-item-action">Dashboard</a>
                    <a href="#parents" class="list-group-item list-group-item-action">Parent/Guardian</a>
                    <a href="#social" class="list-group-item list-group-item-action">Social Background</a>
                </div>
                <form method="post" action="logout.php" class="d-grid mt-auto">
                    <button type="submit" class="btn btn-outline-danger">Logout</button>
                </form>
            </aside>
            <main class="col-12 col-md-9 col-lg-10 p-4">
                <button id="sidebarToggle" class="btn btn-outline-secondary mb-3" type="button">☰ Menu</button>
                <div class="mb-4">
                    <h3 class="mb-1">Additional Enrollment Form</h3>
                    <p class="text-muted mb-4">Section 2</p>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form id="additionalForm" method="post" autocomplete="off">
                            <h5 id="parents" class="form-section-title">4. Parent’s/Guardian’s Information</h5>

                            <div class="row g-3 mt-1">
                                <div class="col-12"><strong>Father’s Name</strong></div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="father_last_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="father_first_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" name="father_middle_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Contact No.</label>
                                    <input type="text" class="form-control" name="father_contact">
                                </div>

                                <div class="col-12"><strong>Mother’s Maiden Name</strong></div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="mother_last_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="mother_first_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" name="mother_middle_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Contact No.</label>
                                    <input type="text" class="form-control" name="mother_contact">
                                </div>

                                <div class="col-12"><strong>Legal Guardian’s Name</strong></div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" name="guardian_last_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" name="guardian_first_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" name="guardian_middle_name">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Contact No.</label>
                                    <input type="text" class="form-control" name="guardian_contact">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">5. Is the Learner under the Special Needs Education Program?</h5>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="sneYes" name="sne_yes">
                                <label class="form-check-label" for="sneYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" id="sneNo" name="sne_no">
                                <label class="form-check-label" for="sneNo">No</label>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">6. With Disability (Check if applicable)</h5>
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_autism" id="dis_autism"><label class="form-check-label" for="dis_autism">Autism Spectrum Disorder</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_adhd" id="dis_adhd"><label class="form-check-label" for="dis_adhd">Attention Deficit Hyperactivity Disorder</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_blind" id="dis_blind"><label class="form-check-label" for="dis_blind">Blind</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_cerebral" id="dis_cerebral"><label class="form-check-label" for="dis_cerebral">Cerebral Palsy</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_deaf" id="dis_deaf"><label class="form-check-label" for="dis_deaf">Deaf</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_ebd" id="dis_ebd"><label class="form-check-label" for="dis_ebd">Emotional-Behavioral Disorder</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_hearing" id="dis_hearing"><label class="form-check-label" for="dis_hearing">Hearing Impairment</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_intellectual" id="dis_intellectual"><label class="form-check-label" for="dis_intellectual">Intellectual Disability</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_learning" id="dis_learning"><label class="form-check-label" for="dis_learning">Learning Disability</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_orthopedic" id="dis_orthopedic"><label class="form-check-label" for="dis_orthopedic">Orthopedic/Physical Handicap</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_speech" id="dis_speech"><label class="form-check-label" for="dis_speech">Speech/Language Disorder</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="dis_visual" id="dis_visual"><label class="form-check-label" for="dis_visual">Visual Impairment</label></div>
                                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" name="dis_others" id="dis_others"><label class="form-check-label" for="dis_others">Others</label></div>
                                    <input type="text" class="form-control" name="dis_others_specify" placeholder="Please specify">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">7. For Returning Learner (Balik-Aral) and those who will Transfer/Move In</h5>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Last Grade Level Completed</label>
                                    <input type="text" class="form-control" name="last_grade_level">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Last School Year Completed</label>
                                    <input type="text" class="form-control" name="last_sy_completed">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Last School Attended</label>
                                    <input type="text" class="form-control" name="last_school">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">School ID</label>
                                    <input type="text" class="form-control" name="school_id" placeholder="e.g., 6 digits">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">8. For Learner in Senior High School</h5>
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label d-block">Semester</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="sem1" name="semester_1st">
                                        <label class="form-check-label" for="sem1">1st</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="sem2" name="semester_2nd">
                                        <label class="form-check-label" for="sem2">2nd</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Track</label>
                                    <input type="text" class="form-control" name="shs_track">
                                </div>
                                <div class="col-12 col-md-4">
                                    <label class="form-label">Strand</label>
                                    <input type="text" class="form-control" name="shs_strand">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">8. Preferred Distance Learning Modalities</h5>
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mod_blended" id="mod_blended"><label class="form-check-label" for="mod_blended">Blended (Combination)</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mod_print" id="mod_print"><label class="form-check-label" for="mod_print">Modular (Print)</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mod_digital" id="mod_digital"><label class="form-check-label" for="mod_digital">Modular (Digital)</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mod_online" id="mod_online"><label class="form-check-label" for="mod_online">Online</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mod_tv" id="mod_tv"><label class="form-check-label" for="mod_tv">Educational TV (Television)</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mod_radio" id="mod_radio"><label class="form-check-label" for="mod_radio">Radio-Based Instruction</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="mod_homeschool" id="mod_homeschool"><label class="form-check-label" for="mod_homeschool">Homeschooling</label></div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 class="form-section-title">Certification</h5>
                            <p class="text-muted">I hereby certify that the above information given are true and correct to the best of my knowledge and I allow the Department of Education to use my child’s details to create and/or update his/her learner profile in the Learner Information System.<br><br>The information herein shall be treated as confidential in compliance with the Data Privacy Act of 2012.</p>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Signature over Printed Name of Parent/Guardian</label>
                                    <input type="text" class="form-control" name="cert_signature">
                                </div>
                                <div class="col-12 col-md-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" name="cert_date">
                                </div>
                            </div>

                            <hr class="my-4">
                            <h5 id="social" class="form-section-title">SOCIAL BACKGROUND</h5>
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">4Ps</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="fourpsYes" name="fourps_yes">
                                        <label class="form-check-label" for="fourpsYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="fourpsNo" name="fourps_no">
                                        <label class="form-check-label" for="fourpsNo">No</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">If Yes, Household No.</label>
                                    <input type="text" class="form-control" name="fourps_household_no">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">IP’s</label>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="ipsYes" name="ips_yes">
                                        <label class="form-check-label" for="ipsYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" id="ipsNo" name="ips_no">
                                        <label class="form-check-label" for="ipsNo">No</label>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">Tribe</label>
                                    <input type="text" class="form-control" name="tribe">
                                </div>

                                <div class="col-12">
                                    <label class="form-label d-block">Do you have difficulty in</label>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="diff_seeing" id="diff_seeing"><label class="form-check-label" for="diff_seeing">Seeing</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="diff_hearing" id="diff_hearing"><label class="form-check-label" for="diff_hearing">Hearing</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="diff_remembering" id="diff_remembering"><label class="form-check-label" for="diff_remembering">Remembering</label></div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="diff_concentrating" id="diff_concentrating"><label class="form-check-label" for="diff_concentrating">Concentrating</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="diff_others" id="diff_others"><label class="form-check-label" for="diff_others">Others</label></div>
                                            <input type="text" class="form-control" name="diff_others_specify" placeholder="Please specify">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Do you have a tattoo?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tattoo_yes" id="tattoo_yes"><label class="form-check-label" for="tattoo_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="tattoo_no" id="tattoo_no"><label class="form-check-label" for="tattoo_no">No</label></div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Are you pregnant / have you made someone pregnant?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="pregnant_yes" id="pregnant_yes"><label class="form-check-label" for="pregnant_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="pregnant_no" id="pregnant_no"><label class="form-check-label" for="pregnant_no">No</label></div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Are you involved in a gang or any unauthorized group?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="gang_yes" id="gang_yes"><label class="form-check-label" for="gang_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="gang_no" id="gang_no"><label class="form-check-label" for="gang_no">No</label></div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Are you from a broken family?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="broken_yes" id="broken_yes"><label class="form-check-label" for="broken_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="broken_no" id="broken_no"><label class="form-check-label" for="broken_no">No</label></div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Are you living with your family?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="living_yes" id="living_yes"><label class="form-check-label" for="living_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="living_no" id="living_no"><label class="form-check-label" for="living_no">No</label></div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Are you a working student?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="working_yes" id="working_yes"><label class="form-check-label" for="working_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="working_no" id="working_no"><label class="form-check-label" for="working_no">No</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">If Yes, Name of Guardian</label>
                                    <input type="text" class="form-control" name="guardian_name">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Do you drink alcoholic drinks?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="drink_yes" id="drink_yes"><label class="form-check-label" for="drink_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="drink_no" id="drink_no"><label class="form-check-label" for="drink_no">No</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">If Yes, how often?</label>
                                    <input type="text" class="form-control" name="drink_often">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Are you renting/boarding a house?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rent_yes" id="rent_yes"><label class="form-check-label" for="rent_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="rent_no" id="rent_no"><label class="form-check-label" for="rent_no">No</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">If Yes, Address</label>
                                    <input type="text" class="form-control" name="rent_address">
                                </div>

                                <div class="col-12">
                                    <label class="form-label d-block">Mode of transportation</label>
                                    <div class="row g-2">
                                        <div class="col-12 col-md-6">
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="trans_private" id="trans_private"><label class="form-check-label" for="trans_private">Private Vehicle</label></div>
                                            <input type="text" class="form-control mb-2" name="trans_private_specify" placeholder="Specify if private vehicle">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="trans_public" id="trans_public"><label class="form-check-label" for="trans_public">Public Transportation</label></div>
                                            <div class="form-check"><input class="form-check-input" type="checkbox" name="trans_walk" id="trans_walk"><label class="form-check-label" for="trans_walk">By Walking</label></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Are you a transferee student?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="transferee_yes" id="transferee_yes"><label class="form-check-label" for="transferee_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="transferee_no" id="transferee_no"><label class="form-check-label" for="transferee_no">No</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">If Yes, Previous School</label>
                                    <input type="text" class="form-control" name="previous_school">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Have you been a victim of any form of harassment?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="harass_yes" id="harass_yes"><label class="form-check-label" for="harass_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="harass_no" id="harass_no"><label class="form-check-label" for="harass_no">No</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">If Yes, Details</label>
                                    <input type="text" class="form-control" name="harass_details">
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label d-block">Have you been a victim of any form of child abuse?</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="abuse_yes" id="abuse_yes"><label class="form-check-label" for="abuse_yes">Yes</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="abuse_no" id="abuse_no"><label class="form-check-label" for="abuse_no">No</label></div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">If Yes, Type of Abuse</label>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="abuse_sexual" id="abuse_sexual"><label class="form-check-label" for="abuse_sexual">Sexual</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="abuse_physical" id="abuse_physical"><label class="form-check-label" for="abuse_physical">Physical</label></div>
                                    <div class="form-check"><input class="form-check-input" type="checkbox" name="abuse_verbal" id="abuse_verbal"><label class="form-check-label" for="abuse_verbal">Verbal</label></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label d-block">Offender</label>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="offender_parent" id="offender_parent"><label class="form-check-label" for="offender_parent">Parent</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="offender_relative" id="offender_relative"><label class="form-check-label" for="offender_relative">Relative</label></div>
                                    <div class="form-check form-check-inline"><input class="form-check-input" type="checkbox" name="offender_other" id="offender_other"><label class="form-check-label" for="offender_other">Other Person</label></div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="dashboard.php" class="btn btn-outline-secondary">Back</a>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function(){
            var sidebarToggle = document.getElementById('sidebarToggle');
            var sidebar = document.querySelector('aside');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function(){
                    sidebar.classList.toggle('d-none');
                });
            }
        })();
    </script>
</body>
</html>



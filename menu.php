<?php
require_once 'functions.php';

validate_user();
?>
<LINK href="css/menu.css" rel="stylesheet" type="text/css">
<div style="float: left;">
    <ul id="menu">
        <li style="width:55px; padding-left: 15px">
            <div align="left">
                <a href="admin.php">Home</a>
            </div>
        </li>
        <li style="width:90px">
            <div align="left">Applications
              <ul>
                <li><a href="admin.php?pagetoload=new_resident.php">New application</a></li>
                <li><a href="admin.php?pagetoload=new_group.php">New group application</a></li>
                <li><a href="admin.php?pagetoload=applications_list.php&menu=app">Received applications</a></li>
                <li><a href="admin.php?pagetoload=applications_list_accepted.php">Accepted applications</a></li>
              </ul>
            </div>
        </li>
        <li style="width:80px">
            <div align="left">Residents
                <ul>
                <li><a href="admin.php?pagetoload=residents_list.php">List</a></li>
                <li><a href="admin.php?pagetoload=groups_list.php">Groups</a></li>
                <li><a href="admin.php?pagetoload=search.php">Search</a></li>
                <li><a href="admin.php?pagetoload=residents_birthdays.php">Birthdays</a></li>
                <li><a href="admin.php?pagetoload=residents_expenses.php">Outstandings</a></li>
                </ul>
            </div>
        </li>
        <li style="width:65px">
            <div align="left">Rooms
                <ul>
                <li><a href="admin.php?pagetoload=rooms_list.php">Rooms</a></li>
                <li><a href="admin.php?pagetoload=booking_busy_list.php">Find Rooms</a></li>
                <li><a href="admin.php?pagetoload=booking_list.php">Free Rooms</a></li>
                <li><a href="admin.php?pagetoload=rooms_map3.php&small=1">Map 1 month</a></li>
                <li><a href="pdf_rooms_map3.php?small=1" target="_blank">Map 1 month PDF</a></li>
                <li><a href="admin.php?pagetoload=rooms_map3.php">Map 4 Months</a></li>
                <li><a href="pdf_rooms_map3.php" target="_blank">Map 4 Months PDF</a></li>
                </ul>
            </div>
        </li>
        <li style="width:70px">
            <div align="left">Administration
                <ul>
                <li><a href="admin.php?pagetoload=terms_list.php">Terms</a></li>
                <li><a href="admin.php?pagetoload=rooms_type_list.php">Fees</a></li>
                <li><a href="admin.php?pagetoload=countries_list.php">Countries</a></li>
                <!--<li><a href="admin.php?pagetoload=mail_accounts_list.php">Mail Accounts</a></li>-->
                <li><a href="admin.php?pagetoload=users_list.php">Users</a></li>
                <li><a href="exit.php">Exit</a></li>
                </ul>
            </div>
        </li>
    </ul>
</div>
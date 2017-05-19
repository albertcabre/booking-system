function comments(booking_id) {
    window.open('comments.php?booking_id=' + booking_id + '', 'mywindow', 'width=420,height=240,top=200,left=200');
}

function save() {
    if (document.miform.name.value !== "" && document.miform.surname.value !== "") {
        document.miform.operation.value = "save";
        document.miform.submit();
    } else {
        alert("Please indicate a name and a surname");
    }
}

function update(booking_id) {
    document.miform.operation.value = "refresh";
    document.miform.booking_id.value = booking_id;
    document.miform.submit();
}

function save_deposit(resident_id) {
    document.miform.operation.value = "save_deposit";
    document.miform.resident_id.value = resident_id;
    document.miform.submit();
}

function delete_booking(booking_id, from, to) {
    confirmation = confirm("Do you want to delete this booking?\nFrom " + from + " To " + to);
    if (confirmation) {
        document.miform.booking_id.value = booking_id;
        document.miform.operation.value = "delete";
        document.miform.submit();
    }
}

function delete_resident(name) {
    confirmation = confirm("Do you want to delete the information of " + name + " and all his accounts?");
    if (confirmation) {
        confirmation = confirm("You are going to delete the information of " + name + " and all his accounts");
        if (confirmation) {
            document.miform.operation.value = "delete_resident";
            document.miform.submit();
        }
    }
}

function change_status(status, booking_id) {
    document.miform.operation.value = "change_status";
    document.miform.status.value = status;
    document.miform.booking_id.value = booking_id;
    document.miform.submit();
}

function pdf(resident_id) {
    window.open('pdf_application_form.php?resident_id=' + resident_id, 'mywindow');
}

function pdf_outstanding(resident_id) {
    window.open('pdf_outstanding.php?resident_id=' + resident_id, 'mywindow');
}

function fees() {
    mywindow = window.open('rooms_type_list2.php', 'mywindow', 'width=400,height=400,top=25,left=25');
}

function terms() {
    mywindow = window.open('terms_list2.php', 'mywindow', 'width=900,height=500,top=25,left=25');
}

function send_mail(resident_id, email) {
    window.open('mail.php?resident' + resident_id + '=' + email, 'mywindow', 'width=650,height=400,top=50,left=50,scrollbars=1,resizable=0');
}

function send_bill(resident_id, email) {
    window.open('mail2.php?resident' + resident_id + '=' + email, 'mywindow', 'width=900,height=700,top=50,left=50,scrollbars=1,resizable=0');
}

function calculate(name) {
    expression = eval("document.miform." + name + ".value");
    value = eval(expression);
    eval("document.miform." + name + ".value=" + value);
}

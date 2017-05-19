function enableField2()
{
    document.getElementById('save_accomodation').disabled=false;
    document.getElementById('save_accomodation').className='boton_save2_out';

    document.getElementById('finish').disabled=true;
    document.getElementById('finish').className='boton_close_disabled';

    document.getElementById('delete_accomodation').disabled=true;
    document.getElementById('delete_accomodation').className='boton_delete_disabled';


    document.getElementById('room_rate').disabled=false;
    document.getElementById('laundry').disabled=false;
    document.getElementById('hc').disabled=false;
    document.getElementById('printing').disabled=false;
    document.getElementById('extra').disabled=false;
    document.getElementById('received').disabled=false;
    document.getElementById('n_bill').disabled=false;


    document.getElementById('edit_accomodation').disabled=true;
    document.getElementById('edit_accomodation').className='boton_edit2_disabled';

    document.getElementById('calendar_acc').style.display='';
}
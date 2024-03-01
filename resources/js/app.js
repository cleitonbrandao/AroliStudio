import './bootstrap';
import 'flowbite';
import Datepicker from 'flowbite-datepicker/Datepicker';

const datepickerEl = document.getElementById('datepicker');
if(datepickerEl) {
    new Datepicker(datepickerEl, {
        // format: 'dd-mm-yyyy'
        format: 'yyyy-mm-dd'
    });
}
